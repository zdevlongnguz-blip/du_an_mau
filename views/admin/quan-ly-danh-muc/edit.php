<div class="panel-card p-4 w-100">
    <h3 class="mb-4">Chỉnh sửa danh mục</h3>
    <form id="category-edit-form" method="POST" action="<?= url('?action=category-update') ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>">
            <div class="text-danger small error-msg error-name"></div>
        </div>

        <div class="mb-3">
            <div class="text-danger small error-msg error-general"></div>
        </div>

        <button type="submit" class="btn btn-cyan">Cập nhật</button>
        <a href="<?= url('?action=dashboard&table=categories') ?>" class="btn btn-secondary ms-2">Quay lại</a>
    </form>
</div>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    const categoryEditForm = new AjaxForm('#category-edit-form', '<?= url('?action=category-update') ?>');
    categoryEditForm.onSuccess = function(response) {
        Swal.fire({
            title: 'Thành công',
            text: response.message || 'Danh mục đã được cập nhật.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '<?= url('?action=dashboard&table=categories') ?>';
        });
    };
</script>
