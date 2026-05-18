<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ActivityRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT a.*, c.name AS category_name, c.color AS category_color
             FROM activities a
             INNER JOIN categories c ON c.id = a.category_id
             WHERE a.user_id = :user_id
             ORDER BY a.created_at DESC, a.title ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT a.*, c.name AS category_name, c.color AS category_color
             FROM activities a
             INNER JOIN categories c ON c.id = a.category_id
             WHERE a.id = :id AND a.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $activity = $statement->fetch();

        return $activity ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO activities
                (user_id, category_id, title, description, priority, estimated_minutes, is_active)
             VALUES
                (:user_id, :category_id, :title, :description, :priority, :estimated_minutes, :is_active)'
        );
        $statement->execute([
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'estimated_minutes' => $data['estimated_minutes'],
            'is_active' => $data['is_active'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE activities
             SET category_id = :category_id,
                 title = :title,
                 description = :description,
                 priority = :priority,
                 estimated_minutes = :estimated_minutes,
                 is_active = :is_active
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'estimated_minutes' => $data['estimated_minutes'],
            'is_active' => $data['is_active'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM activities WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}
