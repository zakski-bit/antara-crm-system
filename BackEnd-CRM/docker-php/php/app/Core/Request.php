<?php

namespace App\Core;

class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;
    private array $routeParams = [];

    private function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $basePath = trim(Config::get('app.base_url', ''), '/');
        if ($basePath !== '') {
            $prefix = '/' . $basePath;
            if (substr($rawPath, 0, strlen($prefix)) === $prefix) {
                $rawPath = substr($rawPath, strlen($prefix));
                if ($rawPath === '' || $rawPath === false) {
                    $rawPath = '/';
                }
            }
        }
        $this->path = $this->normalize($rawPath);
        $this->query = $_GET ?? [];
        $this->body = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->headers = $this->extractHeaders();

        if ($this->method === 'POST') {
            $override = $this->body['_method'] ?? $this->header('X-Http-Method-Override');
            if ($override) {
                $this->method = strtoupper($override);
                unset($this->body['_method']);
            }
        }
    }

    public static function capture(): self
    {
        return new self();
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key = null, $default = null)
    {
        return $key === null ? $this->query : ($this->query[$key] ?? $default);
    }

    public function input(string $key = null, $default = null)
    {
        $data = $this->all();
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, $default = null)
    {
        $normalized = strtolower($key);
        foreach ($this->headers as $name => $value) {
            if (strtolower($name) === $normalized) {
                return $value;
            }
        }
        return $default;
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function route(string $key = null, $default = null)
    {
        return $key === null ? $this->routeParams : ($this->routeParams[$key] ?? $default);
    }

    private function extractHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }
        $normalized = '/' . trim($path, '/');
        return $normalized === '//' ? '/' : $normalized;
    }
}
