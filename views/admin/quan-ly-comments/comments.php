<form method="GET" action="" class="mb-4">
    <input type="hidden" name="action" value="dashboard">
    <input type="hidden" name="table" value="comments">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-surface border-secondary text-muted">🔍</span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Tìm kiếm bình luận theo nội dung, người dùng hoặc sản phẩm..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="status" class="form-select border-secondary shadow-sm rounded-4">
                <option value="">Tất cả trạng thái</option>
                <option value="approved" <?= (isset($statusFilter) && $statusFilter === 'approved') ? 'selected' : '' ?>>Đã duyệt</option>
                <option value="pending" <?= (isset($statusFilter) && $statusFilter === 'pending') ? 'selected' : '' ?>>Đang chờ duyệt</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button class="btn cyber-btn w-100" type="submit">Lọc</button>
        </div>
    </div>
</form>

<?php if (!empty($comments)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= htmlspecialchars($comment['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($comment['user_fullname'] ?? $comment['fullname'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($comment['product_name'] ?? 'Unknown') ?></td>
                        <td>
                            <?php $status = $comment['status'] ?? 'approved'; ?>
                            <span class="badge bg-<?= $status === 'pending' ? 'warning text-dark' : 'success' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                        </td>
                        <td><?= str_repeat('★', (int)$comment['rating']) . str_repeat('☆', 5 - (int)$comment['rating']) ?></td>
                        <td class="text-truncate" style="max-width:250px;"><?= htmlspecialchars($comment['content'] ?? '') ?></td>
                        <td><?= htmlspecialchars($comment['created_at'] ?? '') ?></td>
                        <td>
                            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                            <a href="<?= url('?action=comment-detail&id=' . (int)$comment['id']) ?>" class="btn btn-sm btn-outline-info">Detail</a>
                            <?php if (($comment['status'] ?? 'approved') === 'pending'): ?>
                                <form action="<?= url('?action=comment-approve') ?>" method="POST" class="m-0" onsubmit="return confirm('Duyệt bình luận này?');">
                                    <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                            <?php endif; ?>
                            <form action="<?= url('?action=comment-delete') ?>" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');">
                                <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Không có bình luận nào.</div>
<?php endif; ?>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('?action=dashboard&table=comments&page=' . $i . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . urlencode($_GET['search']) : '') . ((isset($statusFilter) && $statusFilter !== null) ? '&status=' . urlencode($statusFilter) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
