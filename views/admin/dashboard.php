<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            color-scheme: dark;
            --bg: #061228;
            --surface: rgba(14, 24, 48, 0.94);
            --surface-strong: rgba(10, 16, 32, 0.96);
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.14);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --border: rgba(56, 189, 248, 0.18);
            --shadow: rgba(0, 0, 0, 0.28);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, rgba(56, 189, 248, 0.08), transparent 24%), linear-gradient(180deg, #061228 0%, #020613 100%);
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: rgba(8, 18, 40, 0.96) !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.12);
            box-shadow: 0 20px 50px var(--shadow);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #ffffff !important;
        }

        .brand-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: radial-gradient(circle, #38bdf8, #0ea5e9);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.45);
        }

        .nav-link {
            color: var(--muted) !important;
            transition: color 0.15s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff !important;
        }

        .form-label {
            color: #e2e8f0 !important;
        }

        .text-muted,
        .text-secondary,
        .small-muted,
        .text-muted.small {
            color: #94a3b8 !important;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            background-color: rgba(12, 22, 40, 0.96) !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
            color: #e2e8f0 !important;
            transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.04) !important;
            backdrop-filter: blur(4px);
        }

        .form-control::placeholder,
        .form-select::placeholder,
        textarea.form-control::placeholder {
            color: rgba(226, 232, 240, 0.55) !important;
            opacity: 1;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            background-color: rgba(10, 16, 30, 0.98) !important;
            border-color: rgba(56, 189, 248, 0.18) !important;
            box-shadow: 0 0 0 0.12rem rgba(56, 189, 248, 0.08) !important;
            outline: none !important;
            color: #e2e8f0 !important;
        }

        .table-dark thead th {
            color: #e2e8f0 !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.14);
        }

        .table-dark tbody td {
            color: #e2e8f0 !important;
            border-top: 1px solid rgba(226, 232, 240, 0.08);
        }

        .table-dark tbody td .small-muted {
            color: #94a3b8 !important;
        }

        .btn-secondary {
            color: #e2e8f0 !important;
            background-color: rgba(148, 163, 184, 0.18);
            border-color: rgba(148, 163, 184, 0.25);
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: rgba(148, 163, 184, 0.28);
            color: #ffffff !important;
        }

        .page-link,
        .page-item .page-link {
            color: #e2e8f0;
            background: rgba(10, 18, 32, 0.95);
            border: 1px solid rgba(56, 189, 248, 0.16);
        }

        .page-item.active .page-link {
            background: rgba(56, 189, 248, 0.25);
            border-color: rgba(56, 189, 248, 0.45);
            color: #ffffff;
        }

        .navbar .btn-outline-danger {
            border-color: rgba(248, 113, 113, 0.36);
            color: #fca5a5;
        }

        .navbar .btn-outline-danger:hover {
            background: rgba(248, 113, 113, 0.14);
            color: #fee2e2;
        }

        .cyber-card,
        .panel-card {
            background: linear-gradient(180deg, rgba(10, 18, 32, 0.96), rgba(8, 14, 30, 0.95));
            border: 1px solid rgba(56, 189, 248, 0.16);
            border-radius: 24px;
            box-shadow: 0 28px 60px rgba(0, 0, 0, 0.28);
            color: var(--text);
            backdrop-filter: blur(10px);
        }

        .cyber-card h2,
        .cyber-card h3,
        .cyber-card h4,
        .panel-card h2,
        .panel-card h3,
        .panel-card h4 {
            color: #f8fafc;
        }

        .badge-cyber {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.75rem;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            background: rgba(56, 189, 248, 0.18);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #f8fafc;
        }

        .cyber-btn,
        .btn-cyan {
            background: linear-gradient(135deg, #22d3ee, #0ea5e9);
            border: none;
            color: #061826;
            box-shadow: 0 14px 30px rgba(14, 165, 233, 0.22);
            border-radius: 999px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .cyber-btn:hover,
        .cyber-btn:focus,
        .btn-cyan:hover,
        .btn-cyan:focus {
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            color: #020617;
            transform: translateY(-1px);
            box-shadow: 0 18px 35px rgba(14, 165, 233, 0.25);
        }

        .btn-sm.cyber-btn {
            padding: 0.35rem 0.85rem;
            font-size: 0.78rem;
        }

        .btn-sm.btn-danger {
            border-radius: 0.8rem;
            background: rgba(248, 113, 113, 0.16);
            border-color: rgba(248, 113, 113, 0.28);
            color: #fee2e2;
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .btn-sm.btn-danger:hover,
        .btn-sm.btn-danger:focus {
            background: rgba(248, 113, 113, 0.28);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-secondary {
            border-radius: 999px;
            padding: 0.5rem 1rem;
            transition: background 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            transform: translateY(-1px);
        }

        .btn-danger {
            border-radius: 0.85rem;
            border: 1px solid rgba(248, 113, 113, 0.28);
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .table-responsive.rounded-4.overflow-hidden.shadow-sm {
            background: rgba(10, 18, 32, 0.92);
            border: 1px solid rgba(56, 189, 248, 0.12);
            border-radius: 1rem;
            padding: 0.3rem;
            box-shadow: 0 24px 45px rgba(0, 0, 0, 0.16);
        }

        .table-dark td,
        .table-dark th {
            padding: 1rem 1.15rem;
        }

        .table-dark td {
            vertical-align: middle;
        }

        .table-dark tfoot td,
        .table-dark tfoot th {
            border-top: 1px solid rgba(226, 232, 240, 0.1);
        }

        .table-dark .btn {
            border-radius: 999px;
        }

        .table-dark th,
        .table-dark td {
            border-color: rgba(255, 255, 255, 0.08);
            color: #e5e7eb;
            padding: 1rem 1rem;
            vertical-align: middle;
        }

        .table-dark thead th {
            background: rgba(15, 23, 42, 0.99);
            border-bottom: 1px solid rgba(139, 92, 246, 0.24);
            letter-spacing: 0.12em;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .table-dark tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-dark tbody tr:nth-child(odd) {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .table-dark tbody tr:hover {
            background-color: rgba(56, 189, 248, 0.08);
        }

        .table-dark td.text-muted {
            color: #94a3b8 !important;
        }

        .table-dark img {
            border-radius: 0.75rem;
            max-width: 64px;
            object-fit: cover;
            box-shadow: 0 0 18px rgba(56, 189, 248, 0.12);
        }

        .table-dark .btn-sm {
            min-width: 86px;
            padding: 0.42rem 0.75rem;
            font-size: 0.78rem;
        }

        .bg-surface {
            background-color: rgba(14, 24, 48, 0.92) !important;
            color: #94a3b8 !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        .input-group-text {
            background-color: rgba(14, 24, 48, 0.92) !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            color: #94a3b8 !important;
        }

        .pagination .page-link:hover {
            background: rgba(56, 189, 248, 0.12);
            color: #ffffff;
        }

        .table-dark .btn-danger {
            background: rgba(248, 113, 113, 0.15);
            border-color: rgba(248, 113, 113, 0.28);
            color: #fee2e2;
        }

        .table-dark .btn-danger:hover,
        .table-dark .btn-danger:focus {
            background: rgba(248, 113, 113, 0.25);
            color: #ffffff;
        }

        .badge-cyber {
            background: rgba(139, 92, 246, 0.18);
            color: #e5e7eb;
            border: 1px solid rgba(139, 92, 246, 0.32);
        }

        .divider-glow {
            height: 1px;
            margin: 1.5rem 0;
            background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.55), transparent);
        }

        .table-responsive {
            margin-top: 1rem;
        }

        .table td img {
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.18);
        }

        .small-muted {
            color: var(--muted);
        }

        .page-title {
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 1rem;
            color: #f8fafc;
        }

        .body-container {
            padding: 2rem 0 4rem;
        }

        @media (max-width: 767px) {
            .navbar .navbar-collapse {
                background: rgba(4, 6, 24, 0.97);
                margin-top: 0.75rem;
                border-radius: 1rem;
                padding: 1rem;
            }

            .body-container {
                padding: 1.5rem 0 3rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('?action=dashboard') ?>">
                <span class="brand-dot"></span>
                <span>ADMIN</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (isset($_GET['table']) && $_GET['table'] === 'users') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (!isset($_GET['table']) || $_GET['table'] === 'products') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=products') ?>">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (isset($_GET['table']) && $_GET['table'] === 'orders') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=orders') ?>">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (isset($_GET['table']) && $_GET['table'] === 'comments') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=comments') ?>">Comments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (isset($_GET['table']) && $_GET['table'] === 'categories') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=categories') ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase <?= (isset($_GET['table']) && $_GET['table'] === 'vouchers') ? 'active' : 'text-muted' ?>" href="<?= url('?action=dashboard&table=vouchers') ?>">Vouchers</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) : ?>
                        <div class="text-end">
                            <div class="text-muted small">Xin chào</div>
                            <div class="fw-semibold"><?= $_SESSION['user']['fullname'] ?></div>
                        </div>
                        <a href="<?= url('?action=logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
                    <?php else: ?>
                        <a href="<?= url('?action=login') ?>" class="btn cyber-btn btn-sm">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid body-container px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="cyber-card p-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                        <div>
                            <div class="cyber-heading">Bảng thông tin</div>
                            <h3 class="mb-1"><?= $title ?? 'Admin Control' ?></h3>
                        </div>
                        <?php if (!isset($_GET['table']) || $_GET['table'] === 'products'): ?>
                            <a href="<?= url('?action=product-create') ?>" class="btn cyber-btn">Thêm sản phẩm</a>
                        <?php endif; ?>
                        <?php if (isset($_GET['table']) && $_GET['table'] === 'categories'): ?>
                            <a href="<?= url('?action=category-create') ?>" class="btn cyber-btn">Thêm danh mục</a>
                        <?php endif; ?>
                        <?php if (isset($_GET['table']) && $_GET['table'] === 'vouchers'): ?>
                            <a href="<?= url('?action=voucher-create') ?>" class="btn cyber-btn">Thêm voucher</a>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <?php
                        if (isset($view)) {
                            require_once PATH_VIEW . $view . '.php';
                        }
                        ?>
                    </div>

                    <a href="<?= url('/') ?>" class="btn btn-secondary mt-3">Trở về trang chủ</a>

                </div>
            </div>
        </div>
    </main>

</body>

</html>