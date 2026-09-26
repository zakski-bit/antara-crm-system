<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\ClientRepository;

class ClientsController extends Controller
{
    private ClientRepository $repository;

    private array $allowedRedirects = [
        'clients' => '/crm/clients',
        'clients-grid' => '/crm/clients/grid',
    ];

    private array $allowedStatuses = [
        'active',
        'inactive',
        'prospect',
        'pending',
        'archived',
    ];

    public function __construct()
    {
        $this->repository = new ClientRepository();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once BASE_PATH . '/template/app/functions.php';
    }

    public function index(Request $request): void
    {
        [$clients, $stats, $loadError] = $this->loadClients();

        $this->view('clients/index', [
            'clients' => array_map([$this, 'formatClient'], $clients),
            'client_stats' => $stats,
            'clients_error' => $loadError,
            'baseUrl' => $this->baseUrl(),
        ]);
    }

    public function grid(Request $request): void
    {
        [$clients, $stats, $loadError] = $this->loadClients();

        $this->view('clients/grid', [
            'clients' => array_map([$this, 'formatClient'], $clients),
            'client_stats' => $stats,
            'clients_error' => $loadError,
            'baseUrl' => $this->baseUrl(),
        ]);
    }

    public function handle(Request $request): void
    {
        $action = $request->input('_action', '');
        $target = $this->redirectPath($request->input('_redirect', 'clients'));

        if (!$this->validateCsrf($request->input('_token'))) {
            $this->flash('client_error', 'Sesi kedaluwarsa. Silakan coba lagi.');
            $this->redirect($target);
        }

        switch ($action) {
            case 'client_create':
                $this->processCreate($request, $target);
                break;

            case 'client_update':
                $this->processUpdate($request, $target);
                break;

            case 'client_delete':
                $this->processDelete($request, $target);
                break;

            default:
                $this->flash('client_error', 'Permintaan tidak valid.');
                $this->redirect($target);
        }
    }

