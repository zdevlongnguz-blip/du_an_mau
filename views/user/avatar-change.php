<?php
$avatarPreview = !empty($user['avatar'])
    ? (BASE_ASSETS_UPLOADS . $user['avatar'])
    : "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'><rect width='96' height='96' fill='%23eee'/><circle cx='48' cy='38' r='16' fill='%23bbb'/><path d='M16 88c4-18 18-26 32-26s28 8 32 26z' fill='%23bbb'/></svg>";
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
            <h1 class="hs-page-title">Đổi ảnh đại diện</h1>

            <div class="hs-form-card">
                <form id="change-avatar-form" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="text-center mb-4">
                        <img id="preview" src="<?= htmlspecialchars($avatarPreview) ?>" alt="Avatar" class="hs-avatar-preview">
                    </div>

                    <div class="hs-field">
                        <label for="avatar" class="hs-label">Chọn ảnh mới</label>
                        <input class="form-control hs-input" type="file" id="avatar" name="avatar" accept="image/png,image/jpeg,image/gif,image/webp">
                        <span class="hs-field-error error-msg error-avatar"></span>
                    </div>

                    <div class="hs-hint">JPG, PNG, GIF hoặc WEBP, tối đa 2MB</div>
                    <span class="hs-field-error error-msg error-general"></span>

                    <div class="hs-form-actions">
                        <a href="<?= url('?action=profile') ?>" class="hs-btn hs-btn--ghost">Hủy</a>
                        <button type="submit" class="hs-btn hs-btn--primary">Lưu</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
    document.getElementById('avatar').onchange = function() {
        const [file] = this.files;
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file);
        }
    }
</script>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    const formHandler = new AjaxForm('#change-avatar-form', '<?= url('?action=profile-avatar-update') ?>');

    formHandler.onSuccess = function(response) {
        Swal.fire({
            title: 'Thông báo',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#111',
            background: '#fff',
            backdrop: 'rgba(15, 23, 42, 0.5)',
            customClass: {
                popup: 'rounded-popup'
            }
        }).then(() => {
            window.location.href = "<?= url('?action=profile') ?>";
        });
    };
</script>