<?php

namespace App\Middleware;

use App\Core\Middleware\MiddlewareInterface;
use App\Core\Request;

class Authenticate implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (empty($_SESSION['user_id'])) {
            // Redirect to frontend login page (SSO)
            header('Location: http://localhost:8082/page-login.php', true, 302);
            exit;
        }

        return $next($request);
    }
}
