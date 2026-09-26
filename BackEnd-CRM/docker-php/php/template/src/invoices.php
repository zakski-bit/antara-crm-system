<?php ob_start();

$invoices = $invoices ?? [];
$stats = $stats ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$statusLabels = $statusLabels ?? [];
$sortOptions = $sortOptions ?? [];
$perPageOptions = $perPageOptions ?? [10, 25, 50];
$baseUrl = $baseUrl ?? '';
$listUrl = $listUrl ?? ($baseUrl === '' ? '/invoices.php' : $baseUrl . '/invoices.php');
$storeUrl = $storeUrl ?? (rtrim($baseUrl, '/') . '/crm/invoices');

$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;
$currentStatus = $filters['status'] ?? '';
$currentSort = $filters['sort'] ?? 'recent';
$currentSearch = $filters['search'] ?? '';
$perPage = (int)($filters['per_page'] ?? ($perPageOptions[0] ?? 10));
$currentPage = max(1, (int)($filters['page'] ?? 1));
$currentDirection = $filters['direction'] ?? 'outgoing';
$totalInvoices = (int)($filters['total'] ?? count($invoices));
$totalPages = max(1, (int)($filters['total_pages'] ?? 1));
$rowStart = $totalInvoices === 0 ? 0 : (($currentPage - 1) * $perPage) + 1;
$rowEnd = $totalInvoices === 0 ? 0 : min($totalInvoices, $rowStart + count($invoices) - 1);
$currentStatusLabel = $currentStatus !== '' ? ($statusLabels[$currentStatus] ?? ucfirst($currentStatus)) : 'Semua Status';
$currentSortLabel = $sortOptions[$currentSort] ?? 'Terbaru';
$directionLabels = [
    'outgoing' => 'Tagihan ke Mitra',
    'incoming' => 'Tagihan dari Mitra',
];
$verificationLabels = [
    'pending' => 'Menunggu Verifikasi',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
];
$paymentStatusLabels = [
    'pending' => 'Menunggu Pembayaran',
    'processing' => 'Dalam Proses',
    'settled' => 'Lunas',
];
$oldForm = $old['_form'] ?? '';
$userRole = $userRole ?? ($_SESSION['user_role'] ?? '');
$isCustomer = $userRole === 'customer';
$currentUserId = (int)($_SESSION['user_id'] ?? 0);

$oldItems = [];
if (!empty($old['items']) && is_array($old['items'])) {
    foreach ($old['items'] as $item) {
        if (is_array($item)) {
            $oldItems[] = $item;
        }
    }
}
if (empty($oldItems)) {
    $oldItems[] = [
        'product_name' => '',
        'description' => '',
        'quantity' => '1',
        'unit_price' => '',
        'discount_percent' => '',
    ];
}

$metricDefaults = [
    'total' => ['title' => 'Total Tagihan', 'description' => 'Nilai tagihan bulan ini'],
    'outstanding' => ['title' => 'Outstanding', 'description' => 'Sisa yang belum ditagih'],
    'draft' => ['title' => 'Draft', 'description' => 'Draft internal sebelum dikirim'],
    'overdue' => ['title' => 'Total Overdue', 'description' => 'Tagihan melewati jatuh tempo'],
];

$stats = array_merge([
    'total' => ['value' => 0, 'change' => 0],
    'outstanding' => ['value' => 0, 'change' => 0],
    'draft' => ['value' => 0, 'change' => 0],
    'overdue' => ['value' => 0, 'change' => 0],
], $stats);

// Hitung metrik dasar dari data tagihan (atau DB) jika belum ada
$bucketInvoice = static function (array $invoice, int $nowTs): string {
    $status = strtolower((string)($invoice['status'] ?? ''));
    $dueAt = $invoice['due_at'] ?? $invoice['due_date'] ?? null;
    $dueTs = $dueAt ? strtotime((string)$dueAt) : null;
    $total = (float)($invoice['total_amount'] ?? $invoice['amount'] ?? 0);
    $paid = (float)($invoice['amount_paid'] ?? 0);
    $amountDue = (float)($invoice['amount_due'] ?? max(0, $total - $paid));

    if ($status === 'draft') {
        return 'draft';
    }
    if ($status === 'paid' || $amountDue <= 0) {
        return 'paid';
    }
    if ($status === 'overdue' || ($dueTs && $dueTs < $nowTs)) {
        return 'overdue';
    }
    return 'pending';
};

$calculateStats = static function (array $invoiceList) use ($bucketInvoice): array {
    $now = time();
    $totals = [
        'total' => 0.0,
        'outstanding' => 0.0,
        'draft' => 0.0,
        'overdue' => 0.0,
    ];
    foreach ($invoiceList as $invoice) {
        $totalAmount = (float)($invoice['total_amount'] ?? $invoice['amount'] ?? 0);
        $amountDue = (float)($invoice['amount_due'] ?? max(0, $totalAmount - (float)($invoice['amount_paid'] ?? 0)));
        $bucket = $bucketInvoice($invoice, $now);

        $totals['total'] += $totalAmount;
        if ($bucket === 'draft') {
            $totals['draft'] += $totalAmount;
        }
        if ($bucket === 'overdue') {
            $totals['overdue'] += $amountDue;
        }
        if (in_array($bucket, ['pending', 'overdue'], true)) {
            $totals['outstanding'] += $amountDue;
        }
    }
    return $totals;
};

$statsEmpty = ($stats['total']['value'] ?? 0) == 0
    && ($stats['outstanding']['value'] ?? 0) == 0
    && ($stats['draft']['value'] ?? 0) == 0
    && ($stats['overdue']['value'] ?? 0) == 0;

