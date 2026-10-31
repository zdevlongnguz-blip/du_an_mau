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

<style>
    .catalog-page {
        padding-top: 2rem;
        padding-bottom: 3rem;
        background: #07080a;
    }

    .catalog-shell {
        background: #121317;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1.5rem 3rem rgba(0, 0, 0, 0.3);
    }

    .catalog-sidebar {
        background: #111216;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
        padding: 1rem;
    }

    .catalog-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .catalog-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #f7f7f7;
        margin: 0;
    }

    .catalog-subtitle {
        color: #a0a7ae;
        font-size: 0.9rem;
        margin: 0;
    }

    .catalog-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .catalog-list a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.8rem 0.9rem;
        border-radius: 0.7rem;
        color: #c8ccd2;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.03);
        background: rgba(255,255,255,0.01);
        transition: background .2s ease, border-color .2s ease, color .2s ease;
    }

    .catalog-list a:hover {
        background: rgba(255,255,255,0.025);
    }

    .catalog-list a.active {
        background: rgba(255,122,26,0.08);
        border-color: rgba(255,122,26,0.22);
        color: #f3b27d;
    }

    .catalog-count {
        min-width: 2rem;
        text-align: center;
        border-radius: 999px;
        padding: 0.18rem 0.5rem;
        background: rgba(255,255,255,0.055);
        color: #e8ecef;
        font-size: 0.75rem;
    }

    .catalog-list a.active .catalog-count {
        background: #ff7a1a;
        color: #101214;
    }

    .catalog-filter-box {
        border: 1px solid rgba(255,255,255,0.06);
        background: #101216;
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .catalog-filter-box .form-control,
    .catalog-filter-box .form-select {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.12);
        color: #e8ebf0;
    }

    .catalog-filter-box .form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='rgba(255,255,255,0.7)' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
        background-size: 10px 6px;
    }

    .catalog-filter-box .form-control::placeholder {
        color: rgba(255,255,255,0.55);
    }

    .catalog-filter-box .form-control:focus,
    .catalog-filter-box .form-select:focus {
        border-color: rgba(255,122,26,0.6);
        box-shadow: 0 0 0 0.2rem rgba(255,122,26,0.12);
        background: rgba(255,255,255,0.06);
        color: #fff;
    }

    .catalog-filter-box .form-select option,
    .catalog-filter-box .form-select optgroup {
        background: #101216;
        color: #eef0f3;
    }

    .catalog-filter-box .form-select option:checked {
        background: rgba(255,122,26,0.18);
        color: #fff;
    }

    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 0.9rem;
        background: linear-gradient(180deg, #16181b 0%, #111316 100%);
        color: #f4f4f4;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .product-card:hover {
        border-color: rgba(255,122,26,0.5);
        transform: translateY(-4px);
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.18);
    }

    .img-wrap {
        position: relative;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #08090a;
    }

    .img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease, filter .35s ease;
    }

    .product-card:hover .img-wrap img {
        transform: scale(1.06);
        filter: brightness(0.82);
    }

    .badge-cat {
        position: absolute;
        bottom: 0.55rem;
        left: 0.55rem;
        background: rgba(0,0,0,0.68);
        color: #fff;
        font-size: 0.66rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.16);
        z-index: 2;
    }

    .badge-hot,
    .badge-stock-low {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        font-size: 0.64rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        z-index: 2;
    }

    .badge-hot {
        background: #ff7a1a;
        color: #101214;
    }

    .badge-stock-low {
        background: #dc3545;
        color: #fff;
    }

    .badge-out-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
    }

    .badge-out-overlay span {
        border: 1px solid rgba(255,255,255,0.9);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
    }

    .img-wrap.is-out img {
        filter: grayscale(1) brightness(0.65);
    }

    .body {
        padding: 1rem 1.1rem 1.2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .code-view-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .product-code {
        font-size: 0.72rem;
        color: #9aa0a6;
        margin-bottom: 0.2rem;
        display: block;
    }

    .view-count {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        color: #9aa0a6;
    }

    .product-card .body h3 {
        font-size: 0.92rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
        color: #f7f7f7;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-card .body .desc {
        font-size: 0.8rem;
        color: #b2bac4;
        margin-bottom: 0.6rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .price {
        font-weight: 700;
        font-size: 1.1rem;
        color: #ff9b4e;
        margin-bottom: 0.75rem;
        margin-top: auto;
    }

    .card-actions {
        padding: 0 1.1rem 1.1rem;
        display: flex;
        gap: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .card-actions-raised {
        position: relative;
        z-index: 2;
    }

    .btn-view,
    .btn-add-cart {
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.55rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all .15s ease;
        text-decoration: none;
    }

    .btn-view {
        border: 1px solid rgba(255,255,255,0.16);
        background: rgba(255,255,255,0.04);
        color: #edf2f7;
        flex: 1 1 0;
    }

    .btn-view:hover {
        background: #ff7a1a;
        border-color: #ff7a1a;
        color: #101214;
    }

    .btn-add-cart {
        border: 1px solid rgba(255,122,26,0.8);
        background: rgba(255,122,26,0.1);
        color: #ffc18b;
        flex: 1 1 0;
        cursor: pointer;
    }

    .btn-add-cart:hover {
        background: #ff7a1a;
        color: #101214;
    }

    .btn-add-cart.is-disabled {
        border-color: rgba(255,255,255,0.12);
        color: #8f99a4;
        pointer-events: none;
        cursor: not-allowed;
        background: rgba(255,255,255,0.02);
    }

    .stretched-link {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .empty-state {
        padding: 2rem;
        border-radius: 1rem;
        background: rgba(255,255,255,0.02);
        color: #d3d8df;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .catalog-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .catalog-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="catalog-page">
    <div class="container-fluid px-3 px-lg-5">
        <div class="catalog-header">
            <div>
                <h1 class="catalog-title">Danh mục sản phẩm</h1>
                <p class="catalog-subtitle">
                    <?= (int)($paginationData['total_items'] ?? 0) ?> sản phẩm
                    <?php if (!empty($filterSearch) || !empty($filterCategory)): ?>
                        • đang lọc
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= url('/') ?>" class="btn btn-outline-light btn-sm">Về trang chủ</a>
        </div>

        <div class="catalog-shell">
            <div class="row g-4">
                <aside class="col-lg-3">
                    <div class="catalog-sidebar">
                        <h5 class="text-white mb-3">Danh mục</h5>
                        <div class="catalog-list">
                            <a href="<?= url('?action=products') ?>" class="<?= empty($filterCategory) ? 'active' : '' ?>">
                                <span><i class="bi bi-grid"></i> Tất cả</span>
                                <span class="catalog-count"><?= (int)($paginationData['total_items'] ?? 0) ?></span>
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="<?= url('?action=products&category=' . (int)($category['id'] ?? 0)) ?>" class="<?= ((int)($filterCategory ?? 0) === (int)($category['id'] ?? 0)) ? 'active' : '' ?>">
                                    <span><i class="bi bi-tag-fill"></i> <?= htmlspecialchars($category['name'] ?? 'Danh mục') ?></span>
                                    <span class="catalog-count"><?= (int)($category['product_count'] ?? 0) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>

                <div class="col-lg-9">
                    <?php if (!empty($filterSearch) || !empty($filterCategory)): ?>
                        <div class="catalog-filter-box d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="small text-white-50 mb-0">
                                Đang lọc:
                                <?php if (!empty($filterSearch)): ?>
                                    <strong class="text-white"><?= htmlspecialchars($filterSearch) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($filterCategory)): ?>
                                    <span class="text-white">• <?= $activeCategoryName ?: 'Danh mục đã chọn' ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= url('?action=products') ?>" class="btn btn-sm btn-outline-light">Xem tất cả</a>
                        </div>
                    <?php endif; ?>

                    <form method="GET" action="" class="catalog-filter-box row g-2 align-items-end mb-4">
                        <input type="hidden" name="action" value="products">
                        <div class="col-md-5">
                            <label for="catalog-search" class="form-label small text-secondary mb-1">Tìm kiếm</label>
                            <input type="text" id="catalog-search" name="search" class="form-control bg-transparent border-secondary text-white" value="<?= htmlspecialchars($filterSearch ?? '') ?>" placeholder="Nhập tên hoặc mã sản phẩm...">
                        </div>
                        <div class="col-md-4">
                            <label for="catalog-category" class="form-label small text-secondary mb-1">Danh mục</label>
                            <select id="catalog-category" name="category" class="form-select bg-transparent border-secondary text-white">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)($category['id'] ?? 0) ?>" <?= ((int)($filterCategory ?? 0) === (int)($category['id'] ?? 0)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name'] ?? 'Danh mục') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Lọc</button>
                            <a href="<?= url('?action=products') ?>" class="btn btn-outline-light">Reset</a>
                        </div>
                    </form>

                    <?php if (!empty($products)): ?>
                        <div class="catalog-grid">
                            <?php foreach ($products as $product): $b = productBadges($product); ?>
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
                            <nav aria-label="Phân trang sản phẩm" class="mt-4">
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
                        <div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
