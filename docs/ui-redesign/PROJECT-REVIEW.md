# Rà soát dự án trước khi đổi giao diện

Phạm vi rà: toàn bộ `views/`, `routes/`, `controllers/`, `configs/helper.php`, `configs/env.php`, `assets/js/AjaxForm.js`. Mình chỉ **đọc**, không sửa gì. Chưa chạy ứng dụng, chưa kiểm tra cơ sở dữ liệu thật, nên một số điểm ghi rõ là "cần kiểm".

## A. Chưa đồng bộ trong view (Copilot được sửa khi đổi UI)
| # | Chỗ | Hiện tại | Cần theo thiết kế |
|---|---|---|---|
| A1 | Theme toàn site | Tối: `data-bs-theme="dark"`, biến `--bg:#0b0b0b`, `.card {…!important}`, `.text-muted`/`small` ép màu sáng (`main.php`); CSS nội tuyến nền tối ở `home.php` (~770 dòng), `catalog.php` (~390 dòng), `ai-chat.php`, `mini-game.php` | Sáng, tương phản cao. Phải gỡ các `!important` nền tối ở `main.php` trước, nếu không mọi thẻ sẽ vẫn đen |
| A2 | Tên thương hiệu | Header `Hustler.420` (có dấu chấm), footer `Shop Demo`, trang AI `Hustler420 Chat`/`Hustler420 AI` | Một cách viết duy nhất (chờ bạn chốt, kit đang tạm `HUSTLER420`) |
| A3 | Chữ tiếng Anh | Header: `Login`, `Logout`, `Profile`, `Admin Panel`; footer `All rights reserved` | Toàn bộ tiếng Việt |
| A4 | Footer | Chữ chung chung ("giao hàng nhanh trên toàn quốc", hệ thống không có phí ship), biểu tượng mạng xã hội là `<span>` không có link, thiếu dòng 18+ và cảnh báo sức khỏe | 4 cột theo kit + thanh 18+ (nội dung cam kết giao/đổi trả bạn cần chốt) |
| A5 | Font | Oswald (tiêu đề) + Inter | Roboto hoặc Inter, theo kit |
| A6 | Menu | "Nhận voucher" chỉ hiện khi đã đăng nhập (đúng với backend) | Master prompt của kit viết như hiện cho mọi người: **theo code hiện tại**, giữ điều kiện đăng nhập |
| A7 | Nút chính | Nền cam, bo tròn (`.btn-mono`) | Nền đen, chữ trắng in hoa, bo ~10px; cam chỉ dùng cho voucher/mini game/huy hiệu Hot |
| A8 | Mini game | Chữ "Trả lời đúng các câu PHP" và "AI sẽ tạo 5 câu hỏi PHP" (view) | Đổi câu chữ cho hợp shop. **Nội dung câu hỏi do AI sinh ở backend** (xem B3) |
| A9 | File thừa | `views/web/home-products.php` (không được include ở đâu), `views/web/home-vue.php` (0 byte), `controllers/HomeController.php` (route `/` đi qua `ProductController::index`) | Để nguyên, không sửa/xóa trong đợt này |

## B. Chỗ nằm ngoài view, cần bạn quyết định (Copilot **không** tự sửa)
| # | Chỗ | Vấn đề | Đề xuất |
|---|---|---|---|
| B1 | `controllers/ProductController.php` dòng 47 | Tiêu đề tab trang chủ là `Husler420 - Trang chủ` (sai chính tả, thiếu "t") | Sửa một chuỗi sau khi chốt tên thương hiệu; làm riêng, ngoài lượt đổi UI |
| B2 | `AIChatController::renderChatHistoryHtml`, `sanitizeAiHtml` | Bong bóng chat do controller sinh HTML; thẻ sản phẩm do AI trả về bị bỏ hết `class` | Không sửa. View phải style theo cấu trúc (đã ghi trong CONTRACT.md). Muốn thẻ sản phẩm đẹp như thiết kế (ảnh 80px, nút "Xem chi tiết →") thì CSS phải bám selector `.ai-chat-message …` |
| B3 | `GameController`/`services/AIService.php` | Câu hỏi mini game là câu hỏi PHP do AI tạo, không liên quan shop điếu cày | Sửa prompt ở backend nếu muốn đổi chủ đề; ngoài phạm vi đổi UI |
| B4 | `views/web/product-detail.php` | Sửa/Xóa bình luận dựa trên so sánh nghiêm ngặt `$_SESSION['user']['id'] === $comment['user_id']`. Nếu một bên là chuỗi, bên kia là số thì nút sẽ không hiện | Cần kiểm trên máy bạn (không đổi trong đợt UI; là hành vi hiện có) |

## C. Rủi ro bảo mật bạn nên biết (không liên quan giao diện, **không để Copilot sửa trong đợt này**)
1. **Hai route nguy hiểm không cần đăng nhập** trong `routes/index.php`: `?action=clear` (xóa sạch bảng sản phẩm và ảnh tải lên) và `?action=seed` (tạo 10 tài khoản mật khẩu `123456`). Bất kỳ ai biết URL đều gọi được. Nên xóa trước khi đưa lên mạng.
2. **Khóa Gemini API nằm cứng trong `configs/env.php`**, và **thông tin gửi mail (SMTP) nằm cứng trong `configs/helper.php`**. Hai file này có trong file zip bạn đã gửi, nên hãy coi như lộ: **đổi (rotate) khóa Gemini và mật khẩu ứng dụng của mail**, rồi chuyển sang biến môi trường/file cấu hình không commit.
3. `account.txt` (7 dòng, có email) và `du_an_mau (2).sql` nằm ngay thư mục gốc dự án. Đừng commit lên kho công khai và đừng gửi cho công cụ bên ngoài.
4. Trợ lý AI (`?action=ai-chat-send`) công khai và gọi Gemini mỗi lượt gửi: ai cũng có thể làm tốn hạn mức khóa của bạn. Cân nhắc giới hạn tần suất sau này.
5. Tài khoản cơ sở dữ liệu `root` không mật khẩu chỉ nên dùng khi chạy local.

Mình không audit sâu phần bảo mật (chỉ liếc qua khi đọc để lập hợp đồng). Các controller admin có kiểm tra vai trò `admin` theo quét nhanh, chưa đọc từng đường đi.

## D. Điều kiện để đổi UI an toàn (đã có sẵn, nên tận dụng)
- Layout user (`views/main.php`) và admin (`views/admin/dashboard.php`) **tách riêng hoàn toàn**: mọi `renderView('main', …)` là trang người dùng, mọi `renderView('admin/dashboard', …)` là trang admin. CSS mới chỉ nạp ở `main.php` thì admin không bị ảnh hưởng.
- Controller đã truyền dữ liệu qua mảng rõ ràng, view chỉ cần đọc biến: có hợp đồng ổn định (CONTRACT.md).
- Điểm dễ vỡ nhất khi đổi giao diện: (1) CSS nền tối dùng `!important`; (2) `AjaxForm.js` cần `.error-<field>` trong form và nút submit chỉ chứa chữ; (3) HTML bong bóng AI do controller sinh; (4) JS mini game dựng form bằng id cố định; (5) tổng tiền checkout tính bằng `data-discount`/`data-original-total`.
