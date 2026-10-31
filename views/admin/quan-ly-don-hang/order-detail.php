<div class="row g-4">
    <div class="col-12">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start align-items-md-center">
                <div>
                    <h3 class="mb-1">Chi tiết đơn hàng #<?= (int)($order['id'] ?? 0) ?></h3>
                    <p class="text-muted mb-0">Ngày tạo: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['created_at'] ?? '')) ) ?></p>
                </div>
                <div>
                    <span class="badge badge-cyber"><?= htmlspecialchars(ucfirst($order['status'] ?? '')) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="panel-card p-4 mb-4">
            <h4 class="mb-3">Thông tin khách hàng</h4>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div><strong>Khách hàng:</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($order['user_fullname'] ?? $order['username'] ?? 'N/A') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div><strong>Tên tài khoản:</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($order['username'] ?? 'N/A') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div><strong>Email:</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($order['user_email'] ?? 'N/A') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div><strong>Tổng giá trị:</strong></div>
                    <div class="text-muted">$<?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="col-12">
                    <div><strong>Ghi chú:</strong></div>
                    <div class="text-muted"><?= nl2br(htmlspecialchars($order['note'] ?? 'Không có')) ?></div>
                </div>
            </div>
        </div>

        <div class="panel-card p-4 mb-4">
            <h4 class="mb-3">Thông tin giao hàng</h4>
            <div class="mb-2"><strong>Người nhận:</strong></div>
            <div class="text-muted mb-3"><?= htmlspecialchars($order['receiver_name'] ?? '') ?></div>
            <div class="mb-2"><strong>Điện thoại:</strong></div>
            <div class="text-muted mb-3"><?= htmlspecialchars($order['receiver_phone'] ?? '') ?></div>
            <div class="mb-2"><strong>Địa chỉ:</strong></div>
            <div class="text-muted"><?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?></div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="panel-card p-4 mb-4">
            <h4 class="mb-3">Danh sách sản phẩm</h4>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orderItems)): ?>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="72" height="72" class="rounded-3 object-fit-cover">
                                            <div>
                                                <div><?= htmlspecialchars($item['name']) ?></div>
                                                <div class="small-muted">ID: <?= (int)$item['product_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                    <td class="text-end">$<?= number_format($item['price'], 0, ',', '.') ?></td>
                                    <td class="text-end">$<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Không có sản phẩm trong đơn hàng.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-card p-4">
            <h4 class="mb-3">Tổng kết</h4>
            <div class="d-flex justify-content-between mb-2">
                <span>Số dòng sản phẩm</span>
                <strong><?= count($orderItems) ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Tổng đơn hàng</span>
                <strong>$<?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?></strong>
            </div>
            <a href="<?= url('?action=dashboard&table=orders') ?>" class="btn btn-outline-light w-100">Quay lại danh sách</a>
            <a href="<?= url('?action=order-edit&id=' . (int)$order['id']) ?>" class="btn cyber-btn w-100 mt-2">Chỉnh sửa đơn hàng</a>
        </div>
    </div>
</div>
