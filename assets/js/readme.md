========================================================================
⚡ TÀI LIỆU HƯỚNG DẪN SỬ DỤNG CLASS AJAXFORM VÀ XỬ LÝ CONTROLLER (PHP) ⚡
========================================================================

Tài liệu này tổng hợp toàn bộ quy trình cấu hình, khởi tạo và đồng bộ dữ liệu
giữa Frontend (HTML/JS) và Backend (PHP Controller) sử dụng class AjaxForm.

------------------------------------------------------------------------
1. CẤU HÌNH BẢO MẬT & PHẢN HỒI TẠI CONTROLLER (PHP)
------------------------------------------------------------------------
Đặt đoạn mã này ở ngay đầu hàm xử lý trong Controller để chặn các request 
không hợp lệ và định hình dữ liệu trả về dạng JSON.

<?php
// Chặn nếu người dùng truy cập bằng phương thức khác POST (ví dụ nhập URL trực tiếp)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode([
        'status' => 'error', 
        'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'
    ]);
    exit;
}

// Bắt buộc cấu hình Header trả về định dạng JSON mã hóa UTF-8 cho toàn bộ luồng xử lý
header('Content-Type: application/json; charset=utf-8');


------------------------------------------------------------------------
2. CẤU TRÚC JSON PHẢN HỒI BẮT BUỘC TỪ BACKEND (PHP)
------------------------------------------------------------------------
Cuối hàm xử lý, Controller PHẢI sử dụng `echo json_encode(...); exit;` 
theo đúng 1 trong 2 cấu trúc dưới đây để AjaxForm nhận diện:

👉 TRƯỜNG HỢP 1: THẤT BẠI / DÍNH LỖI NHẬP LIỆU (Status = 'error')
Lưu ý: Các "Key" (như name, category_id, image) phải trùng với class của thẻ hiển thị lỗi.
{
    "status": "error",
    "errors": {
        "name": "Tên sản phẩm không được để trống.",
        "category_id": "Vui lòng chọn danh mục hợp lệ.",
        "image": "Kích thước ảnh quá lớn (Tối đa 2MB)."
    }
}

👉 TRƯỜNG HỢP 2: THÀNH CÔNG MỸ MÃN (Status = 'success')
{
    "status": "success",
    "message": "Cập nhật dữ liệu thành công!"
}


------------------------------------------------------------------------
3. CẤU TRÚC RENDER GIAO DIỆN HTML FORM
------------------------------------------------------------------------
* Không cần thuộc tính action="..." hay method="POST" trong thẻ <form>.
* Các ô input bắt buộc phải có thuộc tính `name`.
* Thẻ chứa lỗi bắt buộc đặt tên class theo cấu trúc: error-{name_cua_input}

<form id="editProductForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">

    <div class="form-group">
        <label>Tên sản phẩm:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">
        <span class="error-msg error-name"></span> </div>

    <div class="form-group">
        <label>Danh mục:</label>
        <select name="category_id">
            <option value="1">Danh mục A</option>
        </select>
        <span class="error-msg error-category_id"></span> </div>

    <div class="form-group">
        <input type="file" name="image">
        <span class="error-msg error-image"></span> </div>

    <button type="submit">Cập nhật</button>
</form>


------------------------------------------------------------------------
4. CÚ PHÁP KHỞI TẠO & GHI ĐÈ SỰ KIỆN Ở FRONTEND (JAVASCRIPT)
------------------------------------------------------------------------
Đặt đoạn script này ở cuối file View, nằm phía sau dòng nhúng file AjaxForm.js.

<script src="<?= url('/AjaxForm.js') ?>"></script>

<script>
    // Khởi tạo class (Tham số 1: ID của form, Tham số 2: URL nhận dữ liệu xử lý)
    const formHandler = new AjaxForm('#editProductForm', '<?= url('/duong-dan-xu-ly') ?>');

    // Ghi đè (Override) hàm onSuccess của class cha để xử lý giao diện theo ý muốn
    formHandler.onSuccess = function(response) {
        // 1. Hiển thị thông báo thành công từ Server gửi về
        alert(response.message); 
        
        // 2. Chuyển hướng người dùng đi trang khác sau khi thành công
        window.location.href = "<?= url('/danh-sach') ?>"; 
        
        // Mẹo: Nếu muốn giữ nguyên trang và xóa sạch dữ liệu vừa nhập trên form:
        // this.form.reset(); 
    };
</script>

//Ở controller lấy các dữ liệu từ form bằng cách sử dụng $_POST['name'], $_POST['category_id'], v.v...
//Nếu có file ảnh được gửi lên, bạn có thể truy cập thông tin file qua $_FILES['image'] và xử lý lưu trữ theo nhu cầu của bạn.

Perfect! Bạn đã có một hướng dẫn chi tiết và dễ hiểu về cách sử dụng class AjaxForm để đồng bộ dữ liệu giữa Frontend và Backend. Hãy đảm bảo rằng bạn tuân thủ đúng cấu trúc JSON phản hồi và cách khởi tạo class để tránh các lỗi không mong muốn. Chúc bạn thành công trong việc triển khai tính năng này!

========================================================================