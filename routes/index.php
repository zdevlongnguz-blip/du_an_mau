<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/' => (new ProductController)->index(),
    'dashboard' => (new DashBoardController)->index(),

    'product-create' => (new ProductAdminController)->create(),
    'product-store' => (new ProductAdminController)->store(),
    'product-generate-content' => (new ProductAdminController)->generateContent(),
    'product-edit' => (new ProductAdminController)->edit(),
    'product-update' => (new ProductAdminController)->update(),
    'product-delete' => (new ProductAdminController)->productDelete(),

    'voucher-create' => (new VoucherAdminController)->create(),
    'voucher-store' => (new VoucherAdminController)->store(),
    'voucher-edit' => (new VoucherAdminController)->edit(),
    'voucher-update' => (new VoucherAdminController)->update(),
    'voucher-delete' => (new VoucherAdminController)->delete(),

    'products' => (new ProductController)->catalog(),
    'product-detail' => (new ProductController)->detail(),

    'ai-chat' => (new AIChatController)->page(),
    'ai-chat-send' => (new AIChatController)->send(),
    'ai-chat-clear' => (new AIChatController)->clear(),
    'mini-game' => (new GameController)->page(),
    'mini-game-start' => (new GameController)->start(),
    'mini-game-answer' => (new GameController)->answer(),
    'mini-game-submit' => (new GameController)->submit(),

    'category-create' => (new CategoryAdminController)->categoryCreate(),
    'category-store' => (new CategoryAdminController)->categoryStore(),
    'category-edit' => (new CategoryAdminController)->categoryEdit(),
    'category-update' => (new CategoryAdminController)->categoryUpdate(),
    'category-delete' => (new CategoryAdminController)->categoryDelete(),
    'category-detail' => (new CategoryAdminController)->categoryDetail(),

    'edit_user' => (new UserController)->adminEdit(),
    'user-update' => (new UserController)->adminUpdate(),
    'delete_user' => (new UserController)->adminDelete(),
    'user-detail' => (new UserController)->adminDetail(),

    'login' => (new AuthController)->login(),
    'auth' => (new AuthController)->auth(),
    'logout' => (new AuthController)->logout(),
    'register' => (new AuthController)->register(),
    'register-confirm' => (new AuthController)->store(),
    'verify' => (new AuthController)->verify(),
    'forgot-password' => (new AuthController)->forgotPassword(),
    'forgot-password-submit' => (new AuthController)->forgotPasswordSubmit(),
    'reset-password' => (new AuthController)->resetPasswordForm(),
    'reset-password-submit' => (new AuthController)->resetPasswordSubmit(),

    'profile' => (new UserController)->profile(),
    'my-vouchers' => (new UserController)->vouchers(),
    'profile-avatar-change' => (new UserController)->avatarChange(),
    'profile-avatar-update' => (new UserController)->avatarUpdate(),
    'profile-in4-change' => (new UserController)->in4Change(),
    'profile-in4-update' => (new UserController)->in4Update(),

    'cart' => (new CartController)->index(),
    'cart-add' => (new CartController)->add(),
    'cart-update' => (new CartController)->update(),
    'cart-remove' => (new CartController)->remove(),

    'checkout' => (new OrderController)->checkout(),
    'order-store' => (new OrderController)->store(),
    'order-history' => (new OrderController)->history(),
    'order-invoice' => (new OrderController)->invoice(),
    'order-edit' => (new OrderController)->adminEdit(),
    'order-detail' => (new OrderController)->adminDetail(),
    'order-update' => (new OrderController)->adminUpdate(),
    'order-delete' => (new OrderController)->adminDelete(),
    'order-export' => (new OrderController)->adminExport(),

    'product-admin-detail' => (new ProductAdminController)->detail(),

    'comment-detail' => (new CommentController)->adminDetail(),
    'comment-store' => (new CommentController)->store(),
    'comment-update' => (new CommentController)->update(),
    'comment-approve' => (new CommentController)->approve(),
    'comment-delete' => (new CommentController)->delete(),

    'clear' => (function () {
        (new Product)->truncate();
        $list = (new Product)->all();
        foreach ($list as $entity) {
            if (($entity) && $entity['image'] !== '') {
                delete_uploaded_file($entity['image']);
            }
        }
        echo "đã clear map";
    })(),

    'seed' => (function () {
        for ($i = 1; $i <= 10; $i++) {
            (new User)->save([
                'username' => 'User_' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => '123456',
                'fullname' => 'Người dùng ' . $i,
                'avatar' => null,
                'role' => 'user',
                'status' => 1,
            ]);
        }
        echo "Đã thêm 10 bản ghi vào bảng users";
    })(),

    default => (new ProductController)->index(),
};
