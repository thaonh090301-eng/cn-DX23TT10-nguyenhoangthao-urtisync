<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\ReminderRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\TimetableRepository;
use App\Services\ScheduleStatusResolver;
use App\Services\TimetableService;
use DateTimeImmutable;

class TimetableController extends Controller
{
    private const DEMO_USER_ID = 1;

    private TimetableRepository $timetableRepository;
    private ReminderRepository $reminderRepository;
    private ActivityRepository $activityRepository;
    private ScheduleRepository $scheduleRepository;
    private ScheduleStatusResolver $statusResolver;
    private TimetableService $timetableService;

    public function __construct()
    {
        $this->timetableRepository = new TimetableRepository();
        $this->reminderRepository = new ReminderRepository();
        $this->activityRepository = new ActivityRepository();
        $this->scheduleRepository = new ScheduleRepository();
        $this->statusResolver = new ScheduleStatusResolver();
        $this->timetableService = new TimetableService($this->statusResolver);
    }

    public function index(): string
    {
        $date = $this->dateFromRequest();
        $schedules = $this->timetableRepository->schedulesForDate($this->authUserId(), $date);
        $reminders = $this->reminderRepository->activeForDate($this->authUserId(), $date);

        return $this->view('timetable/index', [
            'title' => __('nav.timetable'),
            'selectedDate' => $date,
            'schedules' => $schedules,
            'activities' => $this->activityRepository->allByUser($this->authUserId()),
            'newSchedule' => $this->defaultSchedule($date),
            'errors' => [],
            'flash' => $this->consumeFlash(),
            'items' => $this->mergeReminderItems($this->timetableService->build($schedules, $date), $reminders, $date),
            'currentOrNext' => $this->currentOrNext($schedules, $date),
        ]);
    }

    public function storeSchedule(): string
    {
        $data = $this->scheduleDataFromRequest();
        $date = $this->dateFromValue((string) ($_POST['date'] ?? ''));
        $errors = $this->validateSchedule($data);

        if ($errors !== []) {
            http_response_code(422);

            $schedules = $this->timetableRepository->schedulesForDate($this->authUserId(), $date);
            $reminders = $this->reminderRepository->activeForDate($this->authUserId(), $date);

            return $this->view('timetable/index', [
                'title' => __('nav.timetable'),
                'selectedDate' => $date,
                'schedules' => $schedules,
                'activities' => $this->activityRepository->allByUser($this->authUserId()),
                'newSchedule' => $data,
                'errors' => $errors,
                'flash' => [],
                'items' => $this->mergeReminderItems($this->timetableService->build($schedules, $date), $reminders, $date),
                'currentOrNext' => $this->currentOrNext($schedules, $date),
            ]);
        }

        $this->scheduleRepository->create($this->authUserId(), $data);
        $this->flash('success', __('flash.schedule_created'));

        return $this->redirect('/timetable?date=' . urlencode($date));
    }

    public function destroySchedule(string $id): string
    {
        $date = $this->dateFromValue((string) ($_POST['date'] ?? $_GET['date'] ?? ''));
        $schedule = $this->scheduleRepository->findByUser((int) $id, $this->authUserId());

        if ($schedule === null) {
            http_response_code(404);
            exit(__('not_found.schedule'));
        }

        $this->scheduleRepository->delete((int) $id, $this->authUserId());
        $this->flash('success', __('flash.schedule_deleted'));

        return $this->redirect('/timetable?date=' . urlencode($date));
    }

    private function scheduleDataFromRequest(): array
    {
        $date = $this->dateFromValue((string) ($_POST['date'] ?? ''));
        $startTime = $this->normalizeTime((string) ($_POST['start_time'] ?? ''));
        $endTime = $this->normalizeTime((string) ($_POST['end_time'] ?? ''));
        $activityId = (int) ($_POST['activity_id'] ?? 0);
        $activity = $activityId > 0 ? $this->activityRepository->findByUser($activityId, $this->authUserId()) : null;
        $title = trim((string) ($_POST['title'] ?? ''));

        return [
            'activity_id' => $activityId,
            'title' => $title !== '' ? $title : (string) ($activity['title'] ?? ''),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_at' => $startTime === null ? null : $date . ' ' . $startTime . ':00',
            'end_at' => $endTime === null ? null : $date . ' ' . $endTime . ':00',
            'status' => 'scheduled',
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];
    }

    private function validateSchedule(array $data): array
    {
        $errors = [];

        if ($data['activity_id'] <= 0 || $this->activityRepository->findByUser((int) $data['activity_id'], $this->authUserId()) === null) {
            $errors['activity_id'] = __('validation.valid_activity');
        }

        if ($data['start_time'] === null) {
            $errors['start_time'] = __('validation.start_time_required');
        }

        if ($data['end_time'] === null) {
            $errors['end_time'] = __('validation.end_time_required');
        }

        if ($data['start_at'] !== null && $data['end_at'] !== null && strtotime((string) $data['end_at']) <= strtotime((string) $data['start_at'])) {
            $errors['end_time'] = __('validation.end_after_start');
        }

        return $errors;
    }

    private function normalizeTime(string $value): ?string
    {
        $value = trim($value);
        $time = DateTimeImmutable::createFromFormat('H:i', $value);

        if ($time === false || $time->format('H:i') !== $value) {
            return null;
        }

        return $time->format('H:i');
    }

    private function defaultSchedule(string $date): array
    {
        return [
            'activity_id' => 0,
            'title' => '',
            'date' => $date,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'start_at' => $date . ' 08:00:00',
            'end_at' => $date . ' 09:00:00',
            'status' => 'scheduled',
            'notes' => '',
        ];
    }

    private function dateFromValue(string $value): string
    {
        $date = trim($value);

        return $this->isDate($date) ? $date : date('Y-m-d');
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
        $now = $this->statusResolver->now();

        if ($date !== $now->format('Y-m-d')) {
            return $schedules[0] ?? null;
        }

        foreach ($schedules as $schedule) {
            $start = $this->statusResolver->dateTime((string) $schedule['start_at']);
            $end = $this->statusResolver->dateTime((string) $schedule['end_at']);

            if ($start <= $now && $end >= $now) {
                return array_merge($schedule, ['focus_state' => 'current']);
            }
        }

        foreach ($schedules as $schedule) {
            $start = $this->statusResolver->dateTime((string) $schedule['start_at']);

            if ($start > $now) {
                return array_merge($schedule, ['focus_state' => 'next']);
            }
        }

        return null;
    }
}