    private function processCreate(Request $request, string $redirect): void
    {
        $userId = $this->currentUserId();
        $input = $this->collectClientInput($request);
        $password = (string) $request->input('password', '');
        $confirmPassword = (string) $request->input('confirm_password', '');

        $errors = $this->validateClient($userId, $input, $password, $confirmPassword, false);

        $avatarPath = null;
        $avatar = $request->file('avatar');
        if ($avatar && ($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $avatarPath = $this->handleAvatarUpload($avatar, $errors);
        }

        if (!empty($errors)) {
            $this->flash('client_error', reset($errors) ?: 'Periksa kembali data yang Anda masukkan.');
            $this->flash('client_form_errors', $errors);
            $this->flash('client_form_old', $input);
            $this->redirect($redirect);
        }

        $fullName = trim($input['first_name'] . ' ' . $input['last_name']);
        if ($fullName === '') {
            $fullName = $input['first_name'];
        }

        $code = $this->generateClientCode($input['first_name'], $input['last_name']);

        $passwordValue = $password !== '' ? $password : bin2hex(random_bytes(6));

        $data = [
            'code' => $code,
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'] !== '' ? $input['last_name'] : null,
            'name' => $fullName,
            'username' => $input['username'],
            'email' => $input['email'],
            'company' => $input['company'] !== '' ? $input['company'] : null,
            'phone' => $input['phone'],
            'status' => $input['status'],
            'job_title' => $input['job_title'] !== '' ? $input['job_title'] : 'Client',
            'position' => $input['job_title'] !== '' ? $input['job_title'] : 'Client',
            'password_hash' => password_hash($passwordValue, PASSWORD_DEFAULT),
            'avatar_path' => $avatarPath ?? 'assets/img/users/user-01.jpg',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'notes' => json_encode([
                'created_via_form' => true,
                'submitted_at' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format(\DateTimeInterface::ATOM),
            ], JSON_THROW_ON_ERROR),
        ];

        try {
            $this->repository->create($userId, $data);
        } catch (\Throwable $exception) {
            $this->flash('client_error', 'Terjadi kesalahan saat menyimpan data klien.');
            $this->flash('client_form_old', $input);
            $this->redirect($redirect);
        }

        $this->flash('client_success', 'Client berhasil ditambahkan.');
        $this->redirect($redirect);
    }

    private function processUpdate(Request $request, string $redirect): void
    {
        $userId = $this->currentUserId();
        $clientId = (int) $request->input('client_id', 0);
        if ($clientId <= 0) {
            $this->flash('client_error', 'Klien tidak ditemukan atau parameter tidak valid.');
            $this->redirect($redirect);
        }

        $existing = $this->repository->find($userId, $clientId);
        if (!$existing) {
            $this->flash('client_error', 'Klien tidak ditemukan.');
            $this->redirect($redirect);
        }

        $input = $this->collectClientInput($request);
        $password = (string) $request->input('password', '');
        $confirmPassword = (string) $request->input('confirm_password', '');

        $errors = $this->validateClient($userId, $input, $password, $confirmPassword, true, $clientId);

        $avatar = $request->file('avatar');
        $avatarPath = null;
        if ($avatar && ($avatar['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $avatarPath = $this->handleAvatarUpload($avatar, $errors);
        }

        if (!empty($errors)) {
            $this->flash('client_error', reset($errors) ?: 'Periksa kembali data yang Anda masukkan.');
            $this->flash('client_form_errors', $errors);
            $this->flash('client_form_old', $input);
            $this->redirect($redirect);
        }

        $fullName = trim($input['first_name'] . ' ' . $input['last_name']);
        if ($fullName === '') {
            $fullName = $input['first_name'];
        }

        $updateFields = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'] !== '' ? $input['last_name'] : null,
            'name' => $fullName,
            'username' => $input['username'],
            'email' => $input['email'],
            'company' => $input['company'] !== '' ? $input['company'] : null,
            'phone' => $input['phone'],
            'status' => $input['status'],
            'job_title' => $input['job_title'] !== '' ? $input['job_title'] : 'Client',
            'position' => $input['job_title'] !== '' ? $input['job_title'] : 'Client',
        ];

        if ($password !== '') {
            $updateFields['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($avatarPath !== null) {
            $updateFields['avatar_path'] = $avatarPath;
        }

        try {
            $this->repository->update($userId, $clientId, $updateFields);
        } catch (\Throwable $exception) {
            $this->flash('client_error', 'Terjadi kesalahan saat memperbarui data klien.');
            $this->flash('client_form_old', $input);
            $this->redirect($redirect);
        }

        $this->flash('client_success', 'Data klien berhasil diperbarui.');
        $this->redirect($redirect);
    }

    private function processDelete(Request $request, string $redirect): void
    {
        $userId = $this->currentUserId();
        $clientId = (int) $request->input('client_id', 0);
        if ($clientId <= 0) {
            $this->flash('client_error', 'Klien tidak ditemukan atau parameter tidak valid.');
            $this->redirect($redirect);
        }

        try {
            $deleted = $this->repository->delete($userId, $clientId);
            if ($deleted) {
                $this->flash('client_success', 'Klien berhasil dihapus.');
            } else {
                $this->flash('client_error', 'Klien tidak ditemukan atau sudah dihapus.');
            }
        } catch (\Throwable $exception) {
            $this->flash('client_error', 'Terjadi kesalahan saat menghapus klien.');
        }

        $this->redirect($redirect);
    }

    private function collectClientInput(Request $request): array
    {
        return [
            'first_name' => trim((string) $request->input('first_name', '')),
            'last_name' => trim((string) $request->input('last_name', '')),
            'username' => trim((string) $request->input('username', '')),
            'email' => trim((string) $request->input('email', '')),
            'phone' => trim((string) $request->input('phone', '')),
            'company' => trim((string) $request->input('company', '')),
            'job_title' => trim((string) $request->input('job_title', '')),
            'status' => $this->normalizeStatus((string) $request->input('status', 'active')),
        ];
    }

    private function validateClient(int $userId, array $input, string $password, string $confirmPassword, bool $isUpdate, ?int $clientId = null): array
    {
        $errors = [];

        if ($input['first_name'] === '') {
            $errors['first_name'] = 'First name wajib diisi.';
        }

        if ($input['username'] === '') {
            $errors['username'] = 'Username wajib diisi.';
        }

        if ($input['email'] === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if ($input['phone'] === '') {
            $errors['phone'] = 'Nomor telepon wajib diisi.';
        }

        if ($input['job_title'] === '') {
            $input['job_title'] = 'Client';
        }

        if ($password !== '' || $confirmPassword !== '') {
            if ($password !== '' && strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter.';
            }
            if ($confirmPassword !== '' && $confirmPassword !== $password) {
                $errors['confirm_password'] = 'Konfirmasi password tidak sama.';
            }
        }

        if ($this->repository->emailExists($userId, $input['email'], $clientId)) {
            $errors['email'] = 'Email sudah digunakan.';
        }

        if ($this->repository->usernameExists($userId, $input['username'], $clientId)) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        return $errors;
    }

    private function handleAvatarUpload(array $file, array &$errors): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            $errors['avatar'] = 'Gagal mengunggah avatar.';
            return null;
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $errors['avatar'] = 'Format avatar tidak didukung.';
            return null;
        }

        if (($file['size'] ?? 0) > 4 * 1024 * 1024) {
            $errors['avatar'] = 'Ukuran avatar maksimal 4 MB.';
            return null;
        }

        $destinationDir = BASE_PATH . '/assets/img/users';
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $filename = 'client-' . uniqid('', true) . '.' . $extension;
        $targetPath = $destinationDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors['avatar'] = 'Gagal menyimpan avatar.';
            return null;
        }

        return 'assets/img/users/' . $filename;
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return in_array($status, $this->allowedStatuses, true) ? $status : 'active';
    }

    private function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    private function baseUrl(): string
    {
        $base = trim($this->config('app.base_url', ''), '/');
        return $base === '' ? '' : '/' . $base;
    }

    private function url(string $path): string
    {
        $prefix = $this->baseUrl();
        $cleanPath = '/' . ltrim($path, '/');
        return $prefix === '' ? $cleanPath : $prefix . $cleanPath;
    }

    private function redirectPath(string $redirectSlug): string
    {
        $slug = strtolower(trim(pathinfo($redirectSlug, PATHINFO_FILENAME)));
        if ($slug === '' || !isset($this->allowedRedirects[$slug])) {
            $slug = 'clients';
        }

        return $this->url($this->allowedRedirects[$slug]);
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function validateCsrf(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }

    private function loadClients(): array
    {
        $userId = $this->currentUserId();
        try {
            $clients = $this->repository->all($userId, 50);
            $stats = $this->repository->stats($clients);
            $error = null;
        } catch (\Throwable $exception) {
            $clients = [];
            $stats = [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'prospect' => 0,
                'new_this_month' => 0,
            ];
            $error = 'Gagal memuat data klien: ' . $exception->getMessage();
        }

        return [$clients, $stats, $error];
    }

    private function formatClient(array $client): array
    {
        if (empty($client['name'])) {
            $client['name'] = trim(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''));
            if ($client['name'] === '') {
                $client['name'] = $client['username'] ?? 'Client';
            }
        }

        if (empty($client['avatar_path'])) {
            $client['avatar_path'] = 'assets/img/users/user-01.jpg';
        }

        if (empty($client['code']) && isset($client['id'])) {
            $client['code'] = sprintf('CLI-%03d', (int) $client['id']);
        }

        return $client;
    }

    private function generateClientCode(string $firstName, string $lastName): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        return 'CLI-' . strtoupper(bin2hex(random_bytes(2))) . '-' . $initials;
    }
}
