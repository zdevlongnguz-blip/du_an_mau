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
        width: min(100%, 500px);
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
        <h2>Đăng ký</h2>
        <form id="register-form" action="<?= url('?action=register-confirm') ?>" method="POST">
            <input type="hidden" name="registration_token" value="<?= htmlspecialchars($register_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <?php if (isset($message)): ?>
                <div class="alert alert-danger p-2 small text-center mb-3" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="fullname" class="form-label">Họ và tên</label>
                <input type="text" class="form-control form-control-lg fs-6 border-secondary-subtle" id="fullname" name="fullname" placeholder="Họ và tên">
                <span class="error-msg error-fullname text-danger"></span>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control form-control-lg fs-6 border-secondary-subtle" id="email" name="email" placeholder="Email">
                <span class="error-msg error-email text-danger"></span>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control form-control-lg fs-6 border-secondary-subtle" id="username" name="username" placeholder="Tên đăng nhập">
                <span class="error-msg error-username text-danger"></span>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control form-control-lg fs-6 border-secondary-subtle" id="password" name="password" placeholder="Mật khẩu">
                <span class="error-msg error-password text-danger"></span>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control form-control-lg fs-6 border-secondary-subtle" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu">
                <span class="error-msg error-confirm_password text-danger"></span>
            </div>

            <span class="error-msg error-general text-danger"></span>

            <button type="submit" class="btn btn-primary w-100 btn-lg fs-6 fw-medium py-2 shadow-sm">Đăng Ký</button>
            <div class="text-center mt-3">
                <p class="text-muted">Đã có tài khoản? <a class="text-decoration-underline" href="<?= url('?action=login') ?>">Đăng nhập</a></p>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    // Khởi tạo class (Tham số 1: ID của form, Tham số 2: URL nhận dữ liệu xử lý)
    const formHandler = new AjaxForm('#register-form', '<?= url('?action=register-confirm') ?>');

    // Ghi đè (Override) hàm onSuccess của class cha để xử lý giao diện theo ý muốn
    formHandler.onSuccess = function(response) {
        Swal.fire({
            title: 'Thông báo',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6',
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