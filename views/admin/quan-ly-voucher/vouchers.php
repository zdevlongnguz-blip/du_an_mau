<?php
$vouchers = $vouchers ?? [];
$pagination = $pagination ?? ['current_page' => 1, 'total_pages' => 1];
$search = $search ?? '';
?>

<form method="GET" action="" class="mb-4">
    <input type="hidden" name="action" value="dashboard">
    <input type="hidden" name="table" value="vouchers">
    <div class="row g-3 align-items-center">
        <div class="col-12 col-md-8">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-surface border-secondary text-muted">🔍</span>
                <input type="text" name="search" class="form-control border-secondary" placeholder="Tìm theo mã, mức giảm hoặc user ID..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <button class="btn cyber-btn w-100" type="submit">Tìm kiếm</button>
        </div>
    </div>
</form>

<?php if (!empty($vouchers)): ?>
    <div class="table-responsive rounded-4 overflow-hidden shadow-sm">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="small text-uppercase">
                    <th class="px-3">ID</th>
                    <th>Mã voucher</th>
                    <th>Mức giảm</th>
                    <th>Trạng thái</th>
                    <th>Người nhận</th>
                    <th>Ngày nhận</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vouchers as $voucher): ?>
                    <?php
                    $isClaimed = $voucher['user_id'] !== null;
                    $isUsed = (int)$voucher['is_used'] === 1;
                    $isEditable = !$isClaimed && !$isUsed && $voucher['order_id'] === null;
                    ?>
                    <tr>
                        <td class="px-3 text-muted">#<?= (int)$voucher['id'] ?></td>
                        <td><code><?= htmlspecialchars($voucher['code']) ?></code></td>
                        <td><span class="badge bg-warning text-dark">-<?= (int)$voucher['discount_percent'] ?>%</span></td>
                        <td>
                            <?php if ($isUsed): ?>
                                <span class="badge bg-danger">Đã sử dụng</span>
                            <?php elseif ($isClaimed): ?>
                                <span class="badge bg-info text-dark">Đã nhận</span>
                            <?php else: ?>
                                <span class="badge bg-success">Chưa nhận</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $isClaimed ? htmlspecialchars($voucher['fullname'] ?: ($voucher['username'] ?: ('User #' . $voucher['user_id']))) : '<span class="text-muted">-</span>' ?></td>
                        <td><?= $isClaimed ? htmlspecialchars($voucher['created_at']) : '<span class="text-muted">-</span>' ?></td>
                        <td class="text-end">
                            <?php if ($isEditable): ?>
                                <a href="<?= url('?action=voucher-edit&id=' . (int)$voucher['id']) ?>" class="btn btn-sm cyber-btn me-1">Edit</a>
                                <form id="voucher-delete-<?= (int)$voucher['id'] ?>" action="<?= url('?action=voucher-delete') ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int)$voucher['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="py-5 text-center text-muted">Không có voucher nào.</div>
<?php endif; ?>

<?php if (($pagination['total_pages'] ?? 1) > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
            <?php for ($i = 1; $i <= (int)$pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i === (int)$pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('?action=dashboard&table=vouchers&page=' . $i . ($search !== '' ? '&search=' . urlencode($search) : '')) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    document.querySelectorAll('form[id^="voucher-delete-"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            Swal.fire({
                title: 'Xóa voucher?',
                text: 'Voucher chưa nhận và chưa dùng sẽ bị xóa vĩnh viễn.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
        });

        const deleteForm = new AjaxForm(form, form.action);
        deleteForm.onSuccess = function(response) {
            Swal.fire({
                title: 'Đã xóa',
                text: response.message || 'Voucher đã được xóa.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => window.location.reload());
        };
    });
</script>
