<?php

declare(strict_types=1);

namespace App;

use Closure;

final class Router
{
    /** @var array<string, array<string, Closure>> */
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, Closure $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler instanceof Closure) {
            http_response_code(404);
            view('errors/404', ['path' => $path]);
            return;
        }

        $handler();
    }

    private function addRoute(string $method, string $path, Closure $handler): void
    {
        $normalizedPath = rtrim($path, '/') ?: '/';
        $this->routes[$method][$normalizedPath] = $handler;
    }
}
