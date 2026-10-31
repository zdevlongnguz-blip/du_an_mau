<?php
abstract class BaseModel
{
    protected $table;
    protected $pdo;
    public function __construct()
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME);
        try {
            $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
        } catch (PDOException $e) {
            die("Kết nối cơ sở dữ liệu thất bại: {$e->getMessage()}. Vui lòng thử lại sau");
        }
    }



    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        $keys = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ($keys) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "`$key` = :$key, ";
        }
        $fields = rtrim($fields, ", ");

        $query = "UPDATE `{$this->table}` SET $fields WHERE `id` = :id";
        $stmt = $this->pdo->prepare($query);

        $data['id'] = $id; // Gán ID vào mảng để bindValue tự động
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $query = "DELETE FROM `{$this->table}` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function truncate()
    {
        $sql = "TRUNCATE TABLE `{$this->table}`";
        return $this->pdo->exec($sql);
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hủy kết nối CSDL
    public function __destruct()
    {
        $this->pdo = null;
    }

    public function getItemCount(int $userId): int
    {
        $sql = "SELECT SUM(quantity) as total_quantity FROM `{$this->table}` WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total_quantity'] ?? 0);
    }
}
