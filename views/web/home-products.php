<?php
$categories = $categories ?? [];
$products = $products ?? [];
$activeCategoryName = '';
if (!empty($filterCategory)) {
    foreach ($categories as $category) {
        if ((int)($category['id'] ?? 0) === (int)$filterCategory) {
            $activeCategoryName = htmlspecialchars($category['name'] ?? 'Danh mục');
            break;
        }
    }
}
?>
<div class="col-lg-9" id="product-grid-wrapper">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="section-title mb-0">Tất cả sản phẩm</h2>
        <span class="section-link d-none d-sm-inline"><?= (int)($pagination['total_items'] ?? count($products)) ?> sản phẩm</span>
    </div>

    <?php if (!empty($filterSearch) || !empty($filterCategory)): ?>
        <div class="alert alert-secondary border-secondary d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <span class="small mb-0">
                Đang lọc:
                <?php if (!empty($filterSearch)): ?>
                    <strong><?= htmlspecialchars($filterSearch) ?></strong>
                <?php endif; ?>
                <?php if (!empty($filterCategory)): ?>
                    <span class="text-white">• <?= $activeCategoryName ?: 'Danh mục đã chọn' ?></span>
                <?php endif; ?>
            </span>
            <a href="<?= url('/') ?>" class="btn btn-sm btn-outline-light js-product-reset">Xem tất cả</a>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?= url('?action=products') ?>" class="row g-2 align-items-end mb-4 js-product-filter-form">
        <div class="col-12 col-md-5">
            <label for="product-search" class="form-label small text-secondary mb-1">Tìm kiếm</label>
            <input type="text" id="product-search" name="search" class="form-control bg-transparent border-secondary text-white" placeholder="Nhập tên hoặc mã sản phẩm..." value="<?= htmlspecialchars($filterSearch ?? '') ?>">
        </div>
        <div class="col-12 col-md-4">
            <label for="product-category" class="form-label small text-secondary mb-1">Danh mục</label>
            <select id="product-category" name="category" class="form-select bg-transparent border-secondary text-white">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)($category['id'] ?? 0) ?>" <?= ((int)($filterCategory ?? 0) === (int)($category['id'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name'] ?? 'Danh mục') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Lọc</button>
            <a href="<?= url('?action=products') ?>" class="btn btn-outline-light js-product-reset">Reset</a>
        </div>
    </form>

    <?php if (!empty($products)): ?>
        <div class="row g-3 g-lg-4" id="products-result-grid">
            <?php foreach ($products as $product): $b = productBadges($product); ?>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="product-card">
                        <div class="img-wrap<?= $b['isOut'] ? ' is-out' : '' ?>">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">

                            <?php if ($b['categoryName']): ?>
                                <span class="badge-cat"><?= htmlspecialchars($b['categoryName']) ?></span>
                            <?php endif; ?>

                            <?php if ($b['isOut']): ?>
                                <div class="badge-out-overlay"><span>Hết hàng</span></div>
                            <?php elseif ($b['isLow']): ?>
                                <span class="badge-stock-low">Chỉ còn <?= $b['stock'] ?></span>
                            <?php elseif ($b['isHot']): ?>
                                <span class="badge-hot"><i class="bi bi-fire"></i>Hot</span>
                            <?php endif; ?>
                        </div>
                        <div class="body">
                            <div class="code-view-row">
                                <?php if ($b['code']): ?><span class="product-code mb-0">#<?= htmlspecialchars($b['code']) ?></span><?php else: ?><span></span><?php endif; ?>
                                <span class="view-count"><i class="bi bi-eye"></i><?= htmlspecialchars($b['viewsLabel']) ?></span>
                            </div>
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="desc"><?= htmlspecialchars($product['description'] ?? '') ?></p>
                            <div class="price">$<?= number_format((float)($product['price'] ?? 0), 0, ',', '.') ?></div>
                        </div>
                        <div class="card-actions card-actions-raised">
                            <a href="<?= url('?action=product-detail&id=' . (int)($product['id'] ?? 0)) ?>" class="btn-view">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                            <?php if (!$b['isOut']): ?>
                                <form action="<?= url('?action=cart-add') ?>" method="post" class="m-0" style="flex:1 1 0;">
                                    <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="bi bi-cart-plus"></i> Giỏ hàng
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="btn-add-cart is-disabled">Hết hàng</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?= url('?action=product-detail&id=' . (int)($product['id'] ?? 0)) ?>" class="stretched-link" aria-label="<?= htmlspecialchars($product['name']) ?>"></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
            $paginationData = $pagination ?? ['current_page' => 1, 'total_pages' => 1];
            $currentPage = (int)($paginationData['current_page'] ?? 1);
            $totalPages = (int)($paginationData['total_pages'] ?? 1);
        ?>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Phân trang sản phẩm" class="mt-4" id="product-pagination">
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                            $queryParams = [];
                            if (!empty($filterSearch)) {
                                $queryParams['search'] = $filterSearch;
                            }
                            if (!empty($filterCategory)) {
                                $queryParams['category'] = (int)$filterCategory;
                            }
                            $queryParams['page'] = $i;
                        ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link js-pagination-link" href="<?= url('?action=products&' . http_build_query($queryParams)) ?>" data-page="<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>
    <?php endif; ?>
</div>
