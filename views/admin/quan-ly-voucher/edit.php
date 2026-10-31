<?php
$voucher = $voucher ?? [];
$discounts = $discounts ?? [10, 30, 50, 70, 90];
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Voucher</div>
                    <h3 class="mb-0">Chỉnh sửa voucher</h3>
                </div>
                <a href="<?= url('?action=dashboard&table=vouchers') ?>" class="btn btn-secondary">Quay lại</a>
            </div>

            <?php $isLocked = $voucher['user_id'] !== null || (int)$voucher['is_used'] === 1 || $voucher['order_id'] !== null; ?>
            <?php if ($isLocked): ?>
                <div class="alert alert-warning">Voucher đã nhận hoặc đã dùng, chỉ được xem và không thể chỉnh sửa.</div>
            <?php endif; ?>

            <form id="voucher-edit-form" method="POST" action="<?= url('?action=voucher-update') ?>">
                <input type="hidden" name="id" value="<?= (int)($voucher['id'] ?? 0) ?>">
                <div class="mb-4">
                    <label for="voucher-code" class="form-label">Mã voucher</label>
                    <input type="text" class="form-control text-uppercase" id="voucher-code" name="code" maxlength="20" value="<?= htmlspecialchars($voucher['code'] ?? '') ?>" <?= $isLocked ? 'readonly' : '' ?> autocomplete="off">
                    <div class="text-danger small error-msg error-code"></div>
                </div>
                <div class="mb-4">
                    <label for="voucher-discount" class="form-label">Mức giảm</label>
                    <select class="form-select" id="voucher-discount" name="discount_percent" <?= $isLocked ? 'disabled' : '' ?>>
                        <?php foreach ($discounts as $discount): ?>
                            <option value="<?= (int)$discount ?>" <?= (int)($voucher['discount_percent'] ?? 0) === (int)$discount ? 'selected' : '' ?>>Giảm <?= (int)$discount ?>%</option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-danger small error-msg error-discount_percent"></div>
                </div>
                <div class="mb-4 text-danger small error-msg error-general"></div>
                <?php if (!$isLocked): ?>
                    <button type="submit" class="btn cyber-btn">Cập nhật</button>
                <?php endif; ?>
                <a href="<?= url('?action=dashboard&table=vouchers') ?>" class="btn btn-secondary ms-2">Quay lại</a>
            </form>
        </div>
    </div>
</div>

<?php if (!$isLocked): ?>
<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    const voucherEditForm = new AjaxForm('#voucher-edit-form', '<?= url('?action=voucher-update') ?>');
    voucherEditForm.onSuccess = function(response) {
        Swal.fire({ title: 'Thành công', text: response.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => window.location.href = '<?= url('?action=dashboard&table=vouchers') ?>');
    };
</script>
<?php endif; ?>
