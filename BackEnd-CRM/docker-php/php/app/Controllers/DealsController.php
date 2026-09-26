<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\DealRepository;

class DealsController extends Controller
{
    private DealRepository $repository;

    public function __construct()
    {
        $this->repository = new DealRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $deals = array_map([$this, 'transformDeal'], $this->repository->all($userId));

        $this->view('deals/index', [
            'deals' => $deals,
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
            $this->flash('error', 'Gagal menambahkan kerja sama. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/crm/deals'));
        }

        $this->repository->create($userId, $input);

        $this->flash('success', 'Kerja sama iklan baru berhasil disimpan.');
        $this->redirect($this->url('/crm/deals'));
    }

    public function update(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $deal = $this->repository->find($userId, $id);
        if (!$deal) {
            $this->flash('error', 'Data kerja sama tidak ditemukan.');
            $this->redirect($this->url('/crm/deals'));
        }

        $input = $this->sanitize($request);
        $errors = $this->validate($input);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal memperbarui kerja sama. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/crm/deals'));
        }

        $this->repository->update($userId, $id, $input);

        $this->flash('success', 'Kerja sama berhasil diperbarui.');
        $this->redirect($this->url('/crm/deals'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        if (!$this->repository->delete($userId, $id)) {
            $this->flash('error', 'Gagal menghapus data kerja sama.');
        } else {
            $this->flash('success', 'Kerja sama berhasil dihapus.');
        }

        $this->redirect($this->url('/crm/deals'));
    }

    private function sanitize(Request $request): array
    {
        $title = trim((string)$request->input('title', ''));
        $partner = trim((string)$request->input('partner_name', ''));
        $valueRaw = trim((string)$request->input('deal_value', ''));
        $stage = trim((string)$request->input('stage', ''));
        $start = trim((string)$request->input('start_date', ''));
        $end = trim((string)$request->input('end_date', ''));
        $notes = trim((string)$request->input('notes', ''));

        return [
            'title' => $title,
            'partner_name' => $partner,
            'stage' => $stage,
            'deal_value' => $this->normalizeMoney($valueRaw),
            'start_date' => $this->normalizeDate($start),
            'end_date' => $this->normalizeDate($end),
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
            $errors['stage'] = 'Pilih tahapan kerja sama yang valid.';
        }
        if ($input['deal_value'] <= 0) {
            $errors['deal_value'] = 'Nilai kerja sama harus lebih dari 0.';
        }

        if ($input['start_date'] && !$this->isValidDate($input['start_date'])) {
            $errors['start_date'] = 'Tanggal mulai tidak valid.';
        }

        if ($input['end_date'] && !$this->isValidDate($input['end_date'])) {
            $errors['end_date'] = 'Tanggal selesai tidak valid.';
        }
        if ($input['start_date'] && $input['end_date'] && $input['end_date'] < $input['start_date']) {
            $errors['end_date'] = 'Tanggal selesai harus setelah tanggal mulai.';
        }

        return $errors;
    }

    private function transformDeal(array $deal): array
    {
        return [
            'id' => (int)$deal['id'],
            'title' => $deal['title'],
            'partner_name' => $deal['partner_name'],
            'stage' => $deal['stage'],
            'deal_value' => (float)$deal['deal_value'],
            'start_date' => $deal['start_date'],
            'end_date' => $deal['end_date'],
            'notes' => $deal['notes'] ?? '',
        ];
    }

    private function normalizeMoney(string $value): float
    {
        $clean = preg_replace('/[^\d,\.]/', '', $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
        return (float)$clean;
    }

    private function normalizeDate(?string $value): ?string
    {
        $value = trim((string)$value);
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
            'Prospek Awal',
            'Briefing Redaksi',
            'Proposal',
            'Negosiasi Final',
            'Produksi Konten',
            'Berjalan',
            'Selesai',
        ];
    }

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
