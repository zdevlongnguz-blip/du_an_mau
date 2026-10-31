<?php
$categories = $categories ?? [];
$product = $product ?? [];
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Sản phẩm</div>
                    <h3 class="mb-0" style="color: #f8fafc;">Chỉnh sửa sản phẩm</h3>
                </div>
                <a id="btn-back" href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-secondary">Quay lại</a>
            </div>

            <form id="product-edit" enctype="multipart/form-data" class="row g-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id'] ?? '') ?>">

                <div class="col-12 col-lg-6">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" class="form-control" placeholder="Nhập tên sản phẩm...">
                    <span class="error-msg error-name text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Mã sản phẩm</label>
                    <input type="text" name="code" value="<?= htmlspecialchars($product['code'] ?? '') ?>" class="form-control" placeholder="Nhập mã sản phẩm...">
                    <span class="error-msg error-code text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : '' ?>><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-msg error-category_id text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Hình ảnh hiện tại</label>
                    <?php if (!empty($product['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image']) ?>" alt="Product Image" width="120">
                        </div>
                    <?php else: ?>
                        <div class="text-muted small mb-2">Chưa có hình ảnh</div>
                    <?php endif; ?>
                    <label class="form-label">Thay đổi ảnh</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <span class="error-msg error-image text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Price ($)</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" class="form-control" placeholder="Nhập giá sản phẩm (nhỏ hơn 500000)...">
                    <span class="error-msg error-price text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Stock</label>
                    <input type="text" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '') ?>" class="form-control" placeholder="Nhập số lượng...">
                    <span class="error-msg error-stock text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" value="<?= htmlspecialchars($product['description'] ?? '') ?>" class="form-control" placeholder="Nhập mô tả sản phẩm...">
                    <span class="error-msg error-description text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-2 gap-3">
                        <label class="form-label mb-0">Content</label>
                    </div>
                    <textarea name="content" id="product-content" class="form-control" rows="8" placeholder="Nhập nội dung sản phẩm..."><?= htmlspecialchars($product['content'] ?? '') ?></textarea>
                    <span class="error-msg error-content text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12">
                    <span class="error-msg error-general text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 d-flex gap-3 flex-column flex-sm-row">
                    <button id="btn-submit" type="submit" class="btn cyber-btn flex-grow-1">CẬP NHẬT</button>
                    <a href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-secondary flex-grow-1 d-sm-none">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    const formHandler = new AjaxForm('#product-edit', '<?= url('?action=product-update') ?>');

    formHandler.onSuccess = function(response) {
        Swal.fire({
            title: 'Thông báo',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6',
            background: '#fff',
            backdrop: 'rgba(15, 23, 42, 0.5)',
            customClass: { popup: 'rounded-popup' }
        }).then(() => {
            window.location.href = "<?= url('?action=dashboard&table=products') ?>";
        });
    };
</script>

<style>
    .btn-outline-purple:hover {
        background-color: #a855f7 !important;
        color: #fff !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .rounded-popup {
        border-radius: 12px !important;
    }
</style>