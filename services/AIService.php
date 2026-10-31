<?php

class AIService
{
    private $apiKey = GEMINI_API_KEY;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    // ==== Cấu hình Rate Limit ====
    private $rateLimitMax = 10;      // số request tối đa
    private $rateLimitWindow = 60;   // trong khoảng thời gian (giây)
    private $rateLimitDir = null;    // thư mục lưu file rate limit (nếu không dùng APCu)

    public function __construct()
    {
        $this->rateLimitDir = sys_get_temp_dir() . '/gemini_rate_limit';
        if (!is_dir($this->rateLimitDir)) {
            @mkdir($this->rateLimitDir, 0755, true);
        }
    }

    public function generateContentFromImage($imagePath, $mimeType, $productName = '')
    {
        if (!file_exists($imagePath)) return null;
        error_log('[Gemini API] generateContentFromImage called; imagePath=' . $imagePath . '; mimeType=' . $mimeType . '; productName=' . $productName);

        $imageData = base64_encode(file_get_contents($imagePath));

        $prompt = "Bạn là chuyên gia marketing kiêm người viết content hài hước. Hãy quan sát thật kỹ hình ảnh sản phẩm này";
        if (!empty($productName)) {
            $prompt .= " (sản phẩm có tên gợi ý là: '{$productName}')";
        }
        $prompt .= ", sau đó viết bài mô tả sản phẩm thật thu hút, nêu rõ kiểu dáng, màu sắc và tính năng theo vibe hài hước kèm lời kêu gọi hành động (CTA) để khách hàng muốn mua ngay. "
                . "Viết ngắn gọn, đủ ý, khoảng 80-140 từ, chỉ dùng 1-2 đoạn văn và có thể kèm 1-3 bullet ngắn nếu cần. "
                . "Trả về định dạng HTML (dùng <p>, <strong>, <ul>, <li>), không bao gồm ký tự ```html.";

        $contents = [
            [
                "parts" => [
                    ["text" => $prompt],
                    [
                        "inlineData" => [
                            "mimeType" => $mimeType,
                            "data" => $imageData
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->callGeminiApi($contents, '[Gemini API][generateContentFromImage]');
        if ($result === null) return null;

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    /**
     * Kiểm duyệt một bình luận bằng AI.
     *
     * @return array|null ['allowed'=>bool,'label'=>string,'reason'=>string] hoặc null nếu lỗi/bị rate limit
     */
    public function moderateComment($commentText, array $context = [])
    {
        $commentText = trim((string)$commentText);
        if ($commentText === '') return null;

        error_log('[Gemini API] moderateComment called; length=' . mb_strlen($commentText));

        $contextNote = '';
        if (!empty($context['product_name'])) {
            $contextNote = " Bình luận này thuộc về sản phẩm: '{$context['product_name']}'.";
        }

        $prompt = "Bạn là hệ thống kiểm duyệt bình luận cho một website bán hàng." . $contextNote . " "
                . "Hãy đọc bình luận sau và đánh giá xem có nên duyệt hiển thị công khai hay không. "
                . "Từ chối nếu bình luận chứa: ngôn từ thô tục/xúc phạm/toxic, spam, quảng cáo/link không liên quan, "
                . "lừa đảo, thông tin cá nhân nhạy cảm (SĐT, địa chỉ...), hoặc nội dung không liên quan tới sản phẩm/dịch vụ. "
                . "Bình luận bình thường, góp ý, khen chê hợp lý thì được duyệt.\n\n"
                . "Bình luận cần kiểm duyệt:\n\"\"\"\n{$commentText}\n\"\"\"\n\n"
                . "CHỈ trả về một JSON object hợp lệ (không markdown, không giải thích thêm) theo đúng format:\n"
                . '{"allowed": true hoặc false, "label": "clean" hoặc "spam" hoặc "toxic" hoặc "ads" hoặc "other", "reason": "lý do ngắn gọn bằng tiếng Việt"}';

        $contents = [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ];

        $result = $this->callGeminiApi($contents, '[Gemini API][moderateComment]', [
            'response_mime_type' => 'application/json',
        ]);
        if ($result === null) return null;

        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($text === null) {
            error_log('[Gemini API][moderateComment] no text in response: ' . json_encode($result));
            return null;
        }

        $text = trim($text);
        $text = preg_replace('/^```json\s*|\s*```$/i', '', $text);

        $parsed = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
            error_log('[Gemini API][moderateComment] invalid JSON from model: ' . $text);
            return null;
        }

        return [
            'allowed' => (bool)($parsed['allowed'] ?? false),
            'label'   => $parsed['label'] ?? 'other',
            'reason'  => $parsed['reason'] ?? '',
        ];
    }

    public function chat(string $prompt)
    {
        error_log('[Gemini API] chat called; prompt length=' . mb_strlen($prompt));

        $contents = [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ];

        $result = $this->callGeminiApi($contents, '[Gemini API][chat]', [
            'temperature' => 0.7,
            'maxOutputTokens' => 1024,
            'response_mime_type' => 'text/plain',
        ]);

        if ($result === null) {
            return null;
        }

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function generateTriviaQuiz(): ?array
    {
        $prompt = "Tạo đúng 5 câu hỏi trắc nghiệm ngắn bằng tiếng Việt về PHP cơ bản. "
            . "Câu 1 đến 3 ở mức nhập môn, câu 4 đến 5 nâng cao nhẹ nhưng vẫn phù hợp người mới học. "
            . "Tập trung vào cú pháp, kiểu dữ liệu, biến, hàm, mảng, session, PDO hoặc bảo mật cơ bản. "
            . "Mỗi câu chỉ có một đáp án ngắn, rõ ràng, không cần giải thích, tránh câu hỏi gây tranh cãi. "
                . "Chỉ trả về JSON array hợp lệ, không markdown, theo format: "
                . '[{"question":"...","answer":"..."}]';

        $contents = [[
            'parts' => [['text' => $prompt]],
        ]];
        $result = $this->callGeminiApi($contents, '[Gemini API][generateTriviaQuiz]', [
            'temperature' => 0.8,
            'maxOutputTokens' => 512,
            'response_mime_type' => 'application/json',
        ]);

        if ($result === null) {
            return null;
        }

        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            return null;
        }

        $quiz = $this->decodeTriviaQuiz($text);
        if (!is_array($quiz) || count($quiz) !== 5) {
            error_log('[Gemini API][generateTriviaQuiz] invalid quiz: ' . $text);
            return null;
        }

        foreach ($quiz as $item) {
            if (!is_array($item) || trim((string)($item['question'] ?? '')) === '' || trim((string)($item['answer'] ?? '')) === '') {
                return null;
            }
        }

        return array_map(static function (array $item): array {
            return [
                'question' => trim((string)$item['question']),
                'answer' => trim((string)$item['answer']),
            ];
        }, $quiz);
    }

    private function decodeTriviaQuiz(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text);
        $text = trim((string)$text);

        $start = strpos($text, '[');
        $end = strrpos($text, ']');
        if ($start !== false && $end !== false && $end >= $start) {
            $quiz = json_decode(substr($text, $start, $end - $start + 1), true);
            if (is_array($quiz)) {
                return $quiz;
            }
        }

        preg_match_all('/\{(?:[^{}"\\]|\\.|"(?:[^"\\]|\\.)*")*\}/s', $text, $matches);
        if (count($matches[0]) !== 5) {
            return null;
        }

        $quiz = array_map(static fn(string $item): mixed => json_decode($item, true), $matches[0]);
        return count(array_filter($quiz, 'is_array')) === 5 ? $quiz : null;
    }

    public function fallbackTriviaQuiz(): array
    {
        return [
            ['question' => 'Tệp mã nguồn PHP thường có phần mở rộng nào?', 'answer' => '.php'],
            ['question' => 'Lệnh nào dùng để xuất dữ liệu đơn giản trong PHP?', 'answer' => 'echo'],
            ['question' => 'Toán tử nào dùng để so sánh bằng cả giá trị và kiểu dữ liệu?', 'answer' => '==='],
            ['question' => 'Hàm nào kiểm tra biến đã tồn tại và khác null?', 'answer' => 'isset'],
            ['question' => 'PDO với prepared statement giúp phòng chống kiểu tấn công nào?', 'answer' => 'SQL injection'],
        ];
    }

    /**
     * Hàm dùng chung để gọi Gemini API, có kiểm tra rate limit trước khi gọi.
     */
    private function callGeminiApi(array $contents, string $logPrefix = '[Gemini API]', array $generationConfig = [])
    {
        // ==== Kiểm tra rate limit trước khi gọi API ====
        if (!$this->checkRateLimit('gemini_global')) {
            error_log("{$logPrefix} rate limit exceeded ({$this->rateLimitMax} req / {$this->rateLimitWindow}s)");
            return null;
        }

        $url = $this->apiUrl . "?key=" . $this->apiKey;

        $payload = ["contents" => $contents];
        if (!empty($generationConfig)) {
            $payload['generationConfig'] = $generationConfig;
        }

        $jsonPayload = json_encode($payload);
        $response = null;
        $httpCode = 0;

        if (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => $jsonPayload,
                    'ignore_errors' => true,
                    'timeout' => 30,
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                error_log("{$logPrefix} file_get_contents failed for URL: {$url}");
            }
            if (isset($http_response_header) && preg_match('#^HTTP/\d\.\d\s+(\d{3})#i', $http_response_header[0], $matches)) {
                $httpCode = (int)$matches[1];
            }
        } else {
            error_log("{$logPrefix} allow_url_fopen is disabled; cannot call API.");
            return null;
        }

        if ($httpCode !== 200 || empty($response)) {
            $responseLog = is_string($response) ? $response : json_encode($response);
            error_log("{$logPrefix} request failed - httpCode={$httpCode} response={$responseLog}");
            return null;
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("{$logPrefix} invalid JSON response: " . json_last_error_msg());
            return null;
        }

        return $result;
    }

    /**
     * Kiểm tra + cập nhật rate limit theo kiểu sliding window.
     * Trả về true nếu còn được phép gọi, false nếu đã vượt giới hạn.
     *
     * @param string $key Định danh nhóm rate limit, vd: 'gemini_global',
     *                     hoặc theo user/IP: 'gemini_user_123'
     */
    private function checkRateLimit(string $key): bool
    {
        $now = microtime(true);
        $windowStart = $now - $this->rateLimitWindow;

        // Ưu tiên dùng APCu nếu có (nhanh, không cần I/O file)
        if (function_exists('apcu_enabled') && apcu_enabled()) {
            $apcuKey = 'gemini_rl_' . $key;
            $timestamps = apcu_fetch($apcuKey);
            if (!is_array($timestamps)) $timestamps = [];

            // Loại bỏ các timestamp đã hết hạn window
            $timestamps = array_values(array_filter($timestamps, function ($t) use ($windowStart) {
                return $t > $windowStart;
            }));

            if (count($timestamps) >= $this->rateLimitMax) {
                return false;
            }

            $timestamps[] = $now;
            apcu_store($apcuKey, $timestamps, $this->rateLimitWindow + 5);
            return true;
        }

        // Fallback: dùng file + flock
        $file = $this->rateLimitDir . '/' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key) . '.json';
        $fp = fopen($file, 'c+');
        if ($fp === false) {
            // Không tạo/mở được file thì cho qua để tránh chặn nhầm toàn bộ hệ thống
            error_log('[RateLimit] cannot open rate limit file: ' . $file);
            return true;
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return true; // không lấy được lock thì cho qua, không chặn oan
        }

        $content = stream_get_contents($fp);
        $timestamps = json_decode($content, true);
        if (!is_array($timestamps)) $timestamps = [];

        $timestamps = array_values(array_filter($timestamps, function ($t) use ($windowStart) {
            return $t > $windowStart;
        }));

        $allowed = count($timestamps) < $this->rateLimitMax;

        if ($allowed) {
            $timestamps[] = $now;
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($timestamps));
            fflush($fp);
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $allowed;
    }
}