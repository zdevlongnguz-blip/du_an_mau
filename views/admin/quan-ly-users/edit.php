<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Người dùng</div>
                    <h3 class="mb-0">Chỉnh sửa trạng thái</h3>
                </div>
                <a href="<?= url('?action=dashboard&table=users') ?>" class="btn btn-secondary">Quay lại danh sách</a>
            </div>

            <form method="POST" action="<?= url('?action=user-update') ?>" class="row g-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

                <div class="col-12">
                    <label class="form-label">Tên người dùng</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= ($user['is_active'] == 1) ? 'selected' : '' ?>>Mở khóa</option>
                        <option value="0" <?= ($user['is_active'] == 0) ? 'selected' : '' ?>>Khóa</option>
                    </select>
                </div>

                <div class="col-12 d-flex flex-column flex-sm-row gap-3">
                    <button type="submit" class="btn cyber-btn flex-grow-1">Lưu trạng thái</button>
                </div>
            </form>

            <form method="POST" action="<?= url('?action=delete_user') ?>" class="mt-3" onsubmit="return confirm('Bạn chắc chắn muốn xóa người dùng này?');">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                <button type="submit" class="btn btn-danger w-100">Xóa người dùng</button>
            </form>
        </div>
    </div>
</div>
