<?php
ob_start();

$payments = $payments ?? [];
$stats = $stats ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$statusLabels = $statusLabels ?? [];
$verificationOptions = $verificationOptions ?? ['pending', 'approved', 'rejected'];
$verificationLabels = $verificationLabels ?? [
    'pending' => 'Menunggu Verifikasi',
    'approved' => 'Terverifikasi',
    'rejected' => 'Ditolak',
];
$sortOptions = $sortOptions ?? [];
$perPageOptions = $perPageOptions ?? [10, 25, 50];
$invoiceOptions = $invoiceOptions ?? [];
$userRole = $userRole ?? (string) ($_SESSION['user_role'] ?? 'admin');
$isCustomer = isset($isCustomer) ? (bool) $isCustomer : ($userRole === 'customer');
$isStaff = isset($isStaff) ? (bool) $isStaff : in_array($userRole, ['admin', 'employee'], true);
$baseUrl = $baseUrl ?? '';
$listUrl = $listUrl ?? ($baseUrl === '' ? '/payments.php' : $baseUrl . '/payments.php');
$storeUrl = $storeUrl ?? (rtrim($baseUrl, '/') . '/crm/payments');
$verifyUrlBase = $verifyUrlBase ?? (rtrim($baseUrl, '/') . '/crm/payments');
$invoiceListUrl = $baseUrl === '' ? '/invoices.php' : $baseUrl . '/invoices.php';

$currentStatus = $filters['status'] ?? '';
$currentSort = $filters['sort'] ?? 'recent';
$currentSearch = $filters['search'] ?? '';
$startDate = $filters['start_date'] ?? '';
$endDate = $filters['end_date'] ?? '';
$perPage = (int)($filters['per_page'] ?? ($perPageOptions[0] ?? 10));
$currentPage = max(1, (int)($filters['page'] ?? 1));
$totalPayments = (int)($filters['total'] ?? count($payments));
$totalPages = max(1, (int)($filters['total_pages'] ?? 1));
$rowStart = $totalPayments === 0 ? 0 : (($currentPage - 1) * $perPage) + 1;
$rowEnd = $totalPayments === 0 ? 0 : min($totalPayments, $rowStart + count($payments) - 1);
$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;
$oldForm = $old['_form'] ?? '';
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$createButtonLabel = $isCustomer ? 'Upload Bukti Pembayaran' : 'Catat Pembayaran';

$formatCurrency = static function (float $value, string $currency = 'IDR'): string {
    $prefix = strtoupper($currency) === 'IDR' ? 'Rp' : strtoupper($currency) . ' ';
    return $prefix . number_format($value, 0, ',', '.');
};

$formatDate = static function (?string $date): string {
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M Y', $timestamp) : $date;
};

