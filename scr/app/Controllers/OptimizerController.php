<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\OptimizerRepository;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleOptimizer;
use DateTimeImmutable;
use Exception;

class OptimizerController extends Controller
{
    private const DEMO_USER_ID = 1;

    private ActivityRepository $activities;
    private OptimizerRepository $optimizerRepository;
    private ScheduleOptimizer $optimizer;
    private ScheduleRepository $schedules;

    public function __construct()
    {
        $this->activities = new ActivityRepository();
        $this->optimizerRepository = new OptimizerRepository();
        $this->optimizer = new ScheduleOptimizer();
        $this->schedules = new ScheduleRepository();
    }

    public function index(): string
    {
        return $this->renderForm($this->defaultInput(), [], [], $this->consumeFlash());
    }

    public function suggest(): string
    {
        $input = $this->inputFromRequest();
        $errors = $this->validateInput($input);
        $suggestions = [];

        if ($errors === []) {
            $activity = $this->activities->findByUser((int) $input['activity_id'], self::DEMO_USER_ID);
            $rangeStart = new DateTimeImmutable($input['range_start'] . ' 00:00:00');
            $rangeEnd = new DateTimeImmutable($input['range_end'] . ' 00:00:00');
            $busySchedules = $this->optimizerRepository->busySchedulesByUser(
                self::DEMO_USER_ID,
                $rangeStart->format('Y-m-d H:i:s'),
                $rangeEnd->format('Y-m-d H:i:s')
            );

            $suggestions = $this->optimizer->suggest(
                $busySchedules,
                $activity,
                $rangeStart,
                $rangeEnd,
                (int) $input['required_minutes'],
                $input['earliest_time'],
                $input['latest_time']
            );
        } else {
            http_response_code(422);
        }

        return $this->renderForm($input, $errors, $suggestions);
    }

    public function createSchedule(): string
    {
        $activityId = (int) ($_POST['activity_id'] ?? 0);
        $startAt = $this->normalizeDateTime((string) ($_POST['start_at'] ?? ''));
        $endAt = $this->normalizeDateTime((string) ($_POST['end_at'] ?? ''));
        $activity = $this->activities->findByUser($activityId, self::DEMO_USER_ID);

        if ($activity === null || $startAt === null || $endAt === null || strtotime($endAt) <= strtotime($startAt)) {
            $this->flash('danger', 'The suggested slot could not be scheduled.');

            return $this->redirect('/optimizer');
        }

        $busySchedules = $this->optimizerRepository->busySchedulesByUser(self::DEMO_USER_ID, $startAt, $endAt);

        if ($busySchedules !== []) {
            $this->flash('danger', 'That slot is no longer free. Please run suggestions again.');

            return $this->redirect('/optimizer');
        }

        $this->schedules->create(self::DEMO_USER_ID, [
            'activity_id' => $activityId,
            'title' => $activity['title'],
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => 'scheduled',
            'notes' => 'Created from optimizer suggestion.',
        ]);

        $this->flash('success', 'Schedule created from suggestion.');

        return $this->redirect('/schedules');
    }

    private function renderForm(array $input, array $errors, array $suggestions, array $flash = []): string
    {
        return $this->view('optimizer/index', [
            'title' => 'Optimizer',
            'input' => $input,
            'errors' => $errors,
            'suggestions' => $suggestions,
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'flash' => $flash,
        ]);
    }

    private function inputFromRequest(): array
    {
        return [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'range_start' => trim((string) ($_POST['range_start'] ?? '')),
            'range_end' => trim((string) ($_POST['range_end'] ?? '')),
            'required_minutes' => (int) ($_POST['required_minutes'] ?? 0),
            'earliest_time' => trim((string) ($_POST['earliest_time'] ?? '08:00')),
            'latest_time' => trim((string) ($_POST['latest_time'] ?? '18:00')),
        ];
    }

    private function defaultInput(): array
    {
        return [
            'activity_id' => 0,
            'range_start' => date('Y-m-d'),
            'range_end' => date('Y-m-d', strtotime('+1 day')),
            'required_minutes' => 30,
            'earliest_time' => '08:00',
            'latest_time' => '18:00',
        ];
    }

    private function validateInput(array $input): array
    {
        $errors = [];

        if ($input['activity_id'] <= 0 || $this->activities->findByUser((int) $input['activity_id'], self::DEMO_USER_ID) === null) {
            $errors['activity_id'] = 'Choose a valid activity.';
        }

        if ($input['required_minutes'] <= 0) {
            $errors['required_minutes'] = 'Duration must be greater than zero.';
        }

        if (!$this->isDate($input['range_start'])) {
            $errors['range_start'] = 'Choose a valid start date.';
        }

        if (!$this->isDate($input['range_end'])) {
            $errors['range_end'] = 'Choose a valid end date.';
        }

        if (!isset($errors['range_start']) && !isset($errors['range_end']) && strtotime($input['range_end']) <= strtotime($input['range_start'])) {
            $errors['range_end'] = 'Range end must be later than range start.';
        }

        if (!$this->isTime($input['earliest_time'])) {
            $errors['earliest_time'] = 'Choose a valid earliest time.';
        }

        if (!$this->isTime($input['latest_time'])) {
            $errors['latest_time'] = 'Choose a valid latest time.';
        }

        if (!isset($errors['earliest_time']) && !isset($errors['latest_time']) && $input['latest_time'] <= $input['earliest_time']) {
            $errors['latest_time'] = 'Latest allowed time must be later than earliest allowed time.';
        }

        return $errors;
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function isTime(string $value): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
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
}
