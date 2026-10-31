<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="panel-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Chi tiết người dùng</h3>
                    <p class="text-muted mb-0">ID: #<?= (int)$user['id'] ?></p>
                </div>
                <a href="<?= url('?action=dashboard&table=users') ?>" class="btn btn-outline-light">Quay lại danh sách</a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Username</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($user['username'] ?? '') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Email</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Họ và tên</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($user['fullname'] ?? '') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Vai trò</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($user['role'] ?? 'user') ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Trạng thái</strong></div>
                    <div class="text-muted"><?= isset($user['is_active']) && $user['is_active'] ? 'Active' : 'Inactive' ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3"><strong>Ngày tạo</strong></div>
                    <div class="text-muted"><?= htmlspecialchars($user['created_at'] ?? '') ?></div>
                </div>
                <div class="col-12">
                    <div class="mb-3"><strong>Avatar</strong></div>
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($user['avatar']) ?>" alt="Avatar" width="120" class="rounded-3">
                    <?php else: ?>
                        <div class="text-muted">Chưa có avatar</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
