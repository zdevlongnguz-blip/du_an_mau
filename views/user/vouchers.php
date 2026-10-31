<div class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-warning text-uppercase small fw-bold mb-1">Tài khoản của bạn</p>
            <h1 class="h2 mb-0">Túi voucher</h1>
        </div>
        <a href="<?= url('?action=mini-game') ?>" class="btn btn-warning"><i class="bi bi-gift me-1"></i>Nhận thêm voucher</a>
    </div>

    <?php if (empty($vouchers)): ?>
        <div class="alert alert-secondary">Bạn chưa có voucher nào. Hãy chơi mini game để nhận mã.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($vouchers as $voucher): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <span class="badge bg-warning text-dark">Giảm <?= (int)$voucher['discount_percent'] ?>%</span>
                                <?php if ((int)$voucher['is_used'] === 1): ?>
                                    <span class="badge bg-secondary">Đã dùng</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Còn hiệu lực</span>
                                <?php endif; ?>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" value="<?= htmlspecialchars($voucher['code']) ?>" readonly>
                                <button type="button" class="btn btn-outline-warning copy-voucher" data-code="<?= htmlspecialchars($voucher['code']) ?>" title="Sao chép mã"><i class="bi bi-copy"></i></button>
                            </div>
                            <small class="text-secondary">Nhận ngày <?= date('d/m/Y H:i', strtotime($voucher['created_at'])) ?></small>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.copy-voucher').forEach((button) => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(button.dataset.code);
            const original = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check2"></i>';
            setTimeout(() => button.innerHTML = original, 1200);
        });
    });
</script>
