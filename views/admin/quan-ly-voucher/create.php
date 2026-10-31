<?php $discounts = $discounts ?? [10, 30, 50, 70, 90]; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Voucher</div>
                    <h3 class="mb-0">Tạo voucher mới</h3>
                </div>
                <a href="<?= url('?action=dashboard&table=vouchers') ?>" class="btn btn-secondary">Quay lại</a>
            </div>

            <form id="voucher-create-form" method="POST" action="<?= url('?action=voucher-store') ?>">
                <div class="mb-4">
                    <label for="voucher-code" class="form-label">Mã voucher</label>
                    <input type="text" class="form-control text-uppercase" id="voucher-code" name="code" maxlength="20" placeholder="Ví dụ: GAME30-NEW01" autocomplete="off">
                    <div class="text-danger small error-msg error-code"></div>
                </div>
                <div class="mb-4">
                    <label for="voucher-discount" class="form-label">Mức giảm</label>
                    <select class="form-select" id="voucher-discount" name="discount_percent">
                        <option value="">Chọn mức giảm</option>
                        <?php foreach ($discounts as $discount): ?>
                            <option value="<?= (int)$discount ?>">Giảm <?= (int)$discount ?>%</option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-danger small error-msg error-discount_percent"></div>
                </div>
                <div class="mb-4 text-danger small error-msg error-general"></div>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <button type="submit" class="btn cyber-btn flex-grow-1">Lưu voucher</button>
                    <a href="<?= url('?action=dashboard&table=vouchers') ?>" class="btn btn-secondary flex-grow-1">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>
<script>
    const voucherCreateForm = new AjaxForm('#voucher-create-form', '<?= url('?action=voucher-store') ?>');
    voucherCreateForm.onSuccess = function(response) {
        Swal.fire({ title: 'Thành công', text: response.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => window.location.href = '<?= url('?action=dashboard&table=vouchers') ?>');
    };
</script>
