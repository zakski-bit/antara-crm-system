<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\ProductInvoiceRepository;

class InvoicesController extends Controller
{
    private ProductInvoiceRepository $repository;

    /** @var array<string, string> */
    private array $statusLabels = [
        'paid' => 'Lunas',
        'pending' => 'Menunggu',
        'overdue' => 'Jatuh Tempo',
        'draft' => 'Draft',
        'partial' => 'Terbayar Sebagian',
    ];

    /** @var array<string, string> */
    private array $sortLabels = [
        'recent' => 'Terbaru',
        'due_asc' => 'Jatuh Tempo Terdekat',
        'due_desc' => 'Jatuh Tempo Terlama',
        'amount_desc' => 'Nominal Terbesar',
        'amount_asc' => 'Nominal Terkecil',
    ];

    /** @var array<int> */
    private array $perPageOptions = [10, 25, 50, 100];

    /** @var array<string> */
    private array $directionOptions = ['outgoing', 'incoming'];

    /** @var array<string> */
    private array $verificationStatuses = ['pending', 'approved', 'rejected'];

    /** @var array<string> */
    private array $paymentStatuses = ['pending', 'processing', 'settled'];

    public function __construct()
    {
        $this->repository = new ProductInvoiceRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $userRole = $this->currentUserRole();
        $status = $this->normalizeStatus($request->query('status', ''));
        $sort = $this->normalizeSort($request->query('sort', 'recent'));
        $search = trim((string) $request->query('search', ''));
        $perPage = $this->normalizePerPage((int) $request->query('per_page', 10));
        $page = max(1, (int) $request->query('page', 1));
        // Default: semua role diarahkan ke tagihan keluar (outgoing); pelanggan difilter ke tagihan yang ditujukan ke dirinya
        $directionDefault = $userRole === 'customer' ? 'outgoing' : 'outgoing';
        $direction = $this->normalizeDirection($request->query('direction', $directionDefault));
        $recipientUserId = null;
        if ($userRole === 'customer') {
            // Pelanggan bisa melihat tagihan yang ditujukan ke dirinya (recipient)
            $recipientUserId = $userId;
        } elseif ($direction === 'incoming') {
            // Admin/pegawai melihat tagihan yang ditujukan ke dirinya
            $recipientUserId = $userId;
        }

        $result = $this->repository->paginate(
            $userId,
            $status ?: null,
            $sort,
            $perPage,
            $page,
            $search !== '' ? $search : null,
            $direction,
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
                $direction,
                $recipientUserId
            );
        }

        $invoices = array_map([$this, 'transformInvoice'], $result['data']);

        $stats = $this->buildStats($userId, $direction, $recipientUserId);
        $baseUrl = $this->baseUrl();
        $listUrl = $baseUrl === '' ? '/invoices' : $baseUrl . '/invoices';

        $customerOptions = $userRole !== 'customer' ? $this->customerOptions() : [];
        $staffOptions = $this->staffOptions();

