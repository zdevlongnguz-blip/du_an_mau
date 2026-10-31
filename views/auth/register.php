<div class="auth-shell">
    <div class="auth-panel">
        <h2>Đăng ký</h2>
        <form id="register-form" class="auth-form" action="<?= url('?action=register-confirm') ?>" method="POST">
            <input type="hidden" name="registration_token" value="<?= htmlspecialchars($register_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <?php if (isset($message)): ?>
                <div class="auth-alert" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="auth-row">
                <label for="fullname" class="auth-label">Họ và tên</label>
                <input type="text" class="auth-field" id="fullname" name="fullname" placeholder="Họ và tên">
                <span class="error-msg error-fullname"></span>
            </div>

            <div class="auth-row">
                <label for="email" class="auth-label">Email</label>
                <input type="email" class="auth-field" id="email" name="email" placeholder="Email">
                <span class="error-msg error-email"></span>
            </div>

            <div class="auth-row">
                <label for="username" class="auth-label">Tên đăng nhập</label>
                <input type="text" class="auth-field" id="username" name="username" placeholder="Tên đăng nhập">
                <span class="error-msg error-username"></span>
            </div>

            <div class="auth-row">
                <label for="password" class="auth-label">Mật khẩu</label>
                <input type="password" class="auth-field" id="password" name="password" placeholder="Mật khẩu">
                <span class="error-msg error-password"></span>
            </div>

            <div class="auth-row">
                <label for="confirm_password" class="auth-label">Xác nhận mật khẩu</label>
                <input type="password" class="auth-field" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu">
                <span class="error-msg error-confirm_password"></span>
            </div>

            <span class="error-msg error-general"></span>

            <button type="submit" class="auth-submit">Đăng ký</button>

            <div class="auth-divider"></div>
            <div class="text-center mt-3">
                <p class="auth-muted">Đã có tài khoản? <a class="auth-link" href="<?= url('?action=login') ?>">Đăng nhập</a></p>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    const formHandler = new AjaxForm('#register-form', '<?= url('?action=register-confirm') ?>');

    formHandler.onSuccess = function(response) {
        Swal.fire({
            title: 'Thông báo',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ff7a1a',
            background: '#fff',
            backdrop: 'rgba(15, 23, 42, 0.5)',
            customClass: {
                popup: 'rounded-popup'
            }
        }).then(() => {
            window.location.href = "<?= url('?action=login') ?>";
        });
    };
</script>