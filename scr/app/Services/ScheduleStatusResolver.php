<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class ScheduleStatusResolver
{
    private const TIMEZONE = 'Asia/Ho_Chi_Minh';

    public const SCHEDULED = 'scheduled';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';

    public function resolve(array $schedule, ?DateTimeImmutable $now = null): array
    {
        $now = $now === null ? $this->now() : $now->setTimezone($this->timezone());
        $start = $this->dateTime((string) $schedule['start_at']);
        $end = $this->dateTime((string) $schedule['end_at']);

        if ($now < $start) {
            return [
                'key' => self::SCHEDULED,
                'label_key' => 'schedule_status_display.' . self::SCHEDULED,
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

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone());
    }

    public function dateTime(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable($value, $this->timezone());
    }

    private function timezone(): DateTimeZone
    {
        return new DateTimeZone(self::TIMEZONE);
    }
}