// Fallback: muat data pembayaran langsung dari database per pengguna jika belum disuplai controller
if (empty($payments)) {
    require_once __DIR__ . '/../app/functions.php';
    try {
        $pdo = db();
        $where = [];
        $params = [];
        if ($currentUserId > 0) {
            $where[] = '(p.user_id = :uid OR i.recipient_user_id = :uid OR i.user_id = :uid)';
            $params['uid'] = $currentUserId;
            if ($isCustomer) {
                $where[] = "COALESCE(p.verification_status, 'approved') = 'approved'";
            }
        }
        if ($currentStatus !== '') {
            $where[] = 'p.status = :status';
            $params['status'] = $currentStatus;
        }
        if ($startDate !== '') {
            $where[] = 'DATE(p.paid_at) >= :start';
            $params['start'] = $startDate;
        }
        if ($endDate !== '') {
            $where[] = 'DATE(p.paid_at) <= :end';
            $params['end'] = $endDate;
        }
        if ($currentSearch !== '') {
            $where[] = '(p.invoice_no LIKE :search OR p.invoice_title LIKE :search OR p.client_name LIKE :search OR p.company_name LIKE :search OR p.reference_no LIKE :search)';
            $params['search'] = '%' . $currentSearch . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $countSql = "SELECT COUNT(*) FROM crm_payments p LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id {$whereSql}";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalPayments = (int)($countStmt->fetchColumn() ?: 0);

        $offset = max(0, ($currentPage - 1) * $perPage);
        $dataSql = "
            SELECT p.id, p.user_id, p.invoice_id, p.invoice_no, p.invoice_title, p.client_name, p.company_name, p.payment_method AS method,
                   p.channel, p.reference_no, p.status, p.amount, p.currency_code, p.paid_at AS payment_date, p.created_at,
                   COALESCE(p.verification_status, 'approved') AS verification_status, p.verified_at, p.verification_notes,
                   i.status AS invoice_status, i.total_amount AS invoice_amount, (i.total_amount - i.amount_paid) AS invoice_amount_due
            FROM crm_payments p
            LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id
            {$whereSql}
            ORDER BY p.paid_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $dataStmt = $pdo->prepare($dataSql);
        foreach ($params as $key => $value) {
            $dataStmt->bindValue(':' . $key, $value);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $payments = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = max(1, (int)ceil($totalPayments / $perPage));
        $rowStart = $totalPayments === 0 ? 0 : $offset + 1;
        $rowEnd = $totalPayments === 0 ? 0 : min($totalPayments, $rowStart + count($payments) - 1);
    } catch (Throwable $exception) {
        // Biarkan apa adanya jika query gagal
    }
}

if ($isCustomer && !empty($payments)) {
    $payments = array_values(array_filter($payments, static function (array $payment): bool {
        $status = strtolower(trim((string) ($payment['verification_status'] ?? 'approved')));
        if ($status === '') {
            $status = 'approved';
        }
        return $status === 'approved';
    }));
}

$formatDateTimeInput = static function (?string $date): string {
    if (!$date) {
        return '';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('Y-m-d\TH:i', $timestamp) : '';
};

$statusBadge = static function (string $status): string {
    switch ($status) {
        case 'settled':
            return 'badge badge-soft-success';
        case 'partial':
            return 'badge badge-soft-info';
        case 'pending':
            return 'badge badge-soft-warning text-dark';
        case 'failed':
        case 'refunded':
            return 'badge badge-soft-danger';
        default:
            return 'badge badge-soft-secondary';
    }
};

$verificationBadge = static function (string $status): string {
    switch ($status) {
        case 'approved':
            return 'badge badge-soft-success';
        case 'rejected':
            return 'badge badge-soft-danger';
        case 'pending':
            return 'badge badge-soft-warning text-dark';
        default:
            return 'badge badge-soft-secondary';
    }
};

$buildFilterUrl = static function (array $overrides = []) use ($listUrl, $currentStatus, $currentSort, $currentSearch, $startDate, $endDate, $perPage, $currentPage) {
    $query = [
        'status' => $currentStatus,
        'sort' => $currentSort,
        'search' => $currentSearch,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'per_page' => $perPage,
        'page' => $currentPage,
    ];
    $query = array_merge($query, $overrides);
    return $listUrl . '?' . http_build_query($query);
};

$defaultPaidAt = $formatDateTimeInput($old['paid_at'] ?? date('Y-m-d H:i'));
?>
<style>
    .payments-table th,
    .payments-table td {
        vertical-align: middle;
        white-space: normal;
    }
    .payments-table .col-invoice { min-width: 180px; }
    .payments-table .col-client { min-width: 220px; }
    .payments-table .col-method { min-width: 170px; }
    .payments-table .col-amount { width: 170px; }
    .payments-table .col-actions { width: 180px; }
</style>

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Pembayaran Klien</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.php"><i class="ti ti-smart-home"></i></a></li>
                            <li class="breadcrumb-item">Penjualan &amp; Billing</li>
                            <li class="breadcrumb-item active" aria-current="page">Pembayaran Klien</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createPaymentModal"><i class="ti ti-circle-plus me-2"></i><?php echo htmlspecialchars($createButtonLabel); ?></a>
                    </div>
                </div>
            </div>

            <?php if ($successMessage): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xl-4 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Dibayar (periode)</p>
                            <h4 class="mb-1"><?php echo $formatCurrency((float)($stats['paid_amount'] ?? 0)); ?></h4>
                            <p class="text-success mb-0"><i class="ti ti-check me-1"></i>Pemasukan tercatat</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Menunggu / Termin</p>
                            <h4 class="mb-1"><?php echo $formatCurrency((float)($stats['pending_amount'] ?? 0)); ?></h4>
                            <p class="text-warning mb-0"><i class="ti ti-clock me-1"></i>Butuh follow-up</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Sisa Tagihan Terhubung</p>
                            <h4 class="mb-1"><?php echo $formatCurrency((float)($stats['linked_outstanding'] ?? 0)); ?></h4>
                            <p class="text-info mb-0"><i class="ti ti-link me-1"></i>Tagihan produk ANTARA</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <h5>Daftar Pembayaran</h5>
                    <form class="d-flex align-items-center flex-wrap gap-2" method="get" action="<?php echo htmlspecialchars($listUrl); ?>">
                        <input type="hidden" name="page" value="1">
                        <div class="input-group">
                            <span class="input-group-text">Periode</span>
                            <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
                            <span class="input-group-text">s.d.</span>
                            <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
                        </div>
                        <select class="form-select" name="status" onchange="this.form.submit();">
                            <option value="">Semua Status</option>
                            <?php foreach ($statusOptions as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>"<?php echo $currentStatus === $status ? ' selected' : ''; ?>>
                                    <?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select" name="sort" onchange="this.form.submit();">
                            <?php foreach ($sortOptions as $sortKey => $label): ?>
                                <option value="<?php echo htmlspecialchars($sortKey); ?>"<?php echo $currentSort === $sortKey ? ' selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="form-select" name="per_page" onchange="this.form.submit();">
                            <?php foreach ($perPageOptions as $option): ?>
                                <option value="<?php echo $option; ?>"<?php echo $perPage === (int)$option ? ' selected' : ''; ?>><?php echo $option; ?>/hal</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-icon-end position-relative">
                            <input type="search" name="search" class="form-control" placeholder="Cari invoice / klien / referensi" value="<?php echo htmlspecialchars($currentSearch); ?>">
                            <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center"><i class="ti ti-filter me-2"></i>Terapkan</button>
                        <a href="<?php echo htmlspecialchars($listUrl); ?>" class="btn btn-white">Reset</a>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table payments-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="col-invoice">Invoice</th>
                                    <th class="col-client">Klien</th>
                                    <th>Perusahaan</th>
                                    <th class="col-method">Metode</th>
                                    <th>Tanggal Bayar</th>
                                    <th class="text-end col-amount">Nominal</th>
                                    <th class="text-end col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
<?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar avatar-lg bg-light-primary avatar-rounded mb-2"><i class="ti ti-credit-card text-primary fs-20"></i></div>
                                            <h6 class="mb-1">Belum ada pembayaran tercatat</h6>
                                            <p class="text-muted mb-3"><?php echo $isCustomer ? 'Unggah bukti transfer agar dapat diverifikasi admin/pegawai.' : 'Catat pembayaran untuk menghubungkan ke tagihan produk ANTARA.'; ?></p>
                                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal"><i class="ti ti-circle-plus me-2"></i><?php echo htmlspecialchars($createButtonLabel); ?></a>
                                        </div>
                                    </td>
                                </tr>
<?php else: ?>
<?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <?php if (!empty($payment['invoice_no'])): ?>
                                                <a href="<?php echo htmlspecialchars($invoiceListUrl . '?search=' . urlencode($payment['invoice_no'])); ?>" class="link-info fw-semibold"><?php echo htmlspecialchars($payment['invoice_no']); ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak terhubung</span>
                                            <?php endif; ?>
                                            <?php if (!empty($payment['invoice_title'])): ?>
                                                <span class="text-muted small"><?php echo htmlspecialchars($payment['invoice_title']); ?></span>
                                            <?php endif; ?>
                                            <?php if (isset($payment['invoice_amount_due'])): ?>
                                                <span class="badge badge-soft-secondary mt-1">Sisa: <?php echo $formatCurrency(max(0, (float)$payment['invoice_amount_due'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($payment['client_avatar'])): ?>
                                                <span class="avatar avatar-md avatar-rounded"><img src="<?php echo htmlspecialchars($payment['client_avatar']); ?>" alt="Client"></span>
                                            <?php else: ?>
                                                <span class="avatar avatar-md avatar-rounded bg-primary-transparent text-primary"><i class="ti ti-user"></i></span>
                                            <?php endif; ?>
                                            <div class="ms-2">
                                                <h6 class="fw-medium mb-0"><?php echo htmlspecialchars($payment['client_name'] ?: '-'); ?></h6>
                                                <?php if (!empty($payment['client_position'])): ?>
                                                    <span class="text-muted small"><?php echo htmlspecialchars($payment['client_position']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium"><?php echo htmlspecialchars($payment['company_name'] ?: '-'); ?></span>
                                            <?php if (!empty($payment['invoice_status'])): ?>
                                                <span class="text-muted small text-capitalize">Status Invoice: <?php echo htmlspecialchars($payment['invoice_status']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?php echo htmlspecialchars($payment['payment_method'] ?: '-'); ?></div>
                                        <?php if (!empty($payment['channel'])): ?><div class="text-muted small"><?php echo htmlspecialchars($payment['channel']); ?></div><?php endif; ?>
                                        <?php if (!empty($payment['reference_no'])): ?><div class="text-muted small">Ref: <?php echo htmlspecialchars($payment['reference_no']); ?></div><?php endif; ?>
                                    </td>
                                    <td><?php echo $formatDate($payment['paid_at'] ?? null); ?></td>
                                    <td class="text-end">
                                        <div class="fw-semibold"><?php echo $formatCurrency((float)($payment['amount'] ?? 0), $payment['currency_code'] ?? 'IDR'); ?></div>
                                        <span class="<?php echo $statusBadge($payment['status'] ?? ''); ?>"><?php echo htmlspecialchars($statusLabels[$payment['status']] ?? ucfirst((string)($payment['status'] ?? ''))); ?></span>
                                        <?php
                                            $verificationStatus = strtolower(trim((string) ($payment['verification_status'] ?? 'approved')));
                                            if ($verificationStatus === '') {
                                                $verificationStatus = 'approved';
                                            }
                                        ?>
                                        <div class="mt-1">
                                            <span class="<?php echo $verificationBadge($verificationStatus); ?>">
                                                <?php echo htmlspecialchars($verificationLabels[$verificationStatus] ?? ucfirst($verificationStatus)); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-link text-primary p-0" data-payment-detail="<?php echo htmlspecialchars(json_encode($payment, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="modal" data-bs-target="#paymentDetailModal">Detail</button>
                                        <?php if ($isStaff && $verificationStatus === 'pending'): ?>
                                            <button
                                                type="button"
                                                class="btn btn-link text-success p-0 ms-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#verifyPaymentModal"
                                                data-payment-verify="1"
                                                data-verify-url="<?php echo htmlspecialchars($verifyUrlBase . '/' . (int) ($payment['id'] ?? 0) . '/verify'); ?>"
                                                data-invoice-no="<?php echo htmlspecialchars((string) ($payment['invoice_no'] ?? '')); ?>"
                                                data-client-name="<?php echo htmlspecialchars((string) ($payment['client_name'] ?? '')); ?>"
                                            >Verifikasi</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
<?php endforeach; ?>
<?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex flex-wrap align-items-center justify-content-between">
                    <p class="mb-0 text-muted">Menampilkan <?php echo $rowStart; ?>-<?php echo $rowEnd; ?> dari <?php echo number_format($totalPayments); ?> pembayaran</p>
                    <div class="d-flex gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo htmlspecialchars($buildFilterUrl(['page' => $currentPage - 1])); ?>" class="btn btn-white btn-sm"><i class="ti ti-chevron-left"></i></a>
                        <?php else: ?>
                            <button class="btn btn-white btn-sm" disabled><i class="ti ti-chevron-left"></i></button>
                        <?php endif; ?>
                        <span class="align-self-center text-muted">Hal <?php echo $currentPage; ?> / <?php echo $totalPages; ?></span>
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?php echo htmlspecialchars($buildFilterUrl(['page' => $currentPage + 1])); ?>" class="btn btn-white btn-sm"><i class="ti ti-chevron-right"></i></a>
                        <?php else: ?>
                            <button class="btn btn-white btn-sm" disabled><i class="ti ti-chevron-right"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <?php require_once __DIR__ . '/../partials/footer.php'; ?>
    </div>
    <div class="modal fade" id="createPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $isCustomer ? 'Upload Bukti Pembayaran' : 'Catat Pembayaran Klien'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body" enctype="multipart/form-data">
                    <input type="hidden" name="_form" value="create-payment">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hubungkan ke Tagihan<?php if ($isCustomer): ?> <span class="text-danger">*</span><?php endif; ?></label>
                            <select class="form-select" name="invoice_id" id="payment_invoice_id" <?php echo $isCustomer ? 'required' : ''; ?>>
                                <option value=""><?php echo $isCustomer ? 'Pilih tagihan' : 'Pilih tagihan (opsional)'; ?></option>
                                <?php foreach ($invoiceOptions as $invoice): ?>
                                    <option value="<?php echo (int)($invoice['id'] ?? 0); ?>"
                        data-invoice-no="<?php echo htmlspecialchars($invoice['invoice_no'] ?? ''); ?>"
                        data-client-name="<?php echo htmlspecialchars($invoice['client_name'] ?? ''); ?>"
                        data-company="<?php echo htmlspecialchars($invoice['client_company'] ?? ''); ?>"
                        data-position="<?php echo htmlspecialchars($invoice['client_position'] ?? ''); ?>"
                        data-avatar="<?php echo htmlspecialchars($invoice['client_avatar'] ?? ''); ?>"
                        data-payment-proof="<?php echo htmlspecialchars($invoice['payment_proof_path'] ?? ''); ?>">
                                        <?php echo htmlspecialchars(($invoice['invoice_no'] ?? '') . ' - ' . ($invoice['client_name'] ?? '')); ?> (Sisa <?php echo $formatCurrency(max(0, (float)($invoice['amount_due'] ?? 0))); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['invoice_id'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['invoice_id']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Invoice <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" id="payment_invoice_no" class="form-control<?php echo !empty($errors['invoice_no']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($old['invoice_no'] ?? ''); ?>" placeholder="INV-2024-XXXX" <?php echo $isCustomer ? 'readonly' : ''; ?>>
                            <?php if (!empty($errors['invoice_no'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['invoice_no']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="payment_client_name" class="form-control<?php echo !empty($errors['client_name']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($old['client_name'] ?? ''); ?>" placeholder="Nama PIC / klien" <?php echo $isCustomer ? 'readonly' : ''; ?>>
                            <?php if (!empty($errors['client_name'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['client_name']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" name="company_name" id="payment_company_name" class="form-control" value="<?php echo htmlspecialchars($old['company_name'] ?? ''); ?>" placeholder="Perusahaan klien" <?php echo $isCustomer ? 'readonly' : ''; ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="client_position" id="payment_client_position" class="form-control" value="<?php echo htmlspecialchars($old['client_position'] ?? ''); ?>" placeholder="Manager / Direktur" <?php echo $isCustomer ? 'readonly' : ''; ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Avatar (URL)</label>
                            <input type="text" name="client_avatar" id="payment_client_avatar" class="form-control" value="<?php echo htmlspecialchars($old['client_avatar'] ?? ''); ?>" placeholder="assets/img/users/user-45.jpg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" name="payment_method" class="form-control" value="<?php echo htmlspecialchars($old['payment_method'] ?? ''); ?>" placeholder="Transfer Bank / Virtual Account">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Channel / Gateway</label>
                            <input type="text" name="channel" class="form-control" value="<?php echo htmlspecialchars($old['channel'] ?? ''); ?>" placeholder="BCA VA / Midtrans / Paypal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" name="amount" class="form-control<?php echo !empty($errors['amount']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>" placeholder="10000000">
                            <?php if (!empty($errors['amount'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['amount']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bukti Pembayaran (Upload)<?php if ($isCustomer): ?> <span class="text-danger">*</span><?php endif; ?></label>
                            <input type="file" name="payment_proof" class="form-control" accept=".png,.jpg,.jpeg,.webp,.pdf" <?php echo $isCustomer ? 'required' : ''; ?>>
                            <?php if (!empty($errors['payment_proof_path'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['payment_proof_path']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="paid_at" class="form-control<?php echo !empty($errors['paid_at']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($formatDateTimeInput($old['paid_at'] ?? $defaultPaidAt)); ?>">
                            <?php if (!empty($errors['paid_at'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['paid_at']); ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <?php if ($isCustomer): ?>
                                <label class="form-label">Status</label>
                                <input type="hidden" name="status" value="pending">
                                <div class="form-control bg-light">Menunggu verifikasi admin/pegawai</div>
                            <?php else: ?>
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select<?php echo !empty($errors['status']) ? ' is-invalid' : ''; ?>">
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option value="<?php echo htmlspecialchars($status); ?>"<?php echo ($old['status'] ?? 'settled') === $status ? ' selected' : ''; ?>><?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['status'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Referensi / Nomor Transaksi</label>
                            <input type="text" name="reference_no" class="form-control" value="<?php echo htmlspecialchars($old['reference_no'] ?? ''); ?>" placeholder="VA/Transfer Reference">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Catatan internal atau keterangan pembayaran"><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($isStaff): ?>
    <div class="modal fade" id="verifyPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="verifyPaymentForm">
                    <div class="modal-body">
                        <p class="text-muted mb-2">Konfirmasi pembayaran untuk <strong id="verify_payment_client">-</strong>.</p>
                        <p class="text-muted mb-3">Invoice: <span id="verify_payment_invoice">-</span></p>
                        <div class="mb-3">
                            <label class="form-label">Hasil Verifikasi</label>
                            <select class="form-select" name="verification_status" id="verify_status_select" required>
                                <option value="approved">Setujui</option>
                                <option value="rejected">Tolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Pembayaran Saat Disetujui</label>
                            <select class="form-select" name="status" id="verify_payment_status">
                                <option value="settled">Lunas</option>
                                <option value="partial">Sebagian</option>
                            </select>
                            <small class="text-muted">Field ini hanya dipakai ketika verifikasi disetujui.</small>
                        </div>
                        <div>
                            <label class="form-label">Catatan Verifikasi</label>
                            <textarea class="form-control" name="verification_notes" rows="3" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Invoice</p>
                            <h5 class="mb-1" id="detail_invoice_no">-</h5>
                            <p class="text-muted mb-2" id="detail_invoice_title"></p>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md avatar-rounded bg-primary-transparent text-primary me-2" id="detail_avatar_wrapper"><i class="ti ti-user"></i></span>
                                <div>
                                    <h6 class="mb-0" id="detail_client_name">-</h6>
                                    <p class="text-muted small mb-0" id="detail_client_position"></p>
                                </div>
                            </div>
                            <p class="mt-2 mb-0" id="detail_company">-</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Tanggal Bayar</p>
                                    <h6 id="detail_paid_at">-</h6>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Status</p>
                                    <span class="badge" id="detail_status_badge">-</span>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Verifikasi</p>
                                    <p class="mb-0" id="detail_verification_status">-</p>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Nominal</p>
                                    <h5 id="detail_amount">-</h5>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Metode</p>
                                    <p class="mb-0" id="detail_method">-</p>
                                    <p class="text-muted small mb-0" id="detail_channel"></p>
                                    <p class="text-muted small mb-0" id="detail_reference"></p>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Sisa Tagihan</p>
                                    <p class="mb-0" id="detail_outstanding">-</p>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Catatan</p>
                                    <p class="mb-0" id="detail_notes">-</p>
                                </div>
                                <div class="col-12">
                                    <p class="text-muted mb-1">Bukti Pembayaran</p>
                                    <div class="d-flex flex-column gap-1" id="detail_payment_proof_wrapper">
                                        <p class="mb-0" id="detail_payment_proof">-</p>
                                        <div id="detail_payment_proof_preview" class="d-none">
                                            <img src="" alt="Bukti pembayaran" class="img-fluid rounded border" style="max-height:260px;object-fit:contain;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <a href="<?php echo htmlspecialchars($invoiceListUrl); ?>" class="btn btn-primary" id="detail_invoice_link" target="_blank">Buka Tagihan</a>
                </div>
            </div>
        </div>
    </div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../partials/main.php';
?>

<script>
    (function() {
        const detailInvoice = document.getElementById('detail_invoice_no');
        const detailInvoiceTitle = document.getElementById('detail_invoice_title');
        const detailClient = document.getElementById('detail_client_name');
        const detailPosition = document.getElementById('detail_client_position');
        const detailCompany = document.getElementById('detail_company');
        const detailAvatar = document.getElementById('detail_avatar_wrapper');
        const detailPaidAt = document.getElementById('detail_paid_at');
        const detailStatusBadge = document.getElementById('detail_status_badge');
        const detailVerificationStatus = document.getElementById('detail_verification_status');
        const detailAmount = document.getElementById('detail_amount');
        const detailMethod = document.getElementById('detail_method');
        const detailChannel = document.getElementById('detail_channel');
        const detailReference = document.getElementById('detail_reference');
        const detailOutstanding = document.getElementById('detail_outstanding');
        const detailNotes = document.getElementById('detail_notes');
        const detailPaymentProof = document.getElementById('detail_payment_proof');
        const detailPaymentProofWrapper = document.getElementById('detail_payment_proof_wrapper');
        const detailPaymentProofPreview = document.getElementById('detail_payment_proof_preview');
        const detailPaymentProofImage = detailPaymentProofPreview?.querySelector('img');
        const detailInvoiceLink = document.getElementById('detail_invoice_link');
        const verificationLabelMap = <?php echo json_encode($verificationLabels, JSON_UNESCAPED_UNICODE); ?>;

        const currencyFormat = function(value) {
            const number = Number(value || 0);
            return 'Rp' + number.toLocaleString('id-ID');
        };

        const isImageFile = function(path) {
            return typeof path === 'string' && /\.(png|jpe?g|jpeg|webp|gif)$/i.test(path.split('?')[0]);
        };

        document.querySelectorAll('[data-payment-detail]').forEach(function(button) {
            button.addEventListener('click', function() {
                try {
                    const data = JSON.parse(this.getAttribute('data-payment-detail') || '{}');
                    detailInvoice.textContent = data.invoice_no || '-';
                    detailInvoiceTitle.textContent = data.invoice_title || '';
                    detailClient.textContent = data.client_name || '-';
                    detailPosition.textContent = data.client_position || '';
                    detailCompany.textContent = data.company_name || '';
                    detailPaidAt.textContent = data.paid_at ? new Date(data.paid_at).toLocaleString('id-ID') : '-';
                    detailAmount.textContent = currencyFormat(data.amount);
                    detailMethod.textContent = data.payment_method || '-';
                    detailChannel.textContent = data.channel ? 'Channel: ' + data.channel : '';
                    detailReference.textContent = data.reference_no ? 'Ref: ' + data.reference_no : '';
                    detailOutstanding.textContent = typeof data.invoice_amount_due !== 'undefined' ? currencyFormat(Math.max(0, data.invoice_amount_due)) : '-';
                    detailNotes.textContent = data.notes || '-';
                    const verificationStatus = String(data.verification_status || 'approved').toLowerCase();
                    detailVerificationStatus.textContent = verificationLabelMap[verificationStatus] || verificationStatus;
                    if (data.payment_proof_path) {
                        const safeUrl = data.payment_proof_path;
                        detailPaymentProof.innerHTML = '<a href=\"' + safeUrl + '\" target=\"_blank\" rel=\"noopener\">Lihat bukti</a>';
                        if (detailPaymentProofWrapper && detailPaymentProofPreview && detailPaymentProofImage) {
                            if (isImageFile(safeUrl)) {
                                detailPaymentProofPreview.classList.remove('d-none');
                                detailPaymentProofImage.src = safeUrl;
                            } else {
                                detailPaymentProofPreview.classList.add('d-none');
                                detailPaymentProofImage.src = '';
                            }
                        }
                    } else {
                        detailPaymentProof.textContent = '-';
                        if (detailPaymentProofPreview && detailPaymentProofImage) {
                            detailPaymentProofPreview.classList.add('d-none');
                            detailPaymentProofImage.src = '';
                        }
                    }
                    detailStatusBadge.textContent = data.status || '-';
                    const badge = this.closest('tr')?.querySelector('td.text-end .badge');
                    detailStatusBadge.className = 'badge ' + (badge ? badge.className.replace('badge ', '') : 'badge-soft-secondary');

                    if (data.client_avatar) {
                        detailAvatar.innerHTML = '<img src="' + data.client_avatar + '" class="img-fluid rounded-circle" alt="Client">';
                    } else {
                        detailAvatar.innerHTML = '<i class="ti ti-user"></i>';
                        detailAvatar.classList.add('bg-primary-transparent', 'text-primary');
                    }

                    if (data.invoice_no) {
                        detailInvoiceLink.href = '<?php echo htmlspecialchars($invoiceListUrl); ?>' + '?search=' + encodeURIComponent(data.invoice_no);
                        detailInvoiceLink.classList.remove('disabled');
                    } else {
                        detailInvoiceLink.href = '<?php echo htmlspecialchars($invoiceListUrl); ?>';
                        detailInvoiceLink.classList.add('disabled');
                    }
                } catch (error) {
                    console.error('Gagal memuat detail pembayaran', error);
                }
            });
        });

        const invoiceSelect = document.getElementById('payment_invoice_id');
        if (invoiceSelect) {
            invoiceSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (!option) return;

                const invoiceNo = option.getAttribute('data-invoice-no') || '';
                const clientName = option.getAttribute('data-client-name') || '';
                const company = option.getAttribute('data-company') || '';
                const position = option.getAttribute('data-position') || '';
                const avatar = option.getAttribute('data-avatar') || '';

                if (invoiceNo && document.getElementById('payment_invoice_no')) {
                    document.getElementById('payment_invoice_no').value = invoiceNo;
                }
                if (clientName && document.getElementById('payment_client_name')) {
                    document.getElementById('payment_client_name').value = clientName;
                }
                if (company && document.getElementById('payment_company_name')) {
                    document.getElementById('payment_company_name').value = company;
                }
                if (position && document.getElementById('payment_client_position')) {
                    document.getElementById('payment_client_position').value = position;
                }
                if (avatar && document.getElementById('payment_client_avatar')) {
                    document.getElementById('payment_client_avatar').value = avatar;
                }
            });
        }

        const verifyForm = document.getElementById('verifyPaymentForm');
        const verifyClient = document.getElementById('verify_payment_client');
        const verifyInvoice = document.getElementById('verify_payment_invoice');
        const verifyStatusSelect = document.getElementById('verify_status_select');
        const verifyPaymentStatus = document.getElementById('verify_payment_status');

        const syncVerifyPaymentStatus = function() {
            if (!verifyStatusSelect || !verifyPaymentStatus) return;
            const approved = verifyStatusSelect.value === 'approved';
            verifyPaymentStatus.disabled = !approved;
        };

        if (verifyStatusSelect) {
            verifyStatusSelect.addEventListener('change', syncVerifyPaymentStatus);
            syncVerifyPaymentStatus();
        }

        document.querySelectorAll('[data-payment-verify]').forEach(function(button) {
            button.addEventListener('click', function() {
                if (!verifyForm) return;
                verifyForm.setAttribute('action', this.getAttribute('data-verify-url') || '');
                if (verifyClient) {
                    verifyClient.textContent = this.getAttribute('data-client-name') || '-';
                }
                if (verifyInvoice) {
                    verifyInvoice.textContent = this.getAttribute('data-invoice-no') || '-';
                }
            });
        });

        if ('<?php echo $oldForm; ?>' === 'create-payment') {
            const modal = new bootstrap.Modal(document.getElementById('createPaymentModal'));
            modal.show();
        }
    })();
</script>
