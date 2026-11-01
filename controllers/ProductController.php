<?php
class ProductController
{

    private Product $productModel;
    private Category $categoryModel;
    private Comment $commentModel;
    private AIService $aiService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->commentModel = new Comment();
        $this->aiService = new AIService();
    }

    public function index()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $requestedCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $categories = $this->categoryModel->all();
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[(int)($category['id'] ?? 0)] = true;
        }

        $categoryId = $requestedCategoryId > 0 && isset($categoryIds[$requestedCategoryId]) ? $requestedCategoryId : 0;

        $topProducts = $this->productModel->getTopByViews(5);
        $filtered = $this->productModel->getFiltered($search, $categoryId > 0 ? $categoryId : null, $page, 8);
        $categoryCounts = $this->productModel->getCategoryCounts();
        $countMap = [];

        foreach ($categoryCounts as $categoryCount) {
            $countMap[(int)($categoryCount['id'] ?? 0)] = (int)($categoryCount['product_count'] ?? 0);
        }

        foreach ($categories as &$category) {
            $category['product_count'] = $countMap[(int)($category['id'] ?? 0)] ?? 0;
        }
        unset($category);

        renderView('main', [
            'title' => 'Husler420 - Trang chủ',
            'view' => 'web/home',
            'topProducts' => $topProducts,
            'products' => $filtered['data'],
            'pagination' => $filtered['pagination'],
            'categories' => $categories,
            'filterSearch' => $search,
            'filterCategory' => $categoryId,
            'cartItemCount' => isset($_SESSION['user']['id']) ? (new Cart())->getItemCount($_SESSION['user']['id']) : 0,
        ]);
    }

    public function catalog()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $requestedCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $categories = $this->categoryModel->all();
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[(int)($category['id'] ?? 0)] = true;
        }

        $categoryId = $requestedCategoryId > 0 && isset($categoryIds[$requestedCategoryId]) ? $requestedCategoryId : 0;
        $filtered = $this->productModel->getFiltered($search, $categoryId > 0 ? $categoryId : null, $page, 8);

        $categoryCounts = $this->productModel->getCategoryCounts();
        $countMap = [];
        foreach ($categoryCounts as $categoryCount) {
            $countMap[(int)($categoryCount['id'] ?? 0)] = (int)($categoryCount['product_count'] ?? 0);
        }

        foreach ($categories as &$category) {
            $category['product_count'] = $countMap[(int)($category['id'] ?? 0)] ?? 0;
        }
        unset($category);

        renderView('main', [
            'title' => 'Danh mục sản phẩm',
            'view' => 'web/catalog',
            'products' => $filtered['data'],
            'pagination' => $filtered['pagination'],
            'categories' => $categories,
            'filterSearch' => $search,
            'filterCategory' => $categoryId,
            'cartItemCount' => isset($_SESSION['user']['id']) ? (new Cart())->getItemCount($_SESSION['user']['id']) : 0,
        ]);
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->productModel->detail($id);
        if (!$product) {
            throwMessageAndBack('Sản phẩm không tồn tại!', 'error');
        }

        $comments = $this->commentModel->getByProductId($id);
        $summary = $this->commentModel->getSummary($id);
        $relatedProducts = !empty($product['category_id'])
            ? $this->productModel->relatedByCategory((int)$product['category_id'], $id, 4)
            : [];

        // Tăng số lượt xem sản phẩm
        $this->productModel->incrementViewCount($id);

        renderView('main', [
            'title' => 'Chi tiết sản phẩm',
            'view' => 'web/product-detail',
            'product' => $product,
            'comments' => $comments,
            'reviewSummary' => $summary,
            'relatedProducts' => $relatedProducts,
            'cartItemCount' => isset($_SESSION['user']['id']) ? (new Cart())->getItemCount($_SESSION['user']['id']) : 0,
        ]);
    }

}

    