<?php

class GameController extends BaseController
{
    private const COOLDOWN_SECONDS = 300;
    private Voucher $voucherModel;
    private AIService $aiService;

    public function __construct()
    {
        parent::__construct();
        $this->voucherModel = new Voucher();
        $this->aiService = new AIService();
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function hasCooldown(): bool
    {
        return !empty($_SESSION['mini_game_completed_at'])
            && time() - (int)$_SESSION['mini_game_completed_at'] < self::COOLDOWN_SECONDS;
    }

    public function page()
    {
        $todayAward = $this->voucherModel->getTodayAward((int)$_SESSION['user']['id']);

        renderView('main', [
            'title' => 'Mở hộp may mắn - Nhận voucher',
            'view' => 'web/mini-game',
            'todayAward' => $todayAward,
            'hasPlayedToday' => $todayAward !== null,
            'vouchers' => $this->voucherModel->getByUser((int)$_SESSION['user']['id']),
            'recentAwards' => $this->voucherModel->getRecentAwards(),
            'hasCooldown' => $this->hasCooldown(),
            'cooldownRemaining' => $this->hasCooldown() ? self::COOLDOWN_SECONDS - (time() - (int)$_SESSION['mini_game_completed_at']) : 0,
            'cartItemCount' => getCartItemCount(),
        ]);
    }

    public function start()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 405);
        }

        if ($this->hasCooldown()) {
            $remaining = self::COOLDOWN_SECONDS - (time() - (int)$_SESSION['mini_game_completed_at']);
            return $this->respondJson(['status' => 'error', 'message' => "Bạn vừa chơi xong. Hãy quay lại sau khoảng {$remaining} giây."], 409);
        }

        $fallback = false;
        try {
            $quiz = $this->aiService->generateTriviaQuiz();
        } catch (Throwable $exception) {
            error_log('[GameController][mini-game-start] AI quiz failed: ' . $exception->getMessage());
            $quiz = null;
        }

        if (!is_array($quiz) || count($quiz) !== 5) {
            $quiz = $this->aiService->fallbackTriviaQuiz();
            $fallback = true;
        }

        $_SESSION['mini_game_quiz'] = $quiz;
        $_SESSION['mini_game_progress'] = 0;
        $_SESSION['mini_game_correct'] = 0;
        return $this->respondJson([
            'status' => 'success',
            'fallback' => $fallback,
            'message' => $fallback ? 'AI đang bận, hệ thống đã chuyển sang bộ câu hỏi dự phòng.' : null,
            'question' => ['index' => 0, 'question' => $quiz[0]['question']],
        ]);
    }

    public function answer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 405);
        }

        $quiz = $_SESSION['mini_game_quiz'] ?? null;
        $index = (int)($_SESSION['mini_game_progress'] ?? 0);
        if (!is_array($quiz) || count($quiz) !== 5 || $index >= 5) {
            return $this->respondJson(['status' => 'error', 'message' => 'Phiên chơi đã hết.'], 400);
        }

        $given = trim((string)($_POST['answer'] ?? ''));
        $expected = trim((string)$quiz[$index]['answer']);
        $isCorrect = is_numeric(str_replace(',', '.', $given)) && is_numeric(str_replace(',', '.', $expected))
            ? abs((float)str_replace(',', '.', $given) - (float)str_replace(',', '.', $expected)) < 0.001
            : mb_strtolower($given) === mb_strtolower($expected);

        if ($isCorrect) {
            $_SESSION['mini_game_correct'] = (int)($_SESSION['mini_game_correct'] ?? 0) + 1;
        }

        $_SESSION['mini_game_progress'] = $index + 1;
        $payload = [
            'status' => 'success',
            'correct' => $isCorrect,
            'correct_answer' => $expected,
            'question_number' => $index + 1,
        ];

        if ($index < 4) {
            $payload['next_question'] = ['index' => $index + 1, 'question' => $quiz[$index + 1]['question']];
            return $this->respondJson($payload);
        }

        $totalCorrect = (int)$_SESSION['mini_game_correct'];
        $discountMap = [1 => 10, 2 => 30, 3 => 50, 4 => 70, 5 => 90];
        $voucher = $totalCorrect > 0 ? $this->voucherModel->claimExisting((int)$_SESSION['user']['id'], $discountMap[$totalCorrect]) : null;
        if ($totalCorrect > 0 && !$voucher) {
            return $this->respondJson(['status' => 'error', 'message' => "Bạn đúng {$totalCorrect}/5 câu nhưng voucher mức này đã hết."], 409);
        }

        $_SESSION['mini_game_completed_at'] = time();
        unset($_SESSION['mini_game_quiz'], $_SESSION['mini_game_progress'], $_SESSION['mini_game_correct']);
        $payload['finished'] = true;
        $payload['total_correct'] = $totalCorrect;
        $payload['message'] = $totalCorrect > 0
            ? "Bạn đúng {$totalCorrect}/5 câu và nhận voucher {$voucher['discount_percent']}%!"
            : 'Bạn chưa trả lời đúng câu nào. Hẹn bạn sau 5 phút nhé!';
        $payload['voucher'] = $voucher;
        return $this->respondJson($payload);
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 405);
        }

        if ($this->hasCooldown()) {
            $remaining = self::COOLDOWN_SECONDS - (time() - (int)$_SESSION['mini_game_completed_at']);
            return $this->respondJson(['status' => 'error', 'message' => "Bạn vừa chơi xong. Hãy quay lại sau khoảng {$remaining} giây."], 409);
        }

        $quiz = $_SESSION['mini_game_quiz'] ?? null;
        if (!is_array($quiz) || count($quiz) !== 5) {
            return $this->respondJson(['status' => 'error', 'message' => 'Phiên chơi đã hết. Hãy tạo bộ câu hỏi mới.'], 400);
        }

        $answers = $_POST['answers'] ?? [];
        $correct = 0;
        foreach ($quiz as $index => $item) {
            $given = trim((string)($answers[$index] ?? ''));
            $expected = trim((string)$item['answer']);
            if (is_numeric(str_replace(',', '.', $given)) && is_numeric(str_replace(',', '.', $expected))) {
                $correct += abs((float)str_replace(',', '.', $given) - (float)str_replace(',', '.', $expected)) < 0.001 ? 1 : 0;
            } elseif (mb_strtolower($given) === mb_strtolower($expected)) {
                $correct++;
            }
        }

        $discountMap = [1 => 10, 2 => 30, 3 => 50, 4 => 70, 5 => 90];
        $voucher = $correct > 0 ? $this->voucherModel->claimExisting((int)$_SESSION['user']['id'], $discountMap[$correct]) : null;

        if ($correct > 0 && !$voucher) {
            return $this->respondJson(['status' => 'error', 'message' => "Bạn đúng {$correct}/5 câu nhưng voucher mức này đã hết. Vui lòng báo admin."], 409);
        }

        $_SESSION['mini_game_completed_at'] = time();
        unset($_SESSION['mini_game_quiz'], $_SESSION['mini_game_progress'], $_SESSION['mini_game_correct']);

        return $this->respondJson([
            'status' => 'success',
            'correct' => $correct,
            'message' => $correct > 0 ? "Bạn đúng {$correct}/5 câu và nhận được voucher {$voucher['discount_percent']}%!" : 'Bạn chưa trả lời đúng câu nào. Hẹn bạn sau 5 phút nhé!',
            'voucher' => $voucher,
        ]);
    }
}
