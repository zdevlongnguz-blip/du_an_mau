<?php

class Order extends BaseModel
{
    protected $table = 'orders';

    public function getByUserId(int $userId)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithUser(int $orderId)
    {
        $sql = "SELECT o.*, u.username, u.fullname AS user_fullname, u.email AS user_email
                FROM `{$this->table}` o
                LEFT JOIN `users` u ON o.user_id = u.id
                WHERE o.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($search = null, $page = 1, $perPage = 10)
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== null && trim($search) !== '') {
            $where = " WHERE (`{$this->table}`.`id` = :search_id OR `{$this->table}`.`receiver_name` LIKE :keyword OR `{$this->table}`.`receiver_phone` LIKE :keyword OR `{$this->table}`.`shipping_address` LIKE :keyword OR `{$this->table}`.`status` LIKE :keyword OR `u`.`fullname` LIKE :keyword OR `u`.`username` LIKE :keyword)";
            $params['search_id'] = (int)$search;
            $params['keyword'] = "%{$search}%";
        }

        $sql = "SELECT `{$this->table}`.*, `u`.`username`, `u`.`fullname` AS `user_fullname` 
                FROM `{$this->table}`
                LEFT JOIN `users` AS `u` ON `{$this->table}`.`user_id` = `u`.`id`
                {$where}
                ORDER BY `{$this->table}`.`created_at` DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = $key === 'search_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) AS total_items
                     FROM `{$this->table}`
                     LEFT JOIN `users` AS `u` ON `{$this->table}`.`user_id` = `u`.`id`
                     {$where}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $type = $key === 'search_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $countStmt->bindValue(':' . $key, $value, $type);
        }
        $countStmt->execute();
        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalItems = $countRow ? (int)$countRow['total_items'] : 0;

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1,
            ]
        ];
    }

    public function getSummary($search = null)
    {
        $params = [];
        $where = '';

        if ($search !== null && trim($search) !== '') {
            $where = " WHERE (`{$this->table}`.`id` = :search_id OR `{$this->table}`.`receiver_name` LIKE :keyword OR `{$this->table}`.`receiver_phone` LIKE :keyword OR `{$this->table}`.`shipping_address` LIKE :keyword OR `{$this->table}`.`status` LIKE :keyword)";
            $params['search_id'] = (int)$search;
            $params['keyword'] = "%{$search}%";
        }

        $sql = "SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_revenue, COALESCE(AVG(total_amount), 0) AS average_amount
                FROM `{$this->table}`
                {$where}";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = $key === 'search_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStatusTotals($search = null)
    {
        $params = [];
        $where = '';

        if ($search !== null && trim($search) !== '') {
            $where = " WHERE (`{$this->table}`.`id` = :search_id OR `{$this->table}`.`receiver_name` LIKE :keyword OR `{$this->table}`.`receiver_phone` LIKE :keyword OR `{$this->table}`.`shipping_address` LIKE :keyword OR `{$this->table}`.`status` LIKE :keyword)";
            $params['search_id'] = (int)$search;
            $params['keyword'] = "%{$search}%";
        }

        $sql = "SELECT `status`, COUNT(*) AS count FROM `{$this->table}` {$where} GROUP BY `status`";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = $key === 'search_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllForExport($search = null)
    {
        $params = [];
        $where = '';

        if ($search !== null && trim($search) !== '') {
            $where = " WHERE (`{$this->table}`.`id` = :search_id OR `{$this->table}`.`receiver_name` LIKE :keyword OR `{$this->table}`.`receiver_phone` LIKE :keyword OR `{$this->table}`.`shipping_address` LIKE :keyword OR `{$this->table}`.`status` LIKE :keyword OR `u`.`fullname` LIKE :keyword OR `u`.`username` LIKE :keyword)";
            $params['search_id'] = (int)$search;
            $params['keyword'] = "%{$search}%";
        }

        $sql = "SELECT `{$this->table}`.*, `u`.`username`, `u`.`fullname` AS `user_fullname`
                FROM `{$this->table}`
                LEFT JOIN `users` AS `u` ON `{$this->table}`.`user_id` = `u`.`id`
                {$where}
                ORDER BY `{$this->table}`.`created_at` DESC";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = $key === 'search_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $orderId, int $userId)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $orderId, ':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO `{$this->table}` (user_id, total_amount, status, receiver_name, receiver_phone, shipping_address, note, created_at)
                VALUES (:user_id, :total_amount, :status, :receiver_name, :receiver_phone, :shipping_address, :note, NOW())";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute($data)) {
            return (int)$this->pdo->lastInsertId();
        }
        return false;
    }

    public function getOrderItems(int $orderId)
    {
        $sql = "SELECT od.*, p.name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = :order_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOrder(int $orderId)
    {
        $this->pdo->beginTransaction();

        try {
            $deleteDetails = $this->pdo->prepare('DELETE FROM order_details WHERE order_id = :order_id');
            $deleteDetails->execute([':order_id' => $orderId]);

            $deleteOrder = $this->pdo->prepare('DELETE FROM orders WHERE id = :id');
            $result = $deleteOrder->execute([':id' => $orderId]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function createFromCart(int $userId, array $shippingData, array $cartItems, ?int $voucherId = null)
    {
        if (empty($cartItems)) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $voucher = null;
            if ($voucherId !== null) {
                $voucherStmt = $this->pdo->prepare(
                    "SELECT * FROM vouchers
                     WHERE id = :voucher_id AND user_id = :user_id AND is_used = 0
                     LIMIT 1 FOR UPDATE"
                );
                $voucherStmt->execute([
                    ':voucher_id' => $voucherId,
                    ':user_id' => $userId,
                ]);
                $voucher = $voucherStmt->fetch(PDO::FETCH_ASSOC);
                if (!$voucher) {
                    throw new Exception('Voucher không hợp lệ hoặc đã được sử dụng.');
                }

                $discountPercent = max(0, min(100, (int)$voucher['discount_percent']));
                $totalAmount = round($totalAmount * (1 - ($discountPercent / 100)), 2);
            }

            $orderData = [
                ':user_id' => $userId,
                ':total_amount' => $totalAmount,
                ':status' => 'pending',
                ':receiver_name' => $shippingData['receiver_name'],
                ':receiver_phone' => $shippingData['receiver_phone'],
                ':shipping_address' => $shippingData['shipping_address'],
                ':note' => $shippingData['note'] ?? '',
            ];

            $orderId = $this->create($orderData);
            if (!$orderId) {
                throw new Exception('Không thể tạo đơn hàng.');
            }

            $detailSql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
            $detailStmt = $this->pdo->prepare($detailSql);

            $productUpdateSql = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id AND stock >= :quantity";
            $productUpdateStmt = $this->pdo->prepare($productUpdateSql);

            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['stock']) {
                    throw new Exception("Sản phẩm '{$item['name']}' không đủ số lượng trong kho.");
                }

                $detailStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                ]);

                $productUpdateStmt->execute([
                    ':quantity' => $item['quantity'],
                    ':product_id' => $item['product_id'],
                ]);

                if ($productUpdateStmt->rowCount() === 0) {
                    throw new Exception("Sản phẩm '{$item['name']}' không đủ tồn kho.");
                }
            }

            if ($voucher) {
                $usedVoucherStmt = $this->pdo->prepare(
                    "UPDATE vouchers SET is_used = 1, order_id = :order_id, used_at = NOW()
                     WHERE id = :voucher_id AND user_id = :user_id AND is_used = 0"
                );
                $usedVoucherStmt->execute([
                    ':order_id' => $orderId,
                    ':voucher_id' => $voucherId,
                    ':user_id' => $userId,
                ]);
                if ($usedVoucherStmt->rowCount() !== 1) {
                    throw new Exception('Không thể áp dụng voucher.');
                }
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
