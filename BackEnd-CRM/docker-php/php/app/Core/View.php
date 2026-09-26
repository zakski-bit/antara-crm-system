<?php

namespace App\Core;

class View
{
    public static function render(string $template, array $data = []): void
    {
        $path = APP_PATH . '/Views/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("View [{$template}] not found at {$path}");
        }

        extract($data, EXTR_SKIP);
        include $path;
    }
}

