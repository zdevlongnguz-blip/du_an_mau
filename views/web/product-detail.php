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
            <div class="ratio ratio-4x3 rounded-4 overflow-hidden bg-black mb-3">
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
                            <button type="button" id="quantity-decrement" class="btn btn-outline-light" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>-</button>
                            <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" min="1" max="<?= max(1, (int)($product['stock'] ?? 1)) ?>" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                            <button type="button" id="quantity-increment" class="btn btn-outline-light" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>+</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <span class="text-secondary">Tối đa <?= max(0, (int)($product['stock'] ?? 0)) ?> sản phẩm</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-dark" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>Thêm vào giỏ</button>
                    <button type="submit" name="buy_now" class="btn btn-outline-light" <?= (int)($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>Mua ngay</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-xl-8">
            <!--
                Bảng "Thông tin sản phẩm" chỉ còn các trường CHƯA hiển thị ở khu vực trên
                (Mã, Giá, Kho đã có ở badges + block giá phía trên -> bỏ để tránh lặp)
            -->
            <div class="card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Thông tin sản phẩm</h3>
                <div class="row g-3">
                    <?php
                        $info = [
                            'Danh mục' => htmlspecialchars($product['category_name'] ?? 'N/A'),
                            'Lượt xem' => (int)($product['views'] ?? 0),
                            'Ngày tạo' => (!empty($product['created_at']) ? date('d/m/Y H:i', strtotime($product['created_at'])) : 'N/A'),
                            'Cập nhật' => (!empty($product['updated_at']) ? date('d/m/Y H:i', strtotime($product['updated_at'])) : 'N/A'),
                        ];
                        foreach ($info as $label => $val):
                            if ($val === '' || $val === null) continue;
                    ?>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1"><?= htmlspecialchars($label) ?></div>
                            <div class="text-white"><?= $val ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Mô tả chi tiết</h3>
                <div class="text-white lh-lg">
                    <?php
                        $allowed_tags = '<p><a><b><strong><i><em><ul><ol><li><br><h1><h2><h3><h4><h5><h6>';
                        echo strip_tags($product['content'] ?? '', $allowed_tags);
                    ?>
                </div>
            </div>

            <?php if ($youtubeEmbedUrl !== null): ?>
                <div class="card p-4 mb-4">
                    <h3 class="h5 fw-bold mb-3">Video giới thiệu</h3>
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

            <?php if (!empty($relatedProducts)): ?>
                <div class="card p-4 mb-4 border-secondary-subtle" style="background: rgba(18,18,18,0.9);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h5 fw-bold mb-0">Sản phẩm liên quan</h3>
                        <span class="small text-secondary">Cùng danh mục</span>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                            <div class="col-md-6 col-xl-3">
                                <div class="card h-100 border border-secondary-subtle overflow-hidden shadow-sm" style="background: rgba(28,28,28,0.92);">
                                    <a href="<?= url('?action=product-detail&id=' . (int)($relatedProduct['id'] ?? 0)) ?>" class="d-block text-decoration-none text-white position-relative">
                                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($relatedProduct['image'] ?? '') ?>"
                                             alt="<?= htmlspecialchars($relatedProduct['name'] ?? 'Sản phẩm liên quan') ?>"
                                             class="img-fluid w-100 object-fit-cover" style="height: 180px;">
                                    </a>
                                    <div class="card-body d-flex flex-column gap-2 p-3">
                                        <h4 class="h6 fw-semibold mb-0 line-clamp-2">
                                            <a href="<?= url('?action=product-detail&id=' . (int)($relatedProduct['id'] ?? 0)) ?>" class="text-decoration-none text-white">
                                                <?= htmlspecialchars($relatedProduct['name'] ?? 'Sản phẩm') ?>
                                            </a>
                                        </h4>
                                        <div class="text-warning fw-bold small">
                                            $<?= number_format((float)($relatedProduct['price'] ?? 0), 0, ',', '.') ?>
                                        </div>
                                        <a href="<?= url('?action=product-detail&id=' . (int)($relatedProduct['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-light mt-auto w-100">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card p-4">
                <h3 class="h5 fw-bold mb-3">Bình luận và đánh giá</h3>
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
                                                <strong class="text-white"><?= htmlspecialchars($comment['fullname']) ?></strong>
                                                <span class="badge <?= ($comment['role'] ?? '') === 'admin' ? 'bg-danger' : 'bg-secondary' ?> ms-1"><?= htmlspecialchars($comment['role'] ?? 'user') ?></span>
                                                <div class="small text-secondary"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></div>
                                                <?php if (($comment['status'] ?? 'approved') !== 'approved'): ?>
                                                    <span class="badge bg-warning text-dark">Đang đợi duyệt</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($canManage): ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-light comment-edit-toggle" data-comment-id="<?= (int)$comment['id'] ?>">Sửa</button>
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
                                        <div class="comment-content mb-3 text-white"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                                        <?php if ($canManage): ?>
                                            <form class="comment-edit-form d-none" action="<?= url('?action=comment-update') ?>" method="POST" data-comment-id="<?= (int)$comment['id'] ?>">
                                                <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Chỉnh sửa nội dung</label>
                                                    <textarea name="content" class="form-control bg-dark text-white" rows="3" required><?= htmlspecialchars($comment['content']) ?></textarea>
                                                    <div class="form-text text-danger error-content"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Đánh giá</label>
                                                    <select name="rating" class="form-select bg-dark text-white" required>
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
                        <button type="button" id="toggle-comments" class="btn btn-outline-light btn-sm mt-3" aria-expanded="false">
                            Xem tất cả (<?= $commentCount ?> bình luận)
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-secondary">Chưa có bình luận nào. Hãy là người đầu tiên nhận xét!</div>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                <div class="card p-4 mt-4">
                    <h3 class="h5 fw-bold mb-3">Viết bình luận</h3>
                    <form id="comment-create-form" action="<?= url('?action=comment-store') ?>" method="POST">
                        <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea name="content" class="form-control bg-transparent border-secondary text-white" rows="4" required></textarea>
                            <div class="form-text text-danger error-content"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đánh giá</label>
                            <select name="rating" class="form-select text-white" required style="background: rgba(20, 20, 20, 0.98); border-color: rgba(255,255,255,0.12);">
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