<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Lỗi upload: mã lỗi ' . $file['error']);
        }

        $destDir = PATH_ASSETS_UPLOADS . $folder;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $filename   = time() . '-' . basename($file['name']);
        $targetFile = $folder . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}


//================================================================


if (!function_exists('dd')) {
    function dd(...$vars)
    {
        // GIAO DIỆN KIỂM TRA DỮ LIỆU PHONG CÁCH CYBERPUNK ĐỘC QUYỀN
        echo '<div style="background: #060713; color: #39ff14; padding: 25px; font-family: \'Courier New\', monospace; border: 2px solid #00f0ff; box-shadow: 0 0 20px #00f0ff; margin: 20px; border-radius: 4px; z-index: 99999; position: relative;">';
        echo '<h3 style="color: #ff007f; margin-top: 0; text-shadow: 0 0 8px #ff007f; letter-spacing: 2px;">⚡ [CYBERPUNK DUMP INSPECTOR] ⚡</h3>';
        foreach ($vars as $v) {
            echo '<pre style="background: #0c0e22; padding: 15px; border-left: 4px solid #ff007f; overflow-x: auto; color: #00f0ff; font-size: 14px;">';
            var_dump($v);
            echo '</pre>';
        }
        echo '</div>';
        die();
    }
}


function handleGlobalError(Throwable $e)
{
    // 1. Log lỗi vào file (để dev biết đường sửa)
    error_log($e->getMessage() . " in " . $e->getFile() . " at " . $e->getLine());

    // 2. Nếu đang ở môi trường Production thì show Meme
    // Nếu ở môi trường Development thì show chi tiết lỗi để fix
    if (getenv('APP_ENV') === 'production') {
        renderMemeError("Server đã bị hack hoặc code của bạn có vấn đề!");
    } else {
        // Show chi tiết cho Dev
        echo "<h1>[DEV_ERROR_LOG]</h1>";
        echo "<pre>" . $e->getMessage() . "<br>File: " . $e->getFile() . ":" . $e->getLine() . "</pre>";
        renderMemeError('Lỗi rồi kìa!!!');
    }
    die();
}

function renderMemeError($message)
{
    $homeUrl = url('/');
    // Render thẳng một giao diện HTML full màn hình mà không qua main.php (để tránh lỗi vòng lặp)
    echo "
    <style>body{background:#000;color:#0f0;font-family:monospace;text-align:center;padding:50px;}</style>
    <h1>SYSTEM_FAILURE</h1>
    <p>{$message}</p>
    <img src='https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExZ3o0am91aWIyNmJhb2ltOGFnZTFjOHlycHpndGJzYTRhbHhtMTJwdSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/oFTCaycpJYdMVOBD6A/giphy.gif' />
    <br><a href='$homeUrl' style='color:#fff'>Trở về thế giới cũ</a>";
}

/**
 * 1. Validate file tạm (Kiểm tra sâu MIME type và dung lượng)
 */
function validate_upload(
    array $file,
    array $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'],
    array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    int $maxSize = 2097152 // Mặc định 2MB
): bool {
    // Kiểm tra lỗi hệ thống từ PHP
    $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($errorCode !== UPLOAD_ERR_OK) {
        throw new Exception("Lỗi tải lên hệ thống (Mã lỗi: {$errorCode})");
    }

    $tmpPath = $file['tmp_name'] ?? '';
    if (empty($tmpPath) || !is_uploaded_file($tmpPath)) {
        throw new Exception("File tạm thời không hợp lệ hoặc không tồn tại!");
    }

    // Kiểm tra dung lượng
    if ($file['size'] > $maxSize) {
        throw new Exception("Kích thước file vượt quá giới hạn " . round($maxSize / 1024 / 1024, 2) . "MB");
    }

    // Deep Scan MIME type để tránh giả mạo đuôi file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if (!in_array($realMimeType, $allowedMimeTypes)) {
        throw new Exception("Định dạng file không được phép (MIME: {$realMimeType})");
    }

    // Kiểm tra đuôi file
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExtensions)) {
        throw new Exception("Đuôi file [{$fileExt}] không được hỗ trợ!");
    }

    return true;
}

/**
 * 2. Lưu file vào thư mục đích (Tự động tạo thư mục và chống trùng tên)
 */
function save_uploaded_file(array $file, string $subFolder = ''): string
{
    // Xác định đường dẫn gốc từ hằng số toàn cục
    $uploadDir = rtrim(PATH_ASSETS_UPLOADS, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    // Thêm subfolder nếu có
    if (!empty($subFolder)) {
        $uploadDir .= trim($subFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new Exception("Không thể khởi tạo thư mục lưu trữ trên máy chủ!");
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Tạo tên file ngẫu nhiên để tránh trùng lặp và tấn công file injection
    $newFileName = 'cyber_' . bin2hex(random_bytes(6)) . '_' . time() . '.' . $fileExt;
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Không thể di chuyển file từ bộ nhớ tạm đến thư mục lưu trữ!");
    }

    return $newFileName;
}

/**
 * 3. Xóa file vật lý khỏi server
 */
function delete_uploaded_file(string $fileName, string $subFolder = ''): bool
{
    if (empty($fileName)) return false;

    $filePath = rtrim(PATH_ASSETS_UPLOADS, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (!empty($subFolder)) {
        $filePath .= trim($subFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    $filePath .= basename($fileName);

    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }

    return false;
}

/**
 * Helper 4: Tạo URL tuyệt đối (Thay thế cho việc viết URL thủ công)
 * @param string $path Đường dẫn mong muốn (ví dụ: 'login', 'products/add')
 * @return string URL đầy đủ (ví dụ: http://localhost/basemvc/login)
 */
function url(string $path = ''): string
{
    // Loại bỏ dấu / ở đầu để tránh bị trùng lặp
    $path = ltrim($path, '/\\');

    // Nối với hằng số BASE_URL đã định nghĩa trong file config
    return BASE_URL . $path;
}

if (!function_exists('formatViewCount')) {
    function formatViewCount(int $views): string
    {
        if ($views >= 1000000) {
            return rtrim(rtrim(number_format($views / 1000000, 1, '.', ''), '0'), '.') . 'M';
        }
        if ($views >= 1000) {
            return rtrim(rtrim(number_format($views / 1000, 1, '.', ''), '0'), '.') . 'k';
        }
        return (string)$views;
    }
}

if (!function_exists('productBadges')) {
    function productBadges(array $product): array
    {
        $stock = (int)($product['stock'] ?? 0);
        $views = (int)($product['views'] ?? 0);

        return [
            'stock' => $stock,
            'isOut' => $stock <= 0,
            'isLow' => $stock > 0 && $stock <= 3,
            'isHot' => $views >= 500,
            'categoryName' => $product['category_name'] ?? null,
            'code' => $product['code'] ?? null,
            'views' => $views,
            'viewsLabel' => formatViewCount($views),
        ];
    }
}

if (!function_exists('throwMessageAndBack')) {
    function throwMessageAndBack($mess, $icon = 'error')
    {
        $safeMess = json_encode($mess);
        $safeIcon = json_encode($icon);
        $safeUrl = json_encode(BASE_URL);

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Thông báo',
                    text: {$safeMess},
                    icon: {$safeIcon},
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = {$safeUrl};
                });
            });
        </script>";
        exit;
    }
}

function renderView($mainView, $data = [])
{
    extract($data); // Giải nén mảng dữ liệu thành biến riêng lẻ
    require_once PATH_VIEW . $mainView . '.php'; // Gọi trực tiếp file view để tránh lỗi vòng lặp
}

function getCartItemCount(): int
{
    if (!isset($_SESSION['user']['id']) || !class_exists('Cart')) {
        return 0;
    }

    try {
        return (new Cart())->getItemCount((int)$_SESSION['user']['id']);
    } catch (Exception $e) {
        return 0;
    }
}

// Mới

function setFlashMessage($message)
{
    $_SESSION['flash_message'] = $message;
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Xóa sau khi lấy
        return $message;
    }
    return null;
}

function getFlashMessageType(): string
{
    if (isset($_SESSION['flash_message_type'])) {
        $type = $_SESSION['flash_message_type'];
        unset($_SESSION['flash_message_type']);
        return $type;
    }
    return 'info';
}

function setOldInput(array $data): void
{
    $_SESSION['old_input'] = $data;
}

function old(string $key, string $default = ''): string
{
    return htmlspecialchars($_SESSION['old_input'][$key] ?? $default);
}

function forgetOldInput(): void
{
    unset($_SESSION['old_input']);
}

function setValidationErrors(array $errors): void
{
    $_SESSION['validation_errors'] = $errors;
}

function getValidationErrors(): array
{
    $errors = $_SESSION['validation_errors'] ?? [];
    unset($_SESSION['validation_errors']);
    return $errors;
}

function validationError(string $field): string
{
    $errors = $_SESSION['validation_errors'] ?? [];
    return htmlspecialchars($errors[$field] ?? '');
}

function validatePhoneNumber(string $phone): bool
{
    $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

    if ($cleanPhone === '') {
        return false;
    }

    // Chuẩn hóa đầu số Việt Nam
    if (str_starts_with($cleanPhone, '+84')) {
        $cleanPhone = '0' . substr($cleanPhone, 3);
    } elseif (str_starts_with($cleanPhone, '84')) {
        $cleanPhone = '0' . substr($cleanPhone, 2);
    }

    // Cho phép số Việt Nam hợp lệ: bắt đầu 0 và có 10 hoặc 11 chữ số.
    // Nhiều số di động và số cố định Việt Nam có 10 chữ số, một số cũ trước đây có 11.
    if (!preg_match('/^0[0-9]{9,10}$/', $cleanPhone)) {
        return false;
    }

    return true;
}

function redirectWithFlash($url, $message, $type = 'success')
{
    setFlashMessage($message);
    $_SESSION['flash_message_type'] = $type;
    header('Location: ' . $url);
    exit;
}

function getCurrentRoute(): string
{
    return $_GET['action'] ?? '/';
}

function isActiveRoute(string|array $routes): bool
{
    $currentRoute = getCurrentRoute();
    return in_array($currentRoute, (array)$routes, true);
}

function navLinkClass(string|array $routes): string
{
    return isActiveRoute($routes) ? 'nav-link active' : 'nav-link';
}

function iconBtnClass(string|array $routes): string
{
    return isActiveRoute($routes) ? 'icon-btn active' : 'icon-btn';
}

/**
 * Lọc các tham số rỗng và tạo chuỗi query sạch.
 *
 * @param array $params Mảng các tham số đầu vào.
 * @return string Trả về chuỗi query (VD: key=value&key2=value2).
 */
function cleanQueryString(array $params): string
{
    // Lọc mảng: loại bỏ giá trị null hoặc chuỗi rỗng
    // Giữ lại số 0 (vì giá trị 0 thường là hợp lệ trong tìm kiếm)
    $cleanParams = array_filter($params, function ($value) {
        return $value !== null && $value !== '';
    });

    // Chuyển mảng thành chuỗi query an toàn cho URL
    return http_build_query($cleanParams);
}

/**
 * Tự động build query string và loại bỏ tham số rỗng
 *
 * @param array $params Mảng các tham số đầu vào
 * @return string Chuỗi query string đã được xử lý
 */
function buildCleanQuery(array $params): string
{
    // 1. Lọc bỏ các giá trị rỗng (giữ lại số 0 vì nó thường có nghĩa trong query)
    $cleanParams = array_filter($params, function ($value) {
        return $value !== null && $value !== '' && $value !== false;
    });

    // 2. Chuyển mảng thành query string
    return http_build_query($cleanParams);
}


/* ==========================================================================
   5. EMAIL MAILER (PHPMailer)
   ========================================================================== */

function sendVerificationEmail($toEmail, $token)
{
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'xuanlonghh4@gmail.com';      // Email Gmail hệ thống
        $mail->Password   = 'bhuo juat hgpe ejnj';         // Mật khẩu ứng dụng (App Password 16 ký tự)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Người gửi / Người nhận
        $mail->setFrom('xuanlonghh4@gmail.com', 'Website System');
        $mail->addAddress($toEmail);

        // Tạo liên kết tự động bằng helper url() thay vì hardcode localhost
        $verifyLink = url('?action=verify&token=' . $token);

        // Nội dung Email
        $mail->isHTML(true);
        $mail->Subject = 'Chào mừng bạn đến với Hustler420';
        $mail->Body    = "
            <div style='margin:0; padding:32px 16px; background:#f4f6f8; font-family:Arial,Helvetica,sans-serif; color:#20242a;'>
                <div style='max-width:560px; margin:0 auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden;'>
                    <div style='padding:28px 32px; background:#17191d; color:#ffffff;'>
                        <div style='font-size:24px; font-weight:700; letter-spacing:.3px;'>Hustler<span style='color:#ff7a1a;'>420</span></div>
                        <div style='margin-top:6px; color:#cbd5e1; font-size:13px;'>Thế giới lào thủ</div>
                    </div>
                    <div style='padding:32px;'>
                        <div style='display:inline-block; padding:7px 11px; background:#fff1e8; color:#d85d0b; border-radius:999px; font-size:12px; font-weight:700;'>XÁC THỰC TÀI KHOẢN</div>
                        <h1 style='margin:18px 0 10px; font-size:26px; line-height:1.25; color:#17191d;'>Chào mừng bạn đến với Hustler420!</h1>
                        <p style='margin:0 0 18px; font-size:15px; line-height:1.7; color:#4b5563;'>Cảm ơn bạn đã đăng ký. Chỉ còn một bước nữa để bắt đầu hành trình mua sắm cùng cộng đồng lào thủ.</p>
                        <p style='margin:0 0 24px; font-size:15px; line-height:1.7; color:#4b5563;'>Bấm vào nút bên dưới để kích hoạt tài khoản của bạn:</p>
                        <p style='margin:0 0 26px;'><a href='{$verifyLink}' style='display:inline-block; padding:13px 22px; background:#ff7a1a; color:#ffffff; text-decoration:none; border-radius:8px; font-size:15px; font-weight:700;'>Kích hoạt tài khoản</a></p>
                        <p style='margin:0; font-size:12px; line-height:1.6; color:#9ca3af;'>Nếu bạn không thực hiện đăng ký, bạn có thể bỏ qua email này.</p>
                    </div>
                    <div style='padding:18px 32px; background:#fafafa; border-top:1px solid #f0f0f0; color:#9ca3af; font-size:12px;'>Hustler420 - thế giới lào thủ</div>
                </div>
            </div>
        ";
        $mail->AltBody = "Chào mừng bạn đến với Hustler420 - thế giới lào thủ!\n\nVui lòng mở liên kết sau để kích hoạt tài khoản:\n{$verifyLink}\n\nNếu bạn không thực hiện đăng ký, hãy bỏ qua email này.";

        $mail->send();
        error_log('[Mail][verification] SMTP accepted recipient=' . $toEmail . '; message_id=' . $mail->getLastMessageID());
        return true;
    } catch (Exception $e) {
        error_log('[Mail][verification] failed recipient=' . $toEmail . '; error=' . $mail->ErrorInfo);
        return false;
    }
}

function sendResetPasswordEmail($toEmail, $token)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'xuanlonghh4@gmail.com';
        $mail->Password   = 'bhuo juat hgpe ejnj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('xuanlonghh4@gmail.com', 'Website System');
        $mail->addAddress($toEmail);

        $resetLink = url('?action=reset-password&token=' . $token);

        $mail->isHTML(true);
        $mail->Subject = 'Đặt lại mật khẩu Hustler420';
        $mail->Body    = "
            <div style='margin:0; padding:32px 16px; background:#f4f6f8; font-family:Arial,Helvetica,sans-serif; color:#20242a;'>
                <div style='max-width:560px; margin:0 auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden;'>
                    <div style='padding:28px 32px; background:#17191d; color:#ffffff;'>
                        <div style='font-size:24px; font-weight:700; letter-spacing:.3px;'>Hustler<span style='color:#ff7a1a;'>420</span></div>
                        <div style='margin-top:6px; color:#cbd5e1; font-size:13px;'>Thế giới lào thủ</div>
                    </div>
                    <div style='padding:32px;'>
                        <div style='display:inline-block; padding:7px 11px; background:#fff1e8; color:#d85d0b; border-radius:999px; font-size:12px; font-weight:700;'>KHÔI PHỤC TÀI KHOẢN</div>
                        <h1 style='margin:18px 0 10px; font-size:26px; line-height:1.25; color:#17191d;'>Đặt lại mật khẩu</h1>
                        <p style='margin:0 0 18px; font-size:15px; line-height:1.7; color:#4b5563;'>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản Hustler420 của bạn.</p>
                        <p style='margin:0 0 24px; font-size:15px; line-height:1.7; color:#4b5563;'>Bấm vào nút bên dưới để tạo mật khẩu mới. Liên kết có hiệu lực trong <strong>60 phút</strong>.</p>
                        <p style='margin:0 0 26px;'><a href='{$resetLink}' style='display:inline-block; padding:13px 22px; background:#ff7a1a; color:#ffffff; text-decoration:none; border-radius:8px; font-size:15px; font-weight:700;'>Đặt lại mật khẩu</a></p>
                        <p style='margin:0; font-size:12px; line-height:1.6; color:#9ca3af;'>Nếu bạn không yêu cầu thay đổi mật khẩu, hãy bỏ qua email này và kiểm tra bảo mật tài khoản.</p>
                    </div>
                    <div style='padding:18px 32px; background:#fafafa; border-top:1px solid #f0f0f0; color:#9ca3af; font-size:12px;'>Hustler420 - thế giới lào thủ</div>
                </div>
            </div>
        ";
        $mail->AltBody = "Đặt lại mật khẩu Hustler420 - thế giới lào thủ\n\nVui lòng mở liên kết sau để tạo mật khẩu mới (có hiệu lực trong 60 phút):\n{$resetLink}\n\nNếu bạn không yêu cầu thay đổi mật khẩu, hãy bỏ qua email này.";

        $mail->send();
        error_log('[Mail][password-reset] SMTP accepted recipient=' . $toEmail . '; message_id=' . $mail->getLastMessageID());
        return true;
    } catch (Exception $e) {
        error_log('[Mail][password-reset] failed recipient=' . $toEmail . '; error=' . $mail->ErrorInfo);
        return false;
    }
}
