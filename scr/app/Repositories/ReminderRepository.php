<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ReminderRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
        $this->ensureTable();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE user_id = :user_id
             ORDER BY is_active DESC, remind_time ASC, title ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $reminder = $statement->fetch();

        return $reminder ?: null;
    }

    public function activeForDate(int $userId, string $date): array
    {
        $dayOfWeek = (int) date('w', strtotime($date));
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE user_id = :user_id
                AND is_active = 1
                AND (
                    repeat_type = :daily_repeat
                    OR (repeat_type = :none_repeat AND DATE(created_at) = :selected_date)
                    OR (repeat_type = :weekly_repeat AND day_of_week = :day_of_week)
                )
             ORDER BY remind_time ASC, title ASC'
        );
        $statement->execute([
            'user_id' => $userId,
            'daily_repeat' => 'daily',
            'none_repeat' => 'none',
            'weekly_repeat' => 'weekly',
            'selected_date' => $date,
            'day_of_week' => $dayOfWeek,
        ]);

        return $statement->fetchAll();
    }

    public function upcomingToday(int $userId, int $limit = 5): array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE user_id = :user_id
                AND is_active = 1
                AND (
                    repeat_type = :daily_repeat
                    OR (repeat_type = :none_repeat AND DATE(created_at) = :selected_date)
                    OR (repeat_type = :weekly_repeat AND day_of_week = :day_of_week)
                )
                AND remind_time >= :current_time
             ORDER BY remind_time ASC, title ASC
             LIMIT ' . max(1, $limit)
        );
        $statement->execute([
            'user_id' => $userId,
            'daily_repeat' => 'daily',
            'none_repeat' => 'none',
            'weekly_repeat' => 'weekly',
            'selected_date' => date('Y-m-d'),
            'day_of_week' => (int) date('w'),
            'current_time' => date('H:i:s'),
        ]);

        return $statement->fetchAll();
    }

    public function missedToday(int $userId, int $limit = 3): array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE user_id = :user_id
                AND is_active = 1
                AND (
                    repeat_type = :daily_repeat
                    OR (repeat_type = :none_repeat AND DATE(created_at) = :selected_date)
                    OR (repeat_type = :weekly_repeat AND day_of_week = :day_of_week)
                )
                AND remind_time < :current_time
             ORDER BY remind_time DESC, title ASC
             LIMIT ' . max(1, $limit)
        );
        $statement->execute([
            'user_id' => $userId,
            'daily_repeat' => 'daily',
            'none_repeat' => 'none',
            'weekly_repeat' => 'weekly',
            'selected_date' => date('Y-m-d'),
            'day_of_week' => (int) date('w'),
            'current_time' => date('H:i:s'),
        ]);

        return $statement->fetchAll();
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO reminders
                (user_id, title, note, remind_time, repeat_type, day_of_week, is_active)
             VALUES
                (:user_id, :title, :note, :remind_time, :repeat_type, :day_of_week, :is_active)'
        );
        $statement->execute([
            'user_id' => $userId,
            'title' => $data['title'],
            'note' => $data['note'],
            'remind_time' => $data['remind_time'],
            'repeat_type' => $data['repeat_type'],
            'day_of_week' => $data['day_of_week'],
            'is_active' => $data['is_active'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE reminders
             SET title = :title,
                 note = :note,
                 remind_time = :remind_time,
                 repeat_type = :repeat_type,
                 day_of_week = :day_of_week,
                 is_active = :is_active
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'title' => $data['title'],
            'note' => $data['note'],
            'remind_time' => $data['remind_time'],
            'repeat_type' => $data['repeat_type'],
            'day_of_week' => $data['day_of_week'],
            'is_active' => $data['is_active'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function setActive(int $id, int $userId, bool $isActive): bool
    {
        $statement = $this->db->prepare(
            'UPDATE reminders
             SET is_active = :is_active
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'is_active' => $isActive ? 1 : 0,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM reminders WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    private function ensureTable(): void
    {
        $statement = $this->db->prepare(
            "CREATE TABLE IF NOT EXISTS reminders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(150) NOT NULL,
                note TEXT NULL,
                remind_time TIME NOT NULL,
                repeat_type ENUM('none', 'daily', 'weekly') NOT NULL DEFAULT 'none',
                day_of_week TINYINT UNSIGNED NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_reminders_user_time (user_id, remind_time),
                KEY idx_reminders_active (user_id, is_active),
                CONSTRAINT fk_reminders_user
                    FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $statement->execute();
    }
}
