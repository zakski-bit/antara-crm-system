<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\LeadRepository;

class LeadsController extends Controller
{
    private LeadRepository $repository;

    public function __construct()
    {
        $this->repository = new LeadRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $leads = array_map([$this, 'transformLead'], $this->repository->all($userId));

        $this->view('leads/index', [
            'leads' => $leads,
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'stages' => $this->stageOptions(),
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
            $this->flash('error', 'Gagal menambahkan prospek. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/crm/leads'));
        }

        $this->repository->create($userId, $input);

        $this->flash('success', 'Prospek kemitraan baru berhasil disimpan.');
        $this->redirect($this->url('/crm/leads'));
    }

    public function update(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $lead = $this->repository->find($userId, $id);
        if (!$lead) {
            $this->flash('error', 'Data prospek tidak ditemukan.');
            $this->redirect($this->url('/crm/leads'));
        }

        $input = $this->sanitize($request);
        $errors = $this->validate($input);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal memperbarui prospek.');
            $this->redirect($this->url('/crm/leads'));
        }

        $this->repository->update($userId, $id, $input);

        $this->flash('success', 'Prospek berhasil diperbarui.');
        $this->redirect($this->url('/crm/leads'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        if (!$this->repository->delete($userId, $id)) {
            $this->flash('error', 'Gagal menghapus prospek.');
        } else {
            $this->flash('success', 'Prospek berhasil dihapus.');
        }

        $this->redirect($this->url('/crm/leads'));
    }

    private function sanitize(Request $request): array
    {
        $title = trim((string)$request->input('title', ''));
        $organization = trim((string)$request->input('organization', ''));
        $contact = trim((string)$request->input('contact_name', ''));
        $email = trim((string)$request->input('contact_email', ''));
        $phone = trim((string)$request->input('contact_phone', ''));
        $stage = trim((string)$request->input('stage', ''));
        $targetValue = trim((string)$request->input('target_value', ''));
        $notes = trim((string)$request->input('notes', ''));

        return [
            'title' => $title,
            'organization' => $organization,
            'contact_name' => $contact,
            'contact_email' => $email,
            'contact_phone' => $phone,
            'stage' => $stage,
            'target_value' => $this->normalizeMoney($targetValue),
            'notes' => $notes,
        ];
    }

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['title'] === '') {
            $errors['title'] = 'Nama kampanye atau prospek wajib diisi.';
        }
        if ($input['organization'] === '') {
            $errors['organization'] = 'Nama instansi wajib diisi.';
        }
        if ($input['contact_name'] === '') {
            $errors['contact_name'] = 'Kontak utama wajib diisi.';
        }
        if ($input['contact_email'] !== '' && !filter_var($input['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Format email tidak valid.';
        }
        if ($input['stage'] === '' || !in_array($input['stage'], $this->stageOptions(), true)) {
            $errors['stage'] = 'Tahapan prospek tidak valid.';
        }
        if ($input['target_value'] <= 0) {
            $errors['target_value'] = 'Target nilai harus lebih dari 0.';
        }

        return $errors;
    }

    private function transformLead(array $lead): array
    {
        return [
            'id' => (int)$lead['id'],
            'title' => $lead['title'],
            'organization' => $lead['organization'],
            'contact_name' => $lead['contact_name'],
            'contact_email' => $lead['contact_email'],
            'contact_phone' => $lead['contact_phone'],
            'stage' => $lead['stage'],
            'target_value' => (float)$lead['target_value'],
            'notes' => $lead['notes'] ?? '',
        ];
    }

    private function normalizeMoney(string $value): float
    {
        $clean = preg_replace('/[^\d,\.]/', '', $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
        return (float)$clean;
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

    private function stageOptions(): array
    {
        return [
            'Prospek Baru',
            'Briefing',
            'Penawaran',
            'Follow Up',
            'Presentasi',
            'Negosiasi',
            'Menunggu Persetujuan',
        ];
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
