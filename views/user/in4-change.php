<div class="hs-container hs-account-page">
    <div class="hs-account-layout">
        <nav class="hs-account-nav" aria-label="Tài khoản">
            <a class="hs-account-nav__link" href="<?= url('?action=profile') ?>"><i class="bi bi-person"></i>Hồ sơ</a>
            <a class="hs-account-nav__link" href="<?= url('?action=order-history') ?>"><i class="bi bi-receipt"></i>Đơn hàng</a>
            <a class="hs-account-nav__link" href="<?= url('?action=my-vouchers') ?>"><i class="bi bi-ticket-perforated"></i>Túi voucher</a>
            <a class="hs-account-nav__link is-active" href="<?= url('?action=profile-in4-change') ?>"><i class="bi bi-key"></i>Đổi mật khẩu</a>
            <a class="hs-account-nav__link hs-account-nav__link--danger" href="<?= url('?action=logout') ?>"><i class="bi bi-box-arrow-right"></i>Đăng xuất</a>
        </nav>

        <section class="hs-account-main">
            <h1 class="hs-page-title">Đổi mật khẩu</h1>

            <div class="hs-form-card">
                <form id="change-in4-form" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="hs-field">
                        <label for="password" class="hs-label">Mật khẩu hiện tại</label>
                        <div class="hs-pw">
                            <input class="form-control hs-input" type="password" id="password" name="password" autocomplete="current-password">
                            <button type="button" class="hs-pw__toggle" data-toggle-password aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                        </div>
                        <span class="hs-field-error error-msg error-password"></span>
                    </div>

                    <div class="hs-field">
                        <label for="new-password" class="hs-label">Mật khẩu mới</label>
                        <div class="hs-pw">
                            <input class="form-control hs-input" type="password" id="new-password" name="new-password" autocomplete="new-password">
                            <button type="button" class="hs-pw__toggle" data-toggle-password aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="hs-hint">Tối thiểu 6 ký tự</div>
                        <span class="hs-field-error error-msg error-new-password"></span>
                    </div>

                    <div class="hs-field">
                        <label for="confirm-password" class="hs-label">Nhập lại mật khẩu mới</label>
                        <div class="hs-pw">
                            <input class="form-control hs-input" type="password" id="confirm-password" name="confirm-password" autocomplete="new-password">
                            <button type="button" class="hs-pw__toggle" data-toggle-password aria-label="Hiện mật khẩu"><i class="bi bi-eye"></i></button>
                        </div>
                        <span class="hs-field-error error-msg error-confirm-password"></span>
                    </div>

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

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    const formHandler = new AjaxForm('#change-in4-form', '<?= url('?action=profile-in4-update') ?>');

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

<script src="<?= BASE_URL ?>assets/js/user-password.js?v=<?= @filemtime(dirname(__DIR__, 2) . '/assets/js/user-password.js') ?: time() ?>" defer></script>

