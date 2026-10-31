# Màn hình Stitch ↔ view
Dự án: HUSTLER420 Storefront Web App (ID 993609209672100325)

| View | Tên màn hình Stitch (desktop) | Tên màn hình Stitch (mobile) |
|---|---|---|
| views/main.php (header, footer) | Trang Chủ | |
| views/web/home.php | Trang Chủ | Trang Chủ |
| views/web/catalog.php | Danh Sách Sản Phẩm | Danh Sách Sản Phẩm |
| views/web/product-detail.php | Chi Tiết Sản Phẩm | Chi Tiết Sản Phẩm |
| views/web/cart.php | Giỏ Hàng | Giỏ Hàng |
| views/web/checkout.php | Thanh Toán | Thanh Toán |
| views/web/order-history.php | Đơn Hàng Của Tôi | Đơn Hàng Của Tôi |
| views/web/order-invoice.php | Chi Tiết Đơn Hàng #128 | Chi Tiết Đơn Hàng #128 |
| views/auth/login.php | Đăng Nhập | Đăng Nhập |
| views/auth/register.php | Đăng Ký | Đăng Ký |
| views/auth/forgot-password.php | Quên Mật Khẩu | Quên Mật Khẩu |
| views/auth/reset-password.php | Đặt Lại Mật Khẩu | Đặt Lại Mật Khẩu |
| views/web/mini-game.php | Mini Game Thử Thách 5 Câu | Mini Game Thử Thách 5 Câu |
| views/user/vouchers.php | Túi Voucher | Túi Voucher |
| views/user/profile.php | Hồ Sơ Cá Nhân | Hồ Sơ Cá Nhân |
| views/user/avatar-change.php | Đổi Ảnh Đại Diện | Đổi Ảnh Đại Diện |
| views/user/in4-change.php | Đổi Mật Khẩu | Đổi Mật Khẩu |
| views/web/ai-chat.php | Trợ Lý Mua Sắm AI | Trợ Lý Mua Sắm AI |

### Màn hình không khớp 1:1 với một view duy nhất
- Khu Vực Tài Khoản: trong Stitch là nhóm màn hình tổng hợp, trong repo được tách thành 3 view riêng: `profile.php`, `avatar-change.php`, `in4-change.php`.
- Đăng nhập & Đăng ký: Stitch hiển thị như nhóm xác thực, nhưng repo chia thành 2 view riêng: `login.php` và `register.php`.
- Quên & Đặt lại mật khẩu: Stitch hiển thị như nhóm khôi phục mật khẩu, nhưng repo chia thành 2 view riêng: `forgot-password.php` và `reset-password.php`.
- Đơn hàng: Stitch có màn hình “Đơn Hàng Của Tôi” và “Chi Tiết Đơn Hàng #128”, tương ứng với `order-history.php` và `order-invoice.php`.

### Tool Stitch đã thấy trong session
- `mcp_statelessserv_list_projects`
- `mcp_statelessserv_get_project`
- `mcp_statelessserv_list_screens`
- `mcp_statelessserv_get_screen`
- `mcp_statelessserv_list_design_systems`
- `mcp_statelessserv_generate_screen_from_text`
- `mcp_statelessserv_edit_screens`
- `mcp_statelessserv_update_design_system`