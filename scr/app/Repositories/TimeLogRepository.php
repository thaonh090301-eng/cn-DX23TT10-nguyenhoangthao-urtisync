<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class TimeLogRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT tl.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :user_id
             ORDER BY tl.started_at DESC, tl.ended_at DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function dailyReportByUser(int $userId, string $date): array
    {
        $startAt = $date . ' 00:00:00';
        $endAt = date('Y-m-d H:i:s', strtotime($startAt . ' +1 day'));
        $scheduledRows = $this->scheduledReportRows($userId, $startAt, $endAt);
        $unscheduledRows = $this->unscheduledReportRows($userId, $startAt, $endAt);
        $rows = array_merge($scheduledRows, $unscheduledRows);

        usort($rows, static function (array $a, array $b): int {
            $aStart = $a['sort_at'] ?? $a['planned_start_at'] ?? '';
            $bStart = $b['sort_at'] ?? $b['planned_start_at'] ?? '';

            return strcmp((string) $aStart, (string) $bStart);
        });

        return $rows;
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT tl.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.id = :id AND tl.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $timeLog = $statement->fetch();

        return $timeLog ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO time_logs
                (user_id, activity_id, schedule_id, started_at, ended_at, duration_minutes, note)
             VALUES
                (:user_id, :activity_id, :schedule_id, :started_at, :ended_at, :duration_minutes, :note)'
        );
        $statement->execute([
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'schedule_id' => $data['schedule_id'] ?? null,
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => $data['note'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE time_logs
             SET activity_id = :activity_id,
                 started_at = :started_at,
                 ended_at = :ended_at,
                 duration_minutes = :duration_minutes,
                 note = :note
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => $data['note'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM time_logs WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    private function scheduledReportRows(int $userId, string $startAt, string $endAt): array
    {
        $statement = $this->db->prepare(
            'SELECT
                    s.id AS schedule_id,
                    s.activity_id,
                    s.title AS schedule_title,
                    s.start_at AS planned_start_at,
                    s.end_at AS planned_end_at,
                    TIMESTAMPDIFF(MINUTE, s.start_at, s.end_at) AS planned_minutes,
                    s.notes AS report_note,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color,
                    :row_type AS row_type,
                    s.start_at AS sort_at
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.status <> :cancelled_status
             ORDER BY s.start_at ASC, s.end_at ASC'
        );
        $statement->execute([
            'row_type' => 'scheduled',
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'cancelled_status' => 'cancelled',
        ]);

        return $statement->fetchAll();
    }

    private function unscheduledReportRows(int $userId, string $startAt, string $endAt): array
    {
        $statement = $this->db->prepare(
            'SELECT
                    NULL AS schedule_id,
                    tl.activity_id,
                    NULL AS schedule_title,
                    NULL AS planned_start_at,
                    NULL AS planned_end_at,
                    NULL AS planned_minutes,
                    tl.note AS report_note,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color,
                    :row_type AS row_type,
                    tl.started_at AS sort_at
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :log_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND tl.schedule_id IS NULL
                AND tl.started_at >= :start_at
                AND tl.started_at < :end_at
             ORDER BY tl.started_at ASC, tl.ended_at ASC'
        );
        $statement->execute([
            'row_type' => 'unscheduled',
            'log_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return $statement->fetchAll();
    }
}
