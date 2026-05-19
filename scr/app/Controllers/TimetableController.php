<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReminderRepository;
use App\Repositories\TimetableRepository;
use App\Services\TimetableService;
use DateTimeImmutable;

class TimetableController extends Controller
{
    private const DEMO_USER_ID = 1;

    private TimetableRepository $timetableRepository;
    private ReminderRepository $reminderRepository;
    private TimetableService $timetableService;

    public function __construct()
    {
        $this->timetableRepository = new TimetableRepository();
        $this->reminderRepository = new ReminderRepository();
        $this->timetableService = new TimetableService();
    }

    public function index(): string
    {
        $date = $this->dateFromRequest();
        $schedules = $this->timetableRepository->schedulesForDate(self::DEMO_USER_ID, $date);
        $reminders = $this->reminderRepository->activeForDate(self::DEMO_USER_ID, $date);

        return $this->view('timetable/index', [
            'title' => __('nav.timetable'),
            'selectedDate' => $date,
            'schedules' => $schedules,
            'items' => $this->mergeReminderItems($this->timetableService->build($schedules, $date), $reminders, $date),
            'currentOrNext' => $this->currentOrNext($schedules, $date),
        ]);
    }

    private function mergeReminderItems(array $items, array $reminders, string $date): array
    {
        foreach ($reminders as $reminder) {
            $items[] = [
                'type' => 'reminder',
                'state' => 'reminder',
                'reminder' => $reminder,
                'start_at' => $date . ' ' . $reminder['remind_time'],
                'end_at' => $date . ' ' . $reminder['remind_time'],
                'minutes' => 0,
            ];
        }

        usort($items, static function (array $a, array $b): int {
            $timeCompare = strcmp((string) $a['start_at'], (string) $b['start_at']);

            if ($timeCompare !== 0) {
                return $timeCompare;
            }

            $order = ['reminder' => 0, 'schedule' => 1, 'gap' => 2];

            return ($order[$a['type']] ?? 9) <=> ($order[$b['type']] ?? 9);
        });

        return $items;
    }

    private function dateFromRequest(): string
    {
        $date = trim((string) ($_GET['date'] ?? date('Y-m-d')));

        if (!$this->isDate($date)) {
            return date('Y-m-d');
        }

        return $date;
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function currentOrNext(array $schedules, string $date): ?array
    {
        $now = new DateTimeImmutable();

        if ($date !== $now->format('Y-m-d')) {
            return $schedules[0] ?? null;
        }

        foreach ($schedules as $schedule) {
            $start = new DateTimeImmutable($schedule['start_at']);
            $end = new DateTimeImmutable($schedule['end_at']);

            if ($start <= $now && $end >= $now) {
                return array_merge($schedule, ['focus_state' => 'current']);
            }
        }

        foreach ($schedules as $schedule) {
            $start = new DateTimeImmutable($schedule['start_at']);

            if ($start > $now) {
                return array_merge($schedule, ['focus_state' => 'next']);
            }
        }

        return null;
    }
}
