<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card p-4 shadow-sm">
                <h4 class="mb-4 text-center">Thay đổi ảnh đại diện</h4>

                <form id="change-avatar-form" method="POST" enctype="multipart/form-data">

                    <div class="text-center mb-4">
                        <img id="preview" src="<?= (BASE_ASSETS_UPLOADS .  $user['avatar']) ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>" alt="Avatar" class="rounded-circle border" width="150" height="150" style="object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <label for="avatar" class="form-label fw-bold">Chọn ảnh mới:</label>
                        <input class="form-control" type="file" id="avatar" name="avatar">
                        <span class="text-danger error-avatar"></span>
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
    // Khởi tạo class (Tham số 1: ID của form, Tham số 2: URL nhận dữ liệu xử lý)
    const formHandler = new AjaxForm('#change-avatar-form', '<?= url('?action=profile-avatar-update') ?>');

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