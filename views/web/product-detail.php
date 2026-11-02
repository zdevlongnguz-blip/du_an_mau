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

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-light mb-3">
                <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($product['image'] ?? '') ?>"
                     alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>"
                     class="img-fluid object-fit-cover w-100 h-100">
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-secondary">Mã: <?= htmlspecialchars($product['code'] ?? 'N/A') ?></span>
                <span class="badge bg-secondary">Kho: <?= max(0, (int)($product['stock'] ?? 0)) ?></span>
            </div>
        </div>

        <div class="col-lg-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/" class="text-secondary text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('?action=product-detail') ?>" class="text-secondary text-decoration-none">Sản phẩm</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Chi tiết sản phẩm') ?></li>
                </ol>
            </nav>

            <h1 class="h2 fw-bold mb-3"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?></h1>

            <div class="mb-4">
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="fs-3 fw-bold">$<?= number_format((float)($product['price'] ?? 0), 2, '.', ',') ?></span>
                    <?php if ((int)($product['stock'] ?? 0) > 0): ?>
                        <span class="badge bg-success">Còn hàng</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Hết hàng</span>
                    <?php endif; ?>
                </div>
                <div class="text-muted small"><?= (int)($reviewSummary['total_comments'] ?? 0) ?> bình luận · <?= number_format((float)($reviewSummary['average_rating'] ?? 0), 1) ?> sao trung bình</div>
            </div>

            <p class="text-muted mb-4"><?= htmlspecialchars($product['description'] ?? 'Chưa có mô tả ngắn cho sản phẩm này.') ?></p>

            <form id="add-to-cart-form" action="<?= url('?action=cart-add') ?>" method="POST" class="mb-4">
                <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-auto">
                        <label for="quantity" class="col-form-label">Số lượng:</label>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm" style="width: 160px;">
                            <button type="button" id="quantity-decrement" class="btn btn-outline-dark" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>-</button>
                            <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" min="1" max="<?= max(1, (int)($product['stock'] ?? 1)) ?>" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                            <button type="button" id="quantity-increment" class="btn btn-outline-dark" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>+</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="text-secondary">Tối đa <?= max(0, (int)($product['stock'] ?? 0)) ?> sản phẩm</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-dark" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>Thêm vào giỏ</button>
                    <button type="submit" name="buy_now" class="btn btn-outline-dark" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>Mua ngay</button>
                </div>
            </form>
        </div>
    </div>

    <div class="hs-accordion hs-product-accordion accordion mt-5" id="productDetailAccordion">
        <div class="hs-accordion-item accordion-item border-0">
            <h3 class="accordion-header">
                <button class="hs-accordion-button accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-product-info" aria-expanded="true" aria-controls="collapse-product-info">
                    Thông tin sản phẩm
                </button>
            </h3>
            <div id="collapse-product-info" class="accordion-collapse collapse show" data-bs-parent="#productDetailAccordion">
                <div class="accordion-body hs-accordion-body">
                    <?php
                        $info = [
                            'Danh mục' => htmlspecialchars($product['category_name'] ?? 'N/A'),
                            'Lượt xem' => (int)($product['views'] ?? 0),
                            'Ngày tạo' => (!empty($product['created_at']) ? date('d/m/Y H:i', strtotime($product['created_at'])) : 'N/A'),
                            'Cập nhật' => (!empty($product['updated_at']) ? date('d/m/Y H:i', strtotime($product['updated_at'])) : 'N/A'),
                        ];
                    ?>
                    <dl class="hs-info">
                        <?php foreach ($info as $label => $val):
                            if ($val === '' || $val === null || $val === 'N/A') continue;
                        ?>
                            <div>
                                <dt><?= htmlspecialchars($label) ?></dt>
                                <dd><?= $val ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>

                    <div class="hs-prose hs-product-detail-copy mt-4">
                        <?php if (trim((string)($product['content'] ?? '')) === ''): ?>
                            <p class="mb-0 text-muted">Chưa có mô tả cho sản phẩm này</p>
                        <?php else: ?>
                            <?php
                                $allowed_tags = '<p><a><b><strong><i><em><ul><ol><li><br><h1><h2><h3><h4><h5><h6><img><span>';
                            ?>
                            <div class="hs-prose">
                                <?= strip_tags($product['content'] ?? '', $allowed_tags) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($youtubeEmbedUrl !== null): ?>
            <div class="hs-accordion-item accordion-item border-0">
                <h3 class="accordion-header">
                    <button class="hs-accordion-button accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-product-video" aria-expanded="false" aria-controls="collapse-product-video">
                        Video giới thiệu
                    </button>
                </h3>
                <div id="collapse-product-video" class="accordion-collapse collapse" data-bs-parent="#productDetailAccordion">
                    <div class="accordion-body hs-accordion-body">
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-body-tertiary">
                            <iframe
                                src="<?= htmlspecialchars($youtubeEmbedUrl, ENT_QUOTES, 'UTF-8') ?>"
                                title="Video giới thiệu <?= htmlspecialchars($product['name'] ?? 'sản phẩm', ENT_QUOTES, 'UTF-8') ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="hs-accordion-item accordion-item border-0">
            <h3 class="accordion-header">
                <button class="hs-accordion-button accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-product-comments" aria-expanded="false" aria-controls="collapse-product-comments">
                    Đánh giá và bình luận
                </button>
            </h3>
            <div id="collapse-product-comments" class="accordion-collapse collapse" data-bs-parent="#productDetailAccordion">
                <div class="accordion-body hs-accordion-body">
                    <?php if (!empty($comments)): ?>
                        <?php $commentCount = count($comments); ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($comments as $commentIndex => $comment): ?>
                                <?php $canManage = isset($_SESSION['user']['id']) && ($_SESSION['user']['id'] === $comment['user_id'] || ($_SESSION['user']['role'] ?? '') === 'admin'); ?>
                                <li class="list-group-item bg-transparent border-secondary-subtle px-0 py-3 <?= $commentIndex >= 3 ? 'comment-extra d-none' : '' ?>">
                                    <div class="d-flex gap-3">
                                        <img src="<?= !empty($comment['avatar']) ? BASE_ASSETS_UPLOADS . htmlspecialchars($comment['avatar']) : 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>" alt="<?= htmlspecialchars($comment['fullname']) ?>" width="48" height="48" class="rounded-circle object-fit-cover flex-shrink-0">
                                        <div class="flex-fill">
                                            <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                <div>
                                                    <strong><?= htmlspecialchars($comment['fullname']) ?></strong>
                                                    <span class="badge <?= ($comment['role'] ?? '') === 'admin' ? 'bg-danger' : 'bg-secondary' ?> ms-1"><?= htmlspecialchars($comment['role'] ?? 'user') ?></span>
                                                    <div class="small text-secondary"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></div>
                                                    <?php if (($comment['status'] ?? 'approved') !== 'approved'): ?>
                                                        <span class="badge bg-warning text-dark">Đang đợi duyệt</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($canManage): ?>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-dark comment-edit-toggle" data-comment-id="<?= (int)$comment['id'] ?>">Sửa</button>
                                                        <form class="d-inline-block comment-delete-form" action="<?= url('?action=comment-delete') ?>" method="POST" data-comment-id="<?= (int)$comment['id'] ?>">
                                                            <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger">Xóa</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mb-2 rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="<?= $i <= $comment['rating'] ? 'rating-star-filled' : 'rating-star-empty' ?>"><?= $i <= $comment['rating'] ? '★' : '☆' ?></span>
                                                <?php endfor; ?>
                                            </div>
                                            <div class="comment-content mb-3"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                                            <?php if ($canManage): ?>
                                                <form class="comment-edit-form d-none" action="<?= url('?action=comment-update') ?>" method="POST" data-comment-id="<?= (int)$comment['id'] ?>">
                                                    <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Chỉnh sửa nội dung</label>
                                                        <textarea name="content" class="form-control border-secondary" rows="3" required><?= htmlspecialchars($comment['content']) ?></textarea>
                                                        <div class="form-text text-danger error-content"></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Đánh giá</label>
                                                        <select name="rating" class="form-select border-secondary" required>
                                                            <?php for ($r = 5; $r >= 1; $r--): ?>
                                                                <option value="<?= $r ?>" <?= $comment['rating'] == $r ? 'selected' : '' ?>><?= $r ?> sao</option>
                                                            <?php endfor; ?>
                                                        </select>
                                                        <div class="form-text text-danger error-rating"></div>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                                        <button type="button" class="btn btn-sm btn-secondary comment-edit-cancel">Hủy</button>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($commentCount > 3): ?>
                            <button type="button" id="toggle-comments" class="btn btn-outline-dark btn-sm mt-3" aria-expanded="false">
                                Xem tất cả (<?= $commentCount ?> bình luận)
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-secondary">Chưa có bình luận nào. Hãy là người đầu tiên nhận xét!</div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                        <div class="mt-4 border-top pt-4">
                            <h3 class="h5 fw-bold mb-3">Viết bình luận</h3>
                            <form id="comment-create-form" action="<?= url('?action=comment-store') ?>" method="POST">
                                <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nội dung</label>
                                    <textarea name="content" class="form-control border-secondary" rows="4" required></textarea>
                                    <div class="form-text text-danger error-content"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá</label>
                                    <select name="rating" class="form-select border-secondary" required>
                                        <option value="5">5 sao</option>
                                        <option value="4">4 sao</option>
                                        <option value="3">3 sao</option>
                                        <option value="2">2 sao</option>
                                        <option value="1">1 sao</option>
                                    </select>
                                    <div class="form-text text-danger error-rating"></div>
                                </div>
                                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-4">Bạn cần <a href="<?= url('?action=login') ?>">đăng nhập</a> để bình luận và đặt hàng.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
                <div class="hs-product-panel">
                    <div class="hs-product-panel__head">
                        <h3 class="hs-product-panel__title">Sản phẩm liên quan</h3>
                    </div>

                    <div class="hs-grid">
                        <?php foreach ($relatedProducts as $relatedProduct): $b = productBadges($relatedProduct); ?>
                            <div class="hs-card">
                                <div class="hs-card__media <?= $b['isOut'] ? 'is-out' : '' ?>">
                                    <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($relatedProduct['image'] ?? '') ?>"
                                         alt="<?= htmlspecialchars($relatedProduct['name'] ?? 'Sản phẩm liên quan') ?>"
                                         loading="lazy">

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
                                    <h3 class="hs-card__title"><?= htmlspecialchars($relatedProduct['name'] ?? 'Sản phẩm') ?></h3>

                                    <div class="hs-card__footer">
                                        <div class="hs-card__price"><?= number_format((float)($relatedProduct['price'] ?? 0), 0, ',', '.') ?>đ</div>
                                        <div class="hs-card__actions">
                                            <a href="<?= url('?action=product-detail&id=' . (int)($relatedProduct['id'] ?? 0)) ?>" class="hs-btn hs-btn--ghost hs-btn--icon" aria-label="Xem chi tiết" title="Xem chi tiết">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                            </a>

                                            <form action="<?= url('?action=cart-add') ?>" method="post" class="hs-cart-form <?= $b['isOut'] ? 'is-disabled' : '' ?>">
                                                <input type="hidden" name="product_id" value="<?= (int)($relatedProduct['id'] ?? 0) ?>">
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
                </div>
            <?php endif; ?>

            

