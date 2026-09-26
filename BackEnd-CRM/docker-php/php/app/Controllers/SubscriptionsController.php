<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\ClientRepository;
use App\Repositories\ClientSubscriptionRepository;
use PDO;

class SubscriptionsController extends Controller
{
    private ClientSubscriptionRepository $subscriptions;
    private ClientRepository $clients;

    private array $statusOptions = ['active', 'trial', 'pending', 'cancelled', 'paused', 'ended'];
    private array $cycleOptions = ['monthly', 'yearly', 'quarterly', 'weekly', 'lifetime'];

    public function __construct()
    {
        $this->subscriptions = new ClientSubscriptionRepository();
        $this->clients = new ClientRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $cycle = trim((string) $request->query('cycle', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, (int) $request->query('per_page', 25));

        $result = $this->subscriptions->paginate($userId, $perPage, $page, $search !== '' ? $search : null, $status !== '' ? $status : null, $cycle !== '' ? $cycle : null);

        $this->view('subscriptions/index', [
            'subscriptions' => $result['data'],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'cycle' => $cycle,
                'page' => $page,
                'per_page' => $perPage,
                'total' => $result['total'],
                'total_pages' => max(1, (int) ceil($result['total'] / $result['per_page'])),
            ],
            'statusOptions' => $this->statusOptions,
            'cycleOptions' => $this->cycleOptions,
            'clients' => $this->clients->all($userId, 200),
            'customers' => $this->customerUsers(),
            'baseUrl' => $this->pathWithBase(''),
            'flash' => $this->flash(),
            'errors' => $this->flashErrors(),
            'old' => $this->flashOld(),
        ]);
    }

    public function store(Request $request): void
    {
        $userId = $this->currentUserId();

        $input = [
            'customer_user_id' => (int) $request->input('customer_user_id', 0) ?: null,
            'plan_name' => trim((string) $request->input('plan_name')),
            'plan_code' => trim((string) $request->input('plan_code', '')),
            'cycle' => trim((string) $request->input('cycle', 'monthly')),
            'amount' => (float) $request->input('amount', 0),
            'currency_code' => strtoupper(trim((string) $request->input('currency_code', 'IDR'))),
            'status' => trim((string) $request->input('status', 'active')),
            'started_at' => $this->normalizeDate($request->input('started_at')),
            'renewal_at' => $this->normalizeDate($request->input('renewal_at')),
            'ends_at' => $this->normalizeDate($request->input('ends_at')),
            'notes' => trim((string) $request->input('notes', '')),
        ];

        $errors = $this->validateInput($input);

        if (!empty($errors)) {
            $this->flashErrors($errors);
            $this->flashOld($input);
            $this->flashMessage('error', 'Gagal menyimpan langganan.');
            $this->redirect($this->url('/subscriptions'));
        }

        $data = [
            'customer_user_id' => $input['customer_user_id'],
            'client_id' => null,
            'plan_name' => $input['plan_name'],
            'plan_code' => $input['plan_code'] !== '' ? $input['plan_code'] : null,
            'cycle' => $input['cycle'],
            'amount' => $input['amount'],
            'currency_code' => $input['currency_code'] !== '' ? $input['currency_code'] : 'IDR',
            'status' => $input['status'],
            'started_at' => $input['started_at'],
            'renewal_at' => $input['renewal_at'],
            'ends_at' => $input['ends_at'],
            'notes' => $input['notes'] !== '' ? $input['notes'] : null,
        ];

        try {
            $this->subscriptions->create($userId, $data);
            $this->flashMessage('success', 'Langganan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            $this->flashMessage('error', 'Terjadi kesalahan saat menyimpan langganan.');
            $this->flashOld($input);
        }

        $this->redirect($this->pathWithBase('/subscriptions'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $this->subscriptions->delete($userId, $id);
        $this->flashMessage('success', 'Langganan dihapus.');
        $this->redirect($this->pathWithBase('/subscriptions'));
    }

    private function validateInput(array $input): array
    {
        $errors = [];
        if ($input['customer_user_id'] === null) {
            $errors['customer_user_id'] = 'Pilih akun pelanggan.';
        }
        if ($input['plan_name'] === '') {
            $errors['plan_name'] = 'Nama paket wajib diisi.';
        }
        if (!in_array($input['cycle'], $this->cycleOptions, true)) {
            $errors['cycle'] = 'Siklus tidak valid.';
        }
        if (!in_array($input['status'], $this->statusOptions, true)) {
            $errors['status'] = 'Status tidak valid.';
        }
        if ($input['amount'] < 0) {
            $errors['amount'] = 'Nominal tidak boleh negatif.';
        }
        return $errors;
    }

    private function normalizeDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        try {
            $dt = new \DateTimeImmutable($value);
            return $dt->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function customerUsers(): array
    {
        $stmt = $this->db()->prepare("
            SELECT id, name, email
            FROM users
            WHERE role = 'customer'
            ORDER BY name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    private function db(): PDO
    {
        return \App\Core\Database::connection();
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function pathWithBase(string $path): string
    {
        $trimmed = trim($path, '/');
        $base = '';
        if (class_exists('\App\Core\Config')) {
            $base = trim(\App\Core\Config::get('app.base_url', ''), '/');
        }
        $prefix = $base === '' ? '' : '/' . $base;
        return $prefix . ($trimmed === '' ? '' : '/' . $trimmed);
    }

    // Simple flash helpers (session-based)
    private function flash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    private function flashMessage(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    private function flashErrors(array $errors = []): array
    {
        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            return $errors;
        }
        $errors = $_SESSION['flash_errors'] ?? [];
        unset($_SESSION['flash_errors']);
        return $errors;
    }

    private function flashOld(array $old = []): array
    {
        if (!empty($old)) {
            $_SESSION['flash_old'] = $old;
            return $old;
        }
        $old = $_SESSION['flash_old'] ?? [];
        unset($_SESSION['flash_old']);
        return $old;
    }
}

