<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductInvoiceRepository;

class PaymentsController extends Controller
{
    private PaymentRepository $repository;
    private ProductInvoiceRepository $invoiceRepository;

    /** @var array<string, string> */
    private array $statusLabels = [
        'settled' => 'Lunas',
        'partial' => 'Sebagian',
        'pending' => 'Menunggu',
        'failed' => 'Gagal',
        'refunded' => 'Refund',
    ];

    /** @var array<string, string> */
    private array $verificationLabels = [
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];

    /** @var array<string, string> */
    private array $sortLabels = [
        'recent' => 'Terbaru',
        'amount_desc' => 'Nominal Terbesar',
        'amount_asc' => 'Nominal Terkecil',
        'client' => 'Nama Klien',
        'oldest' => 'Terlama',
    ];

    /** @var array<int> */
    private array $perPageOptions = [10, 25, 50, 100];

    public function __construct()
    {
        $this->repository = new PaymentRepository();
        $this->invoiceRepository = new ProductInvoiceRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $userRole = $this->currentUserRole();
        $isCustomer = $this->isCustomerRole($userRole);
        $recipientUserId = $isCustomer ? $userId : null;
        $status = $this->normalizeStatus($request->query('status', ''));
        $sort = $this->normalizeSort($request->query('sort', 'recent'));
        $search = trim((string) $request->query('search', ''));
        $perPage = $this->normalizePerPage((int) $request->query('per_page', 10));
        $page = max(1, (int) $request->query('page', 1));

        $startDate = $this->normalizeDate($request->query('start_date', ''));
        $endDate = $this->normalizeDate($request->query('end_date', ''));

        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $result = $this->repository->paginate(
            $userId,
            $status ?: null,
            $sort,
            $perPage,
            $page,
            $search !== '' ? $search : null,
            $startDate,
            $endDate,
            $recipientUserId
        );

        $totalPages = max(1, (int) ceil($result['total'] / $result['per_page']));
        if ($page > $totalPages && $result['total'] > 0) {
            $page = $totalPages;
            $result = $this->repository->paginate(
                $userId,
                $status ?: null,
                $sort,
                $perPage,
                $page,
                $search !== '' ? $search : null,
                $startDate,
                $endDate,
                $recipientUserId
            );
        }

        $payments = array_map([$this, 'transformPayment'], $result['data']);
        $stats = $this->repository->stats($userId, $startDate, $endDate, $recipientUserId);
        $invoiceOptions = $this->invoiceRepository->listLatest($userId, 50, null, $recipientUserId);

        $baseUrl = $this->baseUrl();
        $listUrl = $baseUrl === '' ? '/payments' : $baseUrl . '/payments';

        $this->view('payments/index', [
            'title' => 'Pembayaran Klien',
            'payments' => $payments,
            'stats' => $stats,
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'filters' => [
                'status' => $status,
                'sort' => $sort,
                'search' => $search,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
                'page' => $page,
                'total' => $result['total'],
                'total_pages' => $totalPages,
            ],
            'statusOptions' => array_keys($this->statusLabels),
            'statusLabels' => $this->statusLabels,
            'verificationOptions' => array_keys($this->verificationLabels),
            'verificationLabels' => $this->verificationLabels,
            'sortOptions' => $this->sortLabels,
            'perPageOptions' => $this->perPageOptions,
            'invoiceOptions' => $invoiceOptions,
            'userRole' => $userRole,
            'isCustomer' => $isCustomer,
            'isStaff' => $this->isStaffRole($userRole),
            'baseUrl' => $baseUrl,
            'listUrl' => $listUrl,
            'storeUrl' => $this->url('/crm/payments'),
            'verifyUrlBase' => $this->url('/crm/payments'),
        ]);
    }

