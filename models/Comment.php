<?php

class Comment extends BaseModel
{
    protected $table = 'comments';

public function getByProductId(int $productId, ?int $limit = null): array
    {
        $sql = "SELECT c.*, u.fullname, u.avatar, u.role
                FROM `{$this->table}` c
                JOIN `users` u ON c.user_id = u.id
                WHERE c.product_id = :product_id
                  AND c.status = 'approved'
                ORDER BY c.created_at DESC, c.id DESC";
        if ($limit !== null) {
            $sql .= ' LIMIT :limit';
            $limit = max(1, $limit);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
                $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(int $userId, int $productId, string $content, int $rating, string $status = 'approved')
    {
        $rating = max(1, min(5, $rating));
        $status = in_array($status, ['approved', 'pending'], true) ? $status : 'pending';
        $sql = "INSERT INTO `{$this->table}` (product_id, user_id, content, rating, status, created_at)
                VALUES (:product_id, :user_id, :content, :rating, :status, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':product_id' => $productId,
            ':user_id' => $userId,
            ':content' => trim($content),
            ':rating' => $rating,
            ':status' => $status,
        ]);
    }

    public function approve(int $commentId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `{$this->table}`
             SET status = 'approved'
             WHERE id = :comment_id AND status = 'pending'"
        );
        $stmt->execute(['comment_id' => $commentId]);
        return $stmt->rowCount() === 1;
    }

    public function getSummary(int $productId)
    {
        $sql = "SELECT COUNT(*) AS total_comments, COALESCE(AVG(rating), 0) AS average_rating
                FROM `{$this->table}`
                WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findWithProductAndUser(int $commentId)
    {
        $sql = "SELECT c.*, u.fullname AS user_fullname, u.username AS user_username, p.name AS product_name, p.code AS product_code
                FROM `{$this->table}` c
                LEFT JOIN `users` u ON c.user_id = u.id
                LEFT JOIN `products` p ON c.product_id = p.id
                WHERE c.id = :comment_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':comment_id' => $commentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($search = null, $page = 1, $perPage = 10, ?string $status = null)
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        $params = [];
        $where = '';
        $clauses = [];

        if ($search !== null && trim($search) !== '') {
            $clauses[] = "(`{$this->table}`.`content` LIKE :keyword OR `u`.`fullname` LIKE :keyword OR `p`.`name` LIKE :keyword OR `{$this->table}`.`id` = :search_id)";
            $params['keyword'] = "%{$search}%";
            $params['search_id'] = (int)$search;
        }

        if ($status !== null && in_array($status, ['approved', 'pending'], true)) {
            $clauses[] = "`{$this->table}`.`status` = :status";
            $params['status'] = $status;
        }

        if (!empty($clauses)) {
            $where = ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql = "SELECT `{$this->table}`.*, `u`.`fullname` AS `user_fullname`, `p`.`name` AS `product_name`
                FROM `{$this->table}`
                LEFT JOIN `users` AS `u` ON `{$this->table}`.`user_id` = `u`.`id`
                LEFT JOIN `products` AS `p` ON `{$this->table}`.`product_id` = `p`.`id`
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
                     LEFT JOIN `products` AS `p` ON `{$this->table}`.`product_id` = `p`.`id`
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
}
