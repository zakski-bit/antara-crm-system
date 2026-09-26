<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class TemplateController extends Controller
{
    public function show(Request $request, string $template): void
    {
        $template = (string) preg_replace('/\.php$/i', '', $template);

        if ($template === 'profile-settings') {
            $this->redirect($this->pathWithBase('/my-info'));
            return;
        }

        if (!preg_match('/^[a-z0-9\-]+$/i', $template)) {
            $this->notFound();
            return;
        }

        if (in_array($template, ['index', 'dashboard', 'dashboard-pegawai'], true)) {
            $this->redirect($this->pathWithBase('/dashboard/pelanggan'));
            return;
        }

        $file = BASE_PATH . '/template/src/' . $template . '.php';
        if (!is_file($file)) {
            $fallback = BASE_PATH . '/template/src/under-construction.php';
            if (is_file($fallback)) {
                $file = $fallback;
            } else {
                $this->notFound();
                return;
            }
        }

        $virtualSelf = '/' . ltrim($template, '/');
        if (substr($virtualSelf, -4) !== '.php') {
            $virtualSelf .= '.php';
        }

        $originalPhpSelf = $_SERVER['PHP_SELF'] ?? null;
        $_SERVER['PHP_SELF'] = $virtualSelf;

        try {
            include $file;
        } finally {
            if ($originalPhpSelf === null) {
                unset($_SERVER['PHP_SELF']);
            } else {
                $_SERVER['PHP_SELF'] = $originalPhpSelf;
            }
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
    }

    private function pathWithBase(string $path): string
    {
        $baseUrl = trim($this->config('app.base_url', ''), '/');

        if ($baseUrl === '') {
            return '/' . ltrim($path, '/');
        }

        return '/' . $baseUrl . '/' . ltrim($path, '/');
    }
}
