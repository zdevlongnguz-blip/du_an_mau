<?php
class ProductAdminController
{
    protected Product $productModel;
    protected Category $categoryModel;
    protected User $userModel;
    protected AIService $aiService;

    public function __construct()
    {
        $this->userModel = new User();

        if (!($this->userModel->isAdmin())) {
            header('Location: ' . url('/'));
            exit;
        }

        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->aiService = new AIService();
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson([
                'status' => 'error',
                'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'
            ], 405);
        }
    }

    private function deleteProductImage(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        delete_uploaded_file($image);
    }

    private function validateProductRequest(array $input, array $files, bool $isUpdate = false): array
    {
        $errors = [];

        $name = isset($input['name']) ? trim($input['name']) : '';
        $code = isset($input['code']) ? trim($input['code']) : '';
        $category_id = isset($input['category_id']) ? trim($input['category_id']) : '';
        $price = isset($input['price']) ? trim($input['price']) : '';
        $stock = isset($input['stock']) ? trim($input['stock']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';

        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống.';
        } elseif (strlen($name) < 3) {
            $errors['name'] = 'Tên sản phẩm phải có ít nhất 3 ký tự.';
        }

        if (empty($code)) {
            $errors['code'] = 'Mã sản phẩm không được để trống.';
        } elseif (strlen($code) < 2) {
            $errors['code'] = 'Mã sản phẩm phải có ít nhất 2 ký tự.';
        } elseif (!preg_match('/^[A-Za-z0-9_\-]+$/', $code)) {
            $errors['code'] = 'Mã sản phẩm chỉ được chứa chữ, số, gạch ngang và gạch dưới.';
        }

        if (empty($category_id)) {
            $errors['category_id'] = 'Danh mục sản phẩm không được để trống.';
        } elseif (!is_numeric($category_id)) {
            $errors['category_id'] = 'Danh mục sản phẩm không hợp lệ.';
        } elseif (!$this->categoryModel->find($category_id)) {
            $errors['category_id'] = 'Danh mục sản phẩm không tồn tại.';
        }

        if ($price === '') {
            $errors['price'] = 'Giá không được để trống.';
        } elseif (!is_numeric($price) || (float)$price <= 0) {
            $errors['price'] = 'Giá phải là số và không âm.';
        } elseif ((float)$price >= 500000) {
            $errors['price'] = 'Giá sản phẩm phải nhỏ hơn 500000.';
        }

        if ($stock === '') {
            $errors['stock'] = 'Số lượng không được để trống.';
        } elseif (!is_numeric($stock) || (int)$stock < 0) {
            $errors['stock'] = 'Số lượng phải là số nguyên và không âm.';
        }

        if (empty($description)) {
            $errors['description'] = 'Mô tả ngắn của sản phẩm không được để trống.';
        }

        if (!$isUpdate || (isset($files['image']) && $files['image']['error'] !== UPLOAD_ERR_NO_FILE)) {
            if (!isset($files['image']) || $files['image']['error'] === UPLOAD_ERR_NO_FILE) {
                if (!$isUpdate) {
                    $errors['image'] = 'Vui lòng chọn một tệp hình ảnh để tải lên.';
                }
            } else {
                try {
                    validate_upload($files['image']);
                } catch (Exception $e) {
                    $errors['image'] = $e->getMessage();
                }
            }
        }

        return $errors;
    }

    public function create()
    {
        renderView('admin/dashboard', [
            'view' => 'admin/quan-ly-san-pham/create',
            'title' => 'Thêm sản phẩm mới',
            'categories' => $this->categoryModel->all(),
        ]);
    }

    public function store()
    {
        $this->requirePost();

        $errors = $this->validateProductRequest($_POST, $_FILES);
        if (!empty($errors)) {
            $this->respondJson([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $category_id = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';
        $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $image = null;

        try {
            $image = save_uploaded_file($_FILES['image']);
        } catch (Exception $e) {
            $this->respondJson([
                'status' => 'error',
                'errors' => ['image' => $e->getMessage()]
            ], 500);
        }

        try {
            $this->productModel->save([
                'name' => $name,
                'code' => $code,
                'category_id' => (int)$category_id,
                'price' => (float)$price,
                'stock' => (int)$stock,
                'description' => $description,
                'content' => $content,
                'image' => $image
            ]);
            $this->respondJson([
                'status' => 'success',
                'message' => 'Sản phẩm đã được tạo thành công!'
            ]);
        } catch (Exception $e) {
            if (!empty($image) && file_exists(PATH_ASSETS_UPLOADS . $image)) {
                delete_uploaded_file($image);
            }
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $product = $this->productModel->find($id);
        if (!$product) {
            throwMessageAndBack('Sản phẩm không tồn tại!', 'error');
        }

        renderView('admin/dashboard', [
            'view' => 'admin/quan-ly-san-pham/edit',
            'title' => 'Chỉnh sửa sản phẩm',
            'product' => $product,
            'categories' => $this->categoryModel->all(),
        ]);
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $product = $this->productModel->detail($id);
        if (!$product) {
            throwMessageAndBack('Sản phẩm không tồn tại!', 'error');
        }

        renderView('admin/dashboard', [
            'view' => 'admin/quan-ly-san-pham/product-detail',
            'title' => 'Chi tiết sản phẩm',
            'product' => $product,
        ]);
    }

    public function update()
    {
        $this->requirePost();

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $product = $this->productModel->find($id);
        if (!$product) {
            $this->respondJson([
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại.'
            ], 404);
        }

        $errors = $this->validateProductRequest($_POST, $_FILES, true);
        if (!empty($errors)) {
            $this->respondJson([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $category_id = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';
        $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $image = $product['image'];
        $uploadedImage = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadedImage = save_uploaded_file($_FILES['image']);
                $image = $uploadedImage;
            } catch (Exception $e) {
                $this->respondJson([
                    'status' => 'error',
                    'errors' => ['image' => $e->getMessage()]
                ], 500);
            }
        }

        try {
            $this->productModel->update($id, [
                'name' => $name,
                'code' => $code,
                'category_id' => (int)$category_id,
                'price' => (float)$price,
                'stock' => (int)$stock,
                'description' => $description,
                'content' => $content,
                'image' => $image,
            ]);

            if ($uploadedImage && !empty($product['image'])) {
                $this->deleteProductImage($product['image']);
            }

            $this->respondJson([
                'status' => 'success',
                'message' => 'Sản phẩm đã được cập nhật thành công!'
            ]);
        } catch (Exception $e) {
            if ($uploadedImage) {
                $this->deleteProductImage($uploadedImage);
            }
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    public function productDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo 'Method Not Allowed';
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $product = $this->productModel->find($id);
        if (!$product) {
            throwMessageAndBack('Sản phẩm không tồn tại!', 'error');
        }

        $this->productModel->delete($id);
        $this->deleteProductImage($product['image'] ?? null);

        header('Location: ' . url('?action=dashboard&table=products'));
        exit;
    }

    public function generateContent()
    {
        $this->requirePost();

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';

        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->respondJson([
                'status' => 'error',
                'message' => 'Vui lòng chọn hình ảnh để AI có thể đọc.'
            ], 400);
        }

        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileMimeType = mime_content_type($fileTmpPath);
        if (!in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
            $this->respondJson([
                'status' => 'error',
                'message' => 'Định dạng ảnh không hợp lệ (Chỉ nhận JPG, PNG, WEBP).'
            ], 400);
        }

        try {
            $aiGeneratedContent = $this->aiService->generateContentFromImage($fileTmpPath, $fileMimeType, $name);
            if (!empty($aiGeneratedContent)) {
                $this->respondJson([
                    'status' => 'success',
                    'content' => $aiGeneratedContent
                ]);
            }
            $this->respondJson([
                'status' => 'error',
                'message' => 'Gemini không thể nhận diện hoặc trả về nội dung trống.'
            ], 500);
        } catch (Exception $e) {
            $this->respondJson([
                'status' => 'error',
                'message' => 'Lỗi kết nối AI: ' . $e->getMessage()
            ], 500);
        }
    }
}
