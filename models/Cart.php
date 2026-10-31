<?php

class Cart extends BaseModel
{
    protected $table = 'cart';

    public function findItem(int $userId, int $productId)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addItem(int $userId, int $productId, int $quantity)
    {
        $quantity = max(1, $quantity);
        $existing = $this->findItem($userId, $productId);
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $sql = "UPDATE `{$this->table}` SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':quantity' => $newQuantity,
                ':user_id' => $userId,
                ':product_id' => $productId,
            ]);
        }

        $sql = "INSERT INTO `{$this->table}` (user_id, product_id, quantity, created_at) VALUES (:user_id, :product_id, :quantity, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
        ]);
    }

    public function updateItem(int $userId, int $productId, int $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }

        $sql = "UPDATE `{$this->table}` SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':quantity' => $quantity,
            ':user_id' => $userId,
            ':product_id' => $productId,
        ]);
    }

    public function removeItem(int $userId, int $productId)
    {
        $sql = "DELETE FROM `{$this->table}` WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':product_id' => $productId,
        ]);
    }

    public function getItems(int $userId)
    {
        $sql = "SELECT c.*, p.name, p.price, p.image, p.stock, p.description
                FROM `{$this->table}` c
                JOIN `products` p ON c.product_id = p.id
                WHERE c.user_id = :user_id
                ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAmount(int $userId)
    {
        $sql = "SELECT COALESCE(SUM(c.quantity * p.price), 0) AS total
                FROM `{$this->table}` c
                JOIN `products` p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['total'] : 0.0;
    }

    public function clear(int $userId)
    {
        $sql = "DELETE FROM `{$this->table}` WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
}