if ($statsEmpty) {
    $totals = !empty($invoices) ? $calculateStats($invoices) : null;

    if ($totals === null) {
        try {
            require_once __DIR__ . '/../app/functions.php';
            $pdo = db();
            $where = [];
            $params = [];
            if ($currentUserId > 0) {
                if ($isCustomer) {
                    $where[] = 'recipient_user_id = :uid';
                } else {
                    $where[] = '(user_id = :uid OR recipient_user_id = :uid)';
                }
                $params['uid'] = $currentUserId;
            }
            if (!empty($currentDirection)) {
                $where[] = 'direction = :direction';
                $params['direction'] = $currentDirection;
            }
            if ($currentStatus !== '') {
                $where[] = 'status = :status';
                $params['status'] = $currentStatus;
            }
            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
            $sql = "
                SELECT
                    SUM(total_amount) AS total_sum,
                    SUM(CASE WHEN status = 'draft' THEN total_amount ELSE 0 END) AS draft_sum,
                    SUM(CASE WHEN (status = 'overdue' OR (due_date IS NOT NULL AND due_date < NOW()) AND status NOT IN ('paid')) THEN (total_amount - amount_paid) ELSE 0 END) AS overdue_sum,
                    SUM(CASE WHEN status IN ('pending','due','overdue') THEN (total_amount - amount_paid) ELSE 0 END) AS outstanding_sum
                FROM crm_product_invoices
                {$whereSql}
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $totals = [
                'total' => (float)($row['total_sum'] ?? 0),
                'draft' => (float)($row['draft_sum'] ?? 0),
                'overdue' => (float)($row['overdue_sum'] ?? 0),
                'outstanding' => (float)($row['outstanding_sum'] ?? 0),
            ];
        } catch (Throwable $e) {
            $totals = ['total' => 0, 'draft' => 0, 'overdue' => 0, 'outstanding' => 0];
        }
    }

    $stats['total']['value'] = $totals['total'] ?? 0;
    $stats['draft']['value'] = $totals['draft'] ?? 0;
    $stats['overdue']['value'] = $totals['overdue'] ?? 0;
    $stats['outstanding']['value'] = $totals['outstanding'] ?? 0;
}

$formatCurrency = static function (float $value): string {
    return 'Rp' . number_format($value, 0, ',', '.');
};

$customerOptions = $customerOptions ?? [];
$staffOptions = $staffOptions ?? [];

