<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\CategoryRepository;

class ActivityController extends Controller
{
    private const DEMO_USER_ID = 1;
    private const PRIORITIES = ['low', 'medium', 'high'];

    private ActivityRepository $activities;
    private CategoryRepository $categories;

    public function __construct()
    {
        $this->activities = new ActivityRepository();
        $this->categories = new CategoryRepository();
    }

    public function index(): string
    {
        return $this->view('activities/index', [
            'title' => 'Activities',
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('activities/create', [
            'title' => 'Create Activity',
            'activity' => $this->defaultActivity(),
            'categories' => $this->categories->allByUser(self::DEMO_USER_ID),
            'priorities' => self::PRIORITIES,
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->activityDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('activities/create', [
                'title' => 'Create Activity',
                'activity' => $data,
                'categories' => $this->categories->allByUser(self::DEMO_USER_ID),
                'priorities' => self::PRIORITIES,
                'errors' => $errors,
            ]);
        }

        $this->activities->create(self::DEMO_USER_ID, $data);
        $this->flash('success', 'Activity created successfully.');

        return $this->redirect('/activities');
    }

    public function edit(string $id): string
    {
        $activity = $this->findActivityOrFail((int) $id);

        return $this->view('activities/edit', [
            'title' => 'Edit Activity',
            'activity' => $activity,
            'categories' => $this->categories->allByUser(self::DEMO_USER_ID),
            'priorities' => self::PRIORITIES,
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $activity = $this->findActivityOrFail((int) $id);
        $data = $this->activityDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('activities/edit', [
                'title' => 'Edit Activity',
                'activity' => array_merge($activity, $data),
                'categories' => $this->categories->allByUser(self::DEMO_USER_ID),
                'priorities' => self::PRIORITIES,
                'errors' => $errors,
            ]);
        }

        $this->activities->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', 'Activity updated successfully.');

        return $this->redirect('/activities');
    }

    public function delete(string $id): string
    {
        return $this->view('activities/delete', [
            'title' => 'Delete Activity',
            'activity' => $this->findActivityOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findActivityOrFail((int) $id);
        $this->activities->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', 'Activity deleted successfully.');

        return $this->redirect('/activities');
    }

    private function activityDataFromRequest(): array
    {
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'priority' => (string) ($_POST['priority'] ?? 'medium'),
            'estimated_minutes' => (int) ($_POST['estimated_minutes'] ?? 30),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors['title'] = 'Activity title is required.';
        }

        if ($data['category_id'] <= 0 || $this->categories->findByUser($data['category_id'], self::DEMO_USER_ID) === null) {
            $errors['category_id'] = 'Choose a valid category.';
        }

        if (!in_array($data['priority'], self::PRIORITIES, true)) {
            $errors['priority'] = 'Choose a valid priority.';
        }

        if ($data['estimated_minutes'] <= 0) {
            $errors['estimated_minutes'] = 'Estimated minutes must be greater than zero.';
        }

        return $errors;
    }

    private function defaultActivity(): array
    {
        return [
            'category_id' => 0,
            'title' => '',
            'description' => '',
            'priority' => 'medium',
            'estimated_minutes' => 30,
            'is_active' => 1,
        ];
    }

    private function findActivityOrFail(int $id): array
    {
        $activity = $this->activities->findByUser($id, self::DEMO_USER_ID);

        if ($activity !== null) {
            return $activity;
        }

        http_response_code(404);
        exit('Activity not found.');
    }
}
