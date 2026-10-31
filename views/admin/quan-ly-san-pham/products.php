<form method="GET" action="" class="mb-4">
    <input type="hidden" name="action" value="dashboard">
    <input type="hidden" name="table" value="products">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-8">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-surface border-secondary text-muted">🔍</span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <button class="btn cyber-btn w-100" type="submit">Tìm kiếm</button>
        </div>
    </div>
</form>

<?php if (!empty($products)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th class="px-3">Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
               <?php foreach ($products as $item): ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= htmlspecialchars($item['id'] ?? '') ?></td>
                        <td class="px-3 text-muted"><?= htmlspecialchars($item['code'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['category_name'] ?? '') ?></td>
                        <td>$<?= htmlspecialchars($item['price'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['stock'] ?? '') ?></td>
                        <td class="text-truncate px-3" style="max-width: 240px;"><?= htmlspecialchars($item['description'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= BASE_ASSETS_UPLOADS . ($item['image'] ?? '') ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Product Image') ?>" width="50">
                            <?php else: ?>
                                <span class="small-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('?action=product-admin-detail&id=' . $item['id']) ?>" class="btn btn-sm btn-outline-info me-1">Detail</a>
                        <a href="<?= url('?action=product-edit&id=' . $item['id']) ?>" class="btn btn-sm cyber-btn me-1">Edit</a>
                            <form action="<?= url('?action=product-delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Không có sản phẩm nào.</div>
<?php endif; ?>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('?action=dashboard&table=products&page=' . $i . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . urlencode($_GET['search']) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>