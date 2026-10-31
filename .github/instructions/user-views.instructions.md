---
applyTo: "views/main.php,views/web/**,views/auth/**,views/user/**"
---
# Quy tắc riêng cho view người dùng

- Controller gọi `renderView('main', ['view' => 'web/xxx', 'title' => ..., ...dữ liệu])`. `main.php` bọc layout rồi `require` view bên trong `<main>`. Các view là **mảnh HTML**, không có `<html>`/`<head>`/`<body>` (đừng thêm).
- Biến view nhận được do controller truyền sẵn: xem bảng trong `docs/ui-redesign/CONTRACT.md`. Không tự gọi Model từ view, không thêm truy vấn.
- Theme hiện tại là **tối** (`data-bs-theme="dark"`, biến `--bg`, `.card { ... !important }`, `.text-muted`, `small` ép màu sáng). Thiết kế mới là **sáng**: chỉnh các quy tắc đó trong `main.php` / `assets/css/user-theme.css`, đừng chồng thêm `!important` lên trên.
- CSS mới đặt trong `assets/css/user-theme.css`, nạp bằng `BASE_URL . 'assets/css/user-theme.css'` trong `<head>` của `main.php` (không cần thêm hằng số ở `configs/`). File này **chỉ nạp ở `main.php`** nên không ảnh hưởng admin.
- Trang Trợ lý AI: HTML của bong bóng chat do **controller** sinh ra (`ai-message-bubble assistant|user`, `ai-message-meta`, `ai-chat-message`). Không đổi tên các class này. Phần HTML do AI trả về đã bị lọc: bỏ hết thuộc tính `class`, chỉ còn thẻ `div p strong b i em ul ol li a img h1-h4 br span`, nên thẻ sản phẩm trong chat phải được style bằng **selector theo cấu trúc** (ví dụ `.ai-chat-message > div`, `.ai-chat-message img`), không dùng `.product-card`.
- Flash message: loại hợp lệ là `success`, `danger`, `warning`, `info` (không phải `error`). Giữ nguyên danh sách này.
- Trạng thái huy hiệu sản phẩm lấy từ `productBadges($product)`: `isOut`, `isLow` (tồn kho 1 đến 3), `isHot` (từ 500 lượt xem). Dùng lại, đừng tự tính.