    public function store(Request $request): void
    {
        $userId = $this->currentUserId();
        $isCustomer = $this->isCustomerRole($this->currentUserRole());

        [$data, $invoice, $proofErrors] = $this->sanitize($request);
        $errors = array_merge($this->validate($data, $invoice, $isCustomer), $proofErrors);

        if (!empty($errors)) {
            $old = $request->all();
            $old['_form'] = 'create-payment';
            $this->pushErrors($errors);
            $this->pushOld($old);
            $this->flash('error', 'Gagal menyimpan data pembayaran. Mohon periksa kembali input Anda.');
            $this->redirect($this->url('/payments'));
        }

        $ownerId = $invoice ? (int) ($invoice['user_id'] ?? $userId) : $userId;
        $this->repository->create($ownerId, $data);

        if ($isCustomer) {
            $this->flash('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin/pegawai.');
        } else {
            $this->flash('success', 'Pembayaran berhasil dicatat.');
        }

        $this->redirect($this->url('/payments'));
    }

    public function verify(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $userRole = $this->currentUserRole();
        if (!$this->isStaffRole($userRole)) {
            $this->flash('error', 'Hanya admin/pegawai yang dapat memverifikasi pembayaran.');
            $this->redirect($this->url('/payments'));
        }

        $payment = $this->repository->find($userId, $id);
        if (!$payment) {
            $this->flash('error', 'Pembayaran tidak ditemukan.');
            $this->redirect($this->url('/payments'));
        }

        $verificationStatus = $this->normalizeVerificationStatus($request->input('verification_status', ''));
        if ($verificationStatus === '') {
            $this->flash('error', 'Status verifikasi tidak valid.');
            $this->redirect($this->url('/payments'));
        }

        $verificationNotes = trim((string) $request->input('verification_notes', ''));
        $paymentStatus = 'failed';
        if ($verificationStatus === 'approved') {
            $requestedStatus = $this->normalizeStatus($request->input('status', $payment['status'] ?? 'settled'));
            $paymentStatus = in_array($requestedStatus, ['settled', 'partial'], true) ? $requestedStatus : 'settled';
        }

        $verified = $this->repository->verify(
            $userId,
            $id,
            $verificationStatus,
            $userId,
            $verificationNotes !== '' ? $verificationNotes : null,
            $paymentStatus
        );

        if (!$verified) {
            $this->flash('error', 'Gagal menyimpan hasil verifikasi pembayaran.');
            $this->redirect($this->url('/payments'));
        }

        if ($verificationStatus === 'approved') {
            $this->flash('success', 'Pembayaran berhasil diverifikasi.');
        } else {
            $this->flash('success', 'Pembayaran ditolak dan tidak dihitung sebagai pembayaran diterima.');
        }
        $this->redirect($this->url('/payments'));
    }

    public function update(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        $userRole = $this->currentUserRole();
        if ($this->isCustomerRole($userRole)) {
            $this->flash('error', 'Akses pelanggan tidak diperbolehkan untuk mengubah pembayaran.');
            $this->redirect($this->url('/payments'));
        }

        $payment = $this->repository->find($userId, $id);
        if (!$payment) {
            $this->flash('error', 'Pembayaran tidak ditemukan.');
            $this->redirect($this->url('/payments'));
        }

        [$data, $invoice, $proofErrors] = $this->sanitize($request, $payment);
        $errors = array_merge($this->validate($data, $invoice, false), $proofErrors);

        if (!empty($errors)) {
            $old = $request->all();
            $old['_form'] = 'edit-payment';
            $old['_id'] = $id;
            $this->pushErrors($errors);
            $this->pushOld($old);
            $this->flash('error', 'Gagal memperbarui data pembayaran.');
            $this->redirect($this->url('/payments'));
        }

        $ownerId = $invoice ? (int) ($invoice['user_id'] ?? $userId) : $userId;
        $this->repository->update($ownerId, $id, $data);
        $this->flash('success', 'Data pembayaran diperbarui.');
        $this->redirect($this->url('/payments'));
    }