// Fallback: muat data tagihan langsung dari database per pengguna jika belum disuplai oleh controller
if (empty($invoices)) {
    require_once __DIR__ . '/../app/functions.php';
    try {
        $pdo = db();
        $where = [];
        $params = [];
        if ($currentUserId > 0) {
            if ($isCustomer) {
                $where[] = 'recipient_user_id = :uid';
            } else {
                $where[] = '(user_id = :uid OR recipient_user_id = :uid)';
            }
            $params['uid'] = $currentUserId;
        }
        if ($currentStatus !== '') {
            $where[] = 'status = :status';
            $params['status'] = $currentStatus;
        }
        if ($currentDirection !== '') {
            $where[] = 'direction = :direction';
            $params['direction'] = $currentDirection;
        }
        if ($currentSearch !== '') {
            $where[] = '(invoice_no LIKE :search OR invoice_title LIKE :search OR client_name LIKE :search OR client_company LIKE :search)';
            $params['search'] = '%' . $currentSearch . '%';
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $countSql = "SELECT COUNT(*) FROM crm_product_invoices {$whereSql}";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalInvoices = (int)($countStmt->fetchColumn() ?: 0);

        $offset = max(0, ($currentPage - 1) * $perPage);
        $dataSql = "
            SELECT id, user_id, recipient_user_id, invoice_no, invoice_title, client_name, client_company, client_position,
                   status, total_amount, amount_due, amount_paid, due_date AS due_at, issue_date, direction,
                   created_at
            FROM crm_product_invoices
            {$whereSql}
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $dataStmt = $pdo->prepare($dataSql);
        foreach ($params as $key => $value) {
            $dataStmt->bindValue(':' . $key, $value);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $invoices = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        $totalPages = max(1, (int)ceil($totalInvoices / $perPage));
        $rowStart = $totalInvoices === 0 ? 0 : $offset + 1;
        $rowEnd = $totalInvoices === 0 ? 0 : min($totalInvoices, $rowStart + count($invoices) - 1);
    } catch (Throwable $exception) {
        // Biarkan apa adanya jika query gagal
    }
}

$formatDateTime = static function (?string $date): string {
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M Y, h:i A', $timestamp) : $date;
};

$formatDateInput = static function (?string $date): string {
    if (!$date) {
        return '';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('Y-m-d\TH:i', $timestamp) : '';
};

$buildFilterUrl = static function (array $overrides = []) use ($listUrl, $filters, $perPage, $currentSort, $currentStatus, $currentSearch, $currentPage, $currentDirection) {
    $query = [
        'status' => $currentStatus,
        'sort' => $currentSort,
        'search' => $currentSearch,
        'per_page' => $perPage,
        'page' => $currentPage,
        'direction' => $currentDirection,
    ];
    $query = array_merge($query, $overrides);
    return $listUrl . '?' . http_build_query($query);
};

$directionSwitch = [
    'outgoing' => $buildFilterUrl(['direction' => 'outgoing', 'page' => 1]),
    'incoming' => $buildFilterUrl(['direction' => 'incoming', 'page' => 1]),
];

$percentClass = static function ($change): string {
    if ($change === null) {
        return 'text-secondary';
    }
    return $change >= 0 ? 'text-success' : 'text-danger';
};

$percentText = static function ($change): string {
    if ($change === null) {
        return 'Baru';
    }
    $symbol = $change >= 0 ? '+' : '';
    return $symbol . number_format($change, 2) . '%';
};

$statusClasses = [
    'paid' => 'badge-soft-success',
    'pending' => 'badge-soft-purple',
    'overdue' => 'badge-soft-danger',
    'draft' => 'badge-soft-warning text-dark',
    'partial' => 'badge-soft-info',
];
?>
<style>
    .invoices-table {
        table-layout: auto;
        width: 100%;
    }
    .invoices-table th,
    .invoices-table td {
        white-space: normal;
        vertical-align: middle;
    }
    .invoices-table td:nth-child(1) {
        min-width: 160px;
    }
    .invoices-table td:nth-child(2) {
        min-width: 200px;
    }
</style>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Tagihan Produk</h2>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($directionLabels[$currentDirection] ?? ''); ?></p>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Keuangan Produk
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tagihan Produk</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="btn-group me-2 mb-2">
                        <?php if ($isCustomer): ?>
                            <a href="<?php echo htmlspecialchars($directionSwitch['outgoing']); ?>" class="btn <?php echo $currentDirection === 'outgoing' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                Tagihan dari Antara
                            </a>
                            <a href="<?php echo htmlspecialchars($directionSwitch['incoming']); ?>" class="btn <?php echo $currentDirection === 'incoming' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                Tagihan ke Antara
                            </a>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($directionSwitch['outgoing']); ?>" class="btn <?php echo $currentDirection === 'outgoing' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                Tagihan ke Mitra
                            </a>
                            <a href="<?php echo htmlspecialchars($directionSwitch['incoming']); ?>" class="btn <?php echo $currentDirection === 'incoming' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                Tagihan dari Mitra
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if (!$isCustomer): ?>
                    <div class="mb-2">
                        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#create_invoice"><i class="ti ti-circle-plus me-2"></i>Tambah Tagihan</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- /Breadcrumb -->

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

            <!-- Invoice Data -->
            <div class="row">
                <?php foreach ($metricDefaults as $key => $meta): ?>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <p class="fs-12 fw-normal mb-1 text-truncate"><?php echo htmlspecialchars($meta['title']); ?></p>
                                        <h5><?php echo $formatCurrency((float) ($stats[$key]['value'] ?? 0)); ?></h5>
                                    </div>
                                </div>
                                <div class="attendance-report-bar mb-2">
                                    <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height: 5px;">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div>
                                    <?php $change = $stats[$key]['change'] ?? null; ?>
                                    <p class="fs-12 fw-normal d-flex align-items-center text-truncate">
                                        <span class="<?php echo $percentClass($change); ?> fs-12 d-flex align-items-center me-1">
                                            <i class="ti ti-arrow-wave-right-up me-1"></i><?php echo htmlspecialchars($percentText($change)); ?>
                                        </span>
                                        <?php echo htmlspecialchars($meta['description']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- /Invoice Data -->

            <!-- Invoice DataTable -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                            <div class="mb-3 mb-sm-0">
                                <h4 class="mb-1">Daftar Tagihan</h4>
                                <p class="text-muted mb-0">
                                    <?php echo number_format($totalInvoices); ?> tagihan terekam di sistem CRM ANTARA ·
                                    <?php echo htmlspecialchars($directionLabels[$currentDirection] ?? ucfirst($currentDirection)); ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <form class="d-flex align-items-center flex-wrap gap-2" method="get" action="<?php echo htmlspecialchars($listUrl); ?>" id="invoice-filter-form">
                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($currentStatus); ?>">
                                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($currentSort); ?>">
                                    <input type="hidden" name="direction" value="<?php echo htmlspecialchars($currentDirection); ?>">
                                    <input type="hidden" name="page" value="<?php echo htmlspecialchars((string) $currentPage); ?>">
                                    <div class="input-group input-group-sm w-auto">
                                        <span class="input-group-text bg-white border-end-0">Row / Hal</span>
                                        <select class="form-select" name="per_page" onchange="this.form.querySelector('[name=page]').value='1'; this.form.submit();">
                                            <?php foreach ($perPageOptions as $option): ?>
                                                <option value="<?php echo (int) $option; ?>" <?php echo (int) $option === $perPage ? 'selected' : ''; ?>>
                                                    <?php echo (int) $option; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="input-icon position-relative w-auto">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="search" name="search" class="form-control" placeholder="Cari nomor / klien" value="<?php echo htmlspecialchars($currentSearch); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-light">Cari</button>
                                </form>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <?php echo htmlspecialchars($currentStatusLabel); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <li><a href="#" class="dropdown-item rounded-1 filter-option" data-target="status" data-value="">Semua Status</a></li>
                                        <?php foreach ($statusOptions as $status): ?>
                                            <li>
                                                <a href="#" class="dropdown-item rounded-1 filter-option" data-target="status" data-value="<?php echo htmlspecialchars($status); ?>">
                                                    <?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center fs-12" data-bs-toggle="dropdown">
                                        <span class="fs-12 d-inline-flex me-1">Sortir : </span>
                                        <?php echo htmlspecialchars($currentSortLabel); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <?php foreach ($sortOptions as $value => $label): ?>
                                            <li>
                                                <a href="#" class="dropdown-item rounded-1 filter-option" data-target="sort" data-value="<?php echo htmlspecialchars($value); ?>">
                                                    <?php echo htmlspecialchars($label); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="custom-datatable-filter table-responsive">
                                <table class="table align-middle invoices-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Klien</th>
                                            <th>Dibuat</th>
                                            <th>Total</th>
                                            <th>Sisa Tagihan</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Status</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($invoices)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <p class="mb-1 fw-medium">Belum ada tagihan.</p>
                                                    <p class="text-muted mb-3">
                                                        <?php
                                                        if ($currentDirection === 'incoming') {
                                                            echo $isCustomer ? 'Belum ada tagihan yang Anda kirim ke Antara.' : 'Tidak ada tagihan dari mitra yang tercatat.';
                                                        } else {
                                                            echo $isCustomer ? 'Tidak ada tagihan dari Antara.' : 'Klik "Tambah Tagihan" untuk membuat dokumen baru.';
                                                        }
                                                        ?>
                                                    </p>
                                                    <?php if (!$isCustomer): ?>
                                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_invoice">Tambah Tagihan</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($invoices as $invoice): ?>
                                                <?php
                                                    $statusKey = $invoice['status'] ?? 'draft';
                                                    $badgeClass = $statusClasses[$statusKey] ?? 'badge-soft-secondary';
                                                    $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                                                    $encoded = htmlspecialchars(json_encode($invoice, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="text-dark fw-medium"><?php echo htmlspecialchars($invoice['invoice_no'] ?? '-'); ?></span>
                                                        <div class="fs-12 text-muted"><?php echo htmlspecialchars($invoice['invoice_title'] ?? ''); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar avatar-lg me-2">
                                                                <img src="<?php echo htmlspecialchars($invoice['client_avatar'] ?? 'assets/img/users/user-01.jpg'); ?>" class="rounded-circle" alt="client">
                                                            </span>
                                                            <div>
                                                                <h6 class="fw-medium mb-0"><?php echo htmlspecialchars($invoice['client_name'] ?? '-'); ?></h6>
                                                                <span class="fs-12 text-muted"><?php echo htmlspecialchars($invoice['client_company'] ?? ''); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $formatDateTime($invoice['issue_date'] ?? null); ?></td>
                                                    <td><?php echo $formatCurrency((float) ($invoice['total_amount'] ?? 0)); ?></td>
                                                    <td><?php echo $formatCurrency((float) ($invoice['amount_due'] ?? 0)); ?></td>
                                                    <td><?php echo $formatDateTime($invoice['due_date'] ?? null); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $badgeClass; ?> d-inline-flex align-items-center">
                                                            <i class="ti ti-point-filled me-1"></i><?php echo htmlspecialchars($statusLabel); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="action-icon d-inline-flex">
                                                            <a href="javascript:void(0);" class="me-2" data-bs-toggle="modal" data-bs-target="#view_invoice" data-invoice="<?php echo $encoded; ?>" title="Lihat detail"><i class="ti ti-eye"></i></a>
                                                            <?php if (!$isCustomer): ?>
                                                            <a href="javascript:void(0);" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_invoice" data-invoice="<?php echo $encoded; ?>" title="Edit"><i class="ti ti-edit"></i></a>
                                                            <a href="javascript:void(0);" class="" data-bs-toggle="modal" data-bs-target="#delete_invoice" data-invoice="<?php echo $encoded; ?>" title="Hapus"><i class="ti ti-trash"></i></a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center">
                            <p class="mb-0 text-muted">Menampilkan <?php echo $rowStart; ?>-<?php echo $rowEnd; ?> dari <?php echo number_format($totalInvoices); ?> tagihan</p>
                            <?php if ($totalPages > 1): ?>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $buildFilterUrl(['page' => $currentPage - 1]); ?>">Sebelumnya</a>
                                        </li>
                                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                            <?php if ($page === 1 || $page === $totalPages || abs($page - $currentPage) <= 1): ?>
                                                <li class="page-item <?php echo $page === $currentPage ? 'active' : ''; ?>">
                                                    <a class="page-link" href="<?php echo $buildFilterUrl(['page' => $page]); ?>"><?php echo $page; ?></a>
                                                </li>
                                            <?php elseif ($page === 2 && $currentPage - 2 > 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php elseif ($page === $totalPages - 1 && $currentPage + 2 < $totalPages): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $buildFilterUrl(['page' => $currentPage + 1]); ?>">Berikutnya</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice DataTable -->
        </div>
        <!-- End Content -->

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->


    <div class="modal fade" id="create_invoice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tagihan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body needs-validation" novalidate>
                    <input type="hidden" name="_form" value="create">
                    <input type="hidden" name="direction" value="<?php echo htmlspecialchars($currentDirection); ?>">
                    <?php if (!$isCustomer && $currentDirection === 'outgoing'): ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="broadcast_all" name="broadcast_all" value="1" <?php echo !empty($old['broadcast_all']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="broadcast_all">Kirim ke semua klien</label>
                            </div>
                            <small class="text-muted">Jika aktif, tagihan ini akan dikirim ke seluruh klien (satu per klien).</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Kirim ke Klien<span class="text-danger"> *</span></label>
                            <select name="recipient_user_id" id="recipient_user_id" class="form-select" <?php echo !empty($old['broadcast_all']) ? 'disabled' : 'required'; ?>>
                                <option value="">Pilih klien</option>
                                <?php foreach ($customerOptions as $customer): ?>
                                    <option value="<?php echo (int)($customer['id'] ?? 0); ?>">
                                        <?php echo htmlspecialchars(($customer['name'] ?? 'Klien') . ' - ' . ($customer['email'] ?? '')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php elseif ($isCustomer && $currentDirection === 'incoming'): ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Kirim ke Admin/Pegawai<span class="text-danger"> *</span></label>
                            <select name="recipient_user_id" class="form-select" required>
                                <option value="">Pilih admin / pegawai</option>
                                <?php foreach ($staffOptions as $staff): ?>
                                    <option value="<?php echo (int)($staff['id'] ?? 0); ?>">
                                        <?php echo htmlspecialchars(($staff['name'] ?? 'Admin/Pegawai') . ' - ' . ($staff['email'] ?? '')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($currentDirection === 'incoming'): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Mitra / Vendor<span class="text-danger"> *</span></label>
                            <input type="text" name="vendor_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Layanan<span class="text-danger"> *</span></label>
                            <input type="text" name="period_label" class="form-control" placeholder="Contoh: Oktober 2025" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Diterima</label>
                            <input type="datetime-local" name="received_at" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select name="verification_status" class="form-select">
                                <?php foreach ($verificationLabels as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="payment_status" class="form-select">
                                <?php foreach ($paymentStatusLabels as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lampiran Invoice (URL/Path)</label>
                            <input type="text" name="attachment_path" class="form-control" placeholder="assets/uploads/invoice.pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bukti Pembayaran (URL/Path)</label>
                            <input type="text" name="payment_proof_path" class="form-control" placeholder="Link bukti transfer">
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Tagihan<span class="text-danger"> *</span></label>
                            <input type="text" name="invoice_title" class="form-control" value="<?php echo htmlspecialchars($old['invoice_title'] ?? ''); ?>" required>
                            <?php if (isset($errors['invoice_title'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['invoice_title']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nomor Tagihan</label>
                            <input type="text" name="invoice_no" class="form-control" value="<?php echo htmlspecialchars($old['invoice_no'] ?? ''); ?>" placeholder="Kosongkan untuk otomatis">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>" <?php echo (($old['status'] ?? '') === $status) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['status']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Dibuat</label>
                            <input type="datetime-local" name="issue_date" class="form-control" value="<?php echo htmlspecialchars($formatDateInput($old['issue_date'] ?? '')); ?>">
                            <?php if (isset($errors['issue_date'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['issue_date']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="datetime-local" name="due_date" class="form-control" value="<?php echo htmlspecialchars($formatDateInput($old['due_date'] ?? '')); ?>">
                            <?php if (isset($errors['due_date'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['due_date']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nominal diterima (Rp)</label>
                            <input type="text" name="amount_paid" class="form-control" value="<?php echo htmlspecialchars($old['amount_paid'] ?? ''); ?>" placeholder="0">
                            <?php if (isset($errors['amount_paid'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['amount_paid']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Ref. Kontrak / PO</label>
                            <input type="text" name="reference_no" class="form-control" value="<?php echo htmlspecialchars($old['reference_no'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Klien<span class="text-danger"> *</span></label>
                            <input type="text" name="client_name" class="form-control" value="<?php echo htmlspecialchars($old['client_name'] ?? ''); ?>" required>
                            <?php if (isset($errors['client_name'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['client_name']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan<span class="text-danger"> *</span></label>
                            <input type="text" name="client_company" class="form-control" value="<?php echo htmlspecialchars($old['client_company'] ?? ''); ?>" required>
                            <?php if (isset($errors['client_company'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['client_company']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jabatan Kontak</label>
                            <input type="text" name="client_position" class="form-control" value="<?php echo htmlspecialchars($old['client_position'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Email Klien</label>
                            <input type="email" name="client_email" class="form-control" value="<?php echo htmlspecialchars($old['client_email'] ?? ''); ?>">
                            <?php if (isset($errors['client_email'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['client_email']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="client_phone" class="form-control" value="<?php echo htmlspecialchars($old['client_phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Avatar Klien (opsional)</label>
                            <input type="text" name="client_avatar" class="form-control" value="<?php echo htmlspecialchars($old['client_avatar'] ?? ''); ?>" placeholder="assets/img/users/user-01.jpg">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Channel / Produk</label>
                            <input type="text" name="channel" class="form-control" value="<?php echo htmlspecialchars($old['channel'] ?? ''); ?>" placeholder="Produk / layanan antar">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catatan Internal</label>
                            <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($old['notes'] ?? ''); ?>" placeholder="Catatan tambahan untuk tim">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Syarat & Ketentuan</label>
                            <textarea name="terms" class="form-control" rows="2" placeholder="Termin pembayaran, SLA layanan, dll."><?php echo htmlspecialchars($old['terms'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Detail Layanan / Produk</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary add-invoice-item" data-target="#create-invoice-items"><i class="ti ti-plus me-1"></i>Tambah Baris</button>
                        </div>
                        <?php if (isset($errors['items'])): ?><small class="text-danger d-block mb-2"><?php echo htmlspecialchars($errors['items']); ?></small><?php endif; ?>
                        <div id="create-invoice-items" data-index="<?php echo count($oldItems); ?>">
                            <?php foreach ($oldItems as $index => $item): ?>
                                <div class="invoice-item-row border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Deskripsi / Produk</label>
                                            <input type="text" name="items[<?php echo $index; ?>][description]" class="form-control" value="<?php echo htmlspecialchars($item['description'] ?? $item['product_name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Qty</label>
                                            <input type="text" name="items[<?php echo $index; ?>][quantity]" class="form-control" value="<?php echo htmlspecialchars($item['quantity'] ?? '1'); ?>">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Diskon (%)</label>
                                            <input type="text" name="items[<?php echo $index; ?>][discount_percent]" class="form-control" value="<?php echo htmlspecialchars($item['discount_percent'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Tarif (Rp)</label>
                                            <input type="text" name="items[<?php echo $index; ?>][unit_price]" class="form-control" value="<?php echo htmlspecialchars($item['unit_price'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-sm btn-light remove-invoice-item"><i class="ti ti-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-1">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_invoice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbarui Tagihan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body" id="edit-invoice-form">
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_id" id="edit-invoice-id">
                    <input type="hidden" name="direction" value="<?php echo htmlspecialchars($currentDirection); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Tagihan<span class="text-danger"> *</span></label>
                            <input type="text" name="invoice_title" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nomor Tagihan</label>
                            <input type="text" name="invoice_no" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Dibuat</label>
                            <input type="datetime-local" name="issue_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="datetime-local" name="due_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nominal diterima (Rp)</label>
                            <input type="text" name="amount_paid" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Ref. Kontrak / PO</label>
                            <input type="text" name="reference_no" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Klien<span class="text-danger"> *</span></label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan<span class="text-danger"> *</span></label>
                            <input type="text" name="client_company" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jabatan Kontak</label>
                            <input type="text" name="client_position" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Email Klien</label>
                            <input type="email" name="client_email" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="client_phone" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Avatar Klien</label>
                            <input type="text" name="client_avatar" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Channel / Produk</label>
                            <input type="text" name="channel" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catatan Internal</label>
                            <input type="text" name="notes" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Syarat & Ketentuan</label>
                            <textarea name="terms" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Detail Layanan / Produk</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary add-invoice-item" data-target="#edit-invoice-items"><i class="ti ti-plus me-1"></i>Tambah Baris</button>
                        </div>
                        <div id="edit-invoice-items" data-index="0"></div>
                    </div>
                    <div class="d-flex justify-content-end mt-1">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_invoice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" id="delete-invoice-form">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="direction" value="<?php echo htmlspecialchars($currentDirection); ?>">
                    <div class="modal-body pt-1">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus tagihan ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_invoice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-invoice-title">Detail Tagihan</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center js-print-invoice" disabled>
                            <i class="ti ti-printer me-1"></i> Cetak
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <img src="assets/img/logo-antara.png" alt="Logo ANTARA" class="me-2" style="height: 42px;">
                            <div>
                                <p class="mb-0 fw-semibold text-uppercase fs-12 text-muted">ANTARA News Agency</p>
                                <p class="mb-0 text-muted fs-12">Divisi Produk & Penjualan CRM</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark">Invoice Resmi</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <p class="mb-1 text-muted" id="view-invoice-client"></p>
                                <p class="mb-0 text-muted" id="view-invoice-company"></p>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-soft-primary" id="view-invoice-status">Status</span>
                                <p class="mb-0 text-muted fs-12" id="view-invoice-issued"></p>
                                <p class="mb-0 text-muted fs-12" id="view-invoice-due"></p>
                            </div>
                        </div>
                        <p class="mb-0 text-muted fs-12" id="view-invoice-reference"></p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Tarif</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="view-invoice-items">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th class="text-end" id="view-invoice-total">Rp0</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Dibayar</th>
                                    <th class="text-end" id="view-invoice-paid">Rp0</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Sisa Tagihan</th>
                                    <th class="text-end" id="view-invoice-due-amount">Rp0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="mb-1"><strong>Catatan:</strong> <span id="view-invoice-notes" class="text-muted">-</span></p>
                    <p class="mb-0"><strong>Syarat:</strong> <span id="view-invoice-terms" class="text-muted">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <template id="invoice-item-row-template">
        <div class="invoice-item-row border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Deskripsi / Produk</label>
                    <input type="text" name="items[__INDEX__][description]" class="form-control" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Qty</label>
                    <input type="text" name="items[__INDEX__][quantity]" class="form-control" value="1">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Diskon (%)</label>
                    <input type="text" name="items[__INDEX__][discount_percent]" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Tarif (Rp)</label>
                    <input type="text" name="items[__INDEX__][unit_price]" class="form-control">
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-sm btn-light remove-invoice-item"><i class="ti ti-trash"></i></button>
            </div>
        </div>
    </template>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var filterForm = document.getElementById('invoice-filter-form');
            document.querySelectorAll('.filter-option').forEach(function (element) {
                element.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (!filterForm) {
                        return;
                    }
                    var target = element.getAttribute('data-target');
                    var value = element.getAttribute('data-value') || '';
                    var input = filterForm.querySelector('[name=' + target + ']');
                    if (input) {
                        input.value = value;
                    }
                    var pageInput = filterForm.querySelector('[name=page]');
                    if (pageInput) {
                        pageInput.value = '1';
                    }
                    filterForm.submit();
                });
            });

            var itemTemplate = document.getElementById('invoice-item-row-template');
            var currentInvoiceForPrint = null;
            var globalPrintButton = document.querySelector('.js-print-invoice');

            function addItemRow(container, data) {
                if (!container || !itemTemplate) {
                    return;
                }
                var index = parseInt(container.getAttribute('data-index') || '0', 10);
                var html = itemTemplate.innerHTML.replace(/__INDEX__/g, index);
                var wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                var row = wrapper.firstElementChild;
                if (!row) {
                    return;
                }
                container.appendChild(row);
                container.setAttribute('data-index', (index + 1).toString());

                if (data) {
                    row.querySelector('[name="items[' + index + '][description]"]').value = data.description || data.product_name || '';
                    row.querySelector('[name="items[' + index + '][quantity]"]').value = data.quantity || 1;
                    row.querySelector('[name="items[' + index + '][discount_percent]"]').value = data.discount_percent || '';
                    row.querySelector('[name="items[' + index + '][unit_price]"]').value = data.unit_price || '';
                }
            }

            document.querySelectorAll('.add-invoice-item').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetSelector = button.getAttribute('data-target');
                    if (!targetSelector) {
                        return;
                    }
                    var target = document.querySelector(targetSelector);
                    addItemRow(target);
                });
            });

            document.addEventListener('click', function (event) {
                var removeBtn = event.target.closest('.remove-invoice-item');
                if (removeBtn) {
                    var row = removeBtn.closest('.invoice-item-row');
                    if (row) {
                        row.remove();
                    }
                }
            });

            var editModal = document.getElementById('edit_invoice');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var invoiceData = button ? button.getAttribute('data-invoice') : null;
                    if (!invoiceData) {
                        return;
                    }
                    try {
                        var invoice = JSON.parse(invoiceData);
                        var form = editModal.querySelector('form');
                        form.action = "<?php echo htmlspecialchars($storeUrl); ?>" + '/' + invoice.id;
                        form.querySelector('[name=invoice_title]').value = invoice.invoice_title || '';
                        form.querySelector('[name=invoice_no]').value = invoice.invoice_no || '';
                        form.querySelector('[name=status]').value = invoice.status || '';
                        form.querySelector('[name=issue_date]').value = invoice.issue_date ? formatForInput(invoice.issue_date) : '';
                        form.querySelector('[name=due_date]').value = invoice.due_date ? formatForInput(invoice.due_date) : '';
                        form.querySelector('[name=amount_paid]').value = invoice.amount_paid || '';
                        form.querySelector('[name=reference_no]').value = invoice.reference_no || '';
                        form.querySelector('[name=client_name]').value = invoice.client_name || '';
                        form.querySelector('[name=client_company]').value = invoice.client_company || '';
                        form.querySelector('[name=client_position]').value = invoice.client_position || '';
                        form.querySelector('[name=client_email]').value = invoice.client_email || '';
                        form.querySelector('[name=client_phone]').value = invoice.client_phone || '';
                        form.querySelector('[name=client_avatar]').value = invoice.client_avatar || '';
                        form.querySelector('[name=channel]').value = invoice.channel || '';
                        form.querySelector('[name=notes]').value = invoice.notes || '';
                        form.querySelector('[name=terms]').value = invoice.terms || '';

                        var itemsContainer = form.querySelector('#edit-invoice-items');
                        itemsContainer.innerHTML = '';
                        itemsContainer.setAttribute('data-index', '0');
                        if (invoice.items && invoice.items.length) {
                            invoice.items.forEach(function (item) {
                                addItemRow(itemsContainer, item);
                            });
                        } else {
                            addItemRow(itemsContainer);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            var deleteModal = document.getElementById('delete_invoice');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var invoiceData = button ? button.getAttribute('data-invoice') : null;
                    if (!invoiceData) {
                        return;
                    }
                    try {
                        var invoice = JSON.parse(invoiceData);
                        var form = deleteModal.querySelector('form');
                        form.action = "<?php echo htmlspecialchars($storeUrl); ?>" + '/' + invoice.id;
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            var viewModal = document.getElementById('view_invoice');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var invoiceData = button ? button.getAttribute('data-invoice') : null;
                    if (!invoiceData) {
                        return;
                    }
                    try {
                        var invoice = JSON.parse(invoiceData);
                        currentInvoiceForPrint = invoice;
                        viewModal.querySelector('#view-invoice-title').textContent = (invoice.invoice_no || '-') + ' - ' + (invoice.invoice_title || '');
                        viewModal.querySelector('#view-invoice-client').textContent = invoice.client_name || '-';
                        viewModal.querySelector('#view-invoice-company').textContent = invoice.client_company || '';
                        var statusMap = <?php echo json_encode($statusLabels); ?>;
                        viewModal.querySelector('#view-invoice-status').textContent = invoice.status ? (statusMap[invoice.status] || invoice.status) : '-';
                        viewModal.querySelector('#view-invoice-issued').textContent = 'Dibuat: ' + (formatReadable(invoice.issue_date) || '-');
                        viewModal.querySelector('#view-invoice-due').textContent = 'Jatuh Tempo: ' + (formatReadable(invoice.due_date) || '-');
                        viewModal.querySelector('#view-invoice-reference').textContent = invoice.reference_no ? 'Referensi: ' + invoice.reference_no : '';
                        viewModal.querySelector('#view-invoice-notes').textContent = invoice.notes || '-';
                        viewModal.querySelector('#view-invoice-terms').textContent = invoice.terms || '-';

                        var itemsBody = viewModal.querySelector('#view-invoice-items');
                        itemsBody.innerHTML = '';
                        var total = 0;
                        if (invoice.items && invoice.items.length) {
                            invoice.items.forEach(function (item) {
                                var row = document.createElement('tr');
                                row.innerHTML = '<td>' + (item.description || item.product_name || '-') + '</td>' +
                                    '<td class="text-end">' + (item.quantity || 0) + '</td>' +
                                    '<td class="text-end">' + formatCurrency(item.unit_price || 0) + '</td>' +
                                    '<td class="text-end">' + (item.discount_percent ? (item.discount_percent + '%') : '-') + '</td>' +
                                    '<td class="text-end">' + formatCurrency(item.line_total || 0) + '</td>';
                                itemsBody.appendChild(row);
                                total += parseFloat(item.line_total || 0);
                            });
                        } else {
                            var emptyRow = document.createElement('tr');
                            emptyRow.innerHTML = '<td colspan="5" class="text-center text-muted">Tidak ada item</td>';
                            itemsBody.appendChild(emptyRow);
                        }

                        var paid = parseFloat(invoice.amount_paid || 0);
                        var dueAmount = total - paid;
                        viewModal.querySelector('#view-invoice-total').textContent = formatCurrency(total);
                        viewModal.querySelector('#view-invoice-paid').textContent = formatCurrency(paid);
                        viewModal.querySelector('#view-invoice-due-amount').textContent = formatCurrency(dueAmount);

                        if (globalPrintButton) {
                            globalPrintButton.disabled = false;
                        }
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            if (globalPrintButton) {
                globalPrintButton.addEventListener('click', function () {
                    if (!currentInvoiceForPrint) {
                        return;
                    }
                    openPrintWindow(currentInvoiceForPrint);
                });
            }

            function formatForInput(value) {
                if (!value) {
                    return '';
                }
                var date = new Date(value.replace(' ', 'T'));
                if (Number.isNaN(date.getTime())) {
                    return '';
                }
                var pad = function (num) {
                    return num < 10 ? '0' + num : num;
                };
                return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
            }

            function formatReadable(value) {
                if (!value) {
                    return '';
                }
                var date = new Date(value.replace(' ', 'T'));
                if (Number.isNaN(date.getTime())) {
                    return value;
                }
                return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }

            function formatCurrency(value) {
                var number = parseFloat(value) || 0;
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
            }

            function openPrintWindow(invoice) {
                var printWindow = window.open('', '_blank');
                if (!printWindow) {
                    return;
                }

                var items = invoice.items || [];
                var rowsHtml = '';
                var total = 0;

                if (items.length) {
                    items.forEach(function (item) {
                        var lineTotal = parseFloat(item.line_total || 0);
                        total += lineTotal;
                        rowsHtml += '<tr>' +
                            '<td>' + (item.description || item.product_name || '-') + '</td>' +
                            '<td class="text-end">' + (item.quantity || 0) + '</td>' +
                            '<td class="text-end">' + formatCurrency(item.unit_price || 0) + '</td>' +
                            '<td class="text-end">' + (item.discount_percent ? (item.discount_percent + '%') : '-') + '</td>' +
                            '<td class="text-end">' + formatCurrency(lineTotal) + '</td>' +
                        '</tr>';
                    });
                } else {
                    rowsHtml = '<tr><td colspan="5" class="text-center text-muted">Tidak ada item</td></tr>';
                }

                var paid = parseFloat(invoice.amount_paid || 0);
                var dueAmount = total - paid;

                var styles = '<style>' +
                    'body{font-family:Arial, sans-serif;margin:30px;color:#1f1f1f;}' +
                    '.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}' +
                    '.header img{height:50px;}' +
                    '.meta h1{margin:0;font-size:22px;}' +
                    '.meta p{margin:2px 0;font-size:13px;}' +
                    'table{width:100%;border-collapse:collapse;margin-top:15px;}' +
                    'th,td{border:1px solid #ddd;padding:8px;font-size:12px;}' +
                    'th{background:#f6f6f6;text-align:left;}' +
                    '.text-end{text-align:right;}' +
                    '.totals td{border:none;font-weight:600;}' +
                    '.notes{margin-top:20px;font-size:12px;}' +
                    '</style>';

                var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + (invoice.invoice_no || 'Invoice') + '</title>' + styles + '</head><body>' +
                    '<div class="header">' +
                        '<div class="meta">' +
                            '<img src="assets/img/logo-antara.png" alt="ANTARA">' +
                        '</div>' +
                        '<div class="meta" style="text-align:right;">' +
                            '<h1>' + (invoice.invoice_no || '-') + '</h1>' +
                            '<p>' + (invoice.invoice_title || '') + '</p>' +
                            '<p>Dibuat: ' + (formatReadable(invoice.issue_date) || '-') + '</p>' +
                            '<p>Jatuh Tempo: ' + (formatReadable(invoice.due_date) || '-') + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div>' +
                        '<p><strong>' + (invoice.client_name || '-') + '</strong><br>' +
                        (invoice.client_company || '') + '<br>' +
                        (invoice.reference_no ? 'Referensi: ' + invoice.reference_no : '') + '</p>' +
                    '</div>' +
                    '<table>' +
                        '<thead><tr><th>Deskripsi</th><th class="text-end">Qty</th><th class="text-end">Tarif</th><th class="text-end">Diskon</th><th class="text-end">Subtotal</th></tr></thead>' +
                        '<tbody>' + rowsHtml + '</tbody>' +
                    '</table>' +
                    '<table class="totals" style="margin-top:10px;width:40%;margin-left:auto;">' +
                        '<tr><td class="text-end">Total</td><td class="text-end">' + formatCurrency(total) + '</td></tr>' +
                        '<tr><td class="text-end">Dibayar</td><td class="text-end">' + formatCurrency(paid) + '</td></tr>' +
                        '<tr><td class="text-end">Sisa Tagihan</td><td class="text-end">' + formatCurrency(dueAmount) + '</td></tr>' +
                    '</table>' +
                    '<div class="notes">' +
                        '<p><strong>Catatan:</strong> ' + (invoice.notes || '-') + '</p>' +
                        '<p><strong>Syarat:</strong> ' + (invoice.terms || '-') + '</p>' +
                    '</div>' +
                '</body></html>';

                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function () {
                    printWindow.print();
                    printWindow.close();
                }, 400);
            }

            <?php if (!empty($errors) && $oldForm === 'create'): ?>
                var createModal = new bootstrap.Modal(document.getElementById('create_invoice'));
                createModal.show();
            <?php elseif (!empty($errors) && $oldForm === 'edit'): ?>
                var reopenEdit = document.querySelector('[data-bs-target="#edit_invoice"]');
                if (reopenEdit) {
                    var editModalInstance = new bootstrap.Modal(document.getElementById('edit_invoice'));
                    editModalInstance.show();
                }
            <?php endif; ?>
        });
    </script>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>
