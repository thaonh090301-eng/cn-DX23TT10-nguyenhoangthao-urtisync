<?php

declare(strict_types=1);

use App\Core\Router;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDirectory = APP_PATH . DIRECTORY_SEPARATOR;

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDirectory . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'helpers.php';

\App\Core\Lang::boot(BASE_PATH);

$router = new Router();

require BASE_PATH . DIRECTORY_SEPARATOR . 'routes.php';

$response = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

if (is_string($response)) {
    echo $response;
}
