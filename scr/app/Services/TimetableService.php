<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;

class TimetableService
{
    private const MIN_GAP_MINUTES = 15;

    private ScheduleStatusResolver $statusResolver;

    public function __construct(?ScheduleStatusResolver $statusResolver = null)
    {
        $this->statusResolver = $statusResolver ?? new ScheduleStatusResolver();
    }

    public function build(array $schedules, string $date): array
    {
        $now = new DateTimeImmutable();
        $isToday = $date === $now->format('Y-m-d');
        $items = [];
        $nextScheduleId = $this->nextScheduleId($schedules, $now, $isToday);
        $previousEnd = null;

        foreach ($schedules as $schedule) {
            $start = new DateTimeImmutable($schedule['start_at']);
            $end = new DateTimeImmutable($schedule['end_at']);
            $status = $this->statusResolver->resolve($schedule, $now);

            if ($previousEnd !== null && $start > $previousEnd) {
                $gap = $this->gapItem($previousEnd, $start);

                if ($gap !== null) {
                    $items[] = $gap;
                }
            }

            $items[] = [
                'type' => 'schedule',
                'state' => $this->scheduleState($schedule, $now, $isToday, $nextScheduleId, $status['key']),
                'status_key' => $status['key'],
                'status_type' => $status['type'],
                'schedule' => $schedule,
                'start_at' => $schedule['start_at'],
                'end_at' => $schedule['end_at'],
                'minutes' => $this->minutesBetween($start, $end),
            ];

            if ($previousEnd === null || $end > $previousEnd) {
                $previousEnd = $end;
            }
        }

        return $items;
    }

    private function nextScheduleId(array $schedules, DateTimeImmutable $now, bool $isToday): ?int
    {
        if (!$isToday) {
            return null;
        }

        foreach ($schedules as $schedule) {
            $start = new DateTimeImmutable($schedule['start_at']);

            if ($start > $now) {
                return (int) $schedule['id'];
            }
        }

        return null;
    }

    private function scheduleState(array $schedule, DateTimeImmutable $now, bool $isToday, ?int $nextScheduleId, string $statusKey): string
    {
        if ($statusKey === 'completed') {
            return 'logged';
        }

        if ($isToday) {
            $start = new DateTimeImmutable($schedule['start_at']);
            $end = new DateTimeImmutable($schedule['end_at']);

            if ($start <= $now && $end >= $now) {
                return 'current';
            }

            if ($nextScheduleId !== null && (int) $schedule['id'] === $nextScheduleId) {
                return 'next';
            }
        }

        return 'planned';
    }

    private function gapItem(DateTimeImmutable $start, DateTimeImmutable $end): ?array
    {
        $minutes = $this->minutesBetween($start, $end);

        if ($minutes < self::MIN_GAP_MINUTES) {
            return null;
        }

        return [
            'type' => 'gap',
            'state' => 'gap',
            'start_at' => $start->format('Y-m-d H:i:s'),
            'end_at' => $end->format('Y-m-d H:i:s'),
            'minutes' => $minutes,
        ];
    }

    private function minutesBetween(DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        return max(0, (int) floor(($end->getTimestamp() - $start->getTimestamp()) / 60));
    }
}
