<?php
$statuses = ['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
?>

<form action="<?= url('?action=order-update') ?>" method="POST" class="mb-4">
    <input type="hidden" name="order_id" value="<?= (int)($order['id'] ?? 0) ?>">

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="panel-card p-4">
                <h5 class="mb-3">Thông tin đơn hàng</h5>
                <div class="row gy-2">
                    <div class="col-12 col-md-4">
                        <strong>Order ID:</strong> #<?= (int)($order['id'] ?? 0) ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at'] ?? '') ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <strong>Tổng tiền:</strong> $<?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <strong>Khách hàng:</strong> <?= htmlspecialchars($order['user_fullname'] ?? $order['username'] ?? 'N/A') ?>
                    </div>
                    <div class="col-12 col-md-4">
                        <strong>Tên tài khoản:</strong> <?= htmlspecialchars($order['username'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="mb-3">
                <label class="form-label">Tên người nhận</label>
                <input type="text" name="receiver_name" class="form-control <?= validationError('receiver_name') ? 'is-invalid' : '' ?>" value="<?= old('receiver_name') ?: htmlspecialchars($order['receiver_name'] ?? '') ?>" required>
                <div class="invalid-feedback d-block"><?= validationError('receiver_name') ?></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="receiver_phone" class="form-control <?= validationError('receiver_phone') ? 'is-invalid' : '' ?>" value="<?= old('receiver_phone') ?: htmlspecialchars($order['receiver_phone'] ?? '') ?>" required>
                <div class="invalid-feedback d-block"><?= validationError('receiver_phone') ?></div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label class="form-label">Địa chỉ giao hàng</label>
                <textarea name="shipping_address" rows="3" class="form-control <?= validationError('shipping_address') ? 'is-invalid' : '' ?>" required><?= old('shipping_address') ?: htmlspecialchars($order['shipping_address'] ?? '') ?></textarea>
                <div class="invalid-feedback d-block"><?= validationError('shipping_address') ?></div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="note" rows="2" class="form-control <?= validationError('note') ? 'is-invalid' : '' ?>"><?= old('note') ?: htmlspecialchars($order['note'] ?? '') ?></textarea>
                <div class="invalid-feedback d-block"><?= validationError('note') ?></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select <?= validationError('status') ? 'is-invalid' : '' ?>" required>
                    <option value="">Chọn trạng thái</option>
                    <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?= $key ?>" <?= (old('status') ?: ($order['status'] ?? '')) === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback d-block"><?= validationError('status') ?></div>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn cyber-btn">Lưu thay đổi</button>
            <a href="<?= url('?action=dashboard&table=orders') ?>" class="btn btn-outline-light ms-2">Quay lại</a>
        </div>
    </div>
</form>
