<?php

class Product extends BaseModel{
    // khai báo thuộc tính
    protected $table = 'products';
    protected $parent_table = 'categories';
    protected $fk = 'category_id';

    public function getAll($page=1, $perPage=10){
        $offset = ($page - 1) * $perPage;
        $query = "SELECT `{$this->table}`.*, `{$this->parent_table}`.`name` AS `category_name`
                    FROM `$this->table` 
                    LEFT JOIN `{$this->parent_table}` ON `$this->table`.`{$this->fk}` = `{$this->parent_table}`.`id`
                    ORDER BY `{$this->table}`.`id` DESC
                    LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detail($id){
        $query = "SELECT `{$this->table}`.*, `{$this->parent_table}`.`name` AS `category_name`
                    FROM `$this->table` 
                    LEFT JOIN `{$this->parent_table}` ON `$this->table`.`{$this->fk}` = `{$this->parent_table}`.`id`
                    WHERE `$this->table`.`id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementViewCount($id)
    {
        $query = "UPDATE `{$this->table}` SET `views` = `views` + 1 WHERE `id` = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function relatedByCategory($categoryId, $currentId, $limit = 4)
    {
        $query = "SELECT * FROM `{$this->table}`
                  WHERE `category_id` = :category_id
                    AND `id` != :current_id
                  ORDER BY `views` DESC, `id` DESC
                  LIMIT :limit";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':current_id', (int)$currentId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopByViews(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $query = "SELECT `products`.*, `categories`.`name` AS `category_name`
                  FROM `products`
                  LEFT JOIN `categories` ON `products`.`category_id` = `categories`.`id`
                  ORDER BY `products`.`views` DESC, `products`.`id` DESC
                  LIMIT :limit";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryCounts(): array
    {
        $query = "SELECT `categories`.`id`, `categories`.`name`, COUNT(`products`.`id`) AS `product_count`
                  FROM `categories`
                  LEFT JOIN `products` ON `products`.`category_id` = `categories`.`id`
                  GROUP BY `categories`.`id`, `categories`.`name`
                  ORDER BY `categories`.`name` ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFiltered($search = '', $categoryId = null, $page = 1, $perPage = 8)
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];

        $search = trim((string)$search);
        if ($search !== '') {
            $conditions[] = "(`products`.`name` LIKE :keyword OR `products`.`code` LIKE :keyword OR `products`.`description` LIKE :keyword)";
            $params['keyword'] = "%{$search}%";
        }

        if ($categoryId !== null && $categoryId !== '' && (int)$categoryId > 0) {
            $conditions[] = "`products`.`category_id` = :category_id";
            $params['category_id'] = (int)$categoryId;
        }

        $whereSql = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT `products`.*, `categories`.`name` AS `category_name`
                FROM `products`
                LEFT JOIN `categories` ON `products`.`category_id` = `categories`.`id`
                {$whereSql}
                ORDER BY `products`.`id` DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $type = in_array($key, ['category_id'], true) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) AS total_items
                     FROM `products`
                     {$whereSql}";
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $type = in_array($key, ['category_id'], true) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $countStmt->bindValue(':' . $key, $value, $type);
        }
        $countStmt->execute();
        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalItems = (int)($countRow['total_items'] ?? 0);

        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1;

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ],
        ];
    }

}
