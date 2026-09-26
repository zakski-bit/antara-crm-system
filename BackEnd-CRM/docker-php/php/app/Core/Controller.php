<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function config(string $key, $default = null)
    {
        return Config::get($key, $default);
    }

    protected function redirect(string $url, int $status = 302): void
    {
        header("Location: {$url}", true, $status);
        exit;
    }

    protected function json($data, int $status = 200): void
    {
        header('Content-Type: application/json', true, $status);
        echo json_encode($data);
    }
}
