<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use DateTimeImmutable;
use PDO;

class DashboardRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function summary(int $userId): array
    {
        $today = $this->todayRange();
        $week = $this->weekRange();

        return [
            'planned_today_minutes' => $this->plannedMinutes($userId, $today['start'], $today['end']),
            'schedule_today_minutes' => $this->plannedMinutes($userId, $today['start'], $today['end']),
            'planned_week_minutes' => $this->plannedMinutes($userId, $week['start'], $week['end']),
            'schedule_week_minutes' => $this->plannedMinutes($userId, $week['start'], $week['end']),
            'active_activities_count' => $this->activeActivitiesCount($userId),
            'scheduled_items_count' => $this->scheduledItemsCount($userId),
            'scheduled_today_count' => $this->schedulesCount($userId, $today['start'], $today['end']),
            'focus_logs_today_count' => $this->focusLogsCount($userId, $today['start'], $today['end']),
        ];
    }

    public function plannedMinutesByCategoryThisWeek(int $userId): array
    {
        $week = $this->weekRange();
        $statement = $this->db->prepare(
            'SELECT c.id,
                    c.name,
                    c.color,
                    COALESCE(SUM(
                        CASE
                            WHEN s.id IS NULL THEN 0
                            ELSE TIMESTAMPDIFF(MINUTE, s.start_at, s.end_at)
                        END
                    ), 0) AS minutes
             FROM categories c
             LEFT JOIN activities a
                ON a.category_id = c.id AND a.user_id = :activity_user_id
             LEFT JOIN schedules s
                ON s.activity_id = a.id
                AND s.user_id = :schedule_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.status <> :cancelled_status
             WHERE c.user_id = :category_user_id
             GROUP BY c.id, c.name, c.color
             ORDER BY minutes DESC, c.name ASC'
        );
        $statement->execute([
            'activity_user_id' => $userId,
            'schedule_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $week['start'],
            'end_at' => $week['end'],
            'cancelled_status' => 'cancelled',
        ]);

        return $statement->fetchAll();
    }

    public function scheduleMinutesByCategoryThisWeek(int $userId): array
    {
        return $this->plannedMinutesByCategoryThisWeek($userId);
    }

    public function personalOrRecreationScheduleMinutesToday(int $userId): int
    {
        $today = $this->todayRange();
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, s.start_at, s.end_at)), 0) AS minutes
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.status <> :cancelled_status
                AND LOWER(c.name) IN (
                    :personal_name,
                    :recreation_name,
                    :chill_name,
                    :personal_vi_name,
                    :recreation_vi_name
                )'
        );
        $statement->execute([
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $today['start'],
            'end_at' => $today['end'],
            'cancelled_status' => 'cancelled',
            'personal_name' => 'personal',
            'recreation_name' => 'recreation',
            'chill_name' => 'chill',
            'personal_vi_name' => 'cá nhân',
            'recreation_vi_name' => 'giải trí',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function plannedMinutes(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, start_at, end_at)), 0)
             FROM schedules
             WHERE user_id = :user_id
                AND start_at >= :start_at
                AND start_at < :end_at
                AND status <> :cancelled_status'
        );
        $statement->execute([
            'user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'cancelled_status' => 'cancelled',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function focusLogsCount(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM time_logs
             WHERE user_id = :user_id
                AND started_at >= :start_at
                AND started_at < :end_at
                AND note IN (:focus_note, :focus_note_ascii)'
        );
        $statement->execute([
            'user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'focus_note' => 'Tạo từ chế độ tập trung',
            'focus_note_ascii' => 'Created from focus mode.',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function schedulesCount(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM schedules
             WHERE user_id = :user_id
                AND start_at >= :start_at
                AND start_at < :end_at
                AND status <> :cancelled_status'
        );
        $statement->execute([
            'user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'cancelled_status' => 'cancelled',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function activeActivitiesCount(int $userId): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM activities WHERE user_id = :user_id AND is_active = 1'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }

    private function scheduledItemsCount(int $userId): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM schedules WHERE user_id = :user_id AND status <> :cancelled_status'
        );
        $statement->execute([
            'user_id' => $userId,
            'cancelled_status' => 'cancelled',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function todayRange(): array
    {
        $start = new DateTimeImmutable('today');

        return [
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $start->modify('+1 day')->format('Y-m-d H:i:s'),
        ];
    }

    private function weekRange(): array
    {
        $start = (new DateTimeImmutable('monday this week'))->setTime(0, 0);

        return [
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $start->modify('+7 days')->format('Y-m-d H:i:s'),
        ];
    }
}
