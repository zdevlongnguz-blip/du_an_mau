<form method="GET" action="" class="mb-4">
    <input type="hidden" name="action" value="dashboard">
    <input type="hidden" name="table" value="orders">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-8">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-surface border-secondary text-muted">🔍</span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Tìm kiếm đơn hàng theo ID, tên, số điện thoại, trạng thái..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <button class="btn cyber-btn w-100" type="submit">Tìm kiếm</button>
        </div>
    </div>
</form>

<?php if (!empty($summary)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="panel-card p-3 h-100">
                <div class="mb-2 text-muted">Tổng đơn hàng</div>
                <h3 class="mb-0"><?= number_format($summary['total_orders'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col">
            <div class="panel-card p-3 h-100">
                <div class="mb-2 text-muted">Doanh thu</div>
                        <h3 class="mb-0">$<?= number_format($summary['total_revenue'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col">
            <div class="panel-card p-3 h-100">
                <div class="mb-2 text-muted">Giá trị trung bình</div>
                        <h3 class="mb-0">$<?= number_format($summary['average_amount'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col">
            <div class="panel-card p-3 h-100">
                <div class="mb-2 text-muted">Tìm kiếm</div>
                <h3 class="mb-0"><?= htmlspecialchars($search ?? '') ?></h3>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <a href="<?= url('?action=order-export' . (isset($search) && $search !== '' ? '&search=' . urlencode($search) : '')) ?>" class="btn cyber-btn">Xuất CSV đơn hàng</a>
        <?php if (!empty($statusTotals)): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($statusTotals as $status): ?>
                    <span class="badge badge-cyber"><?= htmlspecialchars(ucfirst($status['status'])) ?>: <?= (int)$status['count'] ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($orders)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th>User</th>
                    <th>Receiver</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= htmlspecialchars($order['id']) ?></td>
                        <td>
                            <?= htmlspecialchars($order['user_fullname'] ?: $order['username'] ?: 'Unknown') ?><br>
                            <span class="small-muted"><?= htmlspecialchars($order['username'] ?? '') ?></span>
                        </td>
                        <td><?= htmlspecialchars($order['receiver_name']) ?></td>
                        <td><?= htmlspecialchars($order['receiver_phone']) ?></td>
                        <td class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($order['shipping_address']) ?></td>
                        <td>$<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td class="text-end">
                            <a href="<?= url('?action=order-detail&id=' . (int)$order['id']) ?>" class="btn btn-sm btn-outline-info me-1">Detail</a>
                        <a href="<?= url('?action=order-edit&id=' . (int)$order['id']) ?>" class="btn btn-sm btn-outline-light me-1">Edit</a>
                            <form action="<?= url('?action=order-delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Chưa có đơn hàng nào.</div>
<?php endif; ?>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('?action=dashboard&table=orders&page=' . $i . ((isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . urlencode($_GET['search']) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>