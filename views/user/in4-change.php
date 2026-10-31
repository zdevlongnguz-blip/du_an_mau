<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card p-4 shadow-sm">
                <h4 class="mb-4 text-center">Thay đổi mật khẩu</h4>

                <form id="change-in4-form" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input class="form-control" type="text" id="password" name="password">
                        <span class="text-danger error-password"></span>
                    </div>

                    <div class="mb-3">
                        <label for="new-password" class="form-label fw-bold">New Password</label>
                        <input class="form-control" type="text" id="new-password" name="new-password">
                        <span class="text-danger error-new-password"></span>
                    </div>

                    <div class="mb-3">`
                        <label for="confirm-password" class="form-label fw-bold">Confirm New Password</label>
                        <input class="form-control" type="text" id="confirm-password" name="confirm-password">
                        <span class="text-danger error-confirm-password"></span>
                    </div>

                    <span class="text-danger error-general"></span>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= url('?action=profile') ?>" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    // Khởi tạo class (Tham số 1: ID của form, Tham số 2: URL nhận dữ liệu xử lý)
    const formHandler = new AjaxForm('#change-in4-form', '<?= url('?action=profile-in4-update') ?>');

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
            window.location.href = "<?= url('?action=profile') ?>";
        });
    };
</script>

