<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\MitraRepository;

class MitraController extends Controller
{
    private MitraRepository $repository;

    public function __construct()
    {
        $this->repository = new MitraRepository();
    }

    public function grid(Request $request): void
    {
        $partners = $this->repository->all();

        $this->view('mitra/grid', [
            'title' => 'Mitra & Korporasi',
            'partners' => array_map([$this, 'transformPartner'], $partners),
            'flash' => $this->pullFlash(),
            'baseUrl' => $this->baseUrl(),
        ]);
    }

    public function table(Request $request): void
    {
        $partners = $this->repository->all();

        $this->view('mitra/table', [
            'title' => 'Daftar Mitra & Korporasi',
            'partners' => array_map([$this, 'transformPartner'], $partners),
            'flash' => $this->pullFlash(),
            'baseUrl' => $this->baseUrl(),
        ]);
    }

    public function create(Request $request): void
    {
        $this->view('mitra/form', [
            'title' => 'Tambah Mitra',
            'baseUrl' => $this->baseUrl(),
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'isEdit' => false,
            'partner' => null,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(Request $request): void
    {
        $input = $this->sanitizeInput($request);
        $file = $request->file('logo');

        $errors = $this->validate($input, $file, false);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($input);
            $this->flash('error', 'Gagal menambahkan mitra. Periksa kembali data yang diisi.');
            $this->redirect($this->url('/crm/mitra/create'));
        }

        $logoPath = $this->handleLogoUpload($file);
        if ($logoPath) {
            $input['logo_path'] = $logoPath;
        }

        $input['logo_path'] = $input['logo_path'] ?? '';

        $this->repository->create($input);

        $this->flash('success', 'Mitra baru berhasil ditambahkan.');
        $this->redirect($this->url('/crm/mitra'));
    }

    public function edit(Request $request, int $id): void
    {
        $partner = $this->repository->find($id);

        if (!$partner) {
            $this->flash('error', 'Data mitra tidak ditemukan.');
            $this->redirect($this->url('/crm/mitra'));
        }

        $this->view('mitra/form', [
            'title' => 'Edit Mitra',
            'baseUrl' => $this->baseUrl(),
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'isEdit' => true,
            'partner' => $this->transformPartner($partner),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, int $id): void
    {
        $partner = $this->repository->find($id);

        if (!$partner) {
            $this->flash('error', 'Data mitra tidak ditemukan.');
            $this->redirect($this->url('/crm/mitra'));
        }

        $input = $this->sanitizeInput($request);
        $file = $request->file('logo');

        $errors = $this->validate($input, $file, true);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($input);
            $this->flash('error', 'Gagal memperbarui mitra. Periksa kembali data yang diisi.');
            $this->redirect($this->url("/crm/mitra/{$id}/edit"));
        }

        $logoPath = $this->handleLogoUpload($file);
        if ($logoPath) {
            $input['logo_path'] = $logoPath;
        } else {
            $input['logo_path'] = $input['logo_path'] ?? $partner['logo_path'];
        }

        $this->repository->update($id, $input);

        $this->flash('success', 'Data mitra berhasil diperbarui.');
        $this->redirect($this->url('/crm/mitra'));
    }

    public function destroy(Request $request, int $id): void
    {
        $partner = $this->repository->find($id);

        if (!$partner) {
            $this->flash('error', 'Data mitra tidak ditemukan atau sudah dihapus.');
            $this->redirect($this->url('/crm/mitra'));
        }

        $this->repository->delete($id);

        $this->flash('success', 'Mitra berhasil dihapus.');
        $this->redirect($this->url('/crm/mitra'));
    }

    private function sanitizeInput(Request $request): array
    {
        $fields = [
            'nama',
            'industri',
            'kontak',
            'email',
            'telepon',
            'status',
            'alamat',
            'logo_path',
        ];

        $data = [];
        foreach ($fields as $field) {
            $value = trim((string)$request->input($field, ''));
            $data[$field] = $value;
        }

        return $data;
    }

    private function validate(array $input, ?array $file, bool $isUpdate): array
    {
        $errors = [];

        if ($input['nama'] === '') {
            $errors['nama'] = 'Nama perusahaan wajib diisi.';
        }

        if ($input['industri'] === '') {
            $errors['industri'] = 'Industri/segmen wajib diisi.';
        }

        if ($input['status'] === '') {
            $errors['status'] = 'Status kemitraan wajib diisi.';
        }

        if ($input['email'] === '' || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Alamat email tidak valid.';
        }

        if ($input['telepon'] === '') {
            $errors['telepon'] = 'Nomor telepon wajib diisi.';
        }

        if ($input['alamat'] === '') {
            $errors['alamat'] = 'Alamat kantor wajib diisi.';
        }

        if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors['logo'] = 'Gagal mengunggah logo. Silakan coba lagi.';
            } else {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
                if (!in_array($extension, $allowed, true)) {
                    $errors['logo'] = 'Format logo harus PNG, JPG, JPEG, SVG, atau WEBP.';
                }
            }
        } elseif (!$isUpdate && $input['logo_path'] === '') {
            $errors['logo'] = 'Unggah logo atau masukkan path logo yang valid.';
        }

        return $errors;
    }

    private function handleLogoUpload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'logo-' . uniqid('', true) . '.' . $extension;
        $destinationDir = BASE_PATH . '/assets/img/mitra';

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $targetPath = $destinationDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return null;
        }

        return 'assets/img/mitra/' . $filename;
    }

    private function transformPartner(array $partner): array
    {
        $relativeLogo = trim((string)($partner['logo_path'] ?? '')) ?: 'assets/img/logo.png';
        $partner['logo'] = $this->templateAssetUrl($relativeLogo);
        return $partner;
    }

    private function templateAssetUrl(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            $path = 'assets/img/logo.png';
        }

        if (preg_match('#^(https?:)?//#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');
        if (stripos($path, 'template/') === 0) {
            $path = substr($path, 9);
        }

        $baseUrl = trim($this->config('app.base_url', ''), '/');
        $prefix = ($baseUrl === '' ? '' : '/' . $baseUrl) . '/';

        return $prefix . $path;
    }

    private function statusOptions(): array
    {
        return [
            'Strategic Alliance',
            'Editorial Partner',
            'Media Distribution',
            'Media Partner',
            'Commercial Partner',
            'Network Member',
            'Technology Partner',
            'Security Partner',
            'Government Relations',
        ];
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

    private function pushOld(array $input): void
    {
        $_SESSION['old'] = $input;
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
}