<script>
    const toggleCommentsButton = document.getElementById('toggle-comments');
    if (toggleCommentsButton) {
        toggleCommentsButton.addEventListener('click', () => {
            const isExpanded = toggleCommentsButton.getAttribute('aria-expanded') === 'true';
            document.querySelectorAll('.comment-extra').forEach((comment) => {
                comment.classList.toggle('d-none', isExpanded);
            });
            toggleCommentsButton.setAttribute('aria-expanded', String(!isExpanded));
            toggleCommentsButton.textContent = isExpanded
                ? 'Xem tất cả (' + <?= (int)($commentCount ?? 0) ?> + ' bình luận)'
                : 'Thu gọn';
        });
    }
</script>

<script>
    const quantityInput = document.getElementById('quantity');
    const decrementBtn = document.getElementById('quantity-decrement');
    const incrementBtn = document.getElementById('quantity-increment');
    const cartForm = document.getElementById('add-to-cart-form');
    const maxStock = Number(quantityInput?.getAttribute('max') || 1);

    const refreshButtons = () => {
        if (!quantityInput) return;
        let value = Number(quantityInput.value) || 1;
        if (value < 1) value = 1;
        if (value > maxStock) value = maxStock;
        quantityInput.value = value;
        if (decrementBtn) decrementBtn.disabled = value <= 1;
        if (incrementBtn) incrementBtn.disabled = value >= maxStock;
    };

    if (quantityInput) {
        quantityInput.addEventListener('input', refreshButtons);
    }
    if (incrementBtn) {
        incrementBtn.addEventListener('click', () => {
            if (!quantityInput) return;
            let value = Number(quantityInput.value) || 1;
            if (value < maxStock) {
                quantityInput.value = value + 1;
                refreshButtons();
            }
        });
    }
    if (decrementBtn) {
        decrementBtn.addEventListener('click', () => {
            if (!quantityInput) return;
            let value = Number(quantityInput.value) || 1;
            if (value > 1) {
                quantityInput.value = value - 1;
                refreshButtons();
            }
        });
    }

    if (cartForm) {
        cartForm.addEventListener('submit', function(event) {
            if (!quantityInput) return;
            let value = Number(quantityInput.value) || 1;
            if (value < 1) value = 1;
            if (value > maxStock) value = maxStock;
            quantityInput.value = value;
            if (value < 1 || value > maxStock) {
                event.preventDefault();
                alert('Số lượng không hợp lệ. Vui lòng chọn số lượng trong khoảng 1 đến ' + maxStock + '.');
            }
        });
    }

    refreshButtons();
