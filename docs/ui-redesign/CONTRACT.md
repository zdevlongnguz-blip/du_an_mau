# Hợp đồng giữa view người dùng và backend

Đây là những thứ **phải giữ nguyên** khi đổi giao diện. Rút ra từ code hiện tại (đọc `routes/index.php`, `controllers/*`, `configs/helper.php` và từng view).
Quy ước link: `url('?action=...')`. Mọi trang người dùng được bọc bởi `views/main.php`.

## 0. Cách một trang được dựng
`Controller` → `renderView('main', ['view' => 'web/xxx', 'title' => '...', ...dữ liệu])` → `extract()` → `main.php` in header, flash, rồi `require PATH_VIEW . $view . '.php'` trong `<main>`, rồi footer.
Admin dùng layout riêng `views/admin/dashboard.php`, **không** dùng `main.php`. Vì vậy CSS mới chỉ nạp trong `main.php` thì không ảnh hưởng admin.

## 1. Layout `views/main.php`
| Mục | Phải giữ |
|---|---|
| Biến | `$title`, `$view`, `$message` (alert info), `$cartItemCount` (nếu không có thì `getCartItemCount()`) |
| Helper | `url()`, `BASE_URL`, `navLinkClass()`, `isActiveRoute()`, `iconBtnClass()`, `getFlashMessage()`, `getFlashMessageType()` |
| Flash | loại hợp lệ: `success`, `danger`, `warning`, `info` |
| Session | `$_SESSION['is_logged_in']`, `$_SESSION['user']['fullname']`, `['role']` (hiện link Admin Panel khi `admin`) |
| JS | Bootstrap bundle (offcanvas `#mobileMenu`, dropdown, alert dismiss) và SweetAlert2 phải còn được nạp |
| Điều kiện | Mục "Nhận voucher" trong menu chỉ hiện khi đã đăng nhập. Chưa đăng nhập: chỉ có nút đến `?action=login`. Giỏ hàng, đơn hàng, tài khoản chỉ hiện khi đã đăng nhập |
| Link | `?action=products`, `?action=ai-chat`, `?action=mini-game`, `?action=cart`, `?action=order-history`, `?action=profile`, `?action=my-vouchers`, `?action=dashboard` (chỉ admin), `?action=logout`, `?action=login` |

## 2. Trang theo trang

### Trang chủ `web/home` (route `/`, controller `ProductController::index`)
- Biến: `topProducts`, `products`, `pagination`, `categories`, `filterSearch`, `filterCategory`.
- Form: thêm giỏ `POST ?action=cart-add` (`product_id`, `quantity`). Link: `?action=products`, `?action=product-detail&id=`, `?action=mini-game`.
- Carousel đang dùng `data-bs-ride`, `data-bs-slide`, `data-bs-slide-to`, `data-bs-target`, `data-bs-interval`. Nếu đổi hero, giữ cơ chế Bootstrap hoặc bỏ hẳn, đừng viết JS carousel riêng.
- `web/home-products.php` và `web/home-vue.php` **không được include ở đâu cả**: bỏ qua.

### Danh sách `web/catalog` (`?action=products`)
- Biến: `products`, `pagination`, `categories`, `filterSearch`, `filterCategory`, `cartItemCount`.
- Form lọc: `GET`, `action=""`, input ẩn `name="action" value="products"`, `name="search"`, `name="category"`. Phân trang dùng tham số `page`, dựng từ `$pagination` và `buildCleanQuery()`/`cleanQueryString()`.
- Form thêm giỏ: `POST ?action=cart-add` (`product_id`, `quantity`).
- Chỉ có tìm theo tên/mã và lọc theo danh mục. **Không có** sắp xếp, lọc giá.

