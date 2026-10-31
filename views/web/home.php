<?php
$categories = $categories ?? [];
$products = $products ?? [];
$topProducts = $topProducts ?? [];
$featured = $topProducts[0] ?? null;
$restTop = array_slice($topProducts, 1, 4);

/**
 * Mock hero banners — replace with data from BE later
 * (e.g. $banners passed in from the controller/DB).
 * Expected shape: image, badge, title, description, cta_text, cta_link.
 */
$banners = $banners ?? [
    [
        'image'       => 'https://thuoclaophugia.com/wp-content/uploads/2020/04/286895424_3283493301927401_8920445492774196762_n.jpg',
        'badge'       => 'New collection',
        'title'       => 'Khám phá sản phẩm phù hợp phong cách của bạn',
        'description' => 'Bố cục rõ ràng, tối ưu cho mọi thiết bị — tập trung vào trải nghiệm mua sắm nhanh và trực quan.',
        'cta_text'    => 'Xem sản phẩm',
        'cta_link'    => '?action=products',
    ],
    [
        'image'       => 'https://thuoclaothanhhoa.vn/wp-content/uploads/2022/10/dieucayminirut.jpg',
        'badge'       => 'Ưu đãi tuần này',
        'title'       => 'Giảm giá đến 30% cho đơn hàng đầu tiên',
        'description' => 'Áp dụng cho tất cả sản phẩm mới về, số lượng có hạn trong tuần này.',
        'cta_text'    => 'Mua ngay',
        'cta_link'    => '?action=products',
    ],
    [
        'image'       => 'https://hatinhcogi.com/wp-content/uploads/2023/05/kieu-dieu-cay-Ha-Tinh.jpg',
        'badge'       => 'Hàng mới về',
        'title'       => 'Bộ sưu tập mới vừa cập bến',
        'description' => 'Cập nhật xu hướng mới nhất, chất lượng đã được kiểm chứng bởi hàng ngàn khách hàng.',
        'cta_text'    => 'Khám phá ngay',
        'cta_link'    => '?action=products',
    ],
];

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

<style>
    :root {
        --bg: #0b0b0b;
        --bg-soft: #141414;
        --card: #17181a;
        --border: rgba(255, 255, 255, 0.08);
        --text: #e9edf1;
        --muted: #9aa0a6;
        --white: #ffffff;
        --accent: #ff7a1a;
        --accent-contrast: #0b0b0b;
    }

    .home-page { padding-bottom: 2rem; }

    .home-voucher-cta {
        border: 1px solid rgba(255, 122, 26, .35);
        background: radial-gradient(circle at 85% 20%, rgba(255, 122, 26, .2), transparent 35%), #17181a;
        padding: 1.25rem 1.5rem;
    }

    .home-voucher-cta .reward-pill {
        border: 1px solid rgba(255, 193, 7, .55);
        color: #ffc107;
        border-radius: 999px;
        padding: .35rem .65rem;
        font-weight: 700;
        font-size: .85rem;
    }

    .section-block {
        padding: 4.5rem 0;
    }

    .section-block.bg-soft { background: var(--bg-soft); }

    /* ---------- HERO ---------- */
    .hero-banner {
        position: relative;
        overflow: hidden;
        background: #000;
        clip-path: polygon(0 0, 100% 0, 100% 92%, 0 100%);
        margin-bottom: -3.5rem; /* lets the overlap card bite into the hero */
    }

    .hero-banner .carousel-inner,
    .hero-banner .carousel-item {
        min-height: 480px;
    }

    .hero-slide-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        min-height: 480px;
        width: 100%;
    }

    .hero-slide-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: brightness(0.78) contrast(1.05);
        z-index: 0;
    }

    .hero-slide-overlay {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0.48) 15%, rgba(0,0,0,0.08) 78%);
        z-index: 0;
    }

    .hero-banner .container-fluid { position: relative; z-index: 1; padding-bottom: 4rem; width: 100%; }

    .hero-indicators {
        bottom: 1.1rem;
        margin-bottom: 0;
        z-index: 2;
    }

    .hero-indicators [data-bs-target] {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: rgba(255,255,255,0.4);
        border: 0;
        opacity: 1;
        margin: 0 4px;
        transition: width .2s ease, background .2s ease;
    }

    .hero-indicators .active {
        width: 22px;
        background: var(--accent);
    }

    .hero-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.25);
        background: rgba(0,0,0,0.35);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
        opacity: 1;
    }

    .hero-banner:hover .hero-nav-btn { display: flex; }
    .hero-nav-btn:hover { background: var(--accent); border-color: var(--accent); color: var(--accent-contrast); }
    .hero-nav-prev { left: 1rem; }
    .hero-nav-next { right: 1rem; }

    .hero-badge {
        font-size: 0.72rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        padding: 0.4rem 0.75rem;
        background: var(--accent);
        color: var(--accent-contrast);
        font-weight: 700;
        border-radius: 999px;
    }

    .hero-banner h1 {
        font-weight: 700;
        text-transform: uppercase;
        font-size: clamp(2rem, 4.5vw, 3.6rem);
        line-height: 1.4;
        max-width: 720px;
        color: #fff;
        margin: 1rem 0;
    }

    .hero-banner p {
        font-size: 1rem;
        max-width: 520px;
        color: rgba(255,255,255,0.85);
    }

    .btn-mono {
        background: var(--accent);
        color: var(--accent-contrast);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 0.85rem;
        padding: 0.7rem 1.6rem;
        border-radius: 999px;
        border: 1px solid var(--accent);
        display: inline-block;
    }

    .btn-mono:hover { background: transparent; color: var(--accent); }

    /* Overlap "ưu đãi" card sitting on the hero seam */
    .overlap-card {
        position: relative;
        z-index: 2;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 1.5rem 3rem rgba(0,0,0,0.45);
    }

    .overlap-card .num {
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 1.8rem;
        color: var(--accent);
    }

    .overlap-card .label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
    }

    /* ---------- SECTION TITLES ---------- */
    .section-title {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--text);
    }

    .section-link {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
    }

    .section-link:hover { color: var(--accent); }

    /* ---------- PRODUCT CARDS w/ hover overlay ---------- */
    .product-card, .top-product-card {
        border: 1px solid var(--border);
        border-radius: 0.7rem;
        background: var(--card);
        color: var(--text);
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: border-color .2s ease, transform .2s ease;
    }

    .product-card:hover, .top-product-card:hover {
        border-color: rgba(255,122,26,0.5);
        transform: translateY(-4px);
    }

    .img-wrap {
        position: relative;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #000;
    }

    .img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease, filter .35s ease;
    }

    .product-card:hover .img-wrap img,
    .top-product-card:hover .img-wrap img {
        transform: scale(1.04);
        filter: brightness(0.75);
    }

    .badge-rank {
        position: absolute;
        top: 0.6rem;
        left: 0.6rem;
        background: rgba(255,122,26,0.95);
        color: #101214;
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 0.75rem;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    /* ---------- PRODUCT BADGES (category / stock / hot) ---------- */
    .badge-cat {
        position: absolute;
        bottom: 0.55rem;
        left: 0.55rem;
        background: rgba(0,0,0,0.72);
        color: #fff;
        font-size: 0.66rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.15);
        z-index: 2;
        backdrop-filter: blur(2px);
    }

    .badge-hot {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        background: var(--accent);
        color: var(--accent-contrast);
        font-size: 0.66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-stock-low {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        background: #dc3545;
        color: #fff;
        font-size: 0.64rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        z-index: 2;
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
        border: 1px solid #fff;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
    }

    .img-wrap.is-out img {
        filter: grayscale(1) brightness(0.5);
    }

    .btn-view.is-disabled {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }

    .product-code {
        font-size: 0.72rem;
        color: var(--muted);
        margin-bottom: 0.2rem;
        display: block;
    }

    .code-view-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .view-count {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        color: var(--muted);
        opacity: 0.85;
    }

    .view-count i {
        font-size: 0.78rem;
    }

    .body {
        padding: 1rem 1.1rem 1.2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .body h3 {
        font-size: 0.92rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
        color: var(--text);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .body p.desc {
        font-size: 0.8rem;
        color: var(--muted);
        margin-bottom: 0.6rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .price {
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--accent);
        margin-bottom: 0.75rem;
        margin-top: auto;
    }

    .btn-view {
        border: 1px solid var(--border);
        color: var(--text);
        background: transparent;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-radius: 999px;
        padding: 0.5rem 0.75rem;
        text-align: center;
        width: 100%;
        transition: all .15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .btn-view:hover {
        background: var(--accent);
        color: var(--accent-contrast);
        border-color: var(--accent);
    }

    /* ---------- ADD TO CART ---------- */
    .card-media-link {
        color: inherit;
        display: block;
    }

    .card-media-link:hover h3 { color: var(--accent); }

    .card-actions {
        padding: 0 1.1rem 1.1rem;
        margin-top: -0.8rem;
        display: flex;
        gap: 0.5rem;
    }

    .card-actions .btn-view,
    .card-actions .btn-add-cart {
        flex: 1 1 0;
        width: auto;
    }

    .btn-add-cart {
        width: 100%;
        border: 1px solid var(--accent);
        background: transparent;
        color: var(--accent);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-radius: 999px;
        padding: 0.55rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all .15s ease;
    }

    .btn-add-cart:hover {
        background: var(--accent);
        color: var(--accent-contrast);
    }

    .btn-add-cart.is-disabled {
        border-color: var(--border);
        color: var(--muted);
        pointer-events: none;
        cursor: not-allowed;
    }

    .btn-add-cart-sm {
        border: 1px solid rgba(255,255,255,0.35);
        background: rgba(255,255,255,0.08);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-radius: 999px;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-add-cart-sm:hover { background: var(--accent); border-color: var(--accent); color: var(--accent-contrast); }

    .btn-view-sm {
        border: 1px solid rgba(255,255,255,0.35);
        background: transparent;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-radius: 999px;
        padding: 0.4rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-view-sm:hover { background: rgba(255,255,255,0.12); border-color: #fff; color: #fff; }

    .featured-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 0.75rem;
    }

    /* Stretched-link pattern: card covers full click area for navigation,
       while buttons (given position:relative + z-index:2) stay clickable above it. */
    .stretched-link {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .card-actions-raised {
        position: relative;
        z-index: 2;
    }

    /* ---------- FEATURED (top #1) — bigger tile ---------- */
    .featured-card {
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        background: var(--card);
        overflow: hidden;
        position: relative;
        height: 100%;
        min-height: 340px;
        display: flex;
        align-items: flex-end;
    }

    .featured-card img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }

    .featured-card:hover img { transform: scale(1.05); }

    .featured-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.92) 100%);
    }

    .featured-card .body {
        position: relative;
        z-index: 1;
        width: 100%;
        padding: 1.5rem;
    }

    .featured-card .featured-tag {
        display: inline-block;
        background: var(--accent);
        color: var(--accent-contrast);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.3rem 0.6rem;
        border-radius: 999px;
        white-space: nowrap;
    }

    .featured-card .featured-cat-label {
        display: inline-block;
        background: rgba(0,0,0,0.55);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.18);
        backdrop-filter: blur(2px);
        white-space: nowrap;
    }

    .featured-card h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #fff;
        -webkit-line-clamp: 2;
    }

    /* ---------- TOP ROW ---------- */
    .top-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 1rem;
        overflow-x: auto;
        padding: 0.25rem 0 1rem;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }

    .top-row::-webkit-scrollbar { height: 6px; }
    .top-row::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

    .top-col {
        min-width: 260px;
        scroll-snap-align: start;
    }

    .top-product-card {
        min-height: 380px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    @media (min-width: 576px) {
        .top-row { grid-template-columns: repeat(2, minmax(260px, 1fr)); }
        .top-col { min-width: auto; }
    }

    @media (min-width: 992px) {
        .top-row {
            grid-template-columns: repeat(2, minmax(260px, 1fr));
            overflow: visible;
            padding: 0;
        }
        .top-col { min-width: auto; }
    }

    /* ---------- MID BANNER ---------- */
    .mid-banner {
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        background: linear-gradient(90deg, rgba(255,122,26,0.12), transparent 70%), var(--card);
        padding: 2rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .mid-banner h3 {
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .mid-banner p { color: var(--muted); font-size: 0.88rem; margin: 0; }

    /* ---------- CATEGORY SIDEBAR ---------- */
    .category-box {
        border: 1px solid var(--border);
        border-radius: 0.7rem;
        background: var(--card);
        padding: 1.25rem;
        position: sticky;
        top: 84px;
    }

    .category-box h2 {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1rem;
    }

    .category-list .list-group-item {
        background: transparent;
        border: 0;
        border-bottom: 1px solid var(--border);
        padding: 0.75rem 0.1rem;
        color: var(--muted);
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: color .15s ease;
    }

    .category-list .list-group-item i.bi-tag { color: var(--accent); font-size: 0.9rem; }
    .category-list .list-group-item .cat-name { flex-grow: 1; }
    .category-list .list-group-item .cat-count {
        font-size: 0.72rem;
        color: var(--muted);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 0.1rem 0.5rem;
    }

    .category-list .list-group-item:hover { color: var(--accent); }
    .category-list .list-group-item.active {
        background: rgba(255,122,26,0.12);
        color: var(--accent);
        border-left: 2px solid var(--accent);
        padding-left: 0.9rem;
    }
    .category-list .list-group-item.active .cat-count {
        background: var(--accent);
        color: var(--accent-contrast);
        border-color: var(--accent);
    }
    .category-list .list-group-item:last-child { border-bottom: 0; }

    .category-scroller {
        display: flex;
        gap: 0.6rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .category-chip {
        flex: 0 0 auto;
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        color: var(--text);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all .15s ease;
    }

    .category-chip i { color: var(--accent); font-size: 0.85rem; }
    .category-chip:hover { border-color: var(--accent); }
    .category-chip.active {
        background: rgba(255,122,26,0.12);
        color: var(--accent);
        border-color: var(--accent);
        box-shadow: inset 0 0 0 1px rgba(255,122,26,0.35);
    }

    .empty-state {
        border: 1px dashed var(--border);
        border-radius: 0.7rem;
        padding: 2rem;
        text-align: center;
        color: var(--muted);
        font-size: 0.9rem;
    }
</style>

<div class="home-page">

    <!-- ================= HERO CAROUSEL ================= -->
    <?php if (!empty($banners)): ?>
        <div id="heroCarousel" class="hero-banner carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <?php foreach ($banners as $i => $banner): ?>
                    <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                        <div class="hero-slide-bg" style="background-image:url('<?= htmlspecialchars($banner['image']) ?>')"></div>
                        <div class="hero-slide-overlay"></div>
                        <div class="hero-slide-content">
                            <div class="container-fluid px-3 px-lg-5">
                                <div class="row">
                                    <div class="col-12 col-lg-8 col-xl-7">
                                        <?php if (!empty($banner['badge'])): ?>
                                            <span class="hero-badge"><?= htmlspecialchars($banner['badge']) ?></span>
                                        <?php endif; ?>
                                        <h1><?= htmlspecialchars($banner['title']) ?></h1>
                                        <?php if (!empty($banner['description'])): ?>
                                            <p class="mb-4"><?= htmlspecialchars($banner['description']) ?></p>
                                        <?php endif; ?>
                                        <a href="<?= htmlspecialchars($banner['cta_link'] ?? '#products') ?>" class="btn-mono"><?= htmlspecialchars($banner['cta_text'] ?? 'Xem thêm') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($banners) > 1): ?>
                <button class="hero-nav-btn hero-nav-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <i class="bi bi-chevron-left"></i>
                    <span class="visually-hidden">Trước</span>
                </button>
                <button class="hero-nav-btn hero-nav-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <i class="bi bi-chevron-right"></i>
                    <span class="visually-hidden">Sau</span>
                </button>

                <div class="carousel-indicators hero-indicators">
                    <?php foreach ($banners as $i => $banner): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
                            class="<?= $i === 0 ? 'active' : '' ?>"
                            <?= $i === 0 ? 'aria-current="true"' : '' ?>
                            aria-label="Banner <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Overlap stat card — bites into the hero seam -->
    <div class="container-fluid px-3 px-lg-5" style="position: relative; z-index: 3;">
        <div class="row">
            <div class="col-12 col-md-7 col-lg-5 col-xl-4">
                <div class="overlap-card d-flex align-items-center gap-4">
                    <div>
                        <div class="num"><?= count($products) ?>+</div>
                        <div class="label">Sản phẩm đa dạng</div>
                    </div>
                    <div class="vr" style="opacity:.2;"></div>
                    <div>
                        <div class="num"><?= count($categories) ?></div>
                        <div class="label">Danh mục</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 px-lg-5 mt-4">
        <div class="home-voucher-cta rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="text-warning text-uppercase small fw-bold mb-1">Mini game mỗi 5 phút</div>
                <h2 class="h4 mb-1">Thử thách PHP 5 câu, săn voucher đến 90%</h2>
                <p class="text-secondary mb-0">Càng trả lời đúng, ưu đãi càng lớn.</p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="reward-pill">10%</span>
                <span class="reward-pill">30%</span>
                <span class="reward-pill">50%</span>
                <span class="reward-pill">70%</span>
                <span class="reward-pill">90%</span>
                <a href="<?= url('?action=mini-game') ?>" class="btn btn-warning fw-bold ms-sm-2"><i class="bi bi-stars me-1"></i>Chơi ngay</a>
            </div>
        </div>
    </div>

    <!-- ================= TOP PRODUCTS ================= -->
    <section class="section-block">
        <div class="container-fluid px-3 px-lg-5">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-end">
                    <h2 class="section-title mb-0">Top sản phẩm</h2>
                    <a href="<?= url('?action=products') ?>" class="section-link">Xem tất cả &rarr;</a>
                </div>
            </div>

            <?php if ($featured): $fb = productBadges($featured); ?>
                <div class="row g-3 g-lg-4">
                    <!-- Featured item #1 — bigger tile, 2 cols wide on desktop -->
                    <div class="col-12 col-lg-6">
                        <div class="featured-card<?= $fb['isOut'] ? ' is-out' : '' ?>">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($featured['image']) ?>" alt="<?= htmlspecialchars($featured['name']) ?>" loading="lazy" style="<?= $fb['isOut'] ? 'filter:grayscale(1) brightness(0.5);' : '' ?>">
                            <?php if ($fb['isOut']): ?>
                                <span class="badge-stock-low" style="background:#495057;">Hết hàng</span>
                            <?php elseif ($fb['isLow']): ?>
                                <span class="badge-stock-low">Chỉ còn <?= $fb['stock'] ?></span>
                            <?php elseif ($fb['isHot']): ?>
                                <span class="badge-hot"><i class="bi bi-fire"></i>Hot</span>
                            <?php endif; ?>
                            <div class="body">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="featured-tag">Best seller #1</span>
                                    <?php if ($fb['categoryName']): ?>
                                        <span class="featured-cat-label"><?= htmlspecialchars($fb['categoryName']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3><?= htmlspecialchars($featured['name']) ?></h3>
                                <div class="price">$<?= number_format((float)($featured['price'] ?? 0), 0, ',', '.') ?></div>

                                <div class="featured-actions card-actions-raised">
                                    <a href="<?= url('?action=product-detail&id=' . (int)($featured['id'] ?? 0)) ?>" class="btn-view-sm">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                    <?php if (!$fb['isOut']): ?>
                                        <form action="<?= url('?action=cart-add') ?>" method="post" class="d-inline-block m-0">
                                            <input type="hidden" name="product_id" value="<?= (int)($featured['id'] ?? 0) ?>">
                                            <button type="submit" class="btn-add-cart-sm">
                                                <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?= url('?action=product-detail&id=' . (int)($featured['id'] ?? 0)) ?>" class="stretched-link" aria-label="<?= htmlspecialchars($featured['name']) ?>"></a>
                        </div>
                    </div>

                    <!-- Remaining top items — smaller, scrollable on mobile -->
                    <div class="col-12 col-lg-6">
                        <div class="top-row h-100">
                            <?php foreach ($restTop as $i => $product): $b = productBadges($product); ?>
                                <div class="top-col">
                                    <div class="top-product-card">
                                        <div class="img-wrap<?= $b['isOut'] ? ' is-out' : '' ?>">
                                            <span class="badge-rank"><?= $i + 2 ?></span>
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
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">Chưa có sản phẩm nào.</div>
            <?php endif; ?>
        </div>
    </section>

    <div class="container-fluid px-3 px-lg-5 mb-5">
        <div class="mid-banner">
            <div>
                <h3>Miễn phí vận chuyển từ 500K</h3>
                <p>Giao hàng nhanh 2-4 ngày trên toàn quốc, đổi trả trong 7 ngày.</p>
            </div>
            <a href="<?= url('?action=products') ?>" class="btn-mono">Mua ngay</a>
        </div>
    </div>

    <div class="container-fluid px-3 px-lg-5 pb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-4 rounded-4" style="background: linear-gradient(135deg, rgba(255,122,26,0.12), rgba(255,255,255,0.02)); border:1px solid rgba(255,255,255,0.08);">
            <div>
                <p class="text-uppercase small text-warning mb-1" style="letter-spacing: .12em;">Catalog</p>
                <h3 class="mb-0 text-white">Khám phá sản phẩm theo danh mục và mục tiêu của bạn</h3>
            </div>
            <a href="<?= url('?action=products') ?>" class="btn-mono">Xem catalog</a>
        </div>
    </div>

