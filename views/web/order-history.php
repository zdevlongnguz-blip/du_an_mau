<div class="container my-5">
    <h1 class="mb-4">Lịch sử đơn hàng</h1>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        <a href="<?= BASE_URL ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>

    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ngày</th>
                    <th>Tiền</th>
                    <th>Trạng thái</th>
                    <th>Người nhận</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>$<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                        <td><?= htmlspecialchars($order['receiver_name']) ?></td>
                        <td>
                            <a href="<?= url('?action=order-invoice&id=' . $order['id']) ?>" class="btn btn-sm btn-outline-light">Xem hóa đơn</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
