<?php
$categories = $categories ?? [];
$products = $products ?? [];
$paginationData = $pagination ?? ['current_page' => 1, 'total_pages' => 1, 'total_items' => 0];
$currentPage = (int)($paginationData['current_page'] ?? 1);
$totalPages = (int)($paginationData['total_pages'] ?? 1);
$filterSearch = $filterSearch ?? '';
$filterCategory = $filterCategory ?? 0;
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

<div class="hs-catalog">
    <div class="container-fluid px-3 px-lg-5">
        <div class="hs-catalog__top">
            <div>
                <h1 class="hs-catalog__title">Danh mục sản phẩm</h1>
                <p class="hs-catalog__subtitle">
                    <?= (int)($paginationData['total_items'] ?? 0) ?> sản phẩm
                    <?php if (!empty($filterSearch) || !empty($filterCategory)): ?>
                        • đang lọc
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= url('/') ?>" class="hs-btn hs-btn--ghost">Về trang chủ</a>
        </div>

        <div class="hs-category-row">
            <a href="<?= url('?action=products') ?>" class="hs-category-pill <?= empty($filterCategory) ? 'is-active' : '' ?>">
                <span>Tất cả</span>
                <span class="hs-category-pill__count"><?= (int)($paginationData['total_items'] ?? 0) ?></span>
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="<?= url('?action=products&category=' . (int)($category['id'] ?? 0)) ?>" class="hs-category-pill <?= ((int)($filterCategory ?? 0) === (int)($category['id'] ?? 0)) ? 'is-active' : '' ?>">
                    <span><?= htmlspecialchars($category['name'] ?? 'Danh mục') ?></span>
                    <?php if (array_key_exists('product_count', $category)): ?>
                        <span class="hs-category-pill__count"><?= (int)($category['product_count'] ?? 0) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" action="" class="hs-filter row g-3 align-items-end mb-4">
            <input type="hidden" name="action" value="products">
            <div class="col-12 col-lg hs-filter__field hs-filter__field--search">
                <label for="catalog-search" class="form-label hs-form-label">Tìm kiếm</label>
                <input type="text" id="catalog-search" name="search" class="form-control hs-input" value="<?= htmlspecialchars($filterSearch ?? '') ?>" placeholder="Nhập tên hoặc mã sản phẩm...">
            </div>
            <div class="col-12 col-lg-auto hs-filter__field hs-filter__field--category">
                <label for="catalog-category" class="form-label hs-form-label">Danh mục</label>
                <select id="catalog-category" name="category" class="form-select hs-input">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)($category['id'] ?? 0) ?>" <?= ((int)($filterCategory ?? 0) === (int)($category['id'] ?? 0)) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name'] ?? 'Danh mục') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-lg-auto hs-filter__actions">
                <button type="submit" class="hs-btn hs-btn--primary">
                    <i class="bi bi-funnel" aria-hidden="true"></i>
                    <span>Lọc</span>
                </button>
                <a href="<?= url('?action=products') ?>" class="hs-btn hs-btn--ghost hs-btn--icon" aria-label="Xóa bộ lọc" title="Xóa bộ lọc">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span class="visually-hidden">Xóa bộ lọc</span>
                </a>
            </div>
        </form>

        <?php if (!empty($products)): ?>
            <div class="hs-grid">
                <?php foreach ($products as $product): $b = productBadges($product); ?>
                    <div class="hs-card">
                        <div class="hs-card__media <?= $b['isOut'] ? 'is-out' : '' ?>">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">

                            <?php if ($b['isHot']): ?>
                                <span class="hs-card__badge hs-card__badge--hot">Hot</span>
                            <?php elseif ($b['isLow']): ?>
                                <span class="hs-card__badge hs-card__badge--low">Chỉ còn <?= $b['stock'] ?></span>
                            <?php elseif ($b['isOut']): ?>
                                <span class="hs-card__badge hs-card__badge--out">Hết hàng</span>
                            <?php endif; ?>
                        </div>

                        <div class="hs-card__body">
                            <?php if ($b['categoryName']): ?>
                                <div class="hs-card__category"><?= htmlspecialchars($b['categoryName']) ?></div>
                            <?php endif; ?>
                            <h3 class="hs-card__title"><?= htmlspecialchars($product['name']) ?></h3>

                            <div class="hs-card__footer">
                                <div class="hs-card__price"><?= number_format((float)($product['price'] ?? 0), 0, ',', '.') ?>đ</div>
                                <div class="hs-card__actions">
                                    <a href="<?= url('?action=product-detail&id=' . (int)($product['id'] ?? 0)) ?>" class="hs-btn hs-btn--ghost hs-btn--icon" aria-label="Xem chi tiết" title="Xem chi tiết">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>

                                    <form action="<?= url('?action=cart-add') ?>" method="post" class="hs-cart-form <?= $b['isOut'] ? 'is-disabled' : '' ?>">
                                        <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="hs-btn hs-btn--primary hs-btn--icon hs-cart-submit" aria-label="Thêm vào giỏ" title="Thêm vào giỏ" <?= $b['isOut'] ? 'disabled' : '' ?>>
                                            <i class="bi <?= $b['isOut'] ? 'bi-bag-x' : 'bi-bag-plus' ?>" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php
                    $baseParams = ['action' => 'products'];
                    if (!empty($filterSearch)) {
                        $baseParams['search'] = $filterSearch;
                    }
                    if (!empty($filterCategory)) {
                        $baseParams['category'] = (int)$filterCategory;
                    }
                ?>
                <nav aria-label="Phân trang sản phẩm" class="hs-pagination mt-4">
                    <ul class="pagination justify-content-center mb-0 flex-wrap">
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('?' . buildCleanQuery(array_merge($baseParams, ['page' => max(1, $currentPage - 1)]))) ?>" aria-label="Trang trước">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('?' . buildCleanQuery(array_merge($baseParams, ['page' => $i]))) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('?' . buildCleanQuery(array_merge($baseParams, ['page' => min($totalPages, $currentPage + 1)]))) ?>" aria-label="Trang sau">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="hs-empty">
                <p class="hs-empty__title">Không tìm thấy sản phẩm phù hợp.</p>
                <div class="hs-empty__actions">
                    <a href="<?= url('?action=products') ?>" class="hs-btn hs-btn--primary">Xóa bộ lọc</a>
                    <a href="<?= url('?action=ai-chat') ?>" class="hs-btn hs-btn--secondary">Hỏi trợ lý AI</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
