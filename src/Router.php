<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App;

use App\Http\Request;
use Closure;

final class Router
{
    /** @var list<array{methods: list<string>, pattern: string, regex: string, handler: Closure|array{0: class-string, 1: string}, middleware: list<string>}>> */
    private array $routes = [];

    public function get(string $pattern, Closure|array $handler, array $middleware = []): void
    {
        $this->addRoute(['GET'], $pattern, $handler, $middleware);
    }

    public function post(string $pattern, Closure|array $handler, array $middleware = []): void
    {
        $this->addRoute(['POST'], $pattern, $handler, $middleware);
    }

    public function match(array $methods, string $pattern, Closure|array $handler, array $middleware = []): void
    {
        $this->addRoute($methods, $pattern, $handler, $middleware);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            $_REQUEST['_route_params'] = $params;

            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw);
            }

            $handler = $route['handler'];
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->{$action}($request, $params);
            } else {
                $handler($request, $params);
            }

            return;
        }

        http_response_code(404);
        view('errors/404', ['path' => $path], 'auth');
    }

    private function addRoute(array $methods, string $pattern, Closure|array $handler, array $middleware): void
    {
        $regex = $this->patternToRegex($pattern);
        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    private function patternToRegex(string $pattern): string
    {
        $normalized = rtrim($pattern, '/') ?: '/';
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static fn (array $m): string => '(?P<' . $m[1] . '>[^/]+)',
            $normalized
        );

        return '#^' . $regex . '$#';
    }

    private function runMiddleware(string $name): void
    {
        match ($name) {
            'guest' => \App\Middleware\GuestMiddleware::handle(),
            'auth' => \App\Middleware\AuthMiddleware::handle(),
            'aluno' => \App\Middleware\RoleMiddleware::handle(['aluno']),
            'staff'      => \App\Middleware\RoleMiddleware::handle(['professor', 'coordenador']),
            'admin_only' => \App\Middleware\RoleMiddleware::handle(['coordenador'], true),
            'parceiro'   => \App\Middleware\RoleMiddleware::handle(['parceiro']),
            default      => null,
        };
    }
}
