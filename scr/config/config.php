<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__, 2);
$envPath = $rootPath . DIRECTORY_SEPARATOR . '.env';

if (is_readable($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

$env = static function (string $key, mixed $default = null): mixed {
    $value = getenv($key);

    if ($value === false) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    return $value;
};

return [
    'app' => [
        'name' => 'Personal Time Optimizer',
        'env' => $env('APP_ENV', 'local'),
        'debug' => filter_var($env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
        'url' => rtrim((string) $env('APP_URL', 'http://localhost/personal-time-optimizer/scr/public'), '/'),
    ],
    'database' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => (int) $env('DB_PORT', 3306),
        'name' => $env('DB_DATABASE', 'personal_time_optimizer'),
        'username' => $env('DB_USERNAME', 'root'),
        'password' => $env('DB_PASSWORD', ''),
        'charset' => $env('DB_CHARSET', 'utf8mb4'),
    ],
];