    public function destroy(Request $request, int $id): void
    {
        $userId = $this->currentUserId();
        if ($this->isCustomerRole($this->currentUserRole())) {
            $this->flash('error', 'Akses pelanggan tidak diperbolehkan untuk menghapus pembayaran.');
            $this->redirect($this->url('/payments'));
        }

        if ($this->repository->delete($userId, $id)) {
            $this->flash('success', 'Pembayaran berhasil dihapus.');
        } else {
            $this->flash('error', 'Gagal menghapus pembayaran.');
        }

        $this->redirect($this->url('/payments'));
    }

    /**
     * @return array{0: array, 1: ?array, 2: array}
     */
    private function sanitize(Request $request, ?array $existing = null): array
    {
        $userId = $this->currentUserId();
        $userRole = $this->currentUserRole();
        $isCustomer = $this->isCustomerRole($userRole);

        $invoiceId = (int) $request->input('invoice_id', 0);
        if ($invoiceId <= 0 && $existing !== null) {
            $invoiceId = (int) ($existing['invoice_id'] ?? 0);
        }
        if ($invoiceId <= 0) {
            $invoiceId = null;
        }

        $invoice = $invoiceId ? $this->invoiceRepository->find($userId, $invoiceId, null, $userId) : null;

        if ($isCustomer && $invoice) {
            $invoiceNo = trim((string) ($invoice['invoice_no'] ?? ''));
            $clientName = trim((string) ($invoice['client_name'] ?? ''));
            $companyName = trim((string) ($invoice['client_company'] ?? ''));
            $clientPosition = trim((string) ($invoice['client_position'] ?? ''));
            $clientAvatar = trim((string) ($invoice['client_avatar'] ?? ''));
        } else {
            $invoiceNo = trim((string) $request->input('invoice_no', $invoice['invoice_no'] ?? ($existing['invoice_no'] ?? '')));
            $clientName = trim((string) $request->input('client_name', $invoice['client_name'] ?? ($existing['client_name'] ?? '')));
            $companyName = trim((string) $request->input('company_name', $invoice['client_company'] ?? ($existing['company_name'] ?? '')));
            $clientPosition = trim((string) $request->input('client_position', $invoice['client_position'] ?? ($existing['client_position'] ?? '')));
            $clientAvatar = trim((string) $request->input('client_avatar', $invoice['client_avatar'] ?? ($existing['client_avatar'] ?? '')));
        }

        $paymentProofPath = trim((string) $request->input('payment_proof_path', $existing['payment_proof_path'] ?? ''));

        $paidAt = $this->normalizeDateTime($request->input('paid_at'));

        $statusDefault = (string) ($existing['status'] ?? ($isCustomer ? 'pending' : 'settled'));
        $status = $this->normalizeStatus($request->input('status', $statusDefault));
        if ($isCustomer) {
            $status = 'pending';
        } elseif ($status === '') {
            $status = 'settled';
        }

        $verificationStatus = 'pending';
        $verifiedByUserId = null;
        $verifiedAt = null;
        $verificationNotes = trim((string) $request->input('verification_notes', $existing['verification_notes'] ?? ''));

        if (!$isCustomer) {
            $existingVerification = $this->normalizeVerificationStatus($existing['verification_status'] ?? '');
            $verificationStatus = $this->normalizeVerificationStatus(
                $request->input('verification_status', $existingVerification !== '' ? $existingVerification : 'approved')
            );
            if ($verificationStatus === '') {
                $verificationStatus = $existingVerification !== '' ? $existingVerification : 'approved';
            }

            $verifiedByUserId = isset($existing['verified_by_user_id']) ? (int) $existing['verified_by_user_id'] : null;
            $verifiedAt = $existing['verified_at'] ?? null;

            if ($verificationStatus === 'approved') {
                $verifiedByUserId = $verifiedByUserId ?: $userId;
                $verifiedAt = $verifiedAt ?: date('Y-m-d H:i:s');
            } elseif ($verificationStatus === 'rejected') {
                $verifiedByUserId = $userId;
                $verifiedAt = date('Y-m-d H:i:s');
                $status = 'failed';
            } else {
                $verifiedByUserId = null;
                $verifiedAt = null;
            }
        }

        $proofErrors = [];
        $proofFile = $request->file('payment_proof');
        if ($proofFile && ($proofFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploaded = $this->handlePaymentProofUpload($proofFile, $proofErrors);
            if ($uploaded) {
                $paymentProofPath = $uploaded;
            }
        }

        $data = [
            'invoice_id' => $invoice ? (int) $invoice['id'] : $invoiceId,
            'invoice_no' => $invoiceNo,
            'client_name' => $clientName,
            'client_position' => $clientPosition,
            'company_name' => $companyName,
            'client_avatar' => $clientAvatar,
            'payment_method' => trim((string) $request->input('payment_method', $existing['payment_method'] ?? '')),
            'channel' => trim((string) $request->input('channel', $existing['channel'] ?? '')),
            'reference_no' => trim((string) $request->input('reference_no', $existing['reference_no'] ?? '')),
            'paid_at' => $paidAt ?: ($existing['paid_at'] ?? date('Y-m-d H:i:s')),
            'amount' => $this->normalizeMoney($request->input('amount', $existing['amount'] ?? '0')),
            'currency_code' => strtoupper(trim((string) $request->input('currency_code', $existing['currency_code'] ?? 'IDR'))) ?: 'IDR',
            'status' => $status,
            'verification_status' => $verificationStatus,
            'verified_by_user_id' => $verifiedByUserId,
            'verified_at' => $verifiedAt,
            'verification_notes' => $verificationNotes !== '' ? $verificationNotes : null,
            'notes' => trim((string) $request->input('notes', $existing['notes'] ?? '')),
            'payment_proof_path' => $paymentProofPath,
        ];

        return [$data, $invoice, $proofErrors];
    }

    private function validate(array $data, ?array $invoice, bool $isCustomer): array
    {
        $errors = [];

        if ($isCustomer && !$invoice) {
            $errors['invoice_id'] = 'Pilih tagihan produk yang valid sebelum mengunggah bukti pembayaran.';
        }

        if (!$invoice && $data['invoice_id']) {
            $errors['invoice_id'] = 'Tagihan produk tidak ditemukan.';
        }

        if (trim((string) $data['invoice_no']) === '') {
            $errors['invoice_no'] = 'Nomor tagihan wajib diisi atau pilih tagihan produk.';
        }

        if (trim((string) $data['client_name']) === '') {
            $errors['client_name'] = 'Nama klien wajib diisi.';
        }

        if ($data['amount'] <= 0) {
            $errors['amount'] = 'Nominal pembayaran harus lebih dari 0.';
        }

        if (!$this->isValidDateTime($data['paid_at'])) {
            $errors['paid_at'] = 'Tanggal pembayaran tidak valid.';
        }

        if ($data['status'] === '') {
            $errors['status'] = 'Status pembayaran tidak valid.';
        }

        if ($this->normalizeVerificationStatus($data['verification_status'] ?? '') === '') {
            $errors['verification_status'] = 'Status verifikasi tidak valid.';
        }

        if ($isCustomer && trim((string) ($data['payment_proof_path'] ?? '')) === '') {
            $errors['payment_proof_path'] = 'Bukti pembayaran wajib diunggah pelanggan.';
        }

        return $errors;
    }

    private function transformPayment(array $payment): array
    {
        $verificationStatus = $this->normalizeVerificationStatus($payment['verification_status'] ?? 'approved');
        if ($verificationStatus === '') {
            $verificationStatus = 'approved';
        }

        return [
            'id' => (int) ($payment['id'] ?? 0),
            'invoice_id' => isset($payment['invoice_id']) ? (int) $payment['invoice_id'] : null,
            'invoice_no' => $payment['invoice_no'] ?? '',
            'invoice_title' => $payment['invoice_title'] ?? '',
            'invoice_total' => isset($payment['invoice_total']) ? (float) $payment['invoice_total'] : null,
            'invoice_amount_paid' => isset($payment['invoice_amount_paid']) ? (float) $payment['invoice_amount_paid'] : null,
            'invoice_amount_due' => isset($payment['invoice_amount_due']) ? (float) $payment['invoice_amount_due'] : null,
            'invoice_status' => $payment['invoice_status'] ?? '',
            'client_name' => $payment['client_name'] ?? ($payment['invoice_client_name'] ?? ''),
            'client_position' => $payment['client_position'] ?? ($payment['invoice_client_position'] ?? ''),
            'company_name' => $payment['company_name'] ?? ($payment['invoice_company'] ?? ''),
            'client_avatar' => $payment['client_avatar'] ?? ($payment['invoice_client_avatar'] ?? ''),
            'payment_method' => $payment['payment_method'] ?? '',
            'channel' => $payment['channel'] ?? '',
            'reference_no' => $payment['reference_no'] ?? '',
            'paid_at' => $payment['paid_at'] ?? null,
            'amount' => (float) ($payment['amount'] ?? 0),
            'currency_code' => $payment['currency_code'] ?? 'IDR',
            'status' => $payment['status'] ?? '',
            'verification_status' => $verificationStatus,
            'verified_by_user_id' => isset($payment['verified_by_user_id']) ? (int) $payment['verified_by_user_id'] : null,
            'verified_at' => $payment['verified_at'] ?? null,
            'verification_notes' => $payment['verification_notes'] ?? '',
            'notes' => $payment['notes'] ?? '',
            'payment_proof_path' => $payment['payment_proof_path'] ?? '',
        ];
    }

    private function normalizeStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return array_key_exists($status, $this->statusLabels) ? $status : '';
    }

