<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm(Request $request): void
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        $this->view('auth/login', [
            'error' => $flash['error'] ?? null,
            'old' => $flash['old'] ?? [],
        ]);
    }

    public function login(Request $request): void
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');

        $rememberMe = (bool)$request->input('remember_me');

        if ($email === '' || $password === '') {
            $this->sendBack('Email dan kata sandi wajib diisi.', [
                'email' => $email,
                'remember_me' => $rememberMe,
            ]);
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->sendBack('Kombinasi email dan kata sandi tidak valid.', [
                'email' => $email,
                'remember_me' => $rememberMe,
            ]);
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        if ($rememberMe) {
            $_SESSION['remember_login'] = true;
        } else {
            unset($_SESSION['remember_login']);
        }

        $this->redirect($this->dashboardPath($user['role']));
    }

    public function logout(Request $request): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->redirect($this->loginPath());
    }

    private function sendBack(string $message, array $oldInput = []): void
    {
        $_SESSION['flash'] = [
            'error' => $message,
            'old' => $oldInput,
        ];

        $this->redirect($this->loginPath());
    }

    private function loginPath(): string
    {
        return $this->pathWithBase('/login');
    }

    private function dashboardPath(string $role): string
    {
        return $this->pathWithBase('/dashboard/pelanggan');
    }

    private function pathWithBase(string $path): string
    {
        $baseUrl = trim(Config::get('app.base_url', ''), '/');

        if ($baseUrl === '') {
            return '/' . ltrim($path, '/');
        }

        return '/' . $baseUrl . '/' . ltrim($path, '/');
    }
}
