<style>
    .auth-page {
        min-height: calc(100vh - 5rem);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: radial-gradient(circle at top, rgba(255,122,26,0.18), transparent 38%), rgba(10, 12, 16, 0.98);
    }

    .auth-card {
        width: min(100%, 460px);
        background: rgba(15, 19, 27, 0.98);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 28px;
        padding: 2rem;
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.35);
    }

    .auth-card h2 {
        margin-bottom: 1.5rem;
        font-size: 2rem;
        letter-spacing: 0.04em;
        color: #fff;
    }

    .auth-card .form-label {
        color: rgba(255,255,255,0.75);
    }

    .auth-card .form-control {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.14);
        color: #f8fafc;
    }

    .auth-card .form-control:focus {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,122,26,0.6);
        box-shadow: 0 0 0 0.16rem rgba(255,122,26,0.18);
    }

    .auth-card .form-text,
    .auth-card .text-muted {
        color: rgba(226,232,240,0.68);
    }

    .auth-card .btn-primary {
        background: #ff7a1a;
        border-color: #ff7a1a;
    }

    .auth-card .btn-primary:hover {
        background: #e26911;
        border-color: #e26911;
    }

    .auth-card .text-decoration-underline {
        color: #e2e8f0;
    }

    .auth-card .text-decoration-underline:hover {
        color: #ff7a1a;
    }

    .auth-card .error-msg {
        min-height: 1rem;
        display: block;
    }
</style>

<div class="auth-page">
    <div class="auth-card">
        <h2>Đăng nhập</h2>
        <form action="<?= url('?action=auth') ?>" method="POST">

            <?php if (isset($message)): ?>
                <div class="alert alert-danger p-2 small text-center mb-3" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control form-control-lg fs-6 border-secondary-subtle" id="email" name="email" placeholder="Nhập email" required autocomplete="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control form-control-lg fs-6 border-secondary-subtle" id="password" name="password" placeholder="Mật khẩu" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg fs-6 fw-medium py-2 shadow-sm">Đăng Nhập</button>
            <div class="text-center mt-3">
                <a href="<?= url('?action=forgot-password') ?>" class="text-decoration-underline">Quên mật khẩu?</a>
            </div>
            <div class="text-center mt-3">
                <p class="text-muted">Chưa có tài khoản? <a class="text-decoration-underline" href="<?= url('?action=register') ?>">Đăng ký</a></p>
            </div>
        </form>
    </div>
</div>