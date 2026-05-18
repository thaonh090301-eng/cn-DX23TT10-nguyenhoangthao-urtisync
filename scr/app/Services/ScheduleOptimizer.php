<?php

declare(strict_types=1);

namespace App\Services;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;

class ScheduleOptimizer
{
    public function suggest(
        array $busySchedules,
        array $activity,
        DateTimeImmutable $rangeStart,
        DateTimeImmutable $rangeEnd,
        int $requiredMinutes,
        string $earliestTime,
        string $latestTime,
        int $limit = 5
    ): array {
        $suggestions = [];
        $searchStart = $rangeStart->getTimestamp();
        $busyIntervals = $this->scheduleIntervals($busySchedules);
        $period = new DatePeriod($rangeStart, new DateInterval('P1D'), $rangeEnd);

        foreach ($period as $day) {
            $windowStart = new DateTimeImmutable($day->format('Y-m-d') . ' ' . $earliestTime);
            $windowEnd = new DateTimeImmutable($day->format('Y-m-d') . ' ' . $latestTime);

            if ($windowEnd <= $windowStart) {
                continue;
            }

            $merged = $this->mergeIntervals($this->clipToWindow($busyIntervals, $windowStart, $windowEnd));
            $cursor = $windowStart;

            foreach ($merged as $interval) {
                if ($interval['start'] > $cursor) {
                    $this->appendSuggestion($suggestions, $activity, $cursor, $interval['start'], $requiredMinutes, $searchStart);
                }

                if ($interval['end'] > $cursor) {
                    $cursor = $interval['end'];
                }
            }

            if ($windowEnd > $cursor) {
                $this->appendSuggestion($suggestions, $activity, $cursor, $windowEnd, $requiredMinutes, $searchStart);
            }
        }

        usort($suggestions, static function (array $a, array $b): int {
            if ($a['score'] === $b['score']) {
                return strcmp($a['start_at'], $b['start_at']);
            }

            return $b['score'] <=> $a['score'];
        });

        return array_slice($suggestions, 0, $limit);
    }

    private function appendSuggestion(
        array &$suggestions,
        array $activity,
        DateTimeImmutable $gapStart,
        DateTimeImmutable $gapEnd,
        int $requiredMinutes,
        int $searchStart
    ): void {
        $gapMinutes = (int) floor(($gapEnd->getTimestamp() - $gapStart->getTimestamp()) / 60);

        if ($gapMinutes < $requiredMinutes) {
            return;
        }

        $suggestedEnd = $gapStart->modify('+' . $requiredMinutes . ' minutes');
        $bufferMinutes = $gapMinutes - $requiredMinutes;
        $earlierPenalty = (int) floor(max(0, $gapStart->getTimestamp() - $searchStart) / 3600);
        $fitPenalty = min(40, (int) floor($bufferMinutes / 10));
        $timePenalty = min(40, $earlierPenalty);
        $score = max(1, 100 - $fitPenalty - $timePenalty);

        $suggestions[] = [
            'start_at' => $gapStart->format('Y-m-d H:i:s'),
            'end_at' => $suggestedEnd->format('Y-m-d H:i:s'),
            'gap_end_at' => $gapEnd->format('Y-m-d H:i:s'),
            'gap_minutes' => $gapMinutes,
            'activity_id' => (int) $activity['id'],
            'activity_title' => $activity['title'],
            'category_name' => $activity['category_name'],
            'category_color' => $activity['category_color'],
            'score' => $score,
            'reason' => $this->reason($bufferMinutes, $earlierPenalty),
        ];
    }

    private function reason(int $bufferMinutes, int $earlierPenalty): string
    {
        if ($bufferMinutes === 0) {
            return 'Exact fit and no schedule overlap.';
        }

        if ($earlierPenalty <= 2) {
            return 'Early available slot with ' . $bufferMinutes . ' free buffer minutes.';
        }

        return 'Available gap with ' . $bufferMinutes . ' free buffer minutes and no overlap.';
    }

    private function scheduleIntervals(array $busySchedules): array
    {
        return array_map(static function (array $schedule): array {
            return [
                'start' => new DateTimeImmutable($schedule['start_at']),
                'end' => new DateTimeImmutable($schedule['end_at']),
            ];
        }, $busySchedules);
    }

    private function clipToWindow(array $intervals, DateTimeImmutable $windowStart, DateTimeImmutable $windowEnd): array
    {
        $clipped = [];

        foreach ($intervals as $interval) {
            if ($interval['end'] <= $windowStart || $interval['start'] >= $windowEnd) {
                continue;
            }

            $clipped[] = [
                'start' => $interval['start'] < $windowStart ? $windowStart : $interval['start'],
                'end' => $interval['end'] > $windowEnd ? $windowEnd : $interval['end'],
            ];
        }

        usort($clipped, static function (array $a, array $b): int {
            if ($a['start']->getTimestamp() === $b['start']->getTimestamp()) {
                return $a['end']->getTimestamp() <=> $b['end']->getTimestamp();
            }

            return $a['start']->getTimestamp() <=> $b['start']->getTimestamp();
        });

        return $clipped;
    }

    private function mergeIntervals(array $intervals): array
    {
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
}
