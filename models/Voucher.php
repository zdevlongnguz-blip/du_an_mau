<?php

class Voucher extends BaseModel
{
    protected $table = 'vouchers';

    public function getTodayAward(int $userId): ?array
    {
        $sql = "SELECT * FROM vouchers
                WHERE user_id = :user_id AND DATE(created_at) = CURDATE()
                ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        return $voucher ?: null;
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM vouchers WHERE user_id = :user_id ORDER BY created_at DESC, id DESC"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM vouchers
             WHERE user_id = :user_id AND is_used = 0
             ORDER BY discount_percent DESC, created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFiltered($search = '', int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $search = trim((string)$search);
        $params = [];
        $conditions = [];

        if ($search !== '') {
            $conditions[] = '(v.code LIKE :keyword OR CAST(v.discount_percent AS CHAR) = :discount OR CAST(v.user_id AS CHAR) = :user_id)';
            $params['keyword'] = "%{$search}%";
            $params['discount'] = $search;
            $params['user_id'] = $search;
        }

        $whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT v.*, u.fullname, u.username
                FROM vouchers v
                LEFT JOIN users u ON u.id = v.user_id
                {$whereSql}
                ORDER BY v.id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM vouchers v {$whereSql}");
        foreach ($params as $key => $value) {
            $countStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $totalItems = (int)$countStmt->fetchColumn();

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1,
            ],
        ];
    }

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM vouchers WHERE code = :code';
        $params = ['code' => $code];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function updateUnclaimedUnused(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE vouchers SET code = :code, discount_percent = :discount_percent
             WHERE id = :id AND user_id IS NULL AND order_id IS NULL AND is_used = 0'
        );
        $stmt->execute([
            'id' => $id,
            'code' => $data['code'],
            'discount_percent' => $data['discount_percent'],
        ]);

        if ($stmt->rowCount() === 1) {
            return true;
        }

        $checkStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM vouchers
             WHERE id = :id AND user_id IS NULL AND order_id IS NULL AND is_used = 0'
        );
        $checkStmt->execute(['id' => $id]);
        return (int)$checkStmt->fetchColumn() === 1;
    }

    public function deleteUnclaimedUnused(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM vouchers
             WHERE id = :id AND user_id IS NULL AND order_id IS NULL AND is_used = 0'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() === 1;
    }

    public function getRecentAwards(int $limit = 10): array
    {
        $limit = max(1, min(50, $limit));
        $stmt = $this->pdo->query(
            "SELECT v.code, v.discount_percent, v.created_at, u.fullname, u.username
             FROM vouchers v
             INNER JOIN users u ON u.id = v.user_id
             WHERE v.user_id IS NOT NULL
             ORDER BY v.created_at DESC, v.id DESC
             LIMIT {$limit}"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOrderId(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM vouchers WHERE order_id = :order_id LIMIT 1");
        $stmt->execute(['order_id' => $orderId]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
        return $voucher ?: null;
    }

    public function claimExisting(int $userId, int $discountPercent): ?array
    {
        $this->pdo->beginTransaction();

        try {
            $voucherStmt = $this->pdo->prepare(
                "SELECT * FROM vouchers
                 WHERE discount_percent = :discount_percent AND user_id IS NULL AND is_used = 0
                 ORDER BY id LIMIT 1 FOR UPDATE"
            );
            $voucherStmt->execute(['discount_percent' => $discountPercent]);
            $voucher = $voucherStmt->fetch(PDO::FETCH_ASSOC);

            if (!$voucher) {
                $this->pdo->rollBack();
                return null;
            }

            $claimStmt = $this->pdo->prepare(
                "UPDATE vouchers
                 SET user_id = :user_id, created_at = NOW()
                 WHERE id = :id AND user_id IS NULL AND is_used = 0"
            );
            $claimStmt->execute(['user_id' => $userId, 'id' => $voucher['id']]);

            if ($claimStmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return null;
            }

            $this->pdo->commit();
            $voucher['user_id'] = $userId;
            $voucher['created_at'] = date('Y-m-d H:i:s');
            return $voucher;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
