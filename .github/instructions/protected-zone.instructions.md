---
applyTo: "controllers/**,models/**,services/**,routes/**,configs/**,views/admin/**,index.php,**/*.sql,composer.json,composer.lock,assets/js/AjaxForm.js,account.txt"
---
# VÙNG CẤM SỬA trong đợt đổi giao diện

Các file khớp mẫu này **không được chỉnh sửa, xóa, đổi tên hay "format lại"** trong đợt thiết kế lại giao diện người dùng.

- Chỉ được **đọc** để hiểu hợp đồng dữ liệu.
- Nếu nhiệm vụ có vẻ cần sửa ở đây (ví dụ cần thêm biến cho view, sửa tiêu đề trang, sửa câu chữ trong controller): **dừng, nêu rõ lý do và đề xuất, rồi chờ người dùng đồng ý**.
- Không tạo file mới trong các thư mục này.
- Không đưa nội dung `configs/env.php`, `configs/helper.php`, `account.txt` hay file `.sql` vào prompt gửi cho công cụ bên ngoài (Stitch MCP, v.v.): chúng chứa thông tin nhạy cảm.
