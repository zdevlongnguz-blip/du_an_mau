<form method="GET" action="" class="mb-4">
    <input type="hidden" name="action" value="dashboard">
    <input type="hidden" name="table" value="users">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-8">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-surface border-secondary text-muted">🔍</span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Tìm kiếm người dùng..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <button class="btn cyber-btn w-100" type="submit">Tìm kiếm</button>
        </div>
    </div>
</form>

<?php if (isset($users) && !empty($users)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Avatar</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                        <td>
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= BASE_ASSETS_UPLOADS . $user['avatar'] ?>" alt="Avatar" width="50">
                            <?php else: ?>
                                <span class="small-muted">No avatar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
                        <td class="text-end">
                            <a href="<?= url('?action=user-detail&id=' . $user['id']) ?>" class="btn btn-sm btn-outline-info me-2">Detail</a>
                        <a href="<?= url('?action=edit_user&id=' . $user['id']) ?>" class="btn btn-sm cyber-btn me-2">Edit</a>
                            <form method="POST" action="<?= url('?action=delete_user') ?>" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Không có người dùng nào.</div>
<?php endif; ?>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('?action=dashboard&table=users&page=' . $i . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . urlencode($_GET['search']) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>