<?php

class VoucherAdminController
{
    private const DISCOUNTS = [10, 30, 50, 70, 90];
    private Voucher $voucherModel;
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }
        $this->voucherModel = new Voucher();
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'], 405);
        }
    }

    private function validateRequest(string $code, string $discount, ?int $excludeId = null): array
    {
        $errors = [];
        if ($code === '') {
            $errors['code'] = 'Mã voucher không được để trống.';
        } elseif (!preg_match('/^[A-Z0-9][A-Z0-9_-]{2,19}$/', $code)) {
            $errors['code'] = 'Mã voucher dài 3-20 ký tự, chỉ gồm chữ in hoa, số, gạch ngang hoặc gạch dưới.';
        } elseif ($this->voucherModel->codeExists($code, $excludeId)) {
            $errors['code'] = 'Mã voucher đã tồn tại.';
        }

        if (!in_array((int)$discount, self::DISCOUNTS, true)) {
            $errors['discount_percent'] = 'Mức giảm chỉ được là 10%, 30%, 50%, 70% hoặc 90%.';
        }
        return $errors;
    }

    public function index(): void
    {
        $search = trim((string)($_GET['search'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->voucherModel->getFiltered($search, $page, 3);
        renderView('admin/dashboard', [
            'title' => 'ADMIN DASHBOARD / VOUCHERS',
            'view' => 'admin/quan-ly-voucher/vouchers',
            'vouchers' => $result['data'],
            'pagination' => $result['pagination'],
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        renderView('admin/dashboard', [
            'title' => 'Create voucher',
            'view' => 'admin/quan-ly-voucher/create',
            'discounts' => self::DISCOUNTS,
        ]);
    }

    public function store(): void
    {
        $this->requirePost();
        $code = strtoupper(trim((string)($_POST['code'] ?? '')));
        $discount = trim((string)($_POST['discount_percent'] ?? ''));
        $errors = $this->validateRequest($code, $discount);
        if ($errors) {
            $this->respondJson(['status' => 'error', 'errors' => $errors], 400);
        }

        try {
            $this->voucherModel->save([
                'code' => $code,
                'discount_percent' => (int)$discount,
                'user_id' => null,
                'order_id' => null,
                'is_used' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'used_at' => null,
            ]);
            $this->respondJson(['status' => 'success', 'message' => 'Voucher đã được tạo thành công!']);
        } catch (Throwable $exception) {
            $this->respondJson(['status' => 'error', 'errors' => ['general' => 'Không thể tạo voucher.']], 500);
        }
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) {
            throwMessageAndBack('Voucher không tồn tại!', 'error');
        }
        renderView('admin/dashboard', [
            'title' => 'Edit voucher',
            'view' => 'admin/quan-ly-voucher/edit',
            'voucher' => $voucher,
            'discounts' => self::DISCOUNTS,
        ]);
    }

    public function update(): void
    {
        $this->requirePost();
        $id = (int)($_POST['id'] ?? 0);
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) {
            $this->respondJson(['status' => 'error', 'errors' => ['general' => 'Voucher không tồn tại.']], 404);
        }
        if ($voucher['user_id'] !== null || (int)$voucher['is_used'] === 1 || $voucher['order_id'] !== null) {
            $this->respondJson(['status' => 'error', 'errors' => ['general' => 'Voucher đã nhận hoặc đã dùng, không thể chỉnh sửa.']], 409);
        }

        $code = strtoupper(trim((string)($_POST['code'] ?? '')));
        $discount = trim((string)($_POST['discount_percent'] ?? ''));
        $errors = $this->validateRequest($code, $discount, $id);
        if ($errors) {
            $this->respondJson(['status' => 'error', 'errors' => $errors], 400);
        }

        if (!$this->voucherModel->updateUnclaimedUnused($id, ['code' => $code, 'discount_percent' => (int)$discount])) {
            $this->respondJson(['status' => 'error', 'errors' => ['general' => 'Voucher đã thay đổi, vui lòng tải lại trang.']], 409);
        }
        $this->respondJson(['status' => 'success', 'message' => 'Voucher đã được cập nhật thành công!']);
    }

    public function delete(): void
    {
        $this->requirePost();
        $id = (int)($_POST['id'] ?? 0);
        if (!$this->voucherModel->deleteUnclaimedUnused($id)) {
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => 'Chỉ có thể xóa voucher chưa nhận và chưa dùng.']
            ], 409);
        }
        $this->respondJson(['status' => 'success', 'message' => 'Voucher đã được xóa thành công!']);
    }
}