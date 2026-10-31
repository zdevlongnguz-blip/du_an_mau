<?php
class CategoryAdminController
{

    protected Category $categoryModel;
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();

        if (!($this->userModel->isAdmin())) {
            header('Location: ' . url('/'));
            exit;
        }

        $this->categoryModel = new Category();
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

    private function validateCategoryName(string $name): array
    {
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên danh mục không được để trống.';
        } elseif (strlen($name) < 3) {
            $errors['name'] = 'Tên danh mục phải có ít nhất 3 ký tự.';
        }

        return $errors;
    }

    public function categoryCreate()
    {
        renderView('admin/dashboard', [
            'title' => 'Create category',
            'view' => 'admin/quan-ly-danh-muc/create'
        ]);
    }

    public function categoryStore()
    {
        $this->requirePost();

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $errors = $this->validateCategoryName($name);

        if (!empty($errors)) {
            $this->respondJson([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        try {
            $this->categoryModel->save([
                'name' => $name
            ]);
            $this->respondJson([
                'status' => 'success',
                'message' => 'Danh mục đã được tạo thành công!'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    public function categoryEdit()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throwMessageAndBack('Danh mục không tồn tại!', 'error');
        }

        renderView('admin/dashboard', [
            'title' => 'Edit category',
            'view' => 'admin/quan-ly-danh-muc/edit',
            'category' => $category
        ]);
    }

    public function categoryDetail()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throwMessageAndBack('Danh mục không tồn tại!', 'error');
        }

        $productModel = new Product();
        $productCount = (int) $productModel->query('SELECT COUNT(*) AS total FROM products WHERE category_id = :category_id', [':category_id' => $id])[0]['total'];

        renderView('admin/dashboard', [
            'title' => 'Chi tiết danh mục',
            'view' => 'admin/quan-ly-danh-muc/category-detail',
            'category' => $category,
            'productCount' => $productCount,
        ]);
    }

    public function categoryUpdate()
    {
        $this->requirePost();

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';

        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => 'Danh mục không tồn tại.']
            ], 404);
        }

        $errors = $this->validateCategoryName($name);
        if (!empty($errors)) {
            $this->respondJson([
                'status' => 'error',
                'errors' => $errors
            ], 400);
        }

        try {
            $this->categoryModel->update($id, ['name' => $name]);
            $this->respondJson([
                'status' => 'success',
                'message' => 'Danh mục đã được cập nhật thành công!'
            ]);
        } catch (Exception $e) {
            $this->respondJson([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ], 500);
        }
    }

    public function categoryDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo 'Method Not Allowed';
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            throwMessageAndBack('Danh mục không hợp lệ!', 'error');
        }

        $this->categoryModel->delete($id);
        header('Location: ' . url('?action=dashboard&table=categories'));
        exit;
    }
}
