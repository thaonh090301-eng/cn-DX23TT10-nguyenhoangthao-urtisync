<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;

class ScheduleStatusResolver
{
    public const RECORDED = 'recorded';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public function resolve(array $schedule, ?DateTimeImmutable $now = null): array
    {
        if (($schedule['status'] ?? '') === self::CANCELLED) {
            return [
                'key' => self::CANCELLED,
                'label_key' => 'schedule_status_display.' . self::CANCELLED,
                'type' => 'alarm',
            ];
        }

        $now ??= new DateTimeImmutable();
        $start = new DateTimeImmutable((string) $schedule['start_at']);
        $end = new DateTimeImmutable((string) $schedule['end_at']);

        if ($now < $start) {
            return [
                'key' => self::RECORDED,
                'label_key' => 'schedule_status_display.' . self::RECORDED,
                'type' => 'info',
            ];
        }

        if ($now <= $end) {
            return [
                'key' => self::IN_PROGRESS,
                'label_key' => 'schedule_status_display.' . self::IN_PROGRESS,
                'type' => 'warning',
            ];
        }

        return [
            'key' => self::COMPLETED,
            'label_key' => 'schedule_status_display.' . self::COMPLETED,
            'type' => 'success',
        ];
    }
}
