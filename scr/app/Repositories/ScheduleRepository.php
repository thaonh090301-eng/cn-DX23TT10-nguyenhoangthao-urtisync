<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Services\ScheduleStatusResolver;
use PDO;

class ScheduleRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT s.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.user_id = :user_id
             ORDER BY s.start_at ASC, s.end_at ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function forDateByUser(int $userId, string $date): array
    {
        $rangeStart = $date . ' 00:00:00';
        $rangeEnd = date('Y-m-d H:i:s', strtotime($rangeStart . ' +1 day'));

        $statement = $this->db->prepare(
            'SELECT s.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.user_id = :user_id
                AND s.start_at < :range_end
                AND s.end_at > :range_start
             ORDER BY s.start_at ASC, s.end_at ASC'
        );
        $statement->execute([
            'user_id' => $userId,
            'range_start' => $rangeStart,
            'range_end' => $rangeEnd,
        ]);

        return $statement->fetchAll();
    }

    public function existsByUser(int $userId): bool
    {
        $statement = $this->db->prepare(
            'SELECT 1 FROM schedules WHERE user_id = :user_id LIMIT 1'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchColumn() !== false;
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT s.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.id = :id AND s.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $schedule = $statement->fetch();

        return $schedule ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO schedules
                (user_id, activity_id, title, start_at, end_at, status, notes)
             VALUES
                (:user_id, :activity_id, :title, :start_at, :end_at, :status, :notes)'
        );
        $statement->execute([
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'title' => $data['title'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'status' => $data['status'],
            'notes' => $data['notes'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE schedules
             SET activity_id = :activity_id,
                 title = :title,
                 start_at = :start_at,
                 end_at = :end_at,
                 status = :status,
                 notes = :notes
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'title' => $data['title'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'status' => $data['status'],
            'notes' => $data['notes'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM schedules WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function calendarEventsByUser(int $userId): array
    {
        $schedules = $this->allByUser($userId);
        $statusResolver = new ScheduleStatusResolver();
        $now = $statusResolver->now();

        return array_map(static function (array $schedule) use ($statusResolver, $now): array {
            $displayStatus = $statusResolver->resolve($schedule, $now);

            return [
                'id' => (string) $schedule['id'],
                'title' => \display_activity_title($schedule['title']),
                'start' => str_replace(' ', 'T', $schedule['start_at']),
                'end' => str_replace(' ', 'T', $schedule['end_at']),
                'backgroundColor' => $schedule['category_color'],
                'borderColor' => $schedule['category_color'],
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'activity' => \display_activity_title($schedule['activity_title']),
                    'category' => \display_category_name($schedule['category_name']),
                    'status' => \__($displayStatus['label_key']),
                    'statusKey' => $displayStatus['key'],
                    'notes' => \display_note($schedule['notes']),
                ],
            ];
        }, $schedules);
    }
}
