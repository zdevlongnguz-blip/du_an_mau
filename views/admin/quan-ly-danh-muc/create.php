<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Danh mục</div>
                    <h3 class="mb-0">Tạo danh mục mới</h3>
                </div>
            </div>

            <form id="category-create-form" method="POST" action="<?= url('?action=category-store') ?>">
                <div class="mb-4">
                    <label for="name" class="form-label">Tên danh mục</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên danh mục">
                    <div class="text-danger small error-msg error-name"></div>
                </div>

                <div class="mb-4">
                    <div class="text-danger small error-msg error-general"></div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3">
                    <button type="submit" class="btn cyber-btn flex-grow-1">Lưu danh mục</button>
                    <a href="<?= url('?action=dashboard&table=categories') ?>" class="btn btn-secondary flex-grow-1">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    const categoryCreateForm = new AjaxForm('#category-create-form', '<?= url('?action=category-store') ?>');
    categoryCreateForm.onSuccess = function(response) {
        Swal.fire({
            title: 'Thành công',
            text: response.message || 'Danh mục đã được tạo.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '<?= url('?action=dashboard&table=categories') ?>';
        });
    };
</script>
