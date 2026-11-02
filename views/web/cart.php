<div class="hs-container hs-cart-page">
    <?php if (empty($cartItems)): ?>
        <div class="hs-cart-empty">
            <div class="hs-cart-empty__icon" aria-hidden="true"><i class="bi bi-bag"></i></div>
            <h1 class="hs-cart-empty__title">Giỏ hàng trống</h1>
            <p class="hs-cart-empty__text">Hãy thêm sản phẩm vào giỏ.</p>
            <a href="<?= url('?action=products') ?>" class="hs-btn hs-btn--primary">Xem sản phẩm</a>
        </div>
    <?php else: ?>
        <h1 class="hs-cart-title">Giỏ hàng (<?= count($cartItems) ?>)</h1>

        <div class="hs-cart-layout">
            <section class="hs-cart-list">
                <?php foreach ($cartItems as $item): ?>
                    <article class="hs-cart-item">
                        <img class="hs-cart-item__img" src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="96" height="96">

                        <div class="hs-cart-item__info">
                            <div class="hs-cart-item__name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="hs-cart-item__stock">Còn lại: <?= (int)$item['stock'] ?> sản phẩm</div>
                            <div class="hs-cart-item__price"><?= number_format((float)$item['price'], 0, ',', '.') ?>đ</div>
                        </div>

                        <form class="hs-cart-item__qty" action="<?= url('?action=cart-update') ?>" method="POST">
                            <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                            <div class="hs-qty" data-max="<?= max(1, (int)$item['stock']) ?>">
                                <button type="button" class="hs-qty__btn" data-qty-minus aria-label="Giảm số lượng">−</button>
                                <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= max(1, (int)$item['stock']) ?>" class="hs-qty__input" aria-label="Số lượng sản phẩm">
                                <button type="button" class="hs-qty__btn" data-qty-plus aria-label="Tăng số lượng">+</button>
                            </div>
                            <button type="submit" class="visually-hidden">Cập nhật số lượng</button>
                        </form>

                        <div class="hs-cart-item__total"><?= number_format((float)($item['quantity'] * $item['price']), 0, ',', '.') ?>đ</div>

                        <form class="hs-cart-item__remove" action="<?= url('?action=cart-remove') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                            <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                            <button type="submit" class="hs-btn hs-btn--ghost hs-btn--icon" aria-label="Xóa khỏi giỏ hàng" title="Xóa khỏi giỏ hàng">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="hs-cart-summary" aria-label="Tóm tắt đơn hàng">
                <div class="hs-summary-card">
                    <h2 class="hs-summary-title">Tóm tắt đơn hàng</h2>
                    <div class="hs-summary-row">
                        <span>Tạm tính</span>
                        <strong><?= number_format((float)$cartTotal, 0, ',', '.') ?>đ</strong>
                    </div>
                    <p class="hs-summary-note">Voucher được chọn ở bước thanh toán.</p>
                    <div class="hs-summary-row hs-summary-row--total">
                        <span>Tổng</span>
                        <strong><?= number_format((float)$cartTotal, 0, ',', '.') ?>đ</strong>
                    </div>
                    <a class="hs-btn hs-btn--primary hs-btn--block" href="<?= url('?action=checkout') ?>">Tiến hành thanh toán</a>
                    <a class="hs-btn hs-btn--ghost hs-btn--block" href="<?= BASE_URL ?>">Tiếp tục mua sắm</a>
                    <a class="hs-summary-link" href="<?= url('?action=mini-game') ?>">Nhận voucher qua mini game</a>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('click', function (event) {
    var btn = event.target.closest('[data-qty-plus]') || event.target.closest('[data-qty-minus]');
    if (!btn) return;

    var form = btn.closest('form.hs-cart-item__qty');
    if (!form) return;

    var input = form.querySelector('input[name="quantity"]');
    if (!input) return;

    event.preventDefault();
    event.stopPropagation();

    var min = parseInt(input.min, 10) || 1;
    var max = parseInt(input.max, 10) || 9999;
    var current = parseInt(input.value, 10) || min;
    var nextValue = current;

    if (btn.hasAttribute('data-qty-minus')) {
        nextValue = Math.max(min, current - 1);
    }

    if (btn.hasAttribute('data-qty-plus')) {
        nextValue = Math.min(max, current + 1);
    }

    input.value = String(nextValue);
    form.submit();
});
</script>
