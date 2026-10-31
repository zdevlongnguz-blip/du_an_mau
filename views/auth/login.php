<div class="auth-shell">
    <div class="auth-panel">
        <h2>Đăng nhập</h2>
        <form class="auth-form" action="<?= url('?action=auth') ?>" method="POST">

            <?php if (isset($message)): ?>
                <div class="auth-alert" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="auth-row">
                <label for="email" class="auth-label">Email</label>
                <input type="email" class="auth-field" id="email" name="email" placeholder="Nhập email" required autocomplete="email">
            </div>

            <div class="auth-row">
                <label for="password" class="auth-label">Mật khẩu</label>
                <input type="password" class="auth-field" id="password" name="password" placeholder="Mật khẩu" required autocomplete="current-password">
            </div>

            <button type="submit" class="auth-submit">Đăng nhập</button>

            <div class="text-center mt-3">
                <a href="<?= url('?action=forgot-password') ?>" class="auth-link">Quên mật khẩu?</a>
            </div>
            <div class="auth-divider"></div>
            <div class="text-center mt-3">
                <p class="auth-muted">Chưa có tài khoản? <a class="auth-link" href="<?= url('?action=register') ?>">Đăng ký</a></p>
            </div>
        </form>
    </div>
</div>