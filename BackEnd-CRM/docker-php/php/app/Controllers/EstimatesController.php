<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\EstimateRepository;

class EstimatesController extends Controller
{
    private EstimateRepository $repository;

    private array $allowedStatuses = [
        'draft',
        'sent',
        'accepted',
        'declined',
        'expired',
    ];

    private array $allowedSorts = [
        'recent',
        'amount_desc',
        'amount_asc',
        'expiry',
    ];

    public function __construct()
    {
        $this->repository = new EstimateRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $statusFilter = $this->normalizeStatus($request->query('status', ''));
        $sort = $this->normalizeSort($request->query('sort', 'recent'));

        $estimates = array_map([$this, 'transformEstimate'], $this->repository->all(
            $userId,
            $statusFilter ?: null,
            $sort
        ));

        $this->view('estimates/index', [
            'estimates' => $estimates,
            'statusOptions' => $this->allowedStatuses,
            'statusFilter' => $statusFilter,
            'sort' => $sort,
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'baseUrl' => $this->baseUrl(),
        ]);
    }

    public function store(Request $request): void
    {
        $userId = $this->currentUserId();
        $input = $this->sanitize($request);
        $errors = $this->validate($input);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal menyimpan draft penawaran. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/estimates'));
        }

        $this->repository->create($userId, $input);
        $this->flash('success', 'Draft penawaran berhasil ditambahkan.');
        $this->redirect($this->url('/estimates'));
    }

    public function update(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $estimate = $this->repository->find($userId, $id);
        if (!$estimate) {
            $this->flash('error', 'Draft penawaran tidak ditemukan.');
            $this->redirect($this->url('/estimates'));
        }

        $input = $this->sanitize($request);
        $errors = $this->validate($input);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal memperbarui draft penawaran. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/estimates'));
        }

        $this->repository->update($userId, $id, $input);
        $this->flash('success', 'Draft penawaran berhasil diperbarui.');
        $this->redirect($this->url('/estimates'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        if (!$this->repository->delete($userId, $id)) {
            $this->flash('error', 'Gagal menghapus draft penawaran.');
        } else {
            $this->flash('success', 'Draft penawaran berhasil dihapus.');
        }

        $this->redirect($this->url('/estimates'));
    }

    private function sanitize(Request $request): array
    {
        $moneyRaw = trim((string) $request->input('amount', '0'));
        $estimateDate = $this->normalizeDate($request->input('estimate_date'));

        if (!$estimateDate) {
            $estimateDate = date('Y-m-d');
        }

        return [
            'ref_no' => trim((string) $request->input('ref_no', '')),
            'client_name' => trim((string) $request->input('client_name', '')),
            'client_title' => trim((string) $request->input('client_title', '')),
            'company_name' => trim((string) $request->input('company_name', '')),
            'project_title' => trim((string) $request->input('project_title', '')),
            'contact_email' => trim((string) $request->input('contact_email', '')),
            'estimate_date' => $estimateDate,
            'expiry_date' => $this->normalizeDate($request->input('expiry_date')),
            'amount' => $this->normalizeMoney($moneyRaw),
            'status' => $this->normalizeStatus($request->input('status', '')),
            'notes' => trim((string) $request->input('notes', '')),
            'avatar_path' => trim((string) $request->input('avatar_path', '')),
        ];
    }

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['client_name'] === '') {
            $errors['client_name'] = 'Nama klien wajib diisi.';
        }
        if ($input['company_name'] === '') {
            $errors['company_name'] = 'Nama perusahaan wajib diisi.';
        }
        if ($input['status'] === '') {
            $errors['status'] = 'Status penawaran wajib diisi.';
        }
        if ($input['amount'] <= 0) {
            $errors['amount'] = 'Nominal penawaran harus lebih dari 0.';
        }
        if ($input['contact_email'] !== '' && !filter_var($input['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Format email tidak valid.';
        }

        if ($input['estimate_date'] && !$this->isValidDate($input['estimate_date'])) {
            $errors['estimate_date'] = 'Tanggal penawaran tidak valid.';
        }

        if ($input['expiry_date'] && !$this->isValidDate($input['expiry_date'])) {
            $errors['expiry_date'] = 'Tanggal kedaluwarsa tidak valid.';
        }

        if ($input['estimate_date'] && $input['expiry_date'] && $input['expiry_date'] < $input['estimate_date']) {
            $errors['expiry_date'] = 'Tanggal kedaluwarsa harus setelah tanggal penawaran.';
        }

        return $errors;
    }

    private function normalizeMoney(string $value): float
    {
        $clean = preg_replace('/[^\d,\.]/', '', $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
        return (float) $clean;
    }

    private function normalizeDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function isValidDate(?string $date): bool
    {
        if (!$date) {
            return true;
        }

        $d = date_create_from_format('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function normalizeStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return in_array($status, $this->allowedStatuses, true) ? $status : '';
    }

    private function normalizeSort(?string $sort): string
    {
        $sort = strtolower(trim((string) $sort));
        return in_array($sort, $this->allowedSorts, true) ? $sort : 'recent';
    }

    private function transformEstimate(array $estimate): array
    {
        if (empty($estimate['avatar_path'])) {
            $estimate['avatar_path'] = 'assets/img/users/user-01.jpg';
        }

        if (empty($estimate['client_title'])) {
            $estimate['client_title'] = 'Kontak';
        }

        if (empty($estimate['status'])) {
            $estimate['status'] = 'draft';
        }

        $estimate['amount'] = (float) ($estimate['amount'] ?? 0);

        return $estimate;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    private function pullFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    private function pushErrors(array $errors): void
    {
        $_SESSION['errors'] = $errors;
    }

    private function pullErrors(): array
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }

    private function pushOld(array $old): void
    {
        $_SESSION['old'] = $old;
    }

    private function pullOld(): array
    {
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        return $old;
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

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}

