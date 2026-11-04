<div class="hs-container hs-account-page">
    <?php
        $statusMap = [
            'pending'    => ['Chờ xác nhận', 'pending'],
            'processing' => ['Đang xử lý', 'processing'],
            'confirmed'  => ['Đang xử lý', 'processing'],
            'shipped'    => ['Đang giao', 'shipped'],
            'shipping'   => ['Đang giao', 'shipped'],
            'completed'  => ['Hoàn thành', 'completed'],
            'cancelled'  => ['Đã hủy', 'cancelled'],
        ];

        $filterCounts = [
            'all' => 0,
            'pending' => 0,
            'shipped' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        foreach ($orders as $order) {
            $st = strtolower((string)($order['status'] ?? ''));
            [$statusLabel, $statusKey] = $statusMap[$st] ?? [ucfirst($st), 'pending'];
            $filterKey = in_array($statusKey, ['pending', 'processing'], true) ? 'pending' : $statusKey;
            $filterCounts['all']++;
            if (isset($filterCounts[$filterKey])) {
                $filterCounts[$filterKey]++;
            }
        }
    ?>

    <h1 class="hs-page-title">Đơn hàng của tôi</h1>

    <?php if (empty($orders)): ?>
        <div class="hs-empty-state">
            <i class="bi bi-receipt"></i>
            <h2>Bạn chưa có đơn hàng</h2>
            <a href="<?= url('?action=products') ?>" class="hs-btn hs-btn--primary">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="hs-filter" role="toolbar" aria-label="Lọc đơn hàng">
            <button type="button" class="hs-filter__btn" data-filter="all" aria-pressed="true">Tất cả (<?= (int)$filterCounts['all'] ?>)</button>
            <?php if ($filterCounts['pending'] > 0): ?>
                <button type="button" class="hs-filter__btn" data-filter="pending" aria-pressed="false">Chờ xác nhận (<?= (int)$filterCounts['pending'] ?>)</button>
            <?php endif; ?>
            <?php if ($filterCounts['shipped'] > 0): ?>
                <button type="button" class="hs-filter__btn" data-filter="shipped" aria-pressed="false">Đang giao (<?= (int)$filterCounts['shipped'] ?>)</button>
            <?php endif; ?>
            <?php if ($filterCounts['completed'] > 0): ?>
                <button type="button" class="hs-filter__btn" data-filter="completed" aria-pressed="false">Hoàn thành (<?= (int)$filterCounts['completed'] ?>)</button>
            <?php endif; ?>
            <?php if ($filterCounts['cancelled'] > 0): ?>
                <button type="button" class="hs-filter__btn" data-filter="cancelled" aria-pressed="false">Đã hủy (<?= (int)$filterCounts['cancelled'] ?>)</button>
            <?php endif; ?>
        </div>

        <?php foreach ($orders as $order): ?>
            <?php
                $st = strtolower((string)($order['status'] ?? ''));
                [$statusLabel, $statusKey] = $statusMap[$st] ?? [ucfirst($st), 'pending'];
                $filterStatusKey = in_array($statusKey, ['pending', 'processing'], true) ? 'pending' : $statusKey;
            ?>
            <article class="hs-order-card" data-status="<?= htmlspecialchars($filterStatusKey) ?>">
                <div class="hs-order-card__head">
                    <div class="hs-order-card__title">Đơn #<?= (int)$order['id'] ?></div>
                    <span class="hs-chip hs-chip--<?= htmlspecialchars($statusKey) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                </div>
                <div class="hs-order-card__meta">Người nhận: <?= htmlspecialchars($order['receiver_name']) ?></div>
                <div class="hs-order-card__date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                <div class="hs-order-card__foot">
                    <div class="hs-order-card__total"><?= number_format((float)$order['total_amount'], 0, ',', '.') ?>đ</div>
                    <a href="<?= url('?action=order-invoice&id=' . $order['id']) ?>" class="hs-btn hs-btn--ghost">Xem chi tiết →</a>
                </div>
            </article>
        <?php endforeach; ?>

        <p class="hs-empty-filter d-none">Không có đơn nào ở trạng thái này.</p>
    <?php endif; ?>

    <?php if (!empty($orders)): ?>
        <script src="<?= BASE_URL ?>assets/js/user-orders.js?v=<?= @filemtime(dirname(__DIR__, 2) . '/assets/js/user-orders.js') ?: time() ?>" defer></script>
    <?php endif; ?>
</div>
