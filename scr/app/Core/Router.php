<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array|string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array|string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array|string $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array|string $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, callable|array|string $handler): void
    {
        $this->routes[$method][] = [
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $method = strtoupper($_POST['_method'] ?? $method);
        $path = $this->normalizeRequestPath($uri);

        foreach ($this->routes[$method] ?? [] as $route) {
            $params = $this->match($route['path'], $path);

            if ($params !== null) {
                return $this->call($route['handler'], $params);
            }
        }

        http_response_code(404);

        return '404 Not Found';
    }

    private function call(callable|array|string $handler, array $params = []): mixed
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();

            return call_user_func_array([$controller, $method], $params);
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $class = 'App\\Controllers\\' . $class;
            $controller = new $class();

            return call_user_func_array([$controller, $method], $params);
        }

        return null;
    }

    private function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function normalizeRequestPath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir)) ?: '/';
        }

        return $this->normalizePath($path);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }
}