    private function normalizeVerificationStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return array_key_exists($status, $this->verificationLabels) ? $status : '';
    }

    private function normalizeSort(?string $sort): string
    {
        $sort = strtolower(trim((string) $sort));
        return array_key_exists($sort, $this->sortLabels) ? $sort : 'recent';
    }

    private function normalizePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true) ? $value : $this->perPageOptions[0];
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

    private function normalizeDateTime(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function handlePaymentProofUpload(array $file, array &$errors): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            $errors['payment_proof_path'] = 'Gagal mengunggah bukti pembayaran.';
            return null;
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp', 'pdf'];
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $errors['payment_proof_path'] = 'Format bukti pembayaran tidak didukung (png, jpg, jpeg, webp, pdf).';
            return null;
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            $errors['payment_proof_path'] = 'Ukuran bukti pembayaran maksimal 5 MB.';
            return null;
        }

        $destinationDir = BASE_PATH . '/assets/uploads/payments';
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $filename = 'payment-proof-' . uniqid('', true) . '.' . $extension;
        $targetPath = $destinationDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors['payment_proof_path'] = 'Gagal menyimpan bukti pembayaran.';
            return null;
        }

        return 'assets/uploads/payments/' . $filename;
    }

    private function normalizeMoney($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d,\.-]/', '', (string) $value);
        $clean = str_replace(['.', ','], ['', '.'], $clean);
        return (float) $clean;
    }

    private function isValidDateTime(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        $d = date_create_from_format('Y-m-d H:i:s', $value);
        return $d && $d->format('Y-m-d H:i:s') === $value;
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

    private function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function currentUserRole(): string
    {
        return (string) ($_SESSION['user_role'] ?? 'admin');
    }

    private function isCustomerRole(string $role): bool
    {
        return strtolower(trim($role)) === 'customer';
    }

    private function isStaffRole(string $role): bool
    {
        return in_array(strtolower(trim($role)), ['admin', 'employee'], true);
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

