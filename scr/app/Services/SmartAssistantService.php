<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\ReminderRepository;
use DateTimeImmutable;
use PDO;

class SmartAssistantService
{
    private const DAY_START = '08:00';
    private const DAY_END = '22:00';
    private const MIN_FREE_GAP_MINUTES = 30;
    private const LONG_FREE_GAP_MINUTES = 90;

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function generate(int $userId): array
    {
        $today = $this->todayRange();
        $week = $this->weekRange();
        $summary = [
            'planned_today_minutes' => $this->plannedMinutes($userId, $today['start'], $today['end']),
            'actual_today_minutes' => $this->actualMinutes($userId, $today['start'], $today['end']),
            'time_logs_today_count' => $this->timeLogsCount($userId, $today['start'], $today['end']),
            'schedules_today_count' => $this->schedulesCount($userId, $today['start'], $today['end']),
            'actual_week_minutes' => $this->actualMinutes($userId, $week['start'], $week['end']),
            'planned_week_minutes' => $this->plannedMinutes($userId, $week['start'], $week['end']),
        ];
        $personalMinutes = $this->personalOrRecreationActualMinutes($userId, $today['start'], $today['end']);
        $personalThreshold = $this->personalThresholdMinutes();
        $longestTimeLog = $this->longestTimeLog($userId, $today['start'], $today['end']);
        $endedUnconfirmed = $this->endedUnconfirmedSchedules($userId);
        $dominantCategory = $this->dominantCategoryThisWeek($userId, $week['start'], $week['end']);
        $freeGaps = $this->freeGapsToday($userId);
        $longGap = $this->longestGap($freeGaps);
        $reminderRepository = new ReminderRepository($this->db);
        $upcomingReminder = $reminderRepository->upcomingToday($userId, 1)[0] ?? null;
        $missedReminder = $reminderRepository->missedToday($userId, 1)[0] ?? null;
        $suggestions = [];
        $overPlannedMinutes = $summary['actual_today_minutes'] - $summary['planned_today_minutes'];

        if ($summary['actual_today_minutes'] > 1440) {
            $suggestions[] = [
                'title' => __('assistant.rule.day_over_24h.title'),
                'explanation' => __('assistant.rule.day_over_24h.explanation', [
                    'actual' => $summary['actual_today_minutes'],
                ]),
                'recommendation' => __('assistant.rule.day_over_24h.recommendation'),
                'severity' => 'alarm',
            ];
        }

        if ($longestTimeLog !== null && $longestTimeLog['duration_minutes'] > 720) {
            $suggestions[] = [
                'title' => __('assistant.rule.long_log_alarm.title'),
                'explanation' => __('assistant.rule.long_log_alarm.explanation', [
                    'activity' => \display_activity_title($longestTimeLog['activity_title'] ?? ''),
                    'minutes' => $longestTimeLog['duration_minutes'],
                ]),
                'recommendation' => __('assistant.rule.long_log_alarm.recommendation'),
                'severity' => 'alarm',
            ];
        } elseif ($longestTimeLog !== null && $longestTimeLog['duration_minutes'] >= 240) {
            $suggestions[] = [
                'title' => __('assistant.rule.long_log_warning.title'),
                'explanation' => __('assistant.rule.long_log_warning.explanation', [
                    'activity' => \display_activity_title($longestTimeLog['activity_title'] ?? ''),
                    'minutes' => $longestTimeLog['duration_minutes'],
                ]),
                'recommendation' => __('assistant.rule.long_log_warning.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($overPlannedMinutes >= 120) {
            $suggestions[] = [
                'title' => __('assistant.rule.overload_alarm.title'),
                'explanation' => __('assistant.rule.overload_alarm.explanation', [
                    'actual' => $summary['actual_today_minutes'],
                    'planned' => $summary['planned_today_minutes'],
                    'over' => $overPlannedMinutes,
                ]),
                'recommendation' => __('assistant.rule.overload_alarm.recommendation'),
                'severity' => 'alarm',
            ];
        } elseif ($overPlannedMinutes > 0) {
            $suggestions[] = [
                'title' => __('assistant.rule.overload.title'),
                'explanation' => __('assistant.rule.overload.explanation', [
                    'actual' => $summary['actual_today_minutes'],
                    'planned' => $summary['planned_today_minutes'],
                    'over' => $overPlannedMinutes,
                ]),
                'recommendation' => __('assistant.rule.overload.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($personalMinutes >= $personalThreshold + 60) {
            $suggestions[] = [
                'title' => __('assistant.rule.balance_alarm.title'),
                'explanation' => __('assistant.rule.balance_alarm.explanation', [
                    'minutes' => $personalMinutes,
                    'threshold' => $personalThreshold,
                ]),
                'recommendation' => __('assistant.rule.balance_alarm.recommendation'),
                'severity' => 'alarm',
            ];
        } elseif ($personalMinutes >= $personalThreshold) {
            $suggestions[] = [
                'title' => __('assistant.rule.balance.title'),
                'explanation' => __('assistant.rule.balance.explanation', [
                    'minutes' => $personalMinutes,
                    'threshold' => $personalThreshold,
                ]),
                'recommendation' => __('assistant.rule.balance.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($summary['schedules_today_count'] > 0 && $summary['time_logs_today_count'] === 0) {
            $suggestions[] = [
                'title' => __('assistant.rule.schedules_without_logs.title'),
                'explanation' => __('assistant.rule.schedules_without_logs.explanation', [
                    'count' => $summary['schedules_today_count'],
                ]),
                'recommendation' => __('assistant.rule.schedules_without_logs.recommendation'),
                'severity' => 'warning',
            ];
        } elseif ($summary['time_logs_today_count'] === 0) {
            $suggestions[] = [
                'title' => __('assistant.rule.no_logs.title'),
                'explanation' => __('assistant.rule.no_logs.explanation'),
                'recommendation' => __('assistant.rule.no_logs.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($endedUnconfirmed !== null) {
            $suggestions[] = [
                'title' => __('assistant.rule.ended_unconfirmed.title'),
                'explanation' => __('assistant.rule.ended_unconfirmed.explanation', [
                    'activity' => \display_activity_title($endedUnconfirmed['activity_title']),
                    'end' => \format_app_time($endedUnconfirmed['end_at']),
                ]),
                'recommendation' => __('assistant.rule.ended_unconfirmed.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($summary['schedules_today_count'] === 0) {
            $suggestions[] = [
                'title' => __('assistant.rule.no_schedules.title'),
                'explanation' => __('assistant.rule.no_schedules.explanation'),
                'recommendation' => __('assistant.rule.no_schedules.recommendation'),
                'severity' => 'info',
            ];
        }

        if ($summary['planned_today_minutes'] >= 120 && $summary['actual_today_minutes'] < (int) floor($summary['planned_today_minutes'] * 0.4)) {
            $suggestions[] = [
                'title' => __('assistant.rule.focus.title'),
                'explanation' => __('assistant.rule.focus.explanation', [
                    'planned' => $summary['planned_today_minutes'],
                    'actual' => $summary['actual_today_minutes'],
                ]),
                'recommendation' => __('assistant.rule.focus.recommendation'),
                'severity' => 'info',
            ];
        }

        if ($upcomingReminder !== null) {
            $suggestions[] = [
                'title' => __('assistant.rule.upcoming_reminder.title'),
                'explanation' => __('assistant.rule.upcoming_reminder.explanation', [
                    'title' => $upcomingReminder['title'],
                    'time' => \format_app_time($upcomingReminder['remind_time']),
                ]),
                'recommendation' => __('assistant.rule.upcoming_reminder.recommendation'),
                'severity' => 'info',
            ];
        } elseif ($missedReminder !== null) {
            $suggestions[] = [
                'title' => __('assistant.rule.missed_reminder.title'),
                'explanation' => __('assistant.rule.missed_reminder.explanation', [
                    'title' => $missedReminder['title'],
                    'time' => \format_app_time($missedReminder['remind_time']),
                ]),
                'recommendation' => __('assistant.rule.missed_reminder.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($dominantCategory !== null) {
            $suggestions[] = [
                'title' => __('assistant.rule.category.title'),
                'explanation' => __('assistant.rule.category.explanation', [
                    'category' => \display_category_name($dominantCategory['name']),
                    'percent' => $dominantCategory['percent'],
                ]),
                'recommendation' => __('assistant.rule.category.recommendation'),
                'severity' => 'warning',
            ];
        }

        if ($freeGaps !== []) {
            $suggestions[] = [
                'title' => __('assistant.rule.free_slots.title'),
                'explanation' => __('assistant.rule.free_slots.explanation', [
                    'count' => count($freeGaps),
                    'minutes' => array_sum(array_column($freeGaps, 'minutes')),
                ]),
                'recommendation' => __('assistant.rule.free_slots.recommendation'),
                'severity' => 'info',
            ];
        }

        if ($longGap !== null) {
            $suggestions[] = [
                'title' => __('assistant.rule.long_gap.title'),
                'explanation' => __('assistant.rule.long_gap.explanation', [
                    'minutes' => $longGap['minutes'],
                    'start' => \format_app_time($longGap['start']->format('Y-m-d H:i:s')),
                    'end' => \format_app_time($longGap['end']->format('Y-m-d H:i:s')),
                ]),
                'recommendation' => __('assistant.rule.long_gap.recommendation'),
                'severity' => 'info',
            ];
        }

        if (
            $summary['schedules_today_count'] > 0
            && $summary['time_logs_today_count'] > 0
            && $summary['actual_today_minutes'] <= max($summary['planned_today_minutes'] + 30, 30)
        ) {
            $suggestions[] = [
                'title' => __('assistant.rule.tracking_success.title'),
                'explanation' => __('assistant.rule.tracking_success.explanation', [
                    'logs' => $summary['time_logs_today_count'],
                    'actual' => $summary['actual_today_minutes'],
                ]),
                'recommendation' => __('assistant.rule.tracking_success.recommendation'),
                'severity' => 'success',
            ];
        }

        if ($suggestions === []) {
            $suggestions[] = [
                'title' => __('assistant.rule.steady.title'),
                'explanation' => __('assistant.rule.steady.explanation'),
                'recommendation' => __('assistant.rule.steady.recommendation'),
                'severity' => 'success',
            ];
        }

        return $suggestions;
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

    private function actualMinutes(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(duration_minutes), 0)
             FROM time_logs
             WHERE user_id = :user_id
                AND started_at >= :start_at
                AND started_at < :end_at'
        );
        $statement->execute([
            'user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return (int) $statement->fetchColumn();
    }

    private function timeLogsCount(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*)
             FROM time_logs
             WHERE user_id = :user_id
                AND started_at >= :start_at
                AND started_at < :end_at'
        );
        $statement->execute([
            'user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
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

    private function longestTimeLog(int $userId, string $startAt, string $endAt): ?array
    {
        $statement = $this->db->prepare(
            'SELECT tl.duration_minutes, COALESCE(a.title, :fallback_title) AS activity_title
             FROM time_logs tl
             LEFT JOIN activities a ON a.id = tl.activity_id AND a.user_id = :activity_user_id
             WHERE tl.user_id = :log_user_id
                AND tl.started_at >= :start_at
                AND tl.started_at < :end_at
             ORDER BY tl.duration_minutes DESC
             LIMIT 1'
        );
        $statement->execute([
            'fallback_title' => __('assistant.unknown_activity'),
            'activity_user_id' => $userId,
            'log_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        $timeLog = $statement->fetch();

        return $timeLog ?: null;
    }

    private function personalOrRecreationActualMinutes(int $userId, string $startAt, string $endAt): int
    {
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(tl.duration_minutes), 0) AS minutes
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :log_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND tl.started_at >= :start_at
                AND tl.started_at < :end_at
                AND LOWER(c.name) IN (
                    :personal_name,
                    :recreation_name,
                    :chill_name,
                    :personal_vi_name,
                    :recreation_vi_name
                )'
        );
        $statement->execute([
            'log_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'personal_name' => 'personal',
            'recreation_name' => 'recreation',
            'chill_name' => 'chill',
            'personal_vi_name' => 'cá nhân',
            'recreation_vi_name' => 'giải trí',
        ]);

        return (int) $statement->fetchColumn();
    }

    private function dominantCategoryThisWeek(int $userId, string $startAt, string $endAt): ?array
    {
        $actual = $this->dominantActualCategoryThisWeek($userId, $startAt, $endAt);

        if ($actual !== null) {
            return $actual;
        }

        return $this->dominantPlannedCategoryThisWeek($userId, $startAt, $endAt);
    }

    private function dominantActualCategoryThisWeek(int $userId, string $startAt, string $endAt): ?array
    {
        $statement = $this->db->prepare(
            'SELECT c.name, COALESCE(SUM(tl.duration_minutes), 0) AS minutes
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :log_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND tl.started_at >= :start_at
                AND tl.started_at < :end_at
             GROUP BY c.id, c.name
             ORDER BY minutes DESC
             LIMIT 1'
        );
        $statement->execute([
            'log_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        $category = $statement->fetch();

        if (!$category) {
            return null;
        }

        $total = $this->actualMinutes($userId, $startAt, $endAt);
        $minutes = (int) $category['minutes'];

        if ($total < 60 || $minutes < 60) {
            return null;
        }

        $percent = (int) round(($minutes / max(1, $total)) * 100);

        if ($percent < 60) {
            return null;
        }

        return [
            'name' => $category['name'],
            'minutes' => $minutes,
            'percent' => $percent,
        ];
    }

    private function dominantPlannedCategoryThisWeek(int $userId, string $startAt, string $endAt): ?array
    {
        $statement = $this->db->prepare(
            'SELECT c.name,
                    COALESCE(SUM(TIMESTAMPDIFF(MINUTE, s.start_at, s.end_at)), 0) AS minutes
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.status <> :cancelled_status
             GROUP BY c.id, c.name
             ORDER BY minutes DESC
             LIMIT 1'
        );
        $statement->execute([
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'cancelled_status' => 'cancelled',
        ]);

        $category = $statement->fetch();

        if (!$category) {
            return null;
        }

        $total = $this->plannedMinutes($userId, $startAt, $endAt);
        $minutes = (int) $category['minutes'];

        if ($total < 60 || $minutes < 60) {
            return null;
        }

        $percent = (int) round(($minutes / max(1, $total)) * 100);

        if ($percent < 60) {
            return null;
        }

        return [
            'name' => $category['name'],
            'minutes' => $minutes,
            'percent' => $percent,
        ];
    }

    private function busySchedules(int $userId, string $startAt, string $endAt): array
    {
        $statement = $this->db->prepare(
            'SELECT start_at, end_at
             FROM schedules
             WHERE user_id = :user_id
                AND status <> :cancelled_status
                AND start_at < :end_at
                AND end_at > :start_at
             ORDER BY start_at ASC, end_at ASC'
        );
        $statement->execute([
            'user_id' => $userId,
            'cancelled_status' => 'cancelled',
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return $statement->fetchAll();
    }

    private function endedUnconfirmedSchedules(int $userId): ?array
    {
        $today = $this->todayRange();
        $statement = $this->db->prepare(
            'SELECT s.id, s.end_at, a.title AS activity_title
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.end_at <= :now_at
                AND s.status <> :cancelled_status
                AND NOT EXISTS (
                    SELECT 1
                    FROM time_logs tl
                    WHERE tl.schedule_id = s.id
                        AND tl.user_id = :log_user_id
                    LIMIT 1
                )
             ORDER BY s.end_at DESC
             LIMIT 1'
        );
        $statement->execute([
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'start_at' => $today['start'],
            'end_at' => $today['end'],
            'now_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'cancelled_status' => 'cancelled',
            'log_user_id' => $userId,
        ]);

        $schedule = $statement->fetch();

        return $schedule ?: null;
    }

    private function freeGapsToday(int $userId): array
    {
        $today = new DateTimeImmutable('today');
        $windowStart = new DateTimeImmutable($today->format('Y-m-d') . ' ' . self::DAY_START);
        $windowEnd = new DateTimeImmutable($today->format('Y-m-d') . ' ' . self::DAY_END);
        $now = new DateTimeImmutable();

        if ($now > $windowStart && $now < $windowEnd) {
            $windowStart = $now;
        }

        if ($windowStart >= $windowEnd) {
            return [];
        }

        $busy = $this->mergeIntervals(array_map(static function (array $schedule): array {
            return [
                'start' => new DateTimeImmutable($schedule['start_at']),
                'end' => new DateTimeImmutable($schedule['end_at']),
            ];
        }, $this->busySchedules($userId, $windowStart->format('Y-m-d H:i:s'), $windowEnd->format('Y-m-d H:i:s'))));
        $cursor = $windowStart;
        $gaps = [];

        foreach ($busy as $interval) {
            if ($interval['start'] > $cursor) {
                $this->appendGap($gaps, $cursor, $interval['start']);
            }

            if ($interval['end'] > $cursor) {
                $cursor = $interval['end'];
            }
        }

        if ($windowEnd > $cursor) {
            $this->appendGap($gaps, $cursor, $windowEnd);
        }

        return $gaps;
    }

    private function appendGap(array &$gaps, DateTimeImmutable $start, DateTimeImmutable $end): void
    {
        $minutes = (int) floor(($end->getTimestamp() - $start->getTimestamp()) / 60);

        if ($minutes < self::MIN_FREE_GAP_MINUTES) {
            return;
        }

        $gaps[] = [
            'start' => $start,
            'end' => $end,
            'minutes' => $minutes,
        ];
    }

    private function longestGap(array $gaps): ?array
    {
        $longest = null;

        foreach ($gaps as $gap) {
            if ($gap['minutes'] < self::LONG_FREE_GAP_MINUTES) {
                continue;
            }

            if ($longest === null || $gap['minutes'] > $longest['minutes']) {
                $longest = $gap;
            }
        }

        return $longest;
    }

    private function mergeIntervals(array $intervals): array
    {
        usort($intervals, static function (array $a, array $b): int {
            if ($a['start']->getTimestamp() === $b['start']->getTimestamp()) {
                return $a['end']->getTimestamp() <=> $b['end']->getTimestamp();
            }

            return $a['start']->getTimestamp() <=> $b['start']->getTimestamp();
        });

        $merged = [];

        foreach ($intervals as $interval) {
            if ($merged === []) {
                $merged[] = $interval;
                continue;
            }

            $lastIndex = count($merged) - 1;

            if ($interval['start'] <= $merged[$lastIndex]['end']) {
                if ($interval['end'] > $merged[$lastIndex]['end']) {
                    $merged[$lastIndex]['end'] = $interval['end'];
                }

                continue;
            }

            $merged[] = $interval;
        }

        return $merged;
    }

    private function personalThresholdMinutes(): int
    {
        $config = require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        return (int) ($config['dashboard']['personal_daily_threshold_minutes'] ?? 180);
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
