# Quy trình đổi giao diện an toàn (từng bước)

## 1. Chuẩn bị (làm một lần, trước khi Copilot đụng vào gì)
1. **Sao lưu và dùng git**: dự án chưa có `.git`. Chạy ở thư mục gốc:
   `git init`, rồi `git add -A`, rồi `git commit -m "baseline truoc khi doi UI"`, rồi `git switch -c ui-redesign`.
   Đừng commit `account.txt` hay file `.sql` lên kho công khai (xem `PROJECT-REVIEW.md`, mục C).
2. **Chép bộ này vào gốc dự án**: thư mục `.github/`, `docs/ui-redesign/`, `tools/`. Các file này nằm ngoài vùng chạy của PHP nên không ảnh hưởng ứng dụng.
3. **Chụp ảnh "trước"** của từng trang người dùng và vài trang admin (dashboard, sản phẩm, đơn hàng) để so sánh sau này.
4. Trong VS Code, mở chat Copilot ở chế độ **Agent**. Nếu bạn cắm Stitch MCP, chỉ cho nó quyền đọc thiết kế; không gửi `configs/env.php`, `configs/helper.php`, `account.txt`, file `.sql` vào bất kỳ prompt nào.

## 2. Thứ tự làm (mỗi dòng một lượt, một commit)
| # | Việc | File được sửa |
|---|---|---|
| 1 | Nền: biến màu/chữ sáng, header, footer, flash | `views/main.php`, `assets/css/user-theme.css` (mới) |
| 2 | Đăng nhập, đăng ký, quên/đặt lại mật khẩu | `views/auth/*` |
| 3 | Danh sách sản phẩm (có thẻ sản phẩm dùng lại) | `views/web/catalog.php` |
| 4 | Trang chủ | `views/web/home.php` |
| 5 | Chi tiết sản phẩm | `views/web/product-detail.php` |
| 6 | Giỏ hàng, thanh toán | `views/web/cart.php`, `checkout.php` |
| 7 | Đơn hàng, hóa đơn | `views/web/order-history.php`, `order-invoice.php` |
| 8 | Túi voucher, mini game | `views/user/vouchers.php`, `views/web/mini-game.php` |
| 9 | Tài khoản (hồ sơ, ảnh, mật khẩu) | `views/user/*` |
| 10 | Trợ lý AI | `views/web/ai-chat.php` |

Làm bước 1 trước vì mọi trang còn lại thừa hưởng nó. Trang chủ (`home.php`) có hơn 700 dòng CSS nội tuyến theo nền tối: nên đổi sau khi nền đã chuẩn.
Trang AI để cuối vì có hợp đồng HTML do controller sinh (xem CONTRACT.md).

## 3. Vòng lặp cho mỗi trang
1. Chạy prompt có sẵn `.github/prompts/redesign-page.prompt.md` (gõ `/redesign-page` trong chat), nhập tên view và tên màn hình Stitch.
2. Copilot phải liệt kê "danh sách giữ nguyên" **trước khi sửa**. Nếu thiếu form, id hoặc biến so với mục tương ứng trong CONTRACT.md, bắt nó bổ sung.
3. Sau khi sửa, chạy `bash tools/check-ui-scope.sh` (Git Bash). Phải ra `OK`.
4. Xem `git diff` của trang đó: các dòng PHP (`<?php`, `<?=`) chỉ được thay đổi vị trí/markup xung quanh, không đổi nội dung logic.
5. Kiểm tra bằng trình duyệt (mục 4), rồi `git commit -m "ui: <tên trang>"`.

## 4. Checklist kiểm tra bằng trình duyệt cho mỗi trang
- [ ] Trang mở được, không có lỗi PHP (bật hiển thị lỗi của XAMPP/Laragon để thấy), console trình duyệt không có lỗi đỏ.
- [ ] Bản desktop (1440px) và mobile (390px) đều dùng được.
- [ ] Mọi nút/form gửi đúng như trước (thêm giỏ, cập nhật số lượng, xóa, đặt hàng, bình luận, đăng nhập, đổi mật khẩu...).
- [ ] Trạng thái: trống (giỏ trống, chưa có đơn, chưa có voucher), lỗi từng ô nhập, flash success/danger, đang gửi.
- [ ] Sản phẩm hết hàng: huy hiệu "Hết hàng", nút bị vô hiệu. Tồn kho 1 đến 3: "Chỉ còn N".
- [ ] Chưa đăng nhập: header chỉ có nút đăng nhập; vào trang cần đăng nhập vẫn bị chuyển về đăng nhập.
- [ ] Đăng nhập bằng tài khoản admin: link Admin Panel vẫn có; **mở lại các trang admin đã chụp, phải giống hệt trước.**
- [ ] Riêng trang chứa form Ajax (đăng ký, đổi ảnh, đổi mật khẩu, bình luận): gửi dữ liệu sai, lỗi phải hiện đúng dưới từng ô.
- [ ] Riêng mini game và Trợ lý AI: chơi/gửi thử một lượt, kết quả hiện đúng.

## 5. Khi có sự cố
- Một trang hỏng: `git restore views/<file>` (hoặc `git revert` commit của trang đó). Vì mỗi trang một commit nên lùi từng trang được.
- Copilot sửa nhầm file cấm: `git restore <file cấm>`, rồi nhắc nó đọc lại `.github/instructions/protected-zone.instructions.md`.
- Cần đổi một việc nằm ngoài vùng được phép (xem `PROJECT-REVIEW.md`, mục B): làm riêng, có chủ đích, sau khi đổi giao diện xong, không để Copilot làm lẫn trong lượt đổi UI.
