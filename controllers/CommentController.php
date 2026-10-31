<?php

class CommentController extends BaseController
{
    private Comment $commentModel;
    private Product $productModel;
    private User $userModel;
    private AIService $aiService;

    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new Comment();
        $this->productModel = new Product();
        $this->userModel = new User();
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

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=product-detail&id=' . ($_POST['product_id'] ?? 0)), 'Yêu cầu không hợp lệ.');
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $content = trim($_POST['content'] ?? '');
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;

        if ($productId <= 0) {
            $message = 'Sản phẩm không hợp lệ.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 400);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $productId), $message);
        }

        $product = $this->productModel->detail($productId);
        if (!$product) {
            $message = 'Sản phẩm không tồn tại.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 404);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $productId), $message);
        }

        if ($content === '') {
            $message = 'Vui lòng nhập nội dung bình luận.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 400);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $productId), $message);
        }

        $moderation = $this->aiService->moderateComment($content, [
            'product_name' => $product['name'] ?? '',
        ]);

        if ($moderation === null || !is_array($moderation) || !isset($moderation['allowed'])) {
            $status = 'pending';
        } elseif (!$moderation['allowed']) {
            $reason = $moderation['reason'] ?? 'nội dung không phù hợp với quy tắc cộng đồng';
            $message = 'Bình luận của bạn không được duyệt: ' . $reason;
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 400);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $productId), $message);
        } else {
            $status = 'approved';
        }

        $this->commentModel->add($_SESSION['user']['id'], $productId, $content, $rating, $status);

        $successMessage = $status === 'pending'
            ? 'Bình luận của bạn đã được lưu và đang chờ duyệt.'
            : 'Bình luận của bạn đã được gửi.';

        if ($this->isAjax()) {
            return $this->respondJson(['status' => 'success', 'message' => $successMessage]);
        }

        redirectWithFlash(url('?action=product-detail&id=' . $productId), $successMessage);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=/'), 'Yêu cầu không hợp lệ.');
        }

        $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $content = trim($_POST['content'] ?? '');
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;

        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            $message = 'Bình luận không tồn tại.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 404);
            }
            redirectWithFlash(url('?action=/'), $message);
        }

        if ($comment['user_id'] !== ($_SESSION['user']['id'] ?? null) && !$this->userModel->isAdmin()) {
            $message = 'Bạn không có quyền chỉnh sửa bình luận này.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 403);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $comment['product_id']), $message);
        }

        if ($content === '') {
            $message = 'Nội dung bình luận không được để trống.';
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'errors' => ['content' => $message]], 400);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $comment['product_id']), $message);
        }

        $rating = max(1, min(5, $rating));
        $this->commentModel->update($commentId, ['content' => $content, 'rating' => $rating]);

        if ($this->isAjax()) {
            return $this->respondJson(['status' => 'success', 'message' => 'Bình luận đã được cập nhật.']);
        }

        redirectWithFlash(url('?action=product-detail&id=' . $comment['product_id']), 'Bình luận đã được cập nhật.');
    }

    public function adminDetail()
    {
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $comment = $this->commentModel->findWithProductAndUser($id);
        if (!$comment) {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Bình luận không tồn tại.', 'error');
        }

        renderView('admin/dashboard', [
            'title' => 'Chi tiết bình luận',
            'view' => 'admin/quan-ly-comments/comment-detail',
            'comment' => $comment,
        ]);
    }

    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Yêu cầu không hợp lệ.', 'error');
        }

        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Bạn không có quyền duyệt bình luận.', 'error');
        }

        $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Bình luận không tồn tại.', 'error');
        }

        if (($comment['status'] ?? '') !== 'pending') {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Bình luận này không còn ở trạng thái chờ duyệt.', 'error');
        }

        if (!$this->commentModel->approve($commentId)) {
            redirectWithFlash(url('?action=dashboard&table=comments&status=pending'), 'Không thể duyệt bình luận, vui lòng thử lại.', 'error');
        }

        redirectWithFlash(url('?action=dashboard&table=comments&status=pending'), 'Bình luận đã được duyệt.');
    }

    public function delete()
    {
        $isAdmin = $this->userModel->isAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.'], 400);
            }
            redirectWithFlash($isAdmin ? url('?action=dashboard&table=comments') : url('?action=/'), 'Yêu cầu không hợp lệ.');
        }

        $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $comment = $this->commentModel->find($commentId);

        if (!$comment) {
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'message' => 'Bình luận không tồn tại.'], 404);
            }
            redirectWithFlash($isAdmin ? url('?action=dashboard&table=comments') : url('?action=/'), 'Bình luận không tồn tại.');
        }

        if ($comment['user_id'] !== ($_SESSION['user']['id'] ?? null) && !$isAdmin) {
            if ($this->isAjax()) {
                return $this->respondJson(['status' => 'error', 'message' => 'Bạn không có quyền xóa bình luận này.'], 403);
            }
            redirectWithFlash(url('?action=product-detail&id=' . $comment['product_id']), 'Bạn không có quyền xóa bình luận này.');
        }

        $this->commentModel->delete($commentId);
        if ($this->isAjax()) {
            return $this->respondJson(['status' => 'success', 'message' => 'Bình luận đã được xóa.']);
        }

        if ($isAdmin) {
            redirectWithFlash(url('?action=dashboard&table=comments'), 'Bình luận đã được xóa.');
        }

        redirectWithFlash(url('?action=product-detail&id=' . $comment['product_id']), 'Bình luận đã được xóa.');
    }
}
