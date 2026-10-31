<!DOCTYPE html>
<html lang="vi" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Home' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg: #0b0b0b;
            --bg-soft: #141414;
            --card: #17181a;
            --border: rgba(255, 255, 255, 0.08);
            --text: #e9edf1;
            --muted: rgba(233,237,241,0.78);
            --white: #ffffff;
            --accent: #ff7a1a;
            --accent-contrast: #0b0b0b;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, .brand-font {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.02em;
        }

        a { text-decoration: none; }

        /* ---------- NAVBAR (compact) ---------- */
        .site-header {
            background: rgba(11, 11, 11, 0.92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .navbar-brand {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--white) !important;
            font-size: 1.1rem;
        }

        .navbar-brand span { color: var(--accent); }

        .site-header .nav-link {
            color: var(--muted) !important;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.5rem 0.85rem !important;
            transition: color .15s ease;
        }

        .site-header .nav-link:hover,
        .site-header .nav-link.active,
        .site-header .nav-link:focus {
            color: var(--accent) !important;
            outline: none;
        }

        /* Compact hamburger trigger */
        .hamburger-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hamburger-btn:hover { border-color: var(--accent); color: var(--accent); }

        .icon-btn {
            color: var(--text);
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 999px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            transition: border-color .2s ease, color .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .icon-btn:hover,
        .icon-btn:focus,
        .icon-btn.active {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(255,122,26,0.1);
            box-shadow: 0 0 0 0.16rem rgba(255,122,26,0.15);
            outline: none;
        }

        .icon-btn:focus-visible {
            outline: none;
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--accent);
            color: var(--accent-contrast);
            font-size: 0.62rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.05rem 0.35rem;
            min-width: 17px;
            text-align: center;
        }

        .btn-mono {
            background: var(--accent);
            color: var(--accent-contrast);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.78rem;
            border-radius: 999px;
            border: 1px solid var(--accent);
            padding: 0.5rem 1rem;
        }

        .btn-mono:hover { background: transparent; color: var(--accent); }

        .dropdown-menu-dark-custom {
            background: var(--card);
            border: 1px solid var(--border);
        }

        .dropdown-menu-dark-custom .dropdown-item {
            color: var(--text);
            font-size: 0.85rem;
        }

        .dropdown-menu-dark-custom .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: var(--accent);
        }

        /* ---------- OFFCANVAS MOBILE MENU ---------- */
        .offcanvas-mono {
            background: var(--bg-soft);
            border-right: 1px solid var(--border);
            width: 280px;
            color: var(--text);
        }

        .offcanvas-mono .offcanvas-header {
            border-bottom: 1px solid var(--border);
            padding: 1.1rem 1.25rem;
        }

        .offcanvas-mono .offcanvas-body {
            padding: 1.25rem;
        }

        .offcanvas-mono .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.7;
        }

        .offcanvas-nav .nav-link {
            color: var(--text) !important;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.85rem 0.25rem !important;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .offcanvas-nav .nav-link:hover,
        .offcanvas-nav .nav-link.active,
        .offcanvas-nav .nav-link:focus {
            color: var(--accent) !important;
            background: rgba(255,122,26,0.08);
        }
        .offcanvas-nav .nav-link:last-child { border-bottom: 0; }

        .offcanvas-account {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        /* ---------- FOOTER ---------- */
        .site-footer {
            background: var(--bg-soft);
            border-top: 1px solid var(--border);
            padding: 3.5rem 0 1.5rem;
            margin-top: 2rem;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .site-footer h6 {
            color: var(--text);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .site-footer a { color: var(--muted); }
        .site-footer a:hover { color: var(--accent); }

        .alert {
            border-radius: 0.6rem;
            border: 1px solid var(--border);
        }

        /* ----- Layout / Card fixes for product-detail ----- */
        main {
            padding-top: 3.5rem; /* ensure content clears sticky header */
        }

        /* Normalize card backgrounds so dark cards match theme and don't appear as empty panels */
        .card {
            background: var(--card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 0.75rem;
        }

        /* Distinguish purely black blocks when needed */
        .card.bg-black { background: var(--bg-soft) !important; }

        /* Utility to ensure images using object-fit behave consistently */
        .object-fit-cover { object-fit: cover; }

        /* Improve contrast for muted/secondary text (match Home page clarity) */
        .text-muted {
            color: var(--muted) !important;
            opacity: 1 !important;
        }

        .text-secondary {
            color: rgba(233,237,241,0.82) !important;
        }

        small, .small {
            color: var(--muted) !important;
        }

        /* Slightly brighter labels inside cards */
        .card .text-muted { color: rgba(233,237,241,0.7) !important; }
    </style>
</head>

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
                                <li><a class="dropdown-item" href="<?= url('?action=profile') ?>"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?= url('?action=my-vouchers') ?>"><i class="bi bi-ticket-perforated me-2"></i>Túi voucher</a></li>
                                <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                                    <li><a class="dropdown-item" href="<?= url('?action=dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                <?php endif ?>
                                <li><hr class="dropdown-divider" style="border-color: var(--border);"></li>
                                <li><a class="dropdown-item" href="<?= url('?action=logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>

                        <!-- Mobile: person icon opens the same offcanvas -->
                        <button class="icon-btn d-sm-none" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <i class="bi bi-person"></i>
                        </button>

                    <?php else: ?>
                        <a href="<?= url('?action=login') ?>" class="btn-mono">Login</a>
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
                    <p class="small text-muted mb-2">Xin chào, <strong class="text-light"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong></p>
                    <div class="d-grid gap-2">
                        <a href="<?= url('?action=profile') ?>" class="btn btn-outline-light btn-sm">Profile</a>
                        <a href="<?= url('?action=my-vouchers') ?>" class="btn btn-outline-light btn-sm">Túi voucher</a>
                        <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                            <a href="<?= url('?action=dashboard') ?>" class="btn btn-outline-light btn-sm">Admin Panel</a>
                        <?php endif ?>
                        <a href="<?= url('?action=logout') ?>" class="btn-mono text-center">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="<?= url('?action=login') ?>" class="btn-mono w-100 text-center d-block">Login</a>
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
                <div class="col-6 col-lg-3">
                    <a class="navbar-brand d-inline-block mb-2" href="<?= BASE_URL ?>">Shop<span>.</span>Demo</a>
                    <p class="mb-0">Chất lượng thật, giá tốt, giao hàng nhanh trên toàn quốc.</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Mua sắm</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                        <li><a href="<?= url('?action=products') ?>">Sản phẩm</a></li>
                        <li><a href="<?= url('?action=products') ?>">Danh mục</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Hỗ trợ</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= url('?action=order-history') ?>">Tra cứu đơn hàng</a></li>
                        <li><a href="<?= url('?action=login') ?>">Tài khoản</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Kết nối</h6>
                    <div class="d-flex gap-2">
                        <span class="icon-btn"><i class="bi bi-facebook"></i></span>
                        <span class="icon-btn"><i class="bi bi-instagram"></i></span>
                        <span class="icon-btn"><i class="bi bi-tiktok"></i></span>
                    </div>
                </div>
            </div>
            <hr class="mt-4" style="border-color: var(--border);">
            <p class="mb-0 text-center small">&copy; <?= date('Y') ?> Shop Demo. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>