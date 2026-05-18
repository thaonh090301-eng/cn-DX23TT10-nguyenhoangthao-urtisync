<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CategoryRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM activities a WHERE a.category_id = c.id) AS activities_count
             FROM categories c
             WHERE c.user_id = :user_id
             ORDER BY c.sort_order ASC, c.name ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM activities a WHERE a.category_id = c.id) AS activities_count
             FROM categories c
             WHERE c.id = :id AND c.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $category = $statement->fetch();

        return $category ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO categories (user_id, name, color, sort_order)
             VALUES (:user_id, :name, :color, :sort_order)'
        );
        $statement->execute([
            'user_id' => $userId,
            'name' => $data['name'],
            'color' => $data['color'],
            'sort_order' => $data['sort_order'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE categories
             SET name = :name, color = :color, sort_order = :sort_order
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'name' => $data['name'],
            'color' => $data['color'],
            'sort_order' => $data['sort_order'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM categories WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}
