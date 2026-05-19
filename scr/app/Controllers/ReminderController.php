<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReminderRepository;
use DateTimeImmutable;

class ReminderController extends Controller
{
    private const DEMO_USER_ID = 1;

    private ReminderRepository $reminders;

    public function __construct()
    {
        $this->reminders = new ReminderRepository();
    }

    public function index(): string
    {
        return $this->view('reminders/index', [
            'title' => __('nav.reminders'),
            'reminders' => $this->reminders->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('reminders/create', [
            'title' => __('page.create_reminder'),
            'reminder' => $this->defaultReminder(),
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->reminderDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('reminders/create', [
                'title' => __('page.create_reminder'),
                'reminder' => $data,
                'errors' => $errors,
            ]);
        }

        $this->reminders->create(self::DEMO_USER_ID, $data);
        $this->flash('success', __('flash.reminder_created'));

        return $this->redirect('/reminders');
    }

    public function edit(string $id): string
    {
        return $this->view('reminders/edit', [
            'title' => __('page.edit_reminder'),
            'reminder' => $this->findReminderOrFail((int) $id),
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $reminder = $this->findReminderOrFail((int) $id);
        $data = $this->reminderDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('reminders/edit', [
                'title' => __('page.edit_reminder'),
                'reminder' => array_merge($reminder, $data),
                'errors' => $errors,
            ]);
        }

        $this->reminders->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', __('flash.reminder_updated'));

        return $this->redirect('/reminders');
    }

    public function toggle(string $id): string
    {
        $reminder = $this->findReminderOrFail((int) $id);
        $this->reminders->setActive((int) $id, self::DEMO_USER_ID, (int) $reminder['is_active'] !== 1);
        $this->flash('success', __('flash.reminder_toggled'));

        return $this->redirect('/reminders');
    }

    public function delete(string $id): string
    {
        return $this->view('reminders/delete', [
            'title' => __('page.delete_reminder'),
            'reminder' => $this->findReminderOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findReminderOrFail((int) $id);
        $this->reminders->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', __('flash.reminder_deleted'));

        return $this->redirect('/reminders');
    }

    public function apiToday(): string
    {
        header('Content-Type: application/json; charset=utf-8');
        $today = date('Y-m-d');
        $reminders = array_map(static function (array $reminder) use ($today): array {
            return [
                'id' => (int) $reminder['id'],
                'title' => $reminder['title'],
                'note' => $reminder['note'],
                'remind_at' => $today . 'T' . substr((string) $reminder['remind_time'], 0, 8),
            ];
        }, $this->reminders->activeForDate(self::DEMO_USER_ID, $today));

        return (string) json_encode(['reminders' => $reminders], JSON_UNESCAPED_UNICODE);
    }

    private function reminderDataFromRequest(): array
    {
        $repeatType = trim((string) ($_POST['repeat_type'] ?? 'daily'));
        $repeatType = in_array($repeatType, ['none', 'daily', 'weekly'], true) ? $repeatType : 'daily';
        $dayOfWeek = ($_POST['day_of_week'] ?? '') === '' ? null : (int) $_POST['day_of_week'];

        if ($repeatType !== 'weekly') {
            $dayOfWeek = null;
        }

        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'note' => trim((string) ($_POST['note'] ?? '')),
            'remind_time' => $this->normalizeTime((string) ($_POST['remind_time'] ?? '')),
            'repeat_type' => $repeatType,
            'day_of_week' => $dayOfWeek,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors['title'] = __('validation.reminder_title_required');
        }

        if ($data['remind_time'] === null) {
            $errors['remind_time'] = __('validation.valid_reminder_time');
        }

        if ($data['repeat_type'] === 'weekly' && ($data['day_of_week'] === null || $data['day_of_week'] < 0 || $data['day_of_week'] > 6)) {
            $errors['day_of_week'] = __('validation.valid_day_of_week');
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

        return $time->format('H:i:s');
    }

    private function defaultReminder(): array
    {
        return [
            'title' => '',
            'note' => '',
            'remind_time' => '09:00:00',
            'repeat_type' => 'daily',
            'day_of_week' => null,
            'is_active' => 1,
        ];
    }

    private function findReminderOrFail(int $id): array
    {
        $reminder = $this->reminders->findByUser($id, self::DEMO_USER_ID);

        if ($reminder !== null) {
            return $reminder;
        }

        http_response_code(404);
        exit(__('not_found.reminder'));
    }
}
