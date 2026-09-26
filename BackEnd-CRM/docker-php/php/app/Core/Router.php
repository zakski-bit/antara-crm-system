<?php

namespace App\Core;

use App\Core\Middleware\MiddlewareInterface;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    private array $groupStack = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function patch(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $parent = end($this->groupStack) ?: ['prefix' => '', 'middleware' => []];
        $prefix = $this->joinPaths($parent['prefix'] ?? '', $attributes['prefix'] ?? '');
        $middleware = array_merge($parent['middleware'] ?? [], (array)($attributes['middleware'] ?? []));

        $this->groupStack[] = [
            'prefix' => $prefix,
            'middleware' => $middleware,
        ];

        $callback($this);

        array_pop($this->groupStack);
    }

    public function dispatch(Request $request): void
    {
        $methodRoutes = $this->routes[$request->method()] ?? [];

        foreach ($methodRoutes as $route) {
            if (preg_match($route['pattern'], $request->path(), $matches)) {
                $params = [];
                foreach ($route['parameterNames'] as $index => $name) {
                    $params[$name] = $matches[$index + 1];
                }
                $request->setRouteParams($params);

                $this->runMiddleware($route['middleware'], $request, function (Request $request) use ($route, $params) {
                    $this->invokeHandler($route['handler'], $request, $params);
                });
                return;
            }
        }

        if ($template = $this->fallbackTemplate($request->path())) {
            if (class_exists('\App\Controllers\TemplateController')) {
                $controller = new \App\Controllers\TemplateController();
                $controller->show($request, $template);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function fallbackTemplate(string $path): ?string
    {
        $path = trim($path, '/');
        if ($path === '') {
            return null;
        }

        $segments = explode('/', $path);
        $last = end($segments);
        if ($last === false || $last === '') {
            return null;
        }

        if (strpos($last, '.') !== false && !preg_match('/\.php$/i', $last)) {
            return null;
        }

        $slug = preg_replace('/\.php$/i', '', $last);
        if (!preg_match('/^[a-z0-9\-]+$/i', $slug)) {
            return null;
        }

        return $slug;
    }

    private function addRoute(string $method, string $path, $handler, array $middleware = []): void
    {
        $group = end($this->groupStack) ?: ['prefix' => '', 'middleware' => []];
        $fullPath = $this->normalize($this->joinPaths($group['prefix'] ?? '', $path));

        [$pattern, $parameterNames] = $this->compileRoute($fullPath);

        $this->routes[$method][] = [
            'path' => $fullPath,
            'pattern' => $pattern,
            'parameterNames' => $parameterNames,
            'handler' => $handler,
            'middleware' => array_merge($group['middleware'] ?? [], $middleware),
        ];
    }

    private function compileRoute(string $path): array
    {
        $parameterNames = [];

        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', function ($matches) use (&$parameterNames) {
            $parameterNames[] = $matches[1];
            return '([^/]+)';
        }, $path);

        $pattern = '#^' . $pattern . '$#u';

        return [$pattern, $parameterNames];
    }

    private function runMiddleware(array $middleware, Request $request, callable $destination): void
    {
        $pipeline = array_reduce(
            array_reverse($middleware),
            function ($next, $middleware) {
                return function (Request $request) use ($next, $middleware) {
                    if (is_string($middleware) && class_exists($middleware)) {
                        $middleware = new $middleware();
                    }

                    if ($middleware instanceof MiddlewareInterface) {
                        return $middleware->handle($request, $next);
                    }

                    if (is_callable($middleware)) {
                        return $middleware($request, $next);
                    }

                    throw new \RuntimeException('Invalid middleware provided to the router.');
                };
            },
            $destination
        );

        $pipeline($request);
    }

    private function invokeHandler($handler, Request $request, array $params): void
    {
        $arguments = array_merge([$request], array_values($params));

        if (is_array($handler)) {
            [$class, $action] = $handler;
            if (!class_exists($class)) {
                throw new \RuntimeException("Controller {$class} not found.");
            }

            $instance = new $class();

            if (!method_exists($instance, $action)) {
                throw new \RuntimeException("Method {$action} not found in controller {$class}.");
            }

            $instance->{$action}(...$arguments);
            return;
        }

        if (is_callable($handler)) {
            $handler(...$arguments);
            return;
        }

        throw new \RuntimeException('Route handler is not callable.');
    }

    private function joinPaths(string $base, string $path): string
    {
        $base = trim($base, '/');
        $path = trim($path, '/');

        if ($base === '' && $path === '') {
            return '/';
        }

        if ($base === '') {
            return '/' . $path;
        }

        if ($path === '') {
            return '/' . $base;
        }

        return '/' . $base . '/' . $path;
    }

    private function normalize(string $path): string
    {
        return $path === '' ? '/' : ($path === '//' ? '/' : $path);
    }
}
