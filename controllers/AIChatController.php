<?php

class AIChatController
{
    private Product $productModel;
    private Category $categoryModel;
    private AIService $aiService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->aiService = new AIService();
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function getChatHistory(): array
    {
        $history = $_SESSION['ai_chat_history'] ?? [];
        return array_values(array_slice($history, -5));
    }

    private function saveChatHistory(array $history): void
    {
        $_SESSION['ai_chat_history'] = $this->pruneChatHistory($history);
    }

    private function pruneChatHistory(array $history): array
    {
        $history = array_slice($history, -5);
        return array_values($history);
    }

    public function page()
    {
        $chatError = $_SESSION['ai_chat_error'] ?? null;
        $chatSuccess = $_SESSION['ai_chat_success'] ?? null;
        unset($_SESSION['ai_chat_error'], $_SESSION['ai_chat_success']);

        renderView('main', [
            'title' => 'Trợ lý mua sắm AI',
            'view' => 'web/ai-chat',
            'chatHistory' => $this->getChatHistory(),
            'chatError' => $chatError,
            'chatSuccess' => $chatSuccess,
            'cartItemCount' => isset($_SESSION['user']['id']) ? (new Cart())->getItemCount($_SESSION['user']['id']) : 0,
        ]);
    }

    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 400);
        }

        $message = trim($_POST['message'] ?? '');
        if ($message === '') {
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'message' => 'Vui lòng nhập nội dung chat.'], 400);
            }
            $_SESSION['ai_chat_error'] = 'Vui lòng nhập nội dung chat.';
            header('Location: ' . url('?action=ai-chat'));
            exit;
        }

        $products = $this->productModel->all();
        $categories = $this->categoryModel->all();
        $history = $this->getChatHistory();
        $history[] = ['role' => 'user', 'text' => $message];

        $prompt = $this->buildPrompt($message, $products, $categories, $history);
        $aiResponse = $this->aiService->chat($prompt);

        if ($aiResponse === null) {
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'message' => 'Không thể gọi AI. Vui lòng thử lại sau.'], 500);
            }
            $_SESSION['ai_chat_error'] = 'Không thể gọi AI. Vui lòng thử lại sau.';
            header('Location: ' . url('?action=ai-chat'));
            exit;
        }

        $aiResponse = trim($aiResponse);
        $aiResponse = preg_replace('#<script.*?>.*?</script>#is', '', $aiResponse);
        $aiResponse = $this->sanitizeAiHtml($aiResponse);
        $history[] = ['role' => 'assistant', 'text' => $aiResponse];
        $this->saveChatHistory($history);

        if ($this->isAjax()) {
            $chatHistory = $this->getChatHistory();
            return $this->respondJson([
                'status' => 'success',
                'message' => $aiResponse,
                'historyHtml' => $this->renderChatHistoryHtml($chatHistory),
            ]);
        }

        header('Location: ' . url('?action=ai-chat'));
        exit;
    }

    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 400);
        }

        unset($_SESSION['ai_chat_history']);

        if ($this->isAjax()) {
            return $this->respondJson(['status' => 'success', 'message' => 'Đã xóa lịch sử chat.']);
        }

        $_SESSION['ai_chat_success'] = 'Đã xóa lịch sử chat.';
        header('Location: ' . url('?action=ai-chat'));
        exit;
    }

    private function sanitizeAiHtml(string $html): string
    {
        $allowedTags = [
            'div', 'p', 'strong', 'b', 'i', 'em', 'ul', 'ol', 'li', 'a', 'img', 'h1', 'h2', 'h3', 'h4', 'br', 'span'
        ];
        $allowedAttributes = ['href', 'src', 'alt', 'title', 'target', 'rel'];

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $this->sanitizeDomNode($doc->documentElement, $allowedTags, $allowedAttributes);

        $wrapper = $doc->getElementsByTagName('div')->item(0);
        $result = '';
        foreach ($wrapper->childNodes as $child) {
            $result .= $doc->saveHTML($child);
        }

        return $result;
    }

    private function sanitizeDomNode(DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        if ($node instanceof DOMElement) {
            if (!in_array($node->tagName, $allowedTags, true)) {
                $this->unwrapNode($node);
                return;
            }

            $attributesToRemove = [];
            foreach ($node->attributes ?? [] as $attribute) {
                if (!in_array($attribute->name, $allowedAttributes, true)) {
                    $attributesToRemove[] = $attribute->name;
                }
            }
            foreach ($attributesToRemove as $attr) {
                $node->removeAttribute($attr);
            }

            if ($node->tagName === 'img' && $node->hasAttribute('src')) {
                $src = trim($node->getAttribute('src'));
                if ($src !== '') {
                    // Correct common AI placeholder values.
                    $src = str_replace(['BASE_ASSETS_UPLOADS', 'BASE_ASSET_UPLOADS', 'BASE_ASSET_UPLOAD'], rtrim(BASE_ASSETS_UPLOADS, '/'), $src);
                    if (!preg_match('#^(https?:)?//#i', $src)) {
                        $src = rtrim(BASE_ASSETS_UPLOADS, '/') . '/' . ltrim($src, '/');
                    }
                    $node->setAttribute('src', $src);
                }
            }

            if ($node->tagName === 'a' && $node->hasAttribute('href')) {
                $href = trim($node->getAttribute('href'));
                if ($href !== '' && !preg_match('#^(https?:)?//#i', $href) && strpos($href, '#') !== 0) {
                    $href = rtrim(BASE_URL, '/') . '/' . ltrim($href, '/');
                    $node->setAttribute('href', $href);
                }
                if ($node->hasAttribute('target') && strtolower($node->getAttribute('target')) === '_blank') {
                    $node->setAttribute('rel', 'noopener noreferrer');
                }
            }
        }

        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if ($child instanceof DOMComment) {
                $node->removeChild($child);
                continue;
            }
            $this->sanitizeDomNode($child, $allowedTags, $allowedAttributes);
        }
    }

    private function unwrapNode(DOMNode $node): void
    {
        $parent = $node->parentNode;
        if (!$parent) {
            return;
        }
        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }
        $parent->removeChild($node);
    }

    private function renderChatHistoryHtml(array $history): string
    {
        if (empty($history)) {
            return '<div class="d-flex justify-content-start mb-3"><div class="ai-message-bubble assistant"><span class="ai-message-meta">Hustler420 AI</span><div>Chào mừng đến với Hustler420! Tôi sẽ giúp bạn tìm sản phẩm phù hợp và tư vấn nhanh.</div></div></div>';
        }

        $html = '';
        foreach ($history as $item) {
            $isAssistant = ($item['role'] ?? '') === 'assistant';
            $bubbleClass = $isAssistant ? 'assistant' : 'user';
            $label = $isAssistant ? 'Hustler420 AI' : 'Bạn';
            $message = $isAssistant ? ($item['text'] ?? '') : htmlspecialchars($item['text'] ?? '', ENT_QUOTES, 'UTF-8');
            $html .= '<div class="d-flex mb-3 ' . ($isAssistant ? 'justify-content-start' : 'justify-content-end') . '">';
            $html .= '<div class="ai-message-bubble ' . $bubbleClass . '">';
            $html .= '<span class="ai-message-meta">' . $label . '</span>';
            $html .= '<div class="ai-chat-message">' . $message . '</div>';
            $html .= '</div></div>';
        }

        return $html;
    }

    private function buildPrompt(string $userMessage, array $products, array $categories, array $history): string
    {
        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[(int)($category['id'] ?? 0)] = $category['name'] ?? '';
        }

        $productRows = [];
        foreach ($products as $product) {
            $productRows[] = sprintf(
                "- id: %s | Tên: %s | Giá: %s | Danh mục: %s | Mô tả: %s | Ảnh: %s | Link: %s",
                $product['id'] ?? '',
                trim($product['name'] ?? ''),
                number_format((float)($product['price'] ?? 0), 0, ',', '.'),
                $categoryMap[(int)($product['category_id'] ?? 0)] ?? 'Không rõ',
                trim(str_replace(["\r", "\n"], [' ', ' '], $product['description'] ?? '')), 
                !empty($product['image']) ? BASE_ASSETS_UPLOADS . ltrim($product['image'], '/') : 'Không có ảnh',
                url('?action=product-detail&id=' . ((int)$product['id']))
            );
        }

        $historyText = '';
        if (!empty($history)) {
            $historyText = "Ngữ cảnh hội thoại trước đó (chỉ dùng làm tham chiếu):\n";
            foreach ($history as $entry) {
                $role = $entry['role'] === 'assistant' ? 'AI' : 'Người dùng';
                $historyText .= "{$role}: " . trim(str_replace(["\r", "\n"], [' ', ' '], $entry['text'] ?? '')) . "\n";
            }
            $historyText .= "\n";
        }

        $instructions = "Bạn là trợ lý mua sắm AI cho cửa hàng Hustler420. Dữ liệu sản phẩm hiện có được liệt kê bên dưới. "
            . "Luôn trả lời ngắn gọn, đúng mục tiêu và sát yêu cầu của khách. "
            . "Nếu câu hỏi liên quan đến sản phẩm, gợi ý mua sắm, giá cả, danh mục, hãy trả lời bằng thông tin thật từ dữ liệu hiện có. "
            . "Không trả lời các vấn đề ngoài lề, không lan man, và nếu nội dung không liên quan tới mua sắm hoặc sản phẩm tại Hustler420, hãy trả lời ngắn gọn rằng bạn chỉ hỗ trợ tư vấn mua sắm tại Hustler420. "
            . "Bạn vẫn có thể hỏi đáp bình thường nếu câu hỏi là về sản phẩm, chính sách mua hàng, thanh toán hoặc giao hàng. "
            . "Nếu có sản phẩm phù hợp, hãy tạo các thẻ HTML đơn giản cho mỗi sản phẩm gồm tên, giá, mô tả ngắn, danh mục, link chi tiết và ảnh nếu có. "
            . "Sử dụng cấu trúc HTML sạch trong div.product-card cho sản phẩm, ví dụ: <div class=\"product-card\"><h3>Tên sản phẩm</h3><p>Giá...</p><img src=\"https://example.com/assets/uploads/...\" alt=\"...\"><a href=\"https://example.com/...?action=product-detail&id=...\">Xem chi tiết</a></div>. "
            . "Ảnh phải là URL đầy đủ bắt đầu bằng http:// hoặc https://, không dùng placeholder PHP hay biến BASE_ASSETS_UPLOADS. "
            . "Chỉ trả về HTML nội dung, KHÔNG chứa thẻ <html>, <head>, <body> hay <script>. "
            . "Không được tự chế sản phẩm, chỉ dùng sản phẩm có trong danh sách. "
            . "Nếu không có sản phẩm phù hợp, trả lời: 'Hiện tại hệ thống chưa có sản phẩm phù hợp với yêu cầu của bạn.'\n\n";

        $prompt = $instructions
            . "Danh mục hiện có:\n";

        foreach ($categories as $category) {
            $prompt .= sprintf("- %s\n", trim($category['name'] ?? ''));
        }

        $prompt .= "\nDanh sách sản phẩm:\n" . implode("\n", $productRows) . "\n\n";
        $prompt .= $historyText;
        $prompt .= "Yêu cầu mới: " . trim($userMessage) . "\n";
        $prompt .= "Trả lời bằng HTML sạch, dùng các thẻ <div>, <h3>, <p>, <strong>, <img>, <a>, <ul>, <li>.\n";
        $prompt .= "Nếu bạn có thể gợi ý sản phẩm, hãy hiển thị ít nhất một thẻ sản phẩm.\n";

        return $prompt;
    }
}