### Chi tiết `web/product-detail` (`?action=product-detail&id=`)
- Biến: `product`, `comments`, `reviewSummary`, `relatedProducts`, `cartItemCount`.
- Form giỏ: `#add-to-cart-form`, `POST ?action=cart-add`, field `product_id`, `quantity`, `buy_now`. JS dùng `#quantity`, `#quantity-decrement`, `#quantity-increment`.
- Bình luận (dùng `AjaxForm.js`):
  - tạo: `#comment-create-form`, `POST ?action=comment-store`, field `product_id`, `rating`, `content`, chỗ lỗi `.error-rating`, `.error-content`.
  - sửa: `.comment-edit-form` (`POST ?action=comment-update`, field `comment_id`, `content`, `rating`); nút `.comment-edit-toggle`, `.comment-edit-cancel`; form ẩn/hiện bằng class `d-none`.
  - xóa: `.comment-delete-form` (`POST ?action=comment-delete`, field `comment_id`).
  - `#toggle-comments`, thuộc tính `data-comment-id`, `id="<comment id>"` trên từng bình luận.
- Hiển thị: video YouTube lấy từ biến `$youtubeEmbedUrl` do view tính (giữ khối PHP đó); bình luận "đang chờ duyệt"; Sửa/Xóa chỉ hiện với bình luận của chính người dùng (`$canManage`).

### Giỏ hàng `web/cart` (cần đăng nhập)
- Biến: `cartItems`, `cartTotal`. Form: `POST ?action=cart-update` (`product_id`, `quantity`), `POST ?action=cart-remove` (`product_id`). Link: `?action=checkout`.
- Giỏ trống: không hiện tóm tắt và không hiện nút thanh toán.

### Thanh toán `web/checkout` (cần đăng nhập)
- Biến: `cartItems`, `cartTotal`, `availableVouchers`.
- Form: `POST ?action=order-store`, field `receiver_name`, `receiver_phone`, `shipping_address`, `note`, `voucher_id` (`<select id="voucher_id" name="voucher_id">` với option rỗng `Không dùng voucher`; các option chứa `data-discount`; giá trị rỗng = không dùng voucher).
- JS tính tổng: `#checkout-total`, `#voucher-summary`, `#voucher_id`, thuộc tính `data-discount`, `data-original-total` trên lựa chọn voucher.
- Không có chọn phương thức thanh toán, không có phí ship.

### Đơn hàng `web/order-history`, `web/order-invoice`
- `order-history`: biến `orders`; link xem hóa đơn `?action=order-invoice&id=<id đơn>`.
- `order-invoice`: biến `order`, `orderItems`, `appliedVoucher`; view tự tính `subtotal`, `discountAmount` (giữ khối PHP đó). Sau khi đặt hàng, server chuyển tới trang này kèm **flash message** "Đơn hàng đã tạo thành công." (loại success): banner "Đặt hàng thành công" của thiết kế chính là flash này, không tự tạo banner riêng.
- Người dùng **không** hủy đơn được, **không** có theo dõi vận đơn.

### Mini game `web/mini-game` (cần đăng nhập)
- Biến: `todayAward`, `hasPlayedToday`, `vouchers`, `recentAwards`, `hasCooldown`, `cooldownRemaining`.
- JS hiện tại tự dựng câu hỏi bằng `fetch`: `POST ?action=mini-game-start` và `POST ?action=mini-game-answer`. Header gửi đi: `X-Requested-With: XMLHttpRequest`, `Accept: application/json`.
- JSON nhận về: `status` (`success`/lỗi), `message`, `question`, `question_number`, `correct`, `next_question`.
- Id phải giữ: `#quiz-start`, `#start-quiz`, `#quiz-form`, `#question-list`, `#submit-quiz`, `#game-message`; ô trả lời `id="answer" name="answer"`.
- Hồi chiêu 5 phút do server quyết định (`hasCooldown`, `cooldownRemaining`): view chỉ hiển thị.

