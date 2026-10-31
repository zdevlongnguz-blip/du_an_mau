<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Chi tiết danh mục</h3>
                    <p class="text-muted mb-0">ID: #<?= (int)$category['id'] ?></p>
                </div>
                <a href="<?= url('?action=dashboard&table=categories') ?>" class="btn btn-outline-light">Quay lại danh sách</a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-8">
                    <div class="mb-2"><strong>Tên danh mục</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($category['name'] ?? '') ?></div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="mb-2"><strong>Số sản phẩm</strong></div>
                    <div class="text-muted"><?= (int)($productCount ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