        $this->view('invoices/index', [
            'title' => 'Tagihan Produk',
            'invoices' => $invoices,
            'stats' => $stats,
            'flash' => $this->pullFlash(),
            'errors' => $this->pullErrors(),
            'old' => $this->pullOld(),
            'userRole' => $userRole,
            'filters' => [
                'status' => $status,
                'sort' => $sort,
                'search' => $search,
                'per_page' => $perPage,
                'page' => $page,
                'total' => $result['total'],
                'total_pages' => $totalPages,
                'direction' => $direction,
            ],
            'statusOptions' => array_keys($this->statusLabels),
            'statusLabels' => $this->statusLabels,
            'sortOptions' => $this->sortLabels,
            'perPageOptions' => $this->perPageOptions,
            'directionOptions' => $this->directionOptions,
            'customerOptions' => $customerOptions,
            'staffOptions' => $staffOptions,
            'baseUrl' => $baseUrl,
            'listUrl' => $listUrl,
            'storeUrl' => $this->url('/crm/invoices'),
        ]);
    }

    public function store(Request $request): void
    {
        if ($this->currentUserRole() === 'customer') {
            $this->flash('error', 'Pelanggan tidak dapat menambahkan tagihan.');
            $this->redirect($this->url('/invoices'));
        }

        $userId = $this->currentUserId();
        $broadcastAll = (bool) $request->input('broadcast_all', false);
        [$data, $items] = $this->sanitize($request);
        $errors = $this->validate($data, $items, false, $userId, null, $broadcastAll);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal menambahkan tagihan produk. Mohon periksa kembali data yang diisi.');
            $this->redirect($this->url('/invoices'));
        }

        if ($broadcastAll) {
            $customers = $this->customerOptions();
            if (empty($customers)) {
                $this->flash('error', 'Tidak ada klien untuk dikirimi tagihan.');
                $this->redirect($this->url('/invoices'));
            }
            $sent = 0;
            foreach ($customers as $customer) {
                $payload = $data;
                $payload['recipient_user_id'] = (int) ($customer['id'] ?? 0);
                $payload['invoice_no'] = $this->generateInvoiceNumber();
                if (trim((string) $payload['client_name']) === '') {
                    $payload['client_name'] = $customer['name'] ?? 'Klien';
                }
                if (trim((string) $payload['client_company']) === '') {
                    $payload['client_company'] = $customer['email'] ?? '';
                }
                $this->repository->create($userId, $payload, $items);
                $sent++;
            }
            $this->flash('success', "Tagihan produk dikirim ke {$sent} klien.");
            $this->redirect($this->url('/invoices'));
        } else {
            if (trim($data['invoice_no']) === '') {
                $data['invoice_no'] = $this->generateInvoiceNumber();
            }

            $this->repository->create($userId, $data, $items);
            $this->flash('success', 'Tagihan produk berhasil ditambahkan.');
        }
        $this->redirect($this->url('/invoices'));
    }

    public function update(Request $request, int $id): void
    {
        if ($this->currentUserRole() === 'customer') {
            $this->flash('error', 'Pelanggan tidak dapat memperbarui tagihan.');
            $this->redirect($this->url('/invoices'));
        }

        $userId = $this->currentUserId();
        $invoice = $this->repository->find($userId, $id);
        if (!$invoice) {
            $this->flash('error', 'Tagihan tidak ditemukan.');
            $this->redirect($this->url('/invoices'));
        }

        [$data, $items] = $this->sanitize($request, $invoice);
        $errors = $this->validate($data, $items, true, $userId, $id);

        if (!empty($errors)) {
            $this->pushErrors($errors);
            $this->pushOld($request->all());
            $this->flash('error', 'Gagal memperbarui tagihan produk.');
            $this->redirect($this->url('/invoices'));
        }

        if (trim($data['invoice_no']) === '') {
            $data['invoice_no'] = $invoice['invoice_no'] ?? $this->generateInvoiceNumber();
        }

        $this->repository->update($userId, $id, $data, $items);
        $this->flash('success', 'Tagihan produk berhasil diperbarui.');
        $this->redirect($this->url('/invoices'));
    }

    public function destroy(Request $request, int $id): void
    {
        if ($this->currentUserRole() === 'customer') {
            $this->flash('error', 'Pelanggan tidak dapat menghapus tagihan.');
            $this->redirect($this->url('/invoices'));
        }

        $userId = $this->currentUserId();
        $invoice = $this->repository->find($userId, $id);
        if (!$invoice) {
            $this->flash('error', 'Tagihan tidak ditemukan.');
            $this->redirect($this->url('/invoices'));
        }

        if ($this->repository->delete($userId, $id)) {
            $this->flash('success', 'Tagihan produk berhasil dihapus.');
        } else {
            $this->flash('error', 'Gagal menghapus tagihan produk.');
        }

        $this->redirect($this->url('/invoices'));
    }

    /**
     * @return array{0: array, 1: array<int, array>}
     */
    private function sanitize(Request $request, ?array $existing = null): array
    {
        $issueDate = $this->normalizeDateTime($request->input('issue_date'));
        $dueDate = $this->normalizeDateTime($request->input('due_date'));
        $items = $this->normalizeItems($request->input('items', []));
        $verificationStatus = $this->normalizeVerificationStatus($request->input('verification_status', $existing['verification_status'] ?? 'pending'));
        $paymentStatus = $this->normalizePaymentStatus($request->input('payment_status', $existing['payment_status'] ?? 'pending'));
        $receivedAt = $this->normalizeDateTime($request->input('received_at', $existing['received_at'] ?? null));
        $vendorName = trim((string) $request->input('vendor_name', $existing['vendor_name'] ?? ''));
        $periodLabel = trim((string) $request->input('period_label', $existing['period_label'] ?? ''));
        $attachmentPath = trim((string) $request->input('attachment_path', $existing['attachment_path'] ?? ''));
        $paymentProofPath = trim((string) $request->input('payment_proof_path', $existing['payment_proof_path'] ?? ''));

        $subtotal = array_reduce($items, static function ($carry, $item) {
            return $carry + (float) ($item['line_total'] ?? 0);
        }, 0.0);

        $status = $this->normalizeStatus($request->input('status', ''));
        $direction = $this->normalizeDirection($request->input('direction', $existing['direction'] ?? 'outgoing'));
        $recipientUserId = (int) $request->input('recipient_user_id', $existing['recipient_user_id'] ?? 0);
        $amountPaidInput = $this->normalizeMoney($request->input('amount_paid', '0'));
        $amountPaid = $status === 'paid' ? $subtotal : min(max($amountPaidInput, 0), $subtotal);

        $data = [
            'invoice_no' => trim((string) $request->input('invoice_no', $existing['invoice_no'] ?? '')),
            'invoice_title' => trim((string) $request->input('invoice_title', $existing['invoice_title'] ?? '')),
            'client_name' => trim((string) $request->input('client_name', $existing['client_name'] ?? '')),
            'client_company' => trim((string) $request->input('client_company', $existing['client_company'] ?? '')),
            'client_position' => trim((string) $request->input('client_position', $existing['client_position'] ?? '')),
            'client_email' => trim((string) $request->input('client_email', $existing['client_email'] ?? '')),
            'client_phone' => trim((string) $request->input('client_phone', $existing['client_phone'] ?? '')),
            'client_avatar' => trim((string) $request->input('client_avatar', $existing['client_avatar'] ?? '')),
            'issue_date' => $issueDate ?? ($existing['issue_date'] ?? date('Y-m-d H:i:s')),
            'due_date' => $dueDate,
            'status' => $status ?: ($existing['status'] ?? 'draft'),
            'direction' => $direction ?: ($existing['direction'] ?? 'outgoing'),
            'recipient_user_id' => $recipientUserId > 0 ? $recipientUserId : null,
            'currency_code' => 'IDR',
            'total_amount' => $subtotal,
            'amount_paid' => $amountPaid,
            'notes' => trim((string) $request->input('notes', $existing['notes'] ?? '')),
            'terms' => trim((string) $request->input('terms', $existing['terms'] ?? '')),
            'reference_no' => trim((string) $request->input('reference_no', $existing['reference_no'] ?? '')),
            'channel' => trim((string) $request->input('channel', $existing['channel'] ?? '')),
            'vendor_name' => $vendorName,
            'period_label' => $periodLabel,
            'subtotal_amount' => $subtotal,
            'tax_amount' => $this->normalizeMoney($request->input('tax_amount', $existing['tax_amount'] ?? 0)),
            'grand_total' => $this->normalizeMoney($request->input('grand_total', $existing['grand_total'] ?? $subtotal)),
            'received_at' => $receivedAt ?: ($existing['received_at'] ?? null),
            'verification_status' => $verificationStatus,
            'payment_status' => $paymentStatus,
            'attachment_path' => $attachmentPath,
            'payment_proof_path' => $paymentProofPath,
        ];

        if ($data['client_avatar'] === '') {
            $data['client_avatar'] = $existing['client_avatar'] ?? '';
        }

        return [$data, $items];
    }

    /**
     * @param array $data
     * @param array<int, array> $items
     */
    private function validate(array $data, array $items, bool $isUpdate = false, ?int $userId = null, ?int $currentId = null, bool $broadcastAll = false): array
    {
        $errors = [];

        if ($data['invoice_title'] === '') {
            $errors['invoice_title'] = 'Judul tagihan wajib diisi.';
        }

        if ($data['client_name'] === '') {
            $errors['client_name'] = 'Nama kontak klien wajib diisi.';
        }

        if ($data['client_company'] === '') {
            $errors['client_company'] = 'Nama perusahaan wajib diisi.';
        }

        if ($data['client_email'] !== '' && !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['client_email'] = 'Format email tidak valid.';
        }

        if ($data['status'] === '' || !isset($this->statusLabels[$data['status']])) {
            $errors['status'] = 'Status tagihan tidak valid.';
        }

        if (!in_array($data['direction'], $this->directionOptions, true)) {
            $errors['direction'] = 'Arah tagihan tidak valid.';
        }

        if ($data['direction'] === 'outgoing' && empty($data['recipient_user_id']) && !$broadcastAll) {
            $errors['recipient_user_id'] = 'Pilih klien tujuan.';
        }

        if ($data['direction'] === 'incoming') {
            if ($data['vendor_name'] === '') {
                $errors['vendor_name'] = 'Nama mitra / vendor wajib diisi.';
            }
            if ($data['period_label'] === '') {
                $errors['period_label'] = 'Periode layanan wajib diisi.';
            }
            if (!in_array($data['verification_status'], $this->verificationStatuses, true)) {
                $errors['verification_status'] = 'Status verifikasi tidak valid.';
            }
            if (!in_array($data['payment_status'], $this->paymentStatuses, true)) {
                $errors['payment_status'] = 'Status pembayaran tidak valid.';
            }
        }

        if (!$this->isValidDateTime($data['issue_date'])) {
            $errors['issue_date'] = 'Tanggal pembuatan tidak valid.';
        }

        if ($data['due_date'] && !$this->isValidDateTime($data['due_date'])) {
            $errors['due_date'] = 'Tanggal jatuh tempo tidak valid.';
        }

        if ($data['issue_date'] && $data['due_date'] && $data['due_date'] < $data['issue_date']) {
            $errors['due_date'] = 'Tanggal jatuh tempo harus setelah tanggal pembuatan.';
        }

        if (empty($items)) {
            $errors['items'] = 'Tambahkan minimal satu layanan / produk.';
        }

        if ($data['total_amount'] <= 0) {
            $errors['items'] = 'Total tagihan harus lebih besar dari 0.';
        }

        if ($data['amount_paid'] < 0) {
            $errors['amount_paid'] = 'Nominal pelunasan tidak boleh negatif.';
        }

        if ($data['amount_paid'] > $data['total_amount']) {
            $errors['amount_paid'] = 'Nominal pelunasan tidak boleh melebihi total tagihan.';
        }

        if ($userId && $data['invoice_no'] !== '') {
            $exists = $this->repository->invoiceNoExists($userId, $data['invoice_no'], $isUpdate ? $currentId : null);
            if ($exists) {
                $errors['invoice_no'] = 'Nomor tagihan sudah digunakan.';
            }
        }

        return $errors;
    }

    /**
     * @param array<int, array|string> $itemsInput
     * @return array<int, array>
     */
    private function normalizeItems($itemsInput): array
    {
        if (!is_array($itemsInput)) {
            return [];
        }

        $normalized = [];
        foreach ($itemsInput as $index => $row) {
            if (!is_array($row)) {
                continue;
            }
            $description = trim((string) ($row['description'] ?? $row['product_name'] ?? ''));
            if ($description === '') {
                continue;
            }

            $productName = trim((string) ($row['product_name'] ?? $description));
            $quantity = $this->normalizeNumber($row['quantity'] ?? 1);
            $unitPrice = $this->normalizeMoney($row['unit_price'] ?? 0);
            $discountPercent = $this->normalizeNumber($row['discount_percent'] ?? 0);

            $quantity = $quantity <= 0 ? 1 : $quantity;
            $unitPrice = $unitPrice < 0 ? 0 : $unitPrice;
            $discountPercent = max(0, min(100, $discountPercent));

            $lineTotal = ($quantity * $unitPrice);
            if ($discountPercent > 0) {
                $lineTotal -= $lineTotal * ($discountPercent / 100);
            }

            $normalized[] = [
                'line_order' => (int) $index,
                'product_name' => $productName === '' ? $description : $productName,
                'description' => $description,
                'quantity' => round($quantity, 2),
                'unit_price' => round($unitPrice, 2),
                'discount_percent' => round($discountPercent, 2),
                'line_total' => round($lineTotal, 2),
            ];
        }

        return $normalized;
    }

    private function transformInvoice(array $invoice): array
    {
        $invoice['total_amount'] = (float) ($invoice['total_amount'] ?? 0);
        $invoice['amount_paid'] = (float) ($invoice['amount_paid'] ?? 0);
        $invoice['amount_due'] = isset($invoice['amount_due'])
            ? (float) $invoice['amount_due']
            : max(0, $invoice['total_amount'] - $invoice['amount_paid']);
        $invoice['issue_date'] = $invoice['issue_date'] ?? date('Y-m-d H:i:s');
        $invoice['due_date'] = $invoice['due_date'] ?? null;
        $invoice['client_avatar'] = trim((string) ($invoice['client_avatar'] ?? ''));
        if ($invoice['client_avatar'] === '') {
            $invoice['client_avatar'] = 'assets/img/users/user-01.jpg';
        }

        $invoice['items'] = array_values(array_map(static function ($item) {
            $item['quantity'] = (float) ($item['quantity'] ?? 0);
            $item['unit_price'] = (float) ($item['unit_price'] ?? 0);
            $item['line_total'] = (float) ($item['line_total'] ?? 0);
            $item['discount_percent'] = (float) ($item['discount_percent'] ?? 0);
            $item['product_name'] = $item['product_name'] ?? ($item['description'] ?? 'Item');
            return $item;
        }, $invoice['items'] ?? []));

        return $invoice;
    }

    private function buildStats(int $userId, ?string $direction = null, ?int $recipientUserId = null): array
    {
        $summary = $this->repository->statsSummary($userId, $direction, $recipientUserId);
        $current = $summary['current'];
        $previous = $summary['previous'];

        return [
            'total' => [
                'value' => $current['total_amount'],
                'change' => $this->percentChange($current['total_amount'], $previous['total_amount']),
            ],
            'outstanding' => [
                'value' => $current['outstanding'],
                'change' => $this->percentChange($current['outstanding'], $previous['outstanding']),
            ],
            'draft' => [
                'value' => $current['draft_amount'],
                'change' => $this->percentChange($current['draft_amount'], $previous['draft_amount']),
            ],
            'overdue' => [
                'value' => $current['overdue_amount'],
                'change' => $this->percentChange($current['overdue_amount'], $previous['overdue_amount']),
            ],
        ];
    }

    private function percentChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current === 0.0 ? 0.0 : null;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function normalizeStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return array_key_exists($status, $this->statusLabels) ? $status : '';
    }

    private function normalizeSort(?string $sort): string
    {
        $sort = strtolower(trim((string) $sort));
        return array_key_exists($sort, $this->sortLabels) ? $sort : 'recent';
    }

    private function normalizeDirection(?string $direction): string
    {
        $direction = strtolower(trim((string) $direction));
        return in_array($direction, $this->directionOptions, true) ? $direction : 'outgoing';
    }

    private function normalizeVerificationStatus(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, $this->verificationStatuses, true) ? $value : 'pending';
    }

    private function normalizePaymentStatus(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, $this->paymentStatuses, true) ? $value : 'pending';
    }

    private function normalizePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true) ? $value : $this->perPageOptions[0];
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

    private function normalizeMoney($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $clean = preg_replace('/[^\d,\.-]/', '', (string) $value);
        $clean = str_replace(['.', ','], ['', '.'], $clean);
        return (float) $clean;
    }

    private function normalizeNumber($value): float
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
            return true;
        }

        $d = date_create_from_format('Y-m-d H:i:s', $value);
        return $d && $d->format('Y-m-d H:i:s') === $value;
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
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

    private function currentUserRole(): string
    {
        return (string) ($_SESSION['user_role'] ?? 'admin');
    }

    private function customerOptions(): array
    {
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'customer' ORDER BY name ASC");
        return $stmt->fetchAll() ?: [];
    }

    private function staffOptions(): array
    {
        $pdo = \App\Core\Database::connection();
        $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role IN ('admin', 'employee') ORDER BY name ASC");
        return $stmt->fetchAll() ?: [];
    }
}

