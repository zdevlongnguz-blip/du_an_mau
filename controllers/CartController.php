<?php

class CartController extends BaseController
{
    private Cart $cartModel;
    private Product $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    public function index()
    {
        $items = $this->cartModel->getItems($_SESSION['user']['id']);
        $total = $this->cartModel->getTotalAmount($_SESSION['user']['id']);
        renderView('main', [
            'title' => 'Giỏ hàng của bạn',
            'view' => 'web/cart',
            'cartItems' => $items,
            'cartTotal' => $total,
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=cart'), 'Yêu cầu không hợp lệ.');
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
        $buyNow = isset($_POST['buy_now']);

        if ($productId <= 0) {
            redirectWithFlash(url('?action=product-detail&id=' . $productId), 'Sản phẩm không hợp lệ.');
        }

        $product = $this->productModel->detail($productId);
        if (!$product) {
            redirectWithFlash(url('?action=product-detail&id=' . $productId), 'Sản phẩm không tồn tại.');
        }

        if ((int)$product['stock'] <= 0) {
            redirectWithFlash(url('?action=product-detail&id=' . $productId), 'Sản phẩm đã hết hàng.');
        }

        if ($quantity > (int)$product['stock']) {
            redirectWithFlash(url('?action=product-detail&id=' . $productId), 'Số lượng yêu cầu vượt quá tồn kho.');
        }

        $existing = $this->cartModel->findItem($_SESSION['user']['id'], $productId);
        if ($existing && $existing['quantity'] + $quantity > (int)$product['stock']) {
            redirectWithFlash(url('?action=product-detail&id=' . $productId), 'Tổng số lượng trong giỏ vượt quá tồn kho.');
        }

        $this->cartModel->addItem($_SESSION['user']['id'], $productId, $quantity);

        if ($buyNow) {
            redirectWithFlash(url('?action=checkout'), 'Sản phẩm đã được thêm vào giỏ, tiếp tục đặt hàng.');
        }

        redirectWithFlash(url('?action=cart'), 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=cart'), 'Yêu cầu không hợp lệ.');
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($productId <= 0) {
            redirectWithFlash(url('?action=cart'), 'Sản phẩm không hợp lệ.');
        }

        $product = $this->productModel->detail($productId);
        if (!$product) {
            redirectWithFlash(url('?action=cart'), 'Sản phẩm không tồn tại.');
        }

        if ($quantity > (int)$product['stock']) {
            redirectWithFlash(url('?action=cart'), 'Số lượng cập nhật vượt quá tồn kho.');
        }

        $this->cartModel->updateItem($_SESSION['user']['id'], $productId, $quantity);
        redirectWithFlash(url('?action=cart'), 'Giỏ hàng đã được cập nhật.');
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=cart'), 'Yêu cầu không hợp lệ.');
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($productId <= 0) {
            redirectWithFlash(url('?action=cart'), 'Sản phẩm không hợp lệ.');
        }

        $this->cartModel->removeItem($_SESSION['user']['id'], $productId);
        redirectWithFlash(url('?action=cart'), 'Sản phẩm đã được loại bỏ khỏi giỏ hàng.');
    }
}
