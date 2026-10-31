---
description: Đổi giao diện MỘT trang người dùng theo bản thiết kế Stitch, an toàn
---
Trang cần đổi: ${input:page:ví dụ views/web/cart.php}
Màn hình Stitch tương ứng: ${input:stitch:tên màn hình hoặc đường dẫn bản xuất}

Làm theo thứ tự, không bỏ bước:
1. Đọc `.github/copilot-instructions.md` và mục của trang này trong `docs/ui-redesign/CONTRACT.md`.
2. Mở file view hiện tại, liệt kê (chỉ trong câu trả lời, chưa sửa gì): biến PHP đang dùng, form (action/method/name), id/class mà JS dùng. Đây là danh sách phải giữ nguyên.
3. Lấy thiết kế Stitch của trang này. Chuyển sang Bootstrap 5.3 + biến CSS trong `assets/css/user-theme.css`. Không dùng Tailwind. Không hard-code dữ liệu mẫu.
4. Chỉ sửa file trong vùng được phép. Giữ mọi khối PHP, chỉ đổi markup/class/CSS.
5. Chạy `bash tools/check-ui-scope.sh` và báo kết quả.
6. Trả lời: (a) file đã đổi, (b) phần hợp đồng nào giữ nguyên, (c) việc người dùng cần tự kiểm tra bằng trình duyệt. Rồi dừng, không tự sang trang khác.
