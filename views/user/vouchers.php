<?php
$activeVoucherCount = 0;
$usedVoucherCount = 0;
foreach ($vouchers as $voucher) {
    if ((int)$voucher['is_used'] === 1) {
        $usedVoucherCount++;
    } else {
        $activeVoucherCount++;
    }
}
$defaultVoucherFilter = $activeVoucherCount > 0 ? 'active' : 'used';
?>

<div class="hs-container hs-account-page">
    <div class="hs-account-layout">
        <nav class="hs-account-nav" aria-label="Tài khoản">
            <a class="hs-account-nav__link" href="<?= url('?action=profile') ?>"><i class="bi bi-person"></i>Hồ sơ</a>
            <a class="hs-account-nav__link" href="<?= url('?action=order-history') ?>"><i class="bi bi-receipt"></i>Đơn hàng</a>
            <a class="hs-account-nav__link is-active" href="<?= url('?action=my-vouchers') ?>"><i class="bi bi-ticket-perforated"></i>Túi voucher</a>
            <a class="hs-account-nav__link" href="<?= url('?action=profile-in4-change') ?>"><i class="bi bi-key"></i>Đổi mật khẩu</a>
            <a class="hs-account-nav__link hs-account-nav__link--danger" href="<?= url('?action=logout') ?>"><i class="bi bi-box-arrow-right"></i>Đăng xuất</a>
        </nav>

        <section class="hs-account-main">
            <div class="hs-voucher-head">
                <h1 class="hs-page-title">Túi voucher</h1>
                <a class="hs-btn hs-btn--primary" href="<?= url('?action=mini-game') ?>">Nhận thêm voucher</a>
            </div>

            <?php if (empty($vouchers)): ?>
                <div class="hs-empty-state">
                    <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
                    <h2>Bạn chưa có voucher</h2>
                    <p class="hs-hint">Hãy chơi mini game để nhận mã.</p>
                    <a class="hs-btn hs-btn--primary" href="<?= url('?action=mini-game') ?>">Chơi mini game</a>
                </div>
            <?php else: ?>
                <div class="hs-filter" role="tablist" aria-label="Lọc voucher">
                    <button type="button" class="hs-filter__btn" data-filter="active" aria-pressed="<?= $defaultVoucherFilter === 'active' ? 'true' : 'false' ?>">Còn hiệu lực (<?= $activeVoucherCount ?>)</button>
                    <button type="button" class="hs-filter__btn" data-filter="used" aria-pressed="<?= $defaultVoucherFilter === 'used' ? 'true' : 'false' ?>">Đã dùng (<?= $usedVoucherCount ?>)</button>
                </div>

                <div class="hs-voucher-list">
                    <?php foreach ($vouchers as $voucher): ?>
                        <?php $isUsed = (int)$voucher['is_used'] === 1; ?>
                        <article class="hs-ticket <?= $isUsed ? 'is-used' : '' ?>" data-status="<?= $isUsed ? 'used' : 'active' ?>">
                            <div class="hs-ticket__pct">GIẢM <strong><?= (int)$voucher['discount_percent'] ?>%</strong></div>
                            <div class="hs-ticket__body">
                                <div class="hs-ticket__code">
                                    <code><?= htmlspecialchars($voucher['code']) ?></code>
                                    <button type="button" class="hs-btn hs-btn--ghost hs-btn--icon copy-voucher" data-code="<?= htmlspecialchars($voucher['code']) ?>" aria-label="Sao chép mã" title="Sao chép mã"><i class="bi bi-copy"></i></button>
                                </div>
                                <div class="hs-ticket__meta">Nhận ngày <?= date('d/m/Y H:i', strtotime($voucher['created_at'])) ?></div>
                                <?php if ($isUsed): ?>
                                    <span class="hs-chip">Đã dùng</span>
                                <?php else: ?>
                                    <a class="hs-btn hs-btn--ghost" href="<?= url('?action=cart') ?>">Dùng ngay →</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <p class="hs-hint hs-voucher-empty" hidden>Không có voucher nào ở mục này.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    document.querySelectorAll('.copy-voucher').forEach((button) => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(button.dataset.code);
            const original = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check2"></i>';
            setTimeout(() => button.innerHTML = original, 1200);
        });
    });

    const filterButtons = document.querySelectorAll('.hs-filter__btn');
    const voucherItems = document.querySelectorAll('.hs-ticket');
    const voucherEmpty = document.querySelector('.hs-voucher-empty');

    const applyVoucherFilter = (status) => {
        if (!voucherItems.length) {
            return;
        }

        let visibleCount = 0;
        filterButtons.forEach((button) => {
            const isCurrent = button.dataset.filter === status;
            button.setAttribute('aria-pressed', isCurrent ? 'true' : 'false');
        });

        voucherItems.forEach((item) => {
            const matches = item.dataset.status === status;
            item.hidden = !matches;
            if (matches) {
                visibleCount++;
            }
        });

        if (voucherEmpty) {
            voucherEmpty.hidden = visibleCount !== 0;
        }
    };

    const defaultFilter = (() => {
        const activeCount = [...voucherItems].filter((item) => item.dataset.status === 'active').length;
        const usedCount = [...voucherItems].filter((item) => item.dataset.status === 'used').length;
        return activeCount > 0 ? 'active' : usedCount > 0 ? 'used' : 'active';
    })();

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            applyVoucherFilter(button.dataset.filter);
        });
    });

    applyVoucherFilter(defaultFilter);
</script>
