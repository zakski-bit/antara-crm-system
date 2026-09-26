<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect($this->pathWithBase('/login'));
        }

        $this->redirect($this->pathWithBase('/dashboard/pelanggan'));
        return;
    }

    public function show(Request $request, string $slug): void
    {
        $this->view('home', [
            'title' => 'News Detail',
            'message' => "Showing article with identifier: {$slug}",
        ]);
    }

    private function pathWithBase(string $path): string
    {
        $baseUrl = trim($this->config('app.base_url', ''), '/');

        if ($baseUrl === '') {
            return $path;
        }

        $normalizedPath = '/' . ltrim($path, '/');

        if ($baseUrl === '') {
            return $normalizedPath;
        }

        return '/' . $baseUrl . $normalizedPath;
    }
}
