<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Chi tiết bình luận</h3>
                    <p class="text-muted mb-0">ID: #<?= (int)$comment['id'] ?></p>
                </div>
                <a href="<?= url('?action=dashboard&table=comments') ?>" class="btn btn-outline-light">Quay lại danh sách</a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="mb-2"><strong>Người dùng</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($comment['user_fullname'] ?? $comment['user_username'] ?? 'N/A') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2"><strong>Sản phẩm</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($comment['product_name'] ?? 'N/A') ?> (ID #<?= (int)($comment['product_id'] ?? 0) ?>)</div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2"><strong>Trạng thái</strong></div>
                    <div class="text-muted"><span class="badge bg-<?= ($comment['status'] ?? 'approved') === 'pending' ? 'warning text-dark' : 'success' ?>"><?= htmlspecialchars(ucfirst($comment['status'] ?? 'approved')) ?></span></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-2"><strong>Ngày tạo</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($comment['created_at'] ?? '') ?></div>
                </div>
                <div class="col-12">
                    <div class="mb-2"><strong>Nội dung</strong></div>
                    <div class="text-muted"><?= nl2br(htmlspecialchars($comment['content'] ?? '')) ?></div>
                </div>
            </div>

            <?php if (($comment['status'] ?? 'approved') === 'pending'): ?>
                <form action="<?= url('?action=comment-approve') ?>" method="POST" class="mt-4" onsubmit="return confirm('Duyệt bình luận này?');">
                    <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                    <button type="submit" class="btn btn-success">Duyệt bình luận</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
