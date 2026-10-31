<?php
$youtubeEmbedUrl = null;
$youtubeVideo = trim((string)($product['youtube_video'] ?? ''));

if ($youtubeVideo !== '') {
    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $youtubeVideo, $matches)) {
        $youtubeVideo = $matches[1];
    }

    $youtubeUrl = parse_url($youtubeVideo);
    $youtubeHost = strtolower($youtubeUrl['host'] ?? '');
    $youtubePath = $youtubeUrl['path'] ?? '';

    if (
        in_array($youtubeHost, ['www.youtube.com', 'youtube.com', 'm.youtube.com', 'youtu.be'], true)
        && ($youtubeHost === 'youtu.be' || str_starts_with($youtubePath, '/embed/'))
    ) {
        $youtubeEmbedUrl = $youtubeVideo;
    }
}
?>

<div class="row g-4">
    <div class="col-12">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start align-items-md-center">
                <div>
                    <h3 class="mb-1">Chi tiết sản phẩm</h3>
                    <p class="text-muted mb-0">ID: #<?= (int)($product['id'] ?? 0) ?></p>
                </div>
                <a href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-outline-light">Quay lại danh sách</a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" class="img-fluid rounded-4 w-100 mb-4">
                    <?php endif; ?>
                    <div class="panel-card p-4">
                        <div class="mb-3"><strong>Giá</strong></div>
                        <div class="text-muted">$<?= number_format($product['price'] ?? 0, 0, ',', '.') ?></div>
                        <div class="mb-3 mt-3"><strong>Kho</strong></div>
                        <div class="text-muted"><?= (int)($product['stock'] ?? 0) ?></div>
                        <div class="mb-3 mt-3"><strong>Danh mục</strong></div>
                        <div class="text-muted"><?= htmlspecialchars($product['category_name'] ?? 'Không có') ?></div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="panel-card p-4">
                        <h4 class="mb-3"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?></h4>
                        <div class="mb-3"><strong>Mô tả</strong></div>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                        <div class="mb-3"><strong>Mã sản phẩm</strong></div>
                        <p class="text-muted"><?= htmlspecialchars($product['code'] ?? 'N/A') ?></p>
                        <div class="mb-3"><strong>Nội dung</strong></div>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['content'] ?? '')) ?></p>
                        <div class="mb-3"><strong>Ngày tạo</strong></div>
                        <div class="text-muted"><?= htmlspecialchars($product['created_at'] ?? '') ?></div>
                        <div class="mt-4">
                            <a href="<?= url('?action=product-edit&id=' . (int)$product['id']) ?>" class="btn cyber-btn me-2">Chỉnh sửa</a>
                            <a href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-outline-light">Quay lại</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($youtubeEmbedUrl !== null): ?>
                <div class="panel-card p-4 mt-4">
                    <h4 class="mb-3">Video giới thiệu</h4>
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black">
                        <iframe
                            src="<?= htmlspecialchars($youtubeEmbedUrl, ENT_QUOTES, 'UTF-8') ?>"
                            title="Video giới thiệu <?= htmlspecialchars($product['name'] ?? 'sản phẩm', ENT_QUOTES, 'UTF-8') ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen></iframe>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
