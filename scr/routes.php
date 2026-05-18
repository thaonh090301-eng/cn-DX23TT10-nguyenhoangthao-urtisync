<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\ActivityController;
use App\Controllers\CategoryController;
use App\Core\Router;

/** @var Router $router */
$router->get('/', [HomeController::class, 'index']);

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
