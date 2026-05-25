<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\ImportantDateRepository;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleStatusResolver;
use DateTimeImmutable;
use Exception;

class ScheduleController extends Controller
{
    private const DEMO_USER_ID = 1;
    private const DISPLAY_STATUSES = [
        ScheduleStatusResolver::SCHEDULED,
        ScheduleStatusResolver::IN_PROGRESS,
        ScheduleStatusResolver::COMPLETED,
    ];

    private ScheduleRepository $schedules;
    private ActivityRepository $activities;
    private ImportantDateRepository $importantDates;
    private ScheduleStatusResolver $statusResolver;

    public function __construct()
    {
        $this->schedules = new ScheduleRepository();
        $this->activities = new ActivityRepository();
        $this->importantDates = new ImportantDateRepository();
        $this->statusResolver = new ScheduleStatusResolver();
    }

    public function index(): string
    {
        $statusFilter = $this->statusFilter();
        $dateFilter = $this->dateFilterFromRequest();
        $userId = $this->authUserId();
        $schedules = $dateFilter['mode'] === 'all'
            ? $this->schedules->allByUser($userId)
            : $this->schedules->forDateByUser($userId, $dateFilter['date']);
        $hasSchedules = $this->schedules->existsByUser($userId);
        $schedules = $this->withDisplayStatus($schedules);

        if ($statusFilter !== 'all') {
            $schedules = array_values(array_filter($schedules, static function (array $schedule) use ($statusFilter): bool {
                return (string) $schedule['display_status'] === $statusFilter;
            }));
        }

        return $this->view('schedules/index', [
            'title' => 'Schedules',
            'schedules' => $schedules,
            'hasSchedules' => $hasSchedules,
            'selectedStatus' => $statusFilter,
            'statusOptions' => self::DISPLAY_STATUSES,
            'dateMode' => $dateFilter['mode'],
            'selectedDate' => $dateFilter['date'],
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function calendar(): string
    {
        $this->requireAuth();

        return $this->view('schedules/calendar', [
            'title' => 'Schedule Calendar',
        ]);
    }

    public function api(): string
    {
        header('Content-Type: application/json; charset=utf-8');

        return json_encode(
            array_merge(
                $this->schedules->calendarEventsByUser($this->authUserId()),
                $this->importantDates->calendarEventsByUser($this->authUserId())
            ),
            JSON_UNESCAPED_UNICODE
        ) ?: '[]';
    }

    public function create(): string
    {
        return $this->view('schedules/create', [
            'title' => 'Create Schedule',
            'schedule' => $this->defaultSchedule(),
            'activities' => $this->activities->allByUser($this->authUserId()),
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->scheduleDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('schedules/create', [
                'title' => 'Create Schedule',
                'schedule' => $data,
                'activities' => $this->activities->allByUser($this->authUserId()),
                'errors' => $errors,
            ]);
        }

        $this->schedules->create($this->authUserId(), $data);
        $this->flash('success', \__('flash.schedule_created'));

        return $this->redirect('/schedules?date=' . urlencode(substr((string) $data['start_at'], 0, 10)));
    }

    public function edit(string $id): string
    {
        $schedule = $this->findScheduleOrFail((int) $id);

        return $this->view('schedules/edit', [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'activities' => $this->activities->allByUser($this->authUserId()),
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $schedule = $this->findScheduleOrFail((int) $id);
        $data = $this->scheduleDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('schedules/edit', [
                'title' => 'Edit Schedule',
                'schedule' => array_merge($schedule, $data),
                'activities' => $this->activities->allByUser($this->authUserId()),
                'errors' => $errors,
            ]);
        }

        $this->schedules->update((int) $id, $this->authUserId(), $data);
        $this->flash('success', \__('flash.schedule_updated'));

        return $this->redirect('/schedules?date=' . urlencode(substr((string) $data['start_at'], 0, 10)));
    }

    public function delete(string $id): string
    {
        return $this->view('schedules/delete', [
            'title' => 'Delete Schedule',
            'schedule' => $this->findScheduleOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findScheduleOrFail((int) $id);
        $this->schedules->delete((int) $id, $this->authUserId());
        $this->flash('success', \__('flash.schedule_deleted'));

        return $this->redirect('/schedules');
    }

    private function scheduleDataFromRequest(): array
    {
        return [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'start_at' => $this->normalizeDateTime((string) ($_POST['start_at'] ?? '')),
            'end_at' => $this->normalizeDateTime((string) ($_POST['end_at'] ?? '')),
            'status' => 'scheduled',
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];
    }

    private function normalizeDateTime(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($value))->format('Y-m-d H:i:s');
        } catch (Exception) {
            return null;
        }
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['activity_id'] <= 0 || $this->activities->findByUser($data['activity_id'], $this->authUserId()) === null) {
            $errors['activity_id'] = \__('validation.valid_activity');
        }

        if ($data['title'] === '') {
            $errors['title'] = \__('validation.schedule_title_required');
        }

        if ($data['start_at'] === null) {
            $errors['start_at'] = \__('validation.start_time_required');
        }

        if ($data['end_at'] === null) {
            $errors['end_at'] = \__('validation.end_time_required');
        }

        if ($data['start_at'] !== null && $data['end_at'] !== null && strtotime($data['end_at']) <= strtotime($data['start_at'])) {
            $errors['end_at'] = \__('validation.end_after_start');
        }

        return $errors;
    }

    private function defaultSchedule(): array
    {
        return [
            'activity_id' => 0,
            'title' => '',
            'start_at' => date('Y-m-d 08:00:00'),
            'end_at' => date('Y-m-d 09:00:00'),
            'status' => 'scheduled',
            'notes' => '',
        ];
    }

    private function statusFilter(): string
    {
        $status = (string) ($_GET['status'] ?? 'all');

        return in_array($status, ['all', ...self::DISPLAY_STATUSES], true) ? $status : 'all';
    }

    private function dateFilterFromRequest(): array
    {
        $hasDateParameter = array_key_exists('date', $_GET);
        $date = trim((string) ($_GET['date'] ?? date('Y-m-d')));

        if ($date === 'all' || ($hasDateParameter && $date === '')) {
            return [
                'mode' => 'all',
                'date' => date('Y-m-d'),
            ];
        }

        return [
            'mode' => 'day',
            'date' => $this->isDate($date) ? $date : date('Y-m-d'),
        ];
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function withDisplayStatus(array $schedules): array
    {
        $now = $this->statusResolver->now();

        return array_map(function (array $schedule) use ($now): array {
            $displayStatus = $this->statusResolver->resolve($schedule, $now);

            $schedule['display_status'] = $displayStatus['key'];
            $schedule['display_status_label'] = \__($displayStatus['label_key']);
            $schedule['display_status_type'] = $displayStatus['type'];

            return $schedule;
        }, $schedules);
    }

    private function findScheduleOrFail(int $id): array
    {
        $schedule = $this->schedules->findByUser($id, $this->authUserId());

        if ($schedule !== null) {
            return $schedule;
        }

        http_response_code(404);
        exit(\__('not_found.schedule'));
    }
}
