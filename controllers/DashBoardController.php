<?php
class DashBoardController
{

    protected Product $productModel;
    protected Category $categoryModel;
    protected User $userModel;
    protected Order $orderModel;
    protected Comment $commentModel;

    public function __construct()
    {
        $this->userModel = new User();

        if (!($this->userModel->isAdmin())) {
            header('Location: ' . url('/'));
            exit;
        }

        $this->categoryModel = new Category();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->commentModel = new Comment();
    }

    public function index()
    {
        $table = isset($_GET['table']) ? trim($_GET['table']) : 'products';

        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $limit = 3;

        $title = 'ADMIN DASHBOARD / ' . strtoupper($table);


        switch ($table) {
            case 'users':
                $result = $this->userModel->getAll($search, $page, $limit);
                $users = $result['data'];
                $pagination = $result['pagination'];
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-users/users',
                    'users' => $users,
                    'pagination' => $pagination
                ]);
                break;
            
                case 'products':
                $result = $this->productModel->getFiltered($search, null, $page, $limit);
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-san-pham/products',
                    'products' => $result['data'],
                    'pagination' => $result['pagination']
                ]);
                break;

            case 'vouchers':
                $voucherModel = new Voucher();
                $result = $voucherModel->getFiltered($search, $page, 3);
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-voucher/vouchers',
                    'vouchers' => $result['data'],
                    'pagination' => $result['pagination'],
                    'search' => $search,
                ]);
                break;

            case 'orders':
                $result = $this->orderModel->getAll($search, $page, $limit);
                $orderSummary = $this->orderModel->getSummary($search);
                $orderStatusTotals = $this->orderModel->getStatusTotals($search);
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-don-hang/orders',
                    'orders' => $result['data'],
                    'pagination' => $result['pagination'],
                    'summary' => $orderSummary,
                    'statusTotals' => $orderStatusTotals,
                    'search' => $search,
                ]);
                break;

            case 'comments':
                $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : null;
                if (!in_array($statusFilter, ['approved', 'pending'], true)) {
                    $statusFilter = null;
                }
                $result = $this->commentModel->getAll($search, $page, $limit, $statusFilter);
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-comments/comments',
                    'comments' => $result['data'],
                    'pagination' => $result['pagination'],
                    'statusFilter' => $statusFilter,
                    'search' => $search,
                ]);
                break;

            case 'categories':
                renderView('admin/dashboard', [
                    'title' => $title,
                    'view' => 'admin/quan-ly-danh-muc/categories',
                    'categories' => $this->categoryModel->all()
                ]);
                break;

            default:
                echo "kHÔNG CÓ BẢNG LÀO DƯ LÀY!";
                break;
        }
    }
}
