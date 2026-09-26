<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class DashboardController extends Controller
{
    public function admin(Request $request): void
    {
        $this->redirect($this->dashboardPath());
    }

    public function employee(Request $request): void
    {
        $this->redirect($this->dashboardPath());
    }

    public function customer(Request $request): void
    {
        $this->ensureRole(['customer', 'employee', 'admin']);
        $this->view('dashboard/customer');
    }

    private function ensureRole(array $allowedRoles): void
    {
        $role = $_SESSION['user_role'] ?? null;
        if (!in_array($role, $allowedRoles, true)) {
            $this->redirect($this->fallbackPath());
        }
    }

    private function fallbackPath(): string
    {
        $baseUrl = trim($this->config('app.base_url', ''), '/');

        return $baseUrl === '' ? '/' : '/' . $baseUrl;
    }

    private function dashboardPath(): string
    {
        $baseUrl = trim($this->config('app.base_url', ''), '/');
        $path = '/dashboard/pelanggan';
        return $baseUrl === '' ? $path : '/' . $baseUrl . $path;
    }
}
