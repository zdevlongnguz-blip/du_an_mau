<?php
// Giả sử $categories đã được truyền từ Controller qua View
$categories = $categories ?? [];
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="cyber-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <div class="cyber-heading mb-2">Sản phẩm</div>
                    <h3 class="mb-0" style="color: #f8fafc;">Thêm sản phẩm mới</h3>
                </div>
                <a id="btn-back" href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-secondary">Quay lại</a>
            </div>

            <form id="product-create" enctype="multipart/form-data" class="row g-4">
                <input type="hidden" name="id" value="">

                <div class="col-12 col-lg-6">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm...">
                    <span class="error-msg error-name text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Mã sản phẩm</label>
                    <input type="text" name="code" class="form-control" placeholder="Nhập mã sản phẩm...">
                    <span class="error-msg error-code text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-msg error-category_id text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <span class="error-msg error-image text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Price ($)</label>
                    <input type="text" name="price" class="form-control" placeholder="Nhập giá sản phẩm (nhỏ hơn 500000)...">
                    <span class="error-msg error-price text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Stock</label>
                    <input type="text" name="stock" class="form-control" placeholder="Nhập số lượng...">
                    <span class="error-msg error-stock text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Nhập mô tả sản phẩm...">
                    <span class="error-msg error-description text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-2 gap-3">
                        <label class="form-label mb-0">Content</label>
                        <button type="button" id="btn-generate-ai" class="btn cyber-btn btn-sm fw-bold">
                            <i class="fas fa-robot me-1"></i> ✨ Tạo content bằng AI
                        </button>
                    </div>
                    <textarea name="content" id="product-content" class="form-control" rows="8" placeholder="Nhập nội dung sản phẩm hoặc bấm nút phía trên để AI tự động tạo dựa trên hình ảnh..."></textarea>
                    <span class="error-msg error-content text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12">
                    <span class="error-msg error-general text-danger small d-block mt-1"></span>
                </div>

                <div class="col-12 d-flex gap-3 flex-column flex-sm-row">
                    <button id="btn-submit" type="submit" class="btn cyber-btn flex-grow-1">SAVE</button>
                    <a href="<?= url('?action=dashboard&table=products') ?>" class="btn btn-secondary flex-grow-1 d-sm-none">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_ASSETS_JS . 'AjaxForm.js' ?>"></script>

<script>
    // 1. Khởi tạo bộ xử lý gửi dữ liệu Form lưu chính thức qua class AjaxForm của bạn
    const formHandler = new AjaxForm('#product-create', '<?= url('?action=product-store') ?>');

    formHandler.onSuccess = function(response) {
        Swal.fire({
            title: 'Thông báo',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6',
            background: '#fff',
            backdrop: 'rgba(15, 23, 42, 0.5)',
            customClass: { popup: 'rounded-popup' }
        }).then(() => {
            window.location.href = "<?= url('?action=dashboard&table=products') ?>";
        });
    };

    const btnGenerateAi = document.getElementById('btn-generate-ai');
    const submitButton = document.getElementById('btn-submit');
    const backButton = document.getElementById('btn-back');

    const preventBackClick = (event) => {
        event.preventDefault();
    };

    const setAiInProgress = (isLoading) => {
        btnGenerateAi.disabled = isLoading;
        submitButton.disabled = isLoading;

        if (isLoading) {
            backButton.classList.add('disabled');
            backButton.setAttribute('aria-disabled', 'true');
            backButton.addEventListener('click', preventBackClick);
        } else {
            backButton.classList.remove('disabled');
            backButton.setAttribute('aria-disabled', 'false');
            backButton.removeEventListener('click', preventBackClick);
        }
    };

    // 2. Xử lý AJAX độc lập: Gọi Gemini AI phân tích hình ảnh và trả dữ liệu xem trước
    btnGenerateAi.addEventListener('click', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const imageInput = document.querySelector('input[name="image"]');
        const contentTextarea = document.getElementById('product-content');

        if (!imageInput.files || imageInput.files.length === 0) {
            Swal.fire({
                title: 'Thông báo',
                text: 'Vui lòng chọn hình ảnh sản phẩm trước để AI có thể đọc và phân tích.',
                icon: 'warning',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const formData = new FormData();
        formData.append('name', nameInput.value.trim());
        formData.append('image', imageInput.files[0]);

        setAiInProgress(true);

        const originalBtnText = this.innerHTML;
        this.style.color = '#fff';
        this.style.backgroundColor = '#a855f7';
        this.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> AI đang đọc ảnh...`;
        contentTextarea.placeholder = "Gemini AI đang phân tích hình ảnh và viết bài viết chi tiết, vui lòng chờ trong giây lát (khoảng 3-6 giây)...";

        fetch('<?= url('?action=product-generate-content') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Mạng hoặc hệ thống server gặp sự cố.');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                contentTextarea.value = data.content;
                Swal.fire({
                    title: 'Thành công',
                    text: 'AI đã phân tích ảnh và hoàn thành mô tả sản phẩm chi tiết!',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                });
            } else {
                Swal.fire('Thất bại', data.message || 'Không thể sinh nội dung từ AI.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Lỗi', 'Đã xảy ra lỗi kết nối với hệ thống AI hoặc tệp quá lớn.', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.style.color = '#a855f7';
            this.style.backgroundColor = 'transparent';
            this.innerHTML = originalBtnText;
            contentTextarea.placeholder = "Nhập nội dung sản phẩm hoặc bấm nút phía trên để AI tự động tạo dựa trên hình ảnh...";
            setAiInProgress(false);
        });
    });
</script>

<style>
    .btn-outline-purple:hover {
        background-color: #a855f7 !important;
        color: #fff !important;
    }
    .transition {
        transition: all 0.2s ease-in-out;
    }
    .rounded-popup {
        border-radius: 12px !important;
    }
</style>