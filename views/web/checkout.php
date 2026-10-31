<div class="container my-5">
    <h1 class="mb-4">Thanh toán</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card bg-dark border-light mb-4">
                <div class="card-body">
                    <h4 class="card-title text-white">Thông tin giao hàng</h4>
                    <form action="<?= url('?action=order-store') ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Người nhận</label>
                            <input type="text" name="receiver_name" class="form-control <?= validationError('receiver_name') ? 'is-invalid' : '' ?>" value="<?= old('receiver_name') ?>" required>
                            <div class="invalid-feedback d-block"><?= validationError('receiver_name') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="receiver_phone" class="form-control <?= validationError('receiver_phone') ? 'is-invalid' : '' ?>" pattern="^(\+84|84|0)[0-9]{9,10}$" maxlength="14" placeholder="0xxxxxxxxx hoặc +84xxxxxxxx" value="<?= old('receiver_phone') ?>" required>
                            <div class="form-text">Nhập số điện thoại Việt Nam, bắt đầu bằng 0 hoặc +84.</div>
                            <div class="invalid-feedback d-block"><?= validationError('receiver_phone') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ giao hàng</label>
                            <textarea name="shipping_address" class="form-control <?= validationError('shipping_address') ? 'is-invalid' : '' ?>" rows="3" maxlength="255" required><?= old('shipping_address') ?></textarea>
                            <div class="invalid-feedback d-block"><?= validationError('shipping_address') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control <?= validationError('note') ? 'is-invalid' : '' ?>" rows="2"><?= old('note') ?></textarea>
                            <div class="invalid-feedback d-block"><?= validationError('note') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="voucher_id">Voucher</label>
                            <select name="voucher_id" id="voucher_id" class="form-select">
                                <option value="">Không dùng voucher</option>
                                <?php foreach ($availableVouchers as $voucher): ?>
                                    <option value="<?= (int)$voucher['id'] ?>" data-discount="<?= (int)$voucher['discount_percent'] ?>" <?= (string)old('voucher_id') === (string)$voucher['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($voucher['code']) ?> - giảm <?= (int)$voucher['discount_percent'] ?>%
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($availableVouchers)): ?>
                                <div class="form-text">Bạn chưa có voucher chưa sử dụng.</div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card bg-dark border-light">
                <div class="card-body">
                    <h4 class="card-title text-white">Đơn hàng của bạn</h4>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($cartItems as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-light">
                                <div>
                                    <div class="fw-bold text-white"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="text-muted">Số lượng: <?= $item['quantity'] ?></div>
                                </div>
                                <span>$<?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="border-top border-light pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng thanh toán</span>
                            <strong id="checkout-total" data-original-total="<?= (float)$cartTotal ?>">$<?= number_format($cartTotal, 0, ',', '.') ?></strong>
                        </div>
                        <div id="voucher-summary" class="small text-success"></div>
                        <p class="text-muted small">Sau khi xác nhận, hệ thống sẽ tạo đơn hàng và gửi thông tin đến trang hóa đơn.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const voucherSelect = document.getElementById('voucher_id');
    const checkoutTotal = document.getElementById('checkout-total');
    const voucherSummary = document.getElementById('voucher-summary');
    if (voucherSelect && checkoutTotal) {
        voucherSelect.addEventListener('change', () => {
            const originalTotal = Number(checkoutTotal.dataset.originalTotal || 0);
            const discount = Number(voucherSelect.selectedOptions[0]?.dataset.discount || 0);
            const total = originalTotal * (1 - discount / 100);
            checkoutTotal.textContent = '$' + total.toLocaleString('vi-VN');
            voucherSummary.textContent = discount > 0 ? `Đã giảm ${discount}% từ voucher.` : '';
        });
        voucherSelect.dispatchEvent(new Event('change'));
    }
</script>