</script>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commentCreateForm = document.getElementById('comment-create-form');
        if (commentCreateForm) {
            new AjaxForm(commentCreateForm, null, (response) => {
                alert(response.message || 'Bình luận đã được gửi.');
                window.location.reload();
            });
        }

        document.querySelectorAll('.comment-edit-toggle').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.closest('li');
                const editForm = listItem.querySelector('.comment-edit-form');
                if (!editForm) return;
                editForm.classList.toggle('d-none');
            });
        });

        document.querySelectorAll('.comment-edit-cancel').forEach(button => {
            button.addEventListener('click', function () {
                const editForm = this.closest('.comment-edit-form');
                if (!editForm) return;
                editForm.classList.add('d-none');
            });
        });

        document.querySelectorAll('.comment-edit-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!confirm('Bạn có chắc muốn lưu chỉnh sửa bình luận này?')) {
                    event.preventDefault();
                    return;
                }
            });

            new AjaxForm(form, null, (response) => {
                alert(response.message || 'Bình luận đã được cập nhật.');
                window.location.reload();
            });
        });

        document.querySelectorAll('.comment-delete-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const url = this.getAttribute('action');
                const formData = new FormData(this);
                if (!confirm('Bạn có chắc muốn xóa bình luận này?')) {
                    return;
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message || 'Bình luận đã được xóa.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Xóa bình luận không thành công.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối. Vui lòng thử lại.');
                });
            });
        });
    });
</script>

<style>
    .rating-stars {
        font-size: 0.95rem;
        line-height: 1;
    }

    .rating-star-filled {
        color: rgba(255, 192, 56, 0.92);
    }

    .rating-star-empty {
        color: rgba(255, 255, 255, 0.35);
    }
</style>