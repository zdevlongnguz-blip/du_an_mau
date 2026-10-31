<?php

class User extends BaseModel {
    protected $table = 'users';

    public function getAll($search = null, $page = 1, $perPage = 3)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== null && $search !== '') {
            $where = " WHERE `fullname` LIKE :keyword OR `username` LIKE :keyword OR `email` LIKE :keyword";
            $params['keyword'] = "%{$search}%";
        }

        $sql = "SELECT * FROM `{$this->table}`" . $where . " ORDER BY `id` DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int) $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) AS totalItems FROM `{$this->table}`" . $where;
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalItems = $countRow ? (int) $countRow['totalItems'] : 0;

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => ceil($totalItems / $perPage)
            ]
        ];
    }

    public function findByName($username){
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email){
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // CÁC HÀM MỚI BỔ SUNG CHO CHỨC NĂNG XÁC THỰC EMAIL
    // =========================================================================

    /**
     * Đăng ký tài khoản mới (Mặc định is_active = 0)
     */
    public function register($fullname, $username, $email, $password, $token)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO {$this->table} (fullname, username, email, password, verification_token, is_active) 
                VALUES (:fullname, :username, :email, :password, :token, 0)";
                
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':fullname' => $fullname,
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hashedPassword,
            ':token'    => $token
        ]);
    }

    /**
     * Tìm người dùng dựa trên verification token
     */
    public function findByToken($token)
    {
        $sql = "SELECT * FROM {$this->table} WHERE verification_token = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kích hoạt tài khoản khi người dùng nhấp vào Link trong Gmail
     */
    public function activateAccount($token)
    {
        $sql = "UPDATE {$this->table} 
                SET is_active = 1, verification_token = NULL 
                WHERE verification_token = :token AND is_active = 0";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        // Trả về true nếu cập nhật thành công (có dòng bị ảnh hưởng)
        return $stmt->rowCount() > 0;
    }

    public function setPasswordResetToken($userId, $token)
    {
        $sql = "UPDATE {$this->table} 
                SET password_reset_token = :token, password_reset_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':id' => $userId,
        ]);
    }

    public function findByPasswordResetToken($token)
    {
        $sql = "SELECT * FROM {$this->table} WHERE password_reset_token = ? AND password_reset_expires_at >= NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword($userId, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET password = :password, password_reset_token = NULL, password_reset_expires_at = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId,
        ]);
    }

    // =========================================================================
    // AUTH HELPER METHODS
    // =========================================================================

    function isLoggedIn() {
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    function isAdmin() {
        return $this->isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    function userInfo() {
        if ($this->isLoggedIn()) {
            return [
                'fullname' => $_SESSION['user']['fullname'] ?? '',
                'role' => $_SESSION['user']['role'] ?? ''
            ];
        }
        return null;
    }
}