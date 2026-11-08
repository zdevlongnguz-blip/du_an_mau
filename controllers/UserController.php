<?php
class UserController extends BaseController
{
    // Trang profile
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function profile()
    {
        $user = $this->userModel->find($_SESSION['user']['id']);
        if (!$user) {
            throwMessageAndBack('Người dùng không tồn tại!', 'error');
        }
        renderView('main', [
            'title' => 'Profile',
            'view' => 'user/profile',
            'user' => $user
        ]);
    }

    public function vouchers()
    {
        $voucherModel = new Voucher();
        $vouchers = $voucherModel->getByUser((int)$_SESSION['user']['id']);
        renderView('main', [
            'title' => 'Túi voucher',
            'view' => 'user/vouchers',
            'vouchers' => $vouchers,
            'recentAwards' => $voucherModel->getRecentAwards(),
        ]);
    }

    public function avatarChange()
    {
        $user = $this->userModel->find($_SESSION['user']['id']);
        if (!$user) {
            throwMessageAndBack('Người dùng không tồn tại!', 'error');
        }
        renderView('main', [
            'title' => 'Thay đổi ảnh đại diện',
            'view' => 'user/avatar-change',
            'user' => $user
        ]);
    }

    public function avatarUpdate()
    {
        $errors = [];

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode([
                'status' => 'error',
                'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'
            ]);
            exit;
        }

        // echo json_encode([
        //     'status' => 'success',
        //     'message' => 'check ajax form nè ku!'
        // ]);
        // exit();


        $user = $this->userModel->find($_SESSION['user']['id']);
        if (!$user) {
            throwMessageAndBack('Người dùng không tồn tại!', 'error');
        }

        $oldAvatar = $user['avatar'] ?? null;
        $avatar = $oldAvatar;

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errors['avatar'] = 'Vui lòng chọn một tệp ảnh hợp lệ.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit();
        }

        $flag = false;

        try {
            validate_upload($_FILES['avatar']);
            $flag = true;
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['avatar' => $e->getMessage()]
            ]);
            exit();
        }

        if ($flag) {
            try {
                $avatar = save_uploaded_file($_FILES['avatar']);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'errors' => ['avatar' => $e->getMessage()]
                ]);
                exit();
            }
        }

        try {
            $this->userModel->update($user['id'], ['avatar' => $avatar]);
            if ($flag && !empty($oldAvatar)) {
                delete_uploaded_file($oldAvatar);
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Cập nhật ảnh đại diện thành công!'
            ]);
            exit();
        } catch (Exception $e) {
            if ($flag && !empty($avatar)) {
                delete_uploaded_file($avatar);
            }
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ]);
            exit();
        }
    }

    public function in4Change()
    {
        $user = $this->userModel->find($_SESSION['user']['id']);
        if (!$user) {
            throwMessageAndBack('Người dùng không tồn tại!', 'error');
        }
        renderView('main', [
            'title' => 'Thay đổi thông tin',
            'view' => 'user/in4-change',
            'user' => $user
        ]);
    }

    public function in4Update()
    {
        $errors = [];

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode([
                'status' => 'error',
                'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'
            ]);
            exit;
        }

        $user = $this->userModel->find($_SESSION['user']['id']);
        if (!$user) {
            throwMessageAndBack('Người dùng không tồn tại!', 'error');
        }

        $new_password = isset($_POST['new-password']) ? trim($_POST['new-password']) : '';
        $password_confirm = isset($_POST['confirm-password']) ? trim($_POST['confirm-password']) : '';
        $current_password = isset($_POST['password']) ? (string)$_POST['password'] : '';

        //echo json_encode([
        //    'status' => 'success',
        //    'message' => 'check form thay đổi mật khẩu'
        //]);
        //exit();

        //validate

        if ($current_password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu hiện tại.';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors['password'] = 'Mật khẩu hiện tại không đúng.';
        }

        if (empty($new_password)) {
            $errors['new-password'] = 'Mật khẩu mới không được để trống.';
        } elseif (strlen($new_password) < 6) {
            $errors['new-password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        }

        if (empty($password_confirm)) {
            $errors['confirm-password'] = 'Vui lòng xác nhận mật khẩu.';
        } elseif ($new_password !== $password_confirm) {
            $errors['confirm-password'] = 'Mật khẩu xác nhận không khớp.';
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit();
        }

        try {
            $this->userModel->update($user['id'], [
                'password' => password_hash($new_password, PASSWORD_BCRYPT)
            ]);
            echo json_encode([
                'status' => 'success',
                'message' => "Cập nhật mật khẩu thành công"
            ]);
            exit();
        } catch (Exception $e) {
             echo json_encode([
                'status' => 'error',
                'errors' => ['general' => $e->getMessage()]
            ]);
            exit();
        }
    }

    public function adminEdit()
    {
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = $this->userModel->find($id);
        if (!$user) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Người dùng không tồn tại.', 'error');
        }

        renderView('admin/dashboard', [
            'title' => 'Chỉnh sửa người dùng',
            'view' => 'admin/quan-ly-users/edit',
            'user' => $user,
        ]);
    }

    public function adminDetail()
    {
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = $this->userModel->find($id);
        if (!$user) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Người dùng không tồn tại.', 'error');
        }

        renderView('admin/dashboard', [
            'title' => 'Chi tiết người dùng',
            'view' => 'admin/quan-ly-users/user-detail',
            'user' => $user,
        ]);
    }

    public function adminUpdate()
    {
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Yêu cầu không hợp lệ.', 'error');
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
        $isActive = $isActive === 1 ? 1 : 0;

        $user = $this->userModel->find($id);
        if (!$user) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Người dùng không tồn tại.', 'error');
        }

        if ($user['id'] === ($_SESSION['user']['id'] ?? null)) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Bạn không thể thay đổi trạng thái tài khoản của chính mình tại đây.', 'error');
        }

        $this->userModel->update($id, ['is_active' => $isActive]);
        $message = $isActive === 1 ? 'Tài khoản đã được mở khóa.' : 'Tài khoản đã bị khóa.';
        redirectWithFlash(url('?action=dashboard&table=users'), $message);
    }

    public function adminDelete()
    {
        if (!$this->userModel->isAdmin()) {
            header('Location: ' . url('/'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Yêu cầu không hợp lệ.', 'error');
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Người dùng không hợp lệ.', 'error');
        }

        if ($id === ($_SESSION['user']['id'] ?? null)) {
            redirectWithFlash(url('?action=dashboard&table=users'), 'Bạn không thể xóa chính mình.', 'error');
        }

        $this->userModel->delete($id);
        redirectWithFlash(url('?action=dashboard&table=users'), 'Người dùng đã được xóa.');
    }
}
