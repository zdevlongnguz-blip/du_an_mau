<?php

class OrderController extends BaseController
{
    private Cart $cartModel;
    private Order $orderModel;
    private User $userModel;
    private Voucher $voucherModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->voucherModel = new Voucher();
    }

    public function checkout()
    {
        $items = $this->cartModel->getItems($_SESSION['user']['id']);
        if (empty($items)) {
            redirectWithFlash(url('?action=cart'), 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
        }

        $total = $this->cartModel->getTotalAmount($_SESSION['user']['id']);
        renderView('main', [
            'title' => 'Thanh toán',
            'view' => 'web/checkout',
            'cartItems' => $items,
            'cartTotal' => $total,
            'availableVouchers' => $this->voucherModel->getAvailableByUser((int)$_SESSION['user']['id']),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=checkout'), 'Yêu cầu không hợp lệ.');
        }

        $receiverName = trim($_POST['receiver_name'] ?? '');
        $receiverPhone = trim($_POST['receiver_phone'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $voucherId = isset($_POST['voucher_id']) && $_POST['voucher_id'] !== '' ? (int)$_POST['voucher_id'] : null;

        $oldInput = [
            'receiver_name' => $receiverName,
            'receiver_phone' => $receiverPhone,
            'shipping_address' => $shippingAddress,
            'note' => $note,
            'voucher_id' => $voucherId,
        ];

        $errors = [];

        if ($receiverName === '' || $receiverPhone === '' || $shippingAddress === '') {
            $errors['general'] = 'Vui lòng điền đầy đủ thông tin giao hàng.';
        }

        if ($receiverName !== '' && (mb_strlen($receiverName) < 3 || mb_strlen($receiverName) > 100)) {
            $errors['receiver_name'] = 'Tên người nhận không hợp lệ. Vui lòng nhập từ 3 đến 100 ký tự.';
        }

        if ($receiverPhone !== '' && (!function_exists('validatePhoneNumber') || !validatePhoneNumber($receiverPhone))) {
            $errors['receiver_phone'] = 'Số điện thoại không hợp lệ. Vui lòng nhập định dạng 0xxxxxxxxx hoặc +84xxxxxxxx.';
        }

        if ($shippingAddress !== '' && (mb_strlen($shippingAddress) < 10 || mb_strlen($shippingAddress) > 255)) {
            $errors['shipping_address'] = 'Địa chỉ giao hàng quá ngắn hoặc quá dài. Vui lòng nhập 10-255 ký tự.';
        }

        if (mb_strlen($note) > 500) {
            $errors['note'] = 'Ghi chú không được vượt quá 500 ký tự.';
        }

        if (!empty($errors)) {
            setOldInput($oldInput);
            setValidationErrors($errors);
            redirectWithFlash(url('?action=checkout'), 'Có lỗi trong biểu mẫu. Vui lòng kiểm tra lại.');
        }

        $items = $this->cartModel->getItems($_SESSION['user']['id']);
        if (empty($items)) {
            redirectWithFlash(url('?action=cart'), 'Giỏ hàng trống.');
        }

        try {
            $orderId = $this->orderModel->createFromCart(
                $_SESSION['user']['id'],
                [
                    'receiver_name' => $receiverName,
                    'receiver_phone' => $receiverPhone,
                    'shipping_address' => $shippingAddress,
                    'note' => $note,
                ],
                $items,
                $voucherId
            );

            if (!$orderId) {
                redirectWithFlash(url('?action=checkout'), 'Không thể tạo đơn hàng lúc này.');
            }

            $this->cartModel->clear($_SESSION['user']['id']);
            forgetOldInput();
            redirectWithFlash(url('?action=order-invoice&id=' . $orderId), 'Đơn hàng đã tạo thành công.');
        } catch (Exception $e) {
            redirectWithFlash(url('?action=checkout'), $e->getMessage());
        }
    }

    public function history()
    {
        $orders = $this->orderModel->getByUserId($_SESSION['user']['id']);
        renderView('main', [
            'title' => 'Lịch sử đơn hàng',
            'view' => 'web/order-history',
            'orders' => $orders,
        ]);
    }

    public function invoice()
    {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $order = $this->orderModel->getById($orderId, $_SESSION['user']['id']);

        if (!$order) {
            redirectWithFlash(url('?action=order-history'), 'Đơn hàng không tồn tại hoặc không có quyền truy cập.');
        }

        $items = $this->orderModel->getOrderItems($orderId);
        renderView('main', [
            'title' => 'Hóa đơn đơn hàng #' . $orderId,
            'view' => 'web/order-invoice',
            'order' => $order,
            'orderItems' => $items,
            'appliedVoucher' => $this->voucherModel->getByOrderId($orderId),
        ]);
    }

    public function adminEdit()
    {
        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard'), 'Bạn không có quyền truy cập.', 'danger');
        }

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $order = $this->orderModel->getByIdWithUser($orderId);
        if (!$order) {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Đơn hàng không tồn tại.', 'danger');
        }

        renderView('admin/dashboard', [
            'title' => 'Chỉnh sửa đơn hàng #' . $orderId,
            'view' => 'admin/quan-ly-don-hang/order-edit',
            'order' => $order,
        ]);
    }

    public function adminDetail()
    {
        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard'), 'Bạn không có quyền truy cập.', 'danger');
        }

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $order = $this->orderModel->getByIdWithUser($orderId);
        if (!$order) {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Đơn hàng không tồn tại.', 'danger');
        }

        $orderItems = $this->orderModel->getOrderItems($orderId);

        renderView('admin/dashboard', [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'view' => 'admin/quan-ly-don-hang/order-detail',
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function adminUpdate()
    {
        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard'), 'Bạn không có quyền truy cập.', 'danger');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Yêu cầu không hợp lệ.', 'danger');
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Đơn hàng không tồn tại.', 'danger');
        }

        $receiverName = trim($_POST['receiver_name'] ?? '');
        $receiverPhone = trim($_POST['receiver_phone'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $status = trim($_POST['status'] ?? '');

        $oldInput = [
            'receiver_name' => $receiverName,
            'receiver_phone' => $receiverPhone,
            'shipping_address' => $shippingAddress,
            'note' => $note,
            'status' => $status,
        ];

        $errors = [];

        if ($receiverName === '' || $receiverPhone === '' || $shippingAddress === '' || $status === '') {
            $errors['general'] = 'Vui lòng điền đầy đủ thông tin đơn hàng.';
        }

        if ($receiverName !== '' && (mb_strlen($receiverName) < 3 || mb_strlen($receiverName) > 100)) {
            $errors['receiver_name'] = 'Tên người nhận không hợp lệ. Vui lòng nhập từ 3 đến 100 ký tự.';
        }

        if ($receiverPhone !== '' && !validatePhoneNumber($receiverPhone)) {
            $errors['receiver_phone'] = 'Số điện thoại không hợp lệ. Vui lòng nhập định dạng 0xxxxxxxxx hoặc +84xxxxxxxx.';
        }

        if ($shippingAddress !== '' && (mb_strlen($shippingAddress) < 10 || mb_strlen($shippingAddress) > 255)) {
            $errors['shipping_address'] = 'Địa chỉ giao hàng quá ngắn hoặc quá dài. Vui lòng nhập 10-255 ký tự.';
        }

        if (mb_strlen($note) > 500) {
            $errors['note'] = 'Ghi chú không được vượt quá 500 ký tự.';
        }

        $allowedStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
            $errors['status'] = 'Trạng thái đơn hàng không hợp lệ.';
        }

        if (!empty($errors)) {
            setOldInput($oldInput);
            setValidationErrors($errors);
            redirectWithFlash(url('?action=order-edit&id=' . $orderId), 'Có lỗi trong biểu mẫu. Vui lòng kiểm tra lại.', 'danger');
        }

        $this->orderModel->update($orderId, [
            'receiver_name' => $receiverName,
            'receiver_phone' => $receiverPhone,
            'shipping_address' => $shippingAddress,
            'note' => $note,
            'status' => $status,
        ]);

        forgetOldInput();
        redirectWithFlash(url('?action=dashboard&table=orders'), 'Cập nhật đơn hàng thành công.');
    }

    public function adminDelete()
    {
        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard'), 'Bạn không có quyền truy cập.', 'danger');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Yêu cầu không hợp lệ.', 'danger');
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Đơn hàng không tồn tại.', 'danger');
        }

        if (!$this->orderModel->deleteOrder($orderId)) {
            redirectWithFlash(url('?action=dashboard&table=orders'), 'Xóa đơn hàng không thành công.', 'danger');
        }

        redirectWithFlash(url('?action=dashboard&table=orders'), 'Đơn hàng đã được xóa.', 'success');
    }

    public function adminExport()
    {
        if (!$this->userModel->isAdmin()) {
            redirectWithFlash(url('?action=dashboard'), 'Bạn không có quyền truy cập.', 'danger');
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $orders = $this->orderModel->getAllForExport($search);

        $filename = 'orders-export-' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Order ID', 'Username', 'Full Name', 'Phone', 'Shipping Address', 'Total Amount', 'Status', 'Created At', 'Note']);

        foreach ($orders as $order) {
            fputcsv($output, [
                $order['id'],
                $order['username'] ?? '',
                $order['user_fullname'] ?? '',
                $order['receiver_phone'],
                $order['shipping_address'],
                $order['total_amount'],
                ucfirst($order['status']),
                $order['created_at'],
                $order['note'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
