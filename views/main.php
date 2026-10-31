<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Home' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL . 'assets/css/user-theme.css' ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>

    <?php
    // Number of items in the cart, passed in by the controller or derived from session.
    $cartItemCount = isset($cartItemCount) ? (int)$cartItemCount : getCartItemCount();
    ?>

    <header class="site-header">
        <nav class="navbar navbar-dark">
            <div class="container-fluid px-3 px-lg-5 d-flex align-items-center">

                <!-- Hamburger: compact, mobile + tablet -->
                <button class="hamburger-btn d-lg-none me-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu"
                    aria-label="Mở menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <a class="navbar-brand me-auto me-lg-4" href="<?= BASE_URL ?>">Hustler<span>.</span>420</a>

                <!-- Desktop inline nav -->
                <ul class="navbar-nav d-none d-lg-flex flex-row mx-auto gap-1">
                    <li class="nav-item"><a class="<?= navLinkClass('/') ?>" href="<?= BASE_URL ?>" <?= isActiveRoute('/') ? 'aria-current="page"' : '' ?>>Trang chủ</a></li>
                    <li class="nav-item"><a class="<?= navLinkClass('products') ?>" href="<?= url('?action=products') ?>" <?= isActiveRoute('products') ? 'aria-current="page"' : '' ?>>Sản phẩm</a></li>
                    <li class="nav-item"><a class="<?= navLinkClass('ai-chat') ?>" href="<?= url('?action=ai-chat') ?>" <?= isActiveRoute('ai-chat') ? 'aria-current="page"' : '' ?>>Trợ lý AI</a></li>
                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) : ?>
                        <li class="nav-item"><a class="<?= navLinkClass('mini-game') ?>" href="<?= url('?action=mini-game') ?>" <?= isActiveRoute('mini-game') ? 'aria-current="page"' : '' ?>>Nhận voucher</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-2 ms-2 ms-lg-3">

                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) : ?>

                        <a href="<?= url('?action=cart') ?>" class="<?= iconBtnClass('cart') ?>" title="Giỏ hàng">
                            <i class="bi bi-bag"></i>
                            <?php if (!empty($cartItemCount)): ?>
                                <span class="cart-badge"><?= (int)$cartItemCount ?></span>
                            <?php endif; ?>
                        </a>

                        <a href="<?= url('?action=order-history') ?>" class="d-none d-md-inline-flex <?= iconBtnClass('order-history') ?>" title="Đơn hàng">
                            <i class="bi bi-receipt"></i>
                        </a>

                        <div class="dropdown d-none d-sm-block">
                            <button class="<?= iconBtnClass(['profile', 'dashboard']) ?> dropdown-toggle" style="border-radius:999px;" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
                                <i class="bi bi-person"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom mt-2">
                                <li class="px-3 py-2 small text-muted">
                                    Xin chào, <strong class="text-light"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: var(--border);"></li>
                                <li><a class="dropdown-item" href="<?= url('?action=profile') ?>"><i class="bi bi-person-circle me-2"></i>Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="<?= url('?action=my-vouchers') ?>"><i class="bi bi-ticket-perforated me-2"></i>Túi voucher</a></li>
                                <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                                    <li><a class="dropdown-item" href="<?= url('?action=dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Trang quản trị</a></li>
                                <?php endif ?>
                                <li><hr class="dropdown-divider" style="border-color: var(--border);"></li>
                                <li><a class="dropdown-item" href="<?= url('?action=logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>

                        <!-- Mobile: person icon opens the same offcanvas -->
                        <button class="icon-btn d-sm-none" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <i class="bi bi-person"></i>
                        </button>

                    <?php else: ?>
                        <a href="<?= url('?action=login') ?>" class="btn-mono">Đăng nhập</a>
                    <?php endif; ?>

                </div>
            </div>
        </nav>
    </header>

    <!-- ===================== OFFCANVAS MOBILE MENU ===================== -->
    <div class="offcanvas offcanvas-start offcanvas-mono" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <a class="navbar-brand mb-0" href="<?= BASE_URL ?>">Hustler<span>.</span>420</a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <ul class="navbar-nav offcanvas-nav">
                <li class="nav-item"><a class="<?= navLinkClass('/') ?>" href="<?= BASE_URL ?>" <?= isActiveRoute('/') ? 'aria-current="page"' : '' ?>>Trang chủ <i class="bi bi-chevron-right small"></i></a></li>
                <li class="nav-item"><a class="<?= navLinkClass('products') ?>" href="<?= url('?action=products') ?>" <?= isActiveRoute('products') ? 'aria-current="page"' : '' ?>>Sản phẩm <i class="bi bi-chevron-right small"></i></a></li>
                <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) : ?>
                    <li class="nav-item"><a class="<?= navLinkClass('mini-game') ?>" href="<?= url('?action=mini-game') ?>" <?= isActiveRoute('mini-game') ? 'aria-current="page"' : '' ?>>Nhận voucher <i class="bi bi-gift small"></i></a></li>
                    <li class="nav-item">
                        <a class="<?= navLinkClass('cart') ?>" href="<?= url('?action=cart') ?>">
                            Giỏ hàng
                            <span class="d-flex align-items-center gap-2">
                                <?php if (!empty($cartItemCount)): ?>
                                    <span class="badge rounded-pill" style="background:var(--accent); color:var(--accent-contrast);"><?= (int)$cartItemCount ?></span>
                                <?php endif; ?>
                                <i class="bi bi-chevron-right small"></i>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item"><a class="<?= navLinkClass('order-history') ?>" href="<?= url('?action=order-history') ?>" <?= isActiveRoute('order-history') ? 'aria-current="page"' : '' ?>>Đơn hàng <i class="bi bi-chevron-right small"></i></a></li>
                <?php endif; ?>
            </ul>

            <div class="offcanvas-account">
                <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) : ?>
                    <p class="small text-muted mb-2">Xin chào, <strong><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong></p>
                    <div class="d-grid gap-2">
                        <a href="<?= url('?action=profile') ?>" class="btn btn-outline-dark btn-sm">Hồ sơ</a>
                        <a href="<?= url('?action=my-vouchers') ?>" class="btn btn-outline-dark btn-sm">Túi voucher</a>
                        <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                            <a href="<?= url('?action=dashboard') ?>" class="btn btn-outline-dark btn-sm">Trang quản trị</a>
                        <?php endif ?>
                        <a href="<?= url('?action=logout') ?>" class="btn-mono text-center">Đăng xuất</a>
                    </div>
                <?php else: ?>
                    <a href="<?= url('?action=login') ?>" class="btn-mono w-100 text-center d-block">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="container mt-4">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flash = getFlashMessage()): ?>
        <?php $flashType = getFlashMessageType(); ?>
        <div class="container mt-4">
            <div class="alert alert-<?= in_array($flashType, ['success', 'danger', 'warning', 'info'], true) ? $flashType : 'info' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <main>
        <?php
        if (isset($view)) {
            require_once PATH_VIEW . $view . '.php';
        }
        ?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <a class="navbar-brand d-inline-block mb-3" href="<?= BASE_URL ?>">Hustler<span>.</span>420</a>
                    <p class="mb-0">Mang đến trải nghiệm mua sắm hiện đại, an toàn và đáng tin cậy cho người dùng Việt Nam.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Mua sắm</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                        <li><a href="<?= url('?action=products') ?>">Sản phẩm</a></li>
                        <li><a href="<?= url('?action=mini-game') ?>">Nhận voucher</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Hỗ trợ</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= url('?action=order-history') ?>">Đơn hàng</a></li>
                        <li><a href="<?= url('?action=profile') ?>">Hồ sơ</a></li>
                        <li><a href="<?= url('?action=ai-chat') ?>">Trợ lý AI</a></li>
                    </ul>
                </div>
                <div class="col-12 col-lg-3">
                    <h6>Cam kết</h6>
                    <div class="warning-line mb-2"><span class="badge-age">18+</span> <span>Chỉ dành cho người trên 18 tuổi</span></div>
                    <p class="mb-0">Sử dụng có trách nhiệm và tuân thủ quy định pháp luật hiện hành.</p>
                </div>
            </div>
            <hr class="mt-4" style="border-color: var(--border);">
            <p class="mb-0 text-center small">&copy; <?= date('Y') ?> Hustler420. Mọi quyền được bảo lưu.</p>
        </div>
    </footer>

</body>

</html>