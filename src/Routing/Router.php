<?php

declare(strict_types=1);

namespace App\Routing;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $handler = $this->routes[$method][$path] ?? null;
        $parameters = [];

        if ($handler === null) {
            foreach ($this->routes[$method] ?? [] as $route => $candidate) {
                if (!str_contains($route, '{id}')) {
                    continue;
                }

                $pattern = '#^' . str_replace('{id}', '(?P<id>[0-9]+)', $route) . '$#';
                if (preg_match($pattern, $path, $matches) === 1) {
                    $handler = $candidate;
                    $parameters = ['id' => (int) $matches['id']];
                    break;
                }
            }
        }

        if ($handler === null) {
            http_response_code(404);
            return 'Page not found';
        }

        return $handler(...$parameters);
    }
}
