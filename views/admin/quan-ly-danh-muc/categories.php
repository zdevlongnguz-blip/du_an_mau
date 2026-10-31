<?php if (isset($categories) && !empty($categories)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th>Name</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td class="text-end">
                            <a href="<?= url('?action=category-detail&id=' . $category['id']) ?>" class="btn btn-sm btn-outline-info me-2">Detail</a>
                        <a href="<?= url('?action=category-edit&id=' . $category['id']) ?>" class="btn btn-sm cyber-btn me-2">Edit</a>
                            <form method="POST" action="<?= url('?action=category-delete') ?>" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Chưa có danh mục nào.</div>
<?php endif; ?>