### Trợ lý AI `web/ai-chat`
- Biến: `chatHistory`, `chatError`, `chatSuccess`, `cartItemCount`. Công khai, không cần đăng nhập.
- Form gửi: `#aiChatForm`, `POST ?action=ai-chat-send`, field `message`; ô nhập `#aiMessage`, nút `#aiChatSendButton`, vùng trạng thái `#aiChatStatus`, khung hội thoại `#aiChatHistory`.
- Form xóa: `#aiChatClearForm`, `POST ?action=ai-chat-clear`.
- JSON gửi: `status`, `message`, `historyHtml` (HTML bong bóng do controller sinh).
- **HTML do controller sinh, không đổi tên class**: `d-flex mb-3 justify-content-start|end` > `ai-message-bubble assistant|user` > `ai-message-meta` + `ai-chat-message`.
- Nội dung AI trả về bị lọc, mất hết `class`; chỉ còn các thẻ `div p strong b i em ul ol li a img h1-h4 br span`. Style thẻ sản phẩm theo cấu trúc trong `.ai-chat-message`.

### Đăng nhập / đăng ký / quên / đặt lại (`views/auth/*`), công khai
| Trang | Form | Field | Ghi chú JS |
|---|---|---|---|
| `login` | `POST ?action=auth` | `email`, `password` | submit thường (không Ajax). Biến `$message` |
| `register` | `#register-form` → `POST ?action=register-confirm` | `fullname`, `email`, `username`, `password`, `confirm_password`, ẩn `registration_token` (`$register_token`) | dùng `AjaxForm.js`. Chỗ lỗi: `.error-fullname`, `.error-email`, `.error-username`, `.error-password`, `.error-confirm_password`, `.error-general` (đều có class `error-msg`) |
| `forgot-password` | `#forgotPasswordForm` → `POST ?action=forgot-password-submit` | `email` | `#forgotSubmitButton`, `#forgotBackLink` |
| `reset-password` | `#resetPasswordForm` → `POST ?action=reset-password-submit` | `token` (ẩn), `password`, `confirm_password` | `#resetSubmitButton`, `#resetBackLink` |

### Tài khoản (`views/user/*`), cần đăng nhập
| Trang | Biến | Form / hook |
|---|---|---|
| `profile` (`?action=profile`) | `user`, `avatarUrl` | không có form. Link sang `profile-avatar-change`, `profile-in4-change` |
| `avatar-change` | `user` | `#change-avatar-form`, `POST ?action=profile-avatar-update`, `enctype="multipart/form-data"`, field `avatar`; `#avatar`, `#preview`; `.error-avatar`, `.error-general` (Ajax) |
| `in4-change` (đổi mật khẩu) | không | `#change-in4-form`, `POST ?action=profile-in4-update`, field `password`, `new-password`, `confirm-password`; `.error-password`, `.error-new-password`, `.error-confirm-password`, `.error-general` (Ajax) |
| `vouchers` (`?action=my-vouchers`) | `vouchers` | nút sao chép dùng `data-code` |

## 3. `AjaxForm.js` (dùng chung với admin, **không sửa**)
- Gửi `FormData` bằng `fetch`, mong JSON `{status: 'success'|'error', message, errors: {field: 'thông báo'}}`.
- Lỗi hiển thị vào phần tử `.error-<tên field>` **bên trong form**. Thiếu phần tử đó thì lỗi không hiện (chỉ có cảnh báo trong console). Trước mỗi lần gửi, mọi `.error-msg` bị xóa chữ.
- Nút `button[type="submit"]` bị đổi `innerText` thành "Đang xử lý..." rồi khôi phục: nút chỉ được chứa chữ thuần.

## 4. Quyền truy cập (đừng làm giao diện mâu thuẫn)
- Công khai: trang chủ, danh sách, chi tiết, Trợ lý AI, đăng nhập, đăng ký, quên/đặt lại mật khẩu.
- Cần đăng nhập (controller kế thừa `BaseController`, tự chuyển về đăng nhập): giỏ hàng, thanh toán, đơn hàng/hóa đơn, mini game, túi voucher, hồ sơ, đổi ảnh, đổi mật khẩu, bình luận.
