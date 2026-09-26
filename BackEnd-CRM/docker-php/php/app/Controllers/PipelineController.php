<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\PipelineRepository;

class PipelineController extends Controller
{
    private PipelineRepository $repository;

    public function __construct()
    {
        $this->repository = new PipelineRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $entries = array_map([$this, 'transformEntry'], $this->repository->all($userId));

        $grouped = [];
        foreach ($entries as $entry) {
            $grouped[$entry['stage']][] = $entry;
        }

        $this->view('pipeline/index', [
            'entries' => $grouped,
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
            $this->flash('error', 'Gagal menambahkan item pipeline. Mohon periksa kembali data.');
            $this->redirect($this->url('/crm/pipeline'));
        }

        $this->repository->create($userId, $input);

        $this->flash('success', 'Item pipeline berhasil ditambahkan.');
        $this->redirect($this->url('/crm/pipeline'));
    }

    public function update(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $entry = $this->repository->find($userId, $id);
        if (!$entry) {
            $this->flash('error', 'Data pipeline tidak ditemukan.');
            $this->redirect($this->url('/crm/pipeline'));
        }

        $input = $this->sanitize($request);
        $errors = $this->validate($input);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal memperbarui item pipeline.');
            $this->redirect($this->url('/crm/pipeline'));
        }

        $this->repository->update($userId, $id, $input);

        $this->flash('success', 'Item pipeline berhasil diperbarui.');
        $this->redirect($this->url('/crm/pipeline'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        if (!$this->repository->delete($userId, $id)) {
            $this->flash('error', 'Gagal menghapus item pipeline.');
        } else {
            $this->flash('success', 'Item pipeline berhasil dihapus.');
        }

        $this->redirect($this->url('/crm/pipeline'));
    }

    private function sanitize(Request $request): array
    {
        $title = trim((string)$request->input('title', ''));
        $partner = trim((string)$request->input('partner_name', ''));
        $stage = trim((string)$request->input('stage', ''));
        $estimateRaw = trim((string)$request->input('estimate_value', ''));
        $notes = trim((string)$request->input('notes', ''));

        return [
            'title' => $title,
            'partner_name' => $partner,
            'stage' => $stage,
            'estimate_value' => $this->normalizeMoney($estimateRaw),
            'notes' => $notes,
        ];
    }

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['title'] === '') {
            $errors['title'] = 'Judul kerja sama wajib diisi.';
        }
        if ($input['partner_name'] === '') {
            $errors['partner_name'] = 'Nama mitra wajib diisi.';
        }
        if ($input['stage'] === '' || !in_array($input['stage'], $this->stageOptions(), true)) {
            $errors['stage'] = 'Tahapan pipeline tidak valid.';
        }
        if ($input['estimate_value'] <= 0) {
            $errors['estimate_value'] = 'Estimasi nilai harus lebih dari 0.';
        }

        return $errors;
    }

    private function transformEntry(array $entry): array
    {
        return [
            'id' => (int)$entry['id'],
            'title' => $entry['title'],
            'partner_name' => $entry['partner_name'],
            'stage' => $entry['stage'],
            'estimate_value' => (float)$entry['estimate_value'],
            'notes' => $entry['notes'] ?? '',
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
            'Prospecting',
            'Penawaran',
            'Presentasi',
            'Negosiasi',
            'Selesai',
        ];
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
