<?php
class AuthController
{

    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        renderView('main', [
            'title' => 'Login',
            'view' => 'auth/login',
        ]);
    }

    public function auth()
    {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $user = $this->userModel->findByEmail($email);

        // 1. Kiểm tra sự tồn tại của User và Xác thực Mật khẩu đã mã hóa (password_verify)
        if ($user && password_verify($password, $user['password'])) {

            // 2. Kiểm tra xem Tài khoản đã kích hoạt Email chưa (is_active = 1)
            if ((int)($user['is_active'] ?? $user['status'] ?? 0) !== 1) {
                renderView('main', [
                    'title'   => 'Login',
                    'view'    => 'auth/login',
                    'message' => 'Tài khoản của bạn chưa được kích hoạt qua Gmail. Vui lòng kiểm tra hộp thư!'
                ]);
                return;
            }

            // 3. Đăng nhập thành công -> Lưu Session
            unset($_SESSION['ai_chat_history'], $_SESSION['ai_chat_error'], $_SESSION['ai_chat_success']);
            session_regenerate_id(true);
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'fullname' => $user['fullname'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role']
            ];

            header("Location: " . url('/'));
            exit;
        }

        // 4. Đăng nhập thất bại (Sai email hoặc password)
        renderView('main', [
            'title'   => 'Login',
            'view'    => 'auth/login',
            'message' => 'Email hoặc mật khẩu không chính xác!'
        ]);
    }

    public function logout()
    {
        unset($_SESSION['ai_chat_history'], $_SESSION['ai_chat_error'], $_SESSION['ai_chat_success']);
        session_destroy();
        header("Location: " . url('/'));
        exit;
    }

    public function register()
    {
        $_SESSION['register_token'] = bin2hex(random_bytes(32));
        unset($_SESSION['register_in_progress']);

        renderView('main', [
            'title' => 'Register',
            'view' => 'auth/register',
            'register_token' => $_SESSION['register_token'],
        ]);
    }

    private function beginRegistrationLock(): bool
    {
        if (!empty($_SESSION['register_in_progress'])) {
            return false;
        }

        $_SESSION['register_in_progress'] = true;
        return true;
    }

    private function releaseRegistrationLock(): void
    {
        unset($_SESSION['register_in_progress']);
    }

    // Lưu người dùng (Đăng ký tài khoản qua AJAX)
    public function store()
    {
        $errors = [];
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode([
                'status' => 'error',
                'message' => 'Phương thức không được hỗ trợ (Yêu cầu POST).'
            ]);
            exit;
        }

        $full_name = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $password_confirm = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
        $registration_token = isset($_POST['registration_token']) ? trim($_POST['registration_token']) : '';

        if (empty($registration_token) || !hash_equals($_SESSION['register_token'] ?? '', $registration_token)) {
            $this->releaseRegistrationLock();
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => 'Yêu cầu đăng ký đang được xử lý hoặc đã được gửi rồi. Vui lòng đợi trong giây lát.']
            ]);
            exit();
        }

        if (!$this->beginRegistrationLock()) {
            echo json_encode([
                'status' => 'error',
                'errors' => ['general' => 'Đăng ký đang được xử lý. Vui lòng đợi trong giây lát.']
            ]);
            exit();
        }

        // Validate Họ tên
        if (empty($full_name)) {
            $errors['fullname'] = 'Họ và tên không được để trống.';
        } elseif (strlen($full_name) < 3) {
            $errors['fullname'] = 'Họ và tên phải có ít nhất 3 ký tự.';
        }

        // Validate Email
        if (empty($email)) {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Email đã tồn tại.';
        }

        // Validate Username
        if (empty($username)) {
            $errors['username'] = 'Tên đăng nhập không được để trống.';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Tên đăng nhập phải có ít nhất 3 ký tự.';
        } elseif ($this->userModel->findByName($username)) {
            $errors['username'] = 'Tên đăng nhập đã tồn tại.';
        }

        // Validate Mật khẩu
        if (empty($password)) {
            $errors['password'] = 'Mật khẩu không được để trống.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        // Validate Xác nhận Mật khẩu
        if (empty($password_confirm)) {
            $errors['confirm_password'] = 'Vui lòng xác nhận mật khẩu.';
        } elseif ($password !== $password_confirm) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp.';
        }

        // Nếu có lỗi validate thì trả về JSON lỗi
        if (!empty($errors)) {
            $this->releaseRegistrationLock();
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit();
        }

        unset($_SESSION['register_token']);

        try {
            // 1. Tạo Token ngẫu nhiên 64 ký tự để xác thực email
            $token = bin2hex(random_bytes(32));

            // 2. Lưu thông tin vào DB (Lưu ý: is_active = 0, lưu thêm token)
            // Nếu hàm register() bên User Model đã dùng password_hash thì truyền thẳng $password
            $this->userModel->register(
                $full_name,
                $username,
                $email,
                $password,
                $token
            );

            // 3. Gọi hàm gửi email xác thực từ Helper
            $isSent = sendVerificationEmail($email, $token);

            if ($isSent) {
                $this->releaseRegistrationLock();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Đăng ký thành công! Vui lòng kiểm tra hộp thư Gmail để kích hoạt tài khoản.'
                ]);
            } else {
                // Log detailed context to server error log for investigation
                error_log("[AuthController] sendVerificationEmail failed for: {$email}");

                $response = [
                    'status' => 'error',
                    'errors' => ['general' => 'Đăng ký thành công nhưng không thể gửi mail xác nhận. Vui lòng liên hệ quản trị viên!']
                ];

                // In non-production, include a short debug hint
                if (getenv('APP_ENV') !== 'production') {
                    $response['debug'] = 'Mail send failed. Check MAIL_* env vars, Composer autoload, and server error log.';
                }

                $this->releaseRegistrationLock();
                echo json_encode($response);
            }
            exit();
        } catch (Exception $e) {
            $this->releaseRegistrationLock();
            $errors['general'] = $e->getMessage();
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            exit();
        }
    }

    public function verify()
    {
        $token = $_GET['token'] ?? '';

        if (!empty($token) && $this->userModel->activateAccount($token)) {
            // Chuyển hướng sang trang login kèm thông báo thành công
            redirectWithFlash(url('?action=login'), 'Kích hoạt tài khoản thành công! Bạn có thể đăng nhập ngay.');
        } else {
            // Nếu token sai hoặc đã được kích hoạt trước đó
            redirectWithFlash(url('?action=login'), 'Mã xác thực không hợp lệ hoặc tài khoản đã kích hoạt.');
        }
    }

    public function forgotPassword()
    {
        renderView('main', [
            'title' => 'Quên mật khẩu',
            'view' => 'auth/forgot-password',
        ]);
    }

    public function forgotPasswordSubmit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=forgot-password'), 'Yêu cầu không hợp lệ.');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            renderView('main', [
                'title' => 'Quên mật khẩu',
                'view' => 'auth/forgot-password',
                'message' => 'Vui lòng nhập email hợp lệ.'
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            renderView('main', [
                'title' => 'Quên mật khẩu',
                'view' => 'auth/forgot-password',
                'message' => 'Không tìm thấy tài khoản với email này.'
            ]);
            return;
        }

        if ((int)($user['is_active'] ?? 0) !== 1) {
            renderView('main', [
                'title' => 'Quên mật khẩu',
                'view' => 'auth/forgot-password',
                'message' => 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email kích hoạt.'
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $this->userModel->setPasswordResetToken($user['id'], $token);

        $sent = sendResetPasswordEmail($email, $token);
        if ($sent) {
            redirectWithFlash(url('?action=login'), 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn. Vui lòng kiểm tra hộp thư.');
            return;
        }

        renderView('main', [
            'title' => 'Quên mật khẩu',
            'view' => 'auth/forgot-password',
            'message' => 'Không thể gửi email. Vui lòng thử lại hoặc liên hệ quản trị viên.'
        ]);
    }

    public function resetPasswordForm()
    {
        $token = trim($_GET['token'] ?? '');
        $user = $this->userModel->findByPasswordResetToken($token);

        if (empty($token) || !$user) {
            redirectWithFlash(url('?action=login'), 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
            return;
        }

        renderView('main', [
            'title' => 'Đặt lại mật khẩu',
            'view' => 'auth/reset-password',
            'token' => $token,
        ]);
    }

    public function resetPasswordSubmit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithFlash(url('?action=login'), 'Yêu cầu không hợp lệ.');
        }

        $token = trim($_POST['token'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (empty($token)) {
            redirectWithFlash(url('?action=login'), 'Liên kết đặt lại mật khẩu không hợp lệ.');
            return;
        }

        $user = $this->userModel->findByPasswordResetToken($token);
        if (!$user) {
            redirectWithFlash(url('?action=login'), 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
            return;
        }

        if (empty($password) || strlen($password) < 6) {
            renderView('main', [
                'title' => 'Đặt lại mật khẩu',
                'view' => 'auth/reset-password',
                'token' => $token,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự.'
            ]);
            return;
        }

        if ($password !== $confirmPassword) {
            renderView('main', [
                'title' => 'Đặt lại mật khẩu',
                'view' => 'auth/reset-password',
                'token' => $token,
                'message' => 'Mật khẩu xác nhận không khớp.'
            ]);
            return;
        }

        $this->userModel->resetPassword($user['id'], $password);
        redirectWithFlash(url('?action=login'), 'Mật khẩu mới đã được cập nhật. Bạn có thể đăng nhập ngay bây giờ.');
    }
}
