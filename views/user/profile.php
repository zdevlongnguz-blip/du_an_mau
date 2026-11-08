<?php
$avatarUrl = !empty($user['avatar']) ? (BASE_ASSETS_UPLOADS . $user['avatar']) : null;
$fullName = trim((string)($user['fullname'] ?? ''));
$initial = 'U';
if ($fullName !== '') {
    $parts = preg_split('/\s+/', $fullName);
    $lastNamePart = end($parts);
    $initial = mb_strtoupper(mb_substr((string)$lastNamePart, 0, 1));
    if ($initial === '') {
        $initial = 'U';
    }
}
?>

<div class="hs-container hs-account-page">
    <div class="hs-account-layout">
        <nav class="hs-account-nav" aria-label="Tài khoản">
            <a class="hs-account-nav__link is-active" href="<?= url('?action=profile') ?>"><i class="bi bi-person"></i>Hồ sơ</a>
            <a class="hs-account-nav__link" href="<?= url('?action=order-history') ?>"><i class="bi bi-receipt"></i>Đơn hàng</a>
            <a class="hs-account-nav__link" href="<?= url('?action=my-vouchers') ?>"><i class="bi bi-ticket-perforated"></i>Túi voucher</a>
            <a class="hs-account-nav__link" href="<?= url('?action=profile-in4-change') ?>"><i class="bi bi-key"></i>Đổi mật khẩu</a>
            <a class="hs-account-nav__link hs-account-nav__link--danger" href="<?= url('?action=logout') ?>"><i class="bi bi-box-arrow-right"></i>Đăng xuất</a>
        </nav>

        <section class="hs-account-main">
            <h1 class="hs-page-title">Hồ sơ</h1>

            <div class="hs-profile-head">
                <?php if (!empty($avatarUrl)): ?>
                    <img class="hs-avatar" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" />
                <?php else: ?>
                    <span class="hs-avatar hs-avatar--initial"><?= htmlspecialchars($initial) ?></span>
                <?php endif; ?>

                <div>
                    <p class="hs-profile-name"><?= htmlspecialchars($user['fullname']) ?></p>
                    <p class="hs-profile-sub"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>

            <dl class="hs-profile-list">
                <div>
                    <dt>Email</dt>
                    <dd><a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a></dd>
                </div>
                <div>
                    <dt>Tên đăng nhập</dt>
                    <dd>@<?= htmlspecialchars($user['username']) ?></dd>
                </div>
                <div>
                    <dt>Ngày tham gia</dt>
                    <dd><?= date('d/m/Y H:i:s', strtotime($user['created_at'])) ?></dd>
                </div>
            </dl>

            <div class="hs-profile-actions">
                <a href="<?= url('?action=profile-avatar-change') ?>" class="hs-btn hs-btn--ghost">Đổi ảnh đại diện</a>
                <a href="<?= url('?action=profile-in4-change') ?>" class="hs-btn hs-btn--ghost">Đổi mật khẩu</a>
            </div>
        </section>
    </div>
</div>