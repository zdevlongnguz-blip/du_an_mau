<?php
$categories = $categories ?? [];
$products = $products ?? [];
$topProducts = $topProducts ?? [];

/**
 * Shared product badge helpers live in configs/helper.php so every view can reuse them.
 */
if (!function_exists('productBadges')) {
    function productBadges(array $product): array
    {
        $stock = (int)($product['stock'] ?? 0);
        $views = (int)($product['views'] ?? 0);

        return [
            'stock' => $stock,
            'isOut' => $stock <= 0,
            'isLow' => $stock > 0 && $stock <= 3,
            'isHot' => $views >= 500,
            'categoryName' => $product['category_name'] ?? null,
            'code' => $product['code'] ?? null,
            'views' => $views,
            'viewsLabel' => formatViewCount($views),
        ];
    }
}

if (!function_exists('formatViewCount')) {
    function formatViewCount(int $views): string
    {
        if ($views >= 1000000) {
            return rtrim(rtrim(number_format($views / 1000000, 1, '.', ''), '0'), '.') . 'M';
        }
        if ($views >= 1000) {
            return rtrim(rtrim(number_format($views / 1000, 1, '.', ''), '0'), '.') . 'k';
        }
        return (string)$views;
    }
}
?>
<?php
$heroFile = dirname(__DIR__, 2) . '/assets/img/hero.jpg';
$heroUrl = is_file($heroFile) ? BASE_URL . 'assets/img/hero.jpg' : '';
?>
<div class="hs-home">
    <section class="hs-hero"<?= $heroUrl ? ' style="background-image:url(\'' . htmlspecialchars($heroUrl) . '\')"' : '' ?>>
        <div class="hs-container hs-hero__inner">
            <p class="hs-hero__eyebrow">Hustler420</p>
            <h1 class="hs-hero__title">Điếu cày<br>Thuốc lào</h1>
            <p class="hs-hero__sub">Tuyển chọn chuẩn vị</p>
            <a class="hs-btn hs-btn--light hs-btn--lg" href="<?= url('?action=products') ?>">Xem sản phẩm</a>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <h2 class="hs-section-title-c">Bộ sưu tập</h2>
            <div class="hs-collections">
                <?php foreach (array_slice($categories, 0, 2) as $cat): ?>
                    <?php
                    $collectionImage = '';
                    foreach ($products as $collectionProduct) {
                        if (isset($collectionProduct['category_id'], $collectionProduct['image'])
                            && (int)$collectionProduct['category_id'] === (int)($cat['id'] ?? 0)) {
                            $collectionImage = BASE_ASSETS_UPLOADS . $collectionProduct['image'];
                            break;
                        }
                    }
                    ?>
                    <a class="hs-collection" href="<?= url('?action=products&category=' . (int)$cat['id']) ?>"<?= $collectionImage !== '' ? ' style="background-image:url(\'' . htmlspecialchars($collectionImage, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>>
                        <span class="hs-collection__label"><?= htmlspecialchars($cat['name'] ?? 'Danh mục') ?><small><?= (int)($cat['product_count'] ?? 0) ?> sản phẩm</small></span>
                    </a>
                <?php endforeach; ?>
                <a class="hs-collection hs-collection--voucher" href="<?= url('?action=mini-game') ?>">
                    <span class="hs-collection__label">Nhận voucher<small>Chơi mini game</small></span>
                </a>
            </div>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <h2 class="hs-section-title-c">Sản phẩm nổi bật</h2>
            <?php if (!empty($topProducts)): ?>
                <div class="hs-grid">
                    <?php foreach (array_slice($topProducts, 0, 4) as $product): $b = productBadges($product); ?>
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
                <p class="hs-section-more"><a class="hs-link" href="<?= url('?action=products') ?>">Xem tất cả →</a></p>
            <?php else: ?>
                <p class="hs-hint">Chưa có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <div class="hs-strip hs-strip--voucher">
                <div>
                    <p class="hs-eyebrow">Mini game mỗi 5 phút</p>
                    <h2 class="hs-strip__title">Trả lời 5 câu hỏi, nhận voucher đến 90%</h2>
                    <div class="hs-strip__chips">
                        <span class="hs-reward-chip">+10%</span>
                        <span class="hs-reward-chip">+3&#48;%</span>
                        <span class="hs-reward-chip">+50%</span>
                        <span class="hs-reward-chip">+70%</span>
                        <span class="hs-reward-chip">+90%</span>
                    </div>
                </div>
                <a class="hs-btn hs-btn--primary hs-btn--lg" href="<?= url('?action=mini-game') ?>">Chơi ngay</a>
            </div>
        </div>
    </section>

    <section class="hs-section">
        <div class="hs-container">
            <div class="hs-strip">
                <div>
                    <h2 class="hs-strip__title">Chưa biết chọn gì? Hỏi trợ lý AI</h2>
                    <p class="hs-hint">Mô tả nhu cầu, trợ lý gợi ý sản phẩm phù hợp.</p>
                </div>
                <a class="hs-btn hs-btn--ghost hs-btn--lg" href="<?= url('?action=ai-chat') ?>">Hỏi trợ lý AI</a>
            </div>
        </div>
    </section>
</div>
