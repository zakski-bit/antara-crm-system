<?php

namespace App\Middleware;

use App\Core\Config;
use App\Core\Middleware\MiddlewareInterface;
use App\Core\Request;

class EnsureStaff implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $role = $_SESSION['user_role'] ?? null;
        if (!in_array($role, ['admin', 'employee'], true)) {
            header('Location: ' . $this->fallbackPath(), true, 302);
            exit;
        }

        return $next($request);
    }

    private function fallbackPath(): string
    {
        $baseUrl = trim((string) Config::get('app.base_url', ''), '/');
        $path = '/dashboard/pelanggan';

        if ($baseUrl === '') {
            return $path;
        }

        return '/' . $baseUrl . $path;
    }
}
