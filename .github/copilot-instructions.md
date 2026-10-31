# Hướng dẫn cho Copilot: dự án du_an_mau (PHP MVC thuần, shop Hustler420)

## Nhiệm vụ hiện tại (và chỉ nhiệm vụ này)
Thiết kế lại **giao diện người dùng** (HTML/CSS và JS phía trình duyệt) theo bản thiết kế Stitch.
Backend nghiệp vụ đang chạy đúng. **Không được thay đổi hành vi của nó.**
Trang admin giữ nguyên 100%.

Trước khi sửa bất cứ trang nào, đọc `docs/ui-redesign/CONTRACT.md` (dữ liệu, form, id, route mà view đang dùng).

## Thiết kế Stitch (nguồn thiết kế duy nhất)
- Dự án Stitch: "HUSTLER420 Storefront Web App"
- Project ID: 993609209672100325
- URL: https://stitch.withgoogle.com/projects/993609209672100325
- Luôn lấy thiết kế qua MCP server `stitch`, đúng dự án này. Không tạo dự án mới.
- Stitch chỉ để ĐỌC. Không gọi tool tạo, sinh hay sửa màn hình trong Stitch.
- Bảng màn hình Stitch ↔ file view: xem docs/ui-redesign/STITCH-MAP.md
- Nếu docs/ui-refactor-audit.md mâu thuẫn với file này, luôn theo file này.

## Vùng được sửa (whitelist, không có gì khác)
- `views/main.php` (layout chung của người dùng)
- `views/web/*.php`, `views/auth/*.php`, `views/user/*.php`
  (trừ `views/web/home-products.php` và `views/web/home-vue.php`: file thừa, không đụng)
- Tạo MỚI: `assets/css/user-theme.css`, `assets/img/*`, `assets/js/user-*.js`

## Vùng cấm sửa (blacklist)
`controllers/`, `models/`, `services/`, `routes/`, `configs/`, `index.php`, `views/admin/`,
`assets/js/AjaxForm.js` (admin cũng dùng), `*.sql`, `composer.*`, `vendor/`, `docs/*.puml`, `account.txt`.
Nếu thấy việc cần sửa một file cấm: **dừng lại, giải thích lý do, hỏi người dùng**. Không tự sửa "cho tiện".

## Quy tắc bất biến khi sửa view
1. **Giữ nguyên mọi khối PHP**: `<?php ... ?>`, `<?= ... ?>`, vòng lặp, điều kiện, `htmlspecialchars`, các hàm helper (`url()`, `productBadges()`, `getFlashMessage()`, `navLinkClass()`...). Chỉ được đổi markup/class/CSS bao quanh. Không viết lại logic, không đổi tên biến.
2. **Giữ nguyên form**: `action`, `method`, `enctype`, `name` của mọi input, input ẩn (`registration_token`, `token`, `product_id`, `buy_now`, `voucher_id`, `action=products`...).
3. **Giữ nguyên mọi `id` và class mà JS đang dùng** (danh sách trong CONTRACT.md).
4. **Không thêm route, không thêm cột DB, không gọi API mới.** Không thêm tính năng ngoài danh sách có sẵn (xem mục "Không được thêm" bên dưới).
5. **Giữ Bootstrap 5.3.3 + bootstrap-icons + SweetAlert2** đã nạp trong `main.php`. Không thêm Tailwind CDN, không thêm framework mới.
6. Mọi chữ hiển thị bằng **tiếng Việt**; giá dạng `300.000đ` (giữ cách định dạng đang dùng trong view).
7. Thiết kế Stitch xuất ra HTML/Tailwind: **chuyển** sang class Bootstrap + biến CSS trong `assets/css/user-theme.css`. Dữ liệu mẫu của Stitch (tên sản phẩm, giá, mã `XXXX-30`, ảnh mẫu) phải thay bằng biến PHP thật, không hard-code.
8. Mỗi nút submit của form dùng `AjaxForm.js` chỉ chứa **chữ thuần** (không icon/thẻ con), vì thư viện ghi đè `innerText`.
9. Mỗi lần chỉ làm **một trang**, rồi dừng để người dùng kiểm tra.

## Không được thêm vào giao diện (backend không hỗ trợ)
Wishlist/tim yêu thích, so sánh, biến thể size/màu, giá gốc gạch ngang, % giảm giá trên sản phẩm, sắp xếp theo giá, lọc theo giá, chọn phương thức thanh toán, phí vận chuyển, sổ địa chỉ, người dùng tự hủy đơn, theo dõi vận đơn, nhập mã voucher bằng tay, đăng nhập mạng xã hội, đăng ký nhận tin, live chat ngoài trang Trợ lý AI.

## Trước khi báo "xong" một trang
Chạy `bash tools/check-ui-scope.sh` (phải báo OK), rồi làm checklist ở `docs/ui-redesign/WORKFLOW.md`, mục 4.
