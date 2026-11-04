<div class="hs-container hs-checkout-page">
    <?php
        $generalError = validationError('general');
        $receiverNameError = validationError('receiver_name');
        if ($receiverNameError === '' && $generalError !== '' && old('receiver_name') === '') {
            $receiverNameError = 'Vui lòng nhập tên người nhận.';
        }

        $receiverPhoneError = validationError('receiver_phone');
        if ($receiverPhoneError === '' && $generalError !== '' && old('receiver_phone') === '') {
            $receiverPhoneError = 'Vui lòng nhập số điện thoại.';
        }

        $shippingAddressError = validationError('shipping_address');
        if ($shippingAddressError === '' && $generalError !== '' && old('shipping_address') === '') {
            $shippingAddressError = 'Vui lòng nhập địa chỉ giao hàng.';
        }
    ?>

    <?php if ($generalError !== ''): ?>
        <div class="hs-form-alert" role="alert"><?= htmlspecialchars($generalError) ?></div>
    <?php endif; ?>

    <h1 class="hs-page-title">Thanh toán</h1>

    <ol class="hs-steps" aria-label="Các bước đặt hàng">
        <li class="hs-steps__item is-done"><a href="<?= url('?action=cart') ?>"><span class="hs-steps__num"><i class="bi bi-check-lg"></i></span>Giỏ hàng</a></li>
        <li class="hs-steps__item is-active" aria-current="step"><span class="hs-steps__num">2</span>Thông tin</li>
        <li class="hs-steps__item"><span class="hs-steps__num">3</span>Hóa đơn</li>
    </ol>

    <div class="hs-checkout-layout">
        <section class="hs-checkout-form">
            <h2 class="hs-section-title">Thông tin nhận hàng</h2>
            <form action="<?= url('?action=order-store') ?>" method="POST">
                <div class="hs-field">
                    <label class="hs-label" for="receiver_name">Người nhận</label>
                    <input id="receiver_name" type="text" name="receiver_name" class="form-control hs-input <?= $receiverNameError !== '' ? 'is-invalid' : '' ?>" value="<?= old('receiver_name') ?>">
                    <div class="invalid-feedback d-block"><?= $receiverNameError ?></div>
                </div>

                <div class="hs-field">
                    <label class="hs-label" for="receiver_phone">Số điện thoại</label>
                    <input id="receiver_phone" type="tel" name="receiver_phone" class="form-control hs-input <?= $receiverPhoneError !== '' ? 'is-invalid' : '' ?>" pattern="^(\+84|84|0)[0-9]{9,10}$" maxlength="14" placeholder="0xxxxxxxxx hoặc +84xxxxxxxx" value="<?= old('receiver_phone') ?>">
                    <div class="hs-hint">Nhập số điện thoại Việt Nam, bắt đầu bằng 0 hoặc +84.</div>
                    <div class="invalid-feedback d-block"><?= $receiverPhoneError ?></div>
                </div>

                <div class="hs-field">
                    <label class="hs-label" for="shipping_address">Địa chỉ giao hàng</label>
                    <textarea id="shipping_address" name="shipping_address" class="form-control hs-input hs-textarea <?= $shippingAddressError !== '' ? 'is-invalid' : '' ?>" rows="3" maxlength="255"><?= old('shipping_address') ?></textarea>
                    <div class="invalid-feedback d-block"><?= $shippingAddressError ?></div>
                </div>

                <div class="hs-field">
                    <label class="hs-label" for="note">Ghi chú</label>
                    <textarea id="note" name="note" class="form-control hs-input hs-textarea <?= validationError('note') ? 'is-invalid' : '' ?>" rows="2"><?= old('note') ?></textarea>
                    <div class="invalid-feedback d-block"><?= validationError('note') ?></div>
                </div>

                <div class="hs-field">
                    <label class="hs-label" for="voucher_id">Voucher</label>
                    <select class="form-select hs-input hs-select" name="voucher_id" id="voucher_id">
                        <option value="">Không dùng voucher</option>
                        <?php foreach ($availableVouchers as $voucher): ?>
                            <option value="<?= (int)$voucher['id'] ?>" data-discount="<?= (int)$voucher['discount_percent'] ?>" <?= (string)old('voucher_id') === (string)$voucher['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($voucher['code']) ?> - giảm <?= (int)$voucher['discount_percent'] ?>%
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($availableVouchers)): ?>
                        <div class="hs-hint">Bạn chưa có voucher. <a href="<?= url('?action=mini-game') ?>" class="hs-hint__link">Chơi mini game để nhận</a></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="hs-btn hs-btn--primary hs-btn--block hs-btn--lg">Xác nhận đặt hàng</button>
            </form>
        </section>

        <aside class="hs-checkout-summary" aria-label="Tóm tắt đơn hàng">
            <div class="hs-summary-card">
                <h2 class="hs-summary-title">Đơn hàng của bạn</h2>

                <div class="hs-order-list">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="hs-order-item">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="56" height="56">
                            <?php endif; ?>
                            <div class="hs-order-item__meta">
                                <div class="hs-order-item__name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="hs-order-item__qty">Số lượng: <?= (int)$item['quantity'] ?></div>
                            </div>
                            <div class="hs-order-item__total"><?= number_format((float)($item['quantity'] * $item['price']), 0, ',', '.') ?>đ</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="voucher-summary" class="hs-summary-voucher"></div>

                <div class="hs-summary-row hs-summary-row--total">
                    <span>Tổng</span>
                    <strong id="checkout-total" data-original-total="<?= (float)$cartTotal ?>"><?= number_format((float)$cartTotal, 0, ',', '.') ?>đ</strong>
                </div>

                <p class="hs-summary-note">Sau khi xác nhận, hệ thống sẽ tạo đơn hàng và gửi thông tin đến trang hóa đơn.</p>
            </div>
        </aside>
    </div>
</div>

<?php getValidationErrors(); ?>

<script>
    const voucherSelect = document.getElementById('voucher_id');
    const checkoutTotal = document.getElementById('checkout-total');
    const voucherSummary = document.getElementById('voucher-summary');

    if (voucherSelect && checkoutTotal) {
        voucherSelect.addEventListener('change', () => {
            const originalTotal = Number(checkoutTotal.dataset.originalTotal || 0);
            const discount = Number(voucherSelect.selectedOptions[0]?.dataset.discount || 0);
            const total = originalTotal * (1 - discount / 100);
            checkoutTotal.textContent = Math.round(total).toLocaleString('vi-VN') + 'đ';
            voucherSummary.textContent = discount > 0 ? `Đã giảm ${discount}% từ voucher.` : '';
        });
        voucherSelect.dispatchEvent(new Event('change'));
    }

    const checkoutForm = document.querySelector('form[action="<?= url('?action=order-store') ?>"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function () {
            const submitButton = checkoutForm.querySelector('button[type="submit"]');
            if (!submitButton || !checkoutForm.checkValidity()) {
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Đang xử lý...';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const firstAlert = document.querySelector('.hs-form-alert');
        const firstInvalidField = document.querySelector('input.is-invalid, textarea.is-invalid, select.is-invalid');
        const target = firstInvalidField || firstAlert;

        if (target) {
            target.scrollIntoView({ behavior: 'auto', block: 'center' });
        }

        if (firstInvalidField && typeof firstInvalidField.focus === 'function') {
            firstInvalidField.focus();
        }

        ['receiver_name', 'receiver_phone', 'shipping_address', 'note'].forEach(function (fieldName) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            field.addEventListener('blur', function () {
                field.value = field.value.trim();
            });
        });
    });
</script>
