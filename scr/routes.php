<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ActivityController;
use App\Controllers\AssistantController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\FocusController;
use App\Controllers\ImportantDateController;
use App\Controllers\LanguageController;
use App\Controllers\OptimizerController;
use App\Controllers\ReminderController;
use App\Controllers\ScheduleController;
use App\Controllers\TimeLogController;
use App\Controllers\TimetableController;
use App\Core\Router;

/** @var Router $router */
$router->get('/', [HomeController::class, 'index']);
$router->get('/lang', [LanguageController::class, 'change']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/account', [AuthController::class, 'account']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/assistant', [AssistantController::class, 'index']);
$router->post('/assistant', [AssistantController::class, 'generate']);
$router->get('/focus', [FocusController::class, 'index']);
$router->post('/focus', [FocusController::class, 'store']);
$router->get('/timetable', [TimetableController::class, 'index']);
$router->post('/timetable/schedules', [TimetableController::class, 'storeSchedule']);
$router->delete('/timetable/schedules/{id}', [TimetableController::class, 'destroySchedule']);
$router->get('/optimizer', [OptimizerController::class, 'index']);
$router->post('/optimizer', [OptimizerController::class, 'suggest']);
$router->post('/optimizer/schedule', [OptimizerController::class, 'createSchedule']);

$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories', [CategoryController::class, 'store']);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit']);
$router->put('/categories/{id}', [CategoryController::class, 'update']);
$router->get('/categories/{id}/delete', [CategoryController::class, 'delete']);
$router->delete('/categories/{id}', [CategoryController::class, 'destroy']);

$router->get('/activities', [ActivityController::class, 'index']);
$router->get('/activities/create', [ActivityController::class, 'create']);
$router->post('/activities', [ActivityController::class, 'store']);
$router->get('/activities/{id}/edit', [ActivityController::class, 'edit']);
$router->put('/activities/{id}', [ActivityController::class, 'update']);
$router->get('/activities/{id}/delete', [ActivityController::class, 'delete']);
$router->delete('/activities/{id}', [ActivityController::class, 'destroy']);

$router->get('/schedules', [ScheduleController::class, 'index']);
$router->get('/schedules/calendar', [ScheduleController::class, 'calendar']);
$router->get('/calendar', [ScheduleController::class, 'calendar']);
$router->get('/schedules/create', [ScheduleController::class, 'create']);
$router->post('/schedules', [ScheduleController::class, 'store']);
$router->get('/schedules/{id}/edit', [ScheduleController::class, 'edit']);
$router->put('/schedules/{id}', [ScheduleController::class, 'update']);
$router->get('/schedules/{id}/delete', [ScheduleController::class, 'delete']);
$router->delete('/schedules/{id}', [ScheduleController::class, 'destroy']);

$router->get('/api/schedules', [ScheduleController::class, 'api']);
$router->get('/api/reminders/today', [ReminderController::class, 'apiToday']);

$router->get('/reminders', [ReminderController::class, 'index']);
$router->get('/reminders/create', [ReminderController::class, 'create']);
$router->post('/reminders', [ReminderController::class, 'store']);
$router->post('/reminders/{id}/toggle', [ReminderController::class, 'toggle']);
$router->get('/reminders/{id}/edit', [ReminderController::class, 'edit']);
$router->put('/reminders/{id}', [ReminderController::class, 'update']);
$router->get('/reminders/{id}/delete', [ReminderController::class, 'delete']);
$router->delete('/reminders/{id}', [ReminderController::class, 'destroy']);

$router->get('/important-dates', [ImportantDateController::class, 'index']);
$router->get('/important-dates/create', [ImportantDateController::class, 'create']);
$router->post('/important-dates', [ImportantDateController::class, 'store']);
$router->get('/important-dates/{id}/edit', [ImportantDateController::class, 'edit']);
$router->put('/important-dates/{id}', [ImportantDateController::class, 'update']);
$router->get('/important-dates/{id}/delete', [ImportantDateController::class, 'delete']);
$router->delete('/important-dates/{id}', [ImportantDateController::class, 'destroy']);

$router->get('/time-logs', [TimeLogController::class, 'index']);
