<div class="container my-5">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-warning">Giỏ hàng trống. Hãy thêm sản phẩm vào giỏ.</div>
        <a href="<?= BASE_URL ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>

    <div class="table-responsive mb-4">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td class="d-flex gap-3 align-items-center">
                            <img src="<?= BASE_ASSETS_UPLOADS . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="80" height="80" class="img-fluid rounded">
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="text-muted">Còn lại: <?= (int)$item['stock'] ?> sản phẩm</div>
                            </div>
                        </td>
                        <td>$<?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td>
                            <form action="<?= url('?action=cart-update') ?>" method="POST" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, (int)$item['stock']) ?>" class="form-control form-control-sm" style="width: 90px;">
                                <button class="btn btn-sm btn-outline-light" type="submit">Cập nhật</button>
                            </form>
                        </td>
                        <td>$<?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></td>
                        <td>
                            <form action="<?= url('?action=cart-remove') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="row align-items-center">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>" class="btn btn-outline-light">Tiếp tục mua sắm</a>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="mb-3">
                <span class="fw-bold">Tổng thanh toán:</span>
                <span class="fs-4 text-primary">$<?= number_format($cartTotal, 0, ',', '.') ?></span>
            </div>
            <a href="<?= url('?action=checkout') ?>" class="btn btn-success btn-lg">Tiến hành thanh toán</a>
        </div>
    </div>
</div>
