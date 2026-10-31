<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Hóa đơn đơn hàng #<?= $order['id'] ?></h1>
            <p class="text-muted mb-0">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <div>
            <span class="badge bg-success"><?= htmlspecialchars(ucfirst($order['status'])) ?></span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card bg-dark border-light mb-4">
                <div class="card-body">
                    <h4 class="card-title text-white">Thông tin đơn hàng</h4>
                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['receiver_name']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['receiver_phone']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                    <?php if (!empty($order['note'])): ?>
                        <p><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card bg-dark border-light">
                <div class="card-body">
                    <h4 class="card-title text-white">Danh sách sản phẩm</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="d-flex gap-3 align-items-center">
                                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="80" height="80" class="img-fluid rounded">
                                            <span><?= htmlspecialchars($item['name']) ?></span>
                                        </td>
                                        <td><?= (int)$item['quantity'] ?></td>
                                        <td>$<?= number_format($item['price'], 0, ',', '.') ?></td>
                                        <td>$<?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-dark border-light">
                <div class="card-body">
                    <h4 class="card-title text-white">Tóm tắt thanh toán</h4>
                    <?php $subtotal = array_reduce($orderItems, static fn (float $sum, array $item): float => $sum + ((float)$item['price'] * (int)$item['quantity']), 0.0); ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng giá trị</span>
                        <strong>$<?= number_format($subtotal, 0, ',', '.') ?></strong>
                    </div>
                    <?php if (!empty($appliedVoucher)): ?>
                        <?php $discountAmount = $subtotal - (float)$order['total_amount']; ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Voucher <?= htmlspecialchars($appliedVoucher['code']) ?> (-<?= (int)$appliedVoucher['discount_percent'] ?>%)</span>
                            <strong>-$<?= number_format($discountAmount, 0, ',', '.') ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2 border-top pt-2">
                        <span>Thành tiền</span>
                        <strong>$<?= number_format($order['total_amount'], 0, ',', '.') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Trạng thái</span>
                        <strong><?= htmlspecialchars(ucfirst($order['status'])) ?></strong>
                    </div>
                    <a href="<?= url('?action=order-history') ?>" class="btn btn-outline-light mt-3 w-100">Quay về lịch sử đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
