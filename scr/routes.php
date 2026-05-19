<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ActivityController;
use App\Controllers\AssistantController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
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
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/assistant', [AssistantController::class, 'index']);
$router->post('/assistant', [AssistantController::class, 'generate']);
$router->get('/timetable', [TimetableController::class, 'index']);
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

$router->get('/time-logs', [TimeLogController::class, 'index']);
$router->get('/time-logs/create', [TimeLogController::class, 'create']);
$router->post('/time-logs', [TimeLogController::class, 'store']);
$router->post('/time-logs/schedules/{id}/confirm', [TimeLogController::class, 'confirmSchedule']);
$router->get('/time-logs/{id}/edit', [TimeLogController::class, 'edit']);
$router->put('/time-logs/{id}', [TimeLogController::class, 'update']);
$router->get('/time-logs/{id}/delete', [TimeLogController::class, 'delete']);
$router->delete('/time-logs/{id}', [TimeLogController::class, 'destroy']);
