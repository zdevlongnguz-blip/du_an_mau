<div class="hs-container hs-invoice-page">
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

        $st = strtolower((string)($order['status'] ?? ''));
        [$statusLabel, $statusKey] = $statusMap[$st] ?? [ucfirst($st), 'pending'];
        $subtotal = array_reduce($orderItems, static fn (float $sum, array $item): float => $sum + ((float)$item['price'] * (int)$item['quantity']), 0.0);
        $discountAmount = 0.0;
        if (!empty($appliedVoucher)) {
            $discountAmount = max(0, $subtotal - (float)$order['total_amount']);
        }
    ?>

    <div class="hs-order-head">
        <div>
            <h1 class="hs-page-title">Đơn hàng #<?= (int)$order['id'] ?></h1>
            <p class="hs-order-head__meta">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <div class="hs-order-head__right">
            <span class="hs-chip hs-chip--<?= htmlspecialchars($statusKey) ?>"><?= htmlspecialchars($statusLabel) ?></span>
            <button type="button" class="hs-btn hs-btn--ghost" onclick="window.print()">In hóa đơn</button>
        </div>
    </div>

    <?php if ($st === 'cancelled'): ?>
        <div class="hs-cancel-line"><i class="bi bi-x-circle"></i><span>Đơn đã hủy</span></div>
    <?php else: ?>
        <?php $progressSteps = ['pending' => ['Chờ xác nhận', 1], 'processing' => ['Đang xử lý', 2], 'confirmed' => ['Đang xử lý', 2], 'shipped' => ['Đang giao', 3], 'shipping' => ['Đang giao', 3], 'completed' => ['Hoàn thành', 4]]; ?>
        <ol class="hs-progress" aria-label="Tiến độ đơn hàng">
            <?php $currentStep = $progressSteps[$st]['1'] ?? 1; ?>
            <?php foreach ($progressSteps as $stepKey => [$stepLabel, $stepNumber]): ?>
                <?php $isDone = $stepNumber < $currentStep; $isCurrent = $stepNumber === $currentStep; ?>
                <li class="hs-progress__step <?= $isDone ? 'is-done' : '' ?> <?= $isCurrent ? 'is-current' : '' ?>">
                    <span class="hs-progress__num"><?= $isDone ? '<i class="bi bi-check-lg"></i>' : $stepNumber ?></span>
                    <span><?= htmlspecialchars($stepLabel) ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <div class="hs-invoice-layout">
        <div class="hs-invoice-main">
            <section class="hs-panel">
                <h2 class="hs-section-title">Sản phẩm</h2>
                <?php foreach ($orderItems as $item): ?>
                    <div class="hs-invoice-item">
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="64" height="64">
                        <div class="hs-invoice-item__info">
                            <div class="hs-invoice-item__name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="hs-invoice-item__qty"><?= (int)$item['quantity'] ?> × <?= number_format((float)$item['price'], 0, ',', '.') ?>đ</div>
                        </div>
                        <div class="hs-invoice-item__total"><?= number_format((float)($item['price'] * $item['quantity']), 0, ',', '.') ?>đ</div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="hs-panel">
                <h2 class="hs-section-title">Người nhận</h2>
                <dl class="hs-dl">
                    <dt>Người nhận</dt>
                    <dd><?= htmlspecialchars($order['receiver_name']) ?></dd>
                    <dt>Số điện thoại</dt>
                    <dd><?= htmlspecialchars($order['receiver_phone']) ?></dd>
                    <dt>Địa chỉ</dt>
                    <dd><?= htmlspecialchars($order['shipping_address']) ?></dd>
                    <?php if (!empty($order['note'])): ?>
                        <dt>Ghi chú</dt>
                        <dd><?= htmlspecialchars($order['note']) ?></dd>
                    <?php endif; ?>
                </dl>
            </section>
        </div>

        <aside class="hs-checkout-summary">
            <div class="hs-summary-card">
                <h2 class="hs-summary-title">Tóm tắt thanh toán</h2>
                <div class="hs-summary-row">
                    <span>Tạm tính</span>
                    <strong><?= number_format((float)$subtotal, 0, ',', '.') ?>đ</strong>
                </div>
                <?php if (!empty($appliedVoucher)): ?>
                    <div class="hs-summary-row hs-summary-row--voucher">
                        <span>Voucher <?= htmlspecialchars($appliedVoucher['code']) ?> (-<?= (int)$appliedVoucher['discount_percent'] ?>%)</span>
                        <strong class="hs-summary-row__discount">-<?= number_format((float)$discountAmount, 0, ',', '.') ?>đ</strong>
                    </div>
                <?php endif; ?>
                <div class="hs-summary-row hs-summary-row--total">
                    <span>Tổng</span>
                    <strong><?= number_format((float)$order['total_amount'], 0, ',', '.') ?>đ</strong>
                </div>
                <a href="<?= url('?action=order-history') ?>" class="hs-btn hs-btn--ghost hs-btn--block">Về lịch sử đơn</a>
            </div>
        </aside>
    </div>
</div>
