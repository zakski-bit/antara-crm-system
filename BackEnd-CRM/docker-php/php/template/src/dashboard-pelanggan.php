<?php ob_start();

$customer = $customer ?? [];
$customerName = $customerName ?? ($customer['name'] ?? ($_SESSION['customer_name'] ?? 'Pelanggan'));
$billingPeriod = $billingPeriod ?? date('F Y');
$currentUserRole = (string) ($_SESSION['user_role'] ?? 'customer');
$isCustomerRole = strtolower(trim($currentUserRole)) === 'customer';

$subscriptions = $customer['subscriptions'] ?? ($subscriptions ?? []);
$invoices = $customer['invoices'] ?? ($invoices ?? []);
$payments = $customer['payments'] ?? ($payments ?? []);
$activities = $customer['activities'] ?? ($activities ?? []);
$productCatalog = $customer['product_catalog'] ?? ($productCatalog ?? []);

// Additional fallbacks from session to avoid empty dashboard for customers
if (empty($subscriptions) && !empty($_SESSION['customer_subscriptions'])) {
    $subscriptions = $_SESSION['customer_subscriptions'];
}
if (empty($invoices) && !empty($_SESSION['customer_invoices'])) {
    $invoices = $_SESSION['customer_invoices'];
}
if (empty($payments) && !empty($_SESSION['customer_payments'])) {
    $payments = $_SESSION['customer_payments'];
}

// Attempt to load latest subscriptions, invoices & payments from repository/DB if still empty
if (empty($subscriptions) || empty($invoices) || empty($payments)) {
    require_once __DIR__ . '/../app/functions.php';
    $currentUserId = (int)($_SESSION['user_id'] ?? 0);
    // Coba gunakan repository bila tersedia; fallback ke query manual
    if ($currentUserId > 0) {
        try {
            if (empty($subscriptions) && class_exists('\App\Repositories\ClientSubscriptionRepository')) {
                $subRepo = new \App\Repositories\ClientSubscriptionRepository();
                $currentEmail = $_SESSION['user_email'] ?? null;
                $subscriptions = $subRepo->listByCustomer($currentUserId, $currentUserId, $currentEmail);
            }
            if (empty($invoices) && class_exists('\App\Repositories\ProductInvoiceRepository')) {
                $invRepo = new \App\Repositories\ProductInvoiceRepository();
                $result = $invRepo->paginate($currentUserId, null, 'recent', 50, 1, null, null, $currentUserId);
                $invoices = $result['data'] ?? [];
            }
            if (empty($payments) && class_exists('\App\Repositories\PaymentRepository')) {
                $payRepo = new \App\Repositories\PaymentRepository();
                $result = $payRepo->paginate($currentUserId, null, 'recent', 20, 1, null, null, null, $currentUserId);
                $payments = $result['data'] ?? [];
            }
        } catch (\Throwable $e) {
            // fallback to manual query if repos not working
        }
    }
    // fallback manual query jika masih kosong
    if (empty($subscriptions) || empty($invoices) || empty($payments)) {
        try {
            $pdo = db();
            if (empty($subscriptions)) {
                $currentEmail = $_SESSION['user_email'] ?? null;
                $stmt = $pdo->prepare("
                    SELECT plan_name AS name, plan_code, cycle, amount, currency_code, status, started_at, renewal_at, ends_at, notes
                    FROM crm_client_subscriptions s
                    LEFT JOIN clients c ON s.client_id = c.id
                    WHERE (
                        s.customer_user_id = :uid
                        OR (:email IS NOT NULL AND :email <> '' AND c.email = :email)
                    )
                    ORDER BY COALESCE(renewal_at, started_at, created_at) DESC
                    LIMIT 20
                ");
                $stmt->execute(['uid' => $currentUserId, 'email' => $currentEmail]);
                $subscriptions = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            }
            if (empty($invoices)) {
                $stmt = $pdo->prepare("
                    SELECT id, user_id, recipient_user_id, invoice_no, invoice_title, client_name, client_company,
                           status, total_amount, (total_amount - amount_paid) AS amount_due, amount_paid, due_date AS due_at, created_at
                    FROM crm_product_invoices
                    WHERE user_id = :uid OR recipient_user_id = :uid
                    ORDER BY COALESCE(due_date, created_at) DESC
                    LIMIT 50
                ");
                $stmt->execute(['uid' => $currentUserId]);
                $invoices = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            }
            if (empty($payments)) {
                $stmt = $pdo->prepare("
                    SELECT p.id, p.user_id, p.invoice_id, p.invoice_no, p.invoice_title, p.payment_method, p.status,
                           COALESCE(p.verification_status, 'approved') AS verification_status,
                           p.amount, p.paid_at AS payment_date, p.created_at,
                           i.total_amount AS invoice_amount, (i.total_amount - i.amount_paid) AS invoice_amount_due,
                           i.status AS invoice_status, i.due_date AS invoice_due_at
                    FROM crm_payments p
                    LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id
                    WHERE (p.user_id = :uid OR i.recipient_user_id = :uid OR i.user_id = :uid)
                      AND (:is_customer = 0 OR COALESCE(p.verification_status, 'approved') = 'approved')
                    ORDER BY p.paid_at DESC
                    LIMIT 20
                ");
                $stmt->execute([
                    'uid' => $currentUserId,
                    'is_customer' => $isCustomerRole ? 1 : 0,
                ]);
                $payments = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            }
        } catch (Throwable $exception) {
            // Leave arrays as-is if tables are missing or connection fails
        }
    }
}

if ($isCustomerRole && !empty($payments)) {
    $payments = array_values(array_filter($payments, static function (array $payment): bool {
        $verificationStatus = strtolower(trim((string) ($payment['verification_status'] ?? 'approved')));
        if ($verificationStatus === '') {
            $verificationStatus = 'approved';
        }
        return $verificationStatus === 'approved';
    }));
}

// Fallback: jika belum ada invoice di payload, coba turunkan dari data pembayaran
if (empty($invoices) && !empty($payments)) {
    foreach ($payments as $payment) {
        $invoiceStatus = $payment['invoice_status'] ?? ($payment['status'] ?? '');
        $invoiceAmount = $payment['invoice_amount'] ?? ($payment['amount'] ?? 0);
        $invoiceAmountDue = $payment['invoice_amount_due'] ?? ($payment['outstanding'] ?? 0);
        $invoiceDue = $payment['invoice_due_at'] ?? ($payment['due_at'] ?? ($payment['due_date'] ?? ''));
        $invoices[] = [
            'invoice_no' => $payment['invoice_no'] ?? ($payment['invoice_number'] ?? ''),
            'status' => $invoiceStatus,
            'amount' => $invoiceAmount,
            'total_amount' => $invoiceAmount,
            'amount_due' => $invoiceAmountDue,
            'due_at' => $invoiceDue,
            'invoice_title' => $payment['invoice_title'] ?? '',
            'product' => $payment['product'] ?? ($payment['invoice_product'] ?? ''),
        ];
    }
}

$formatCurrency = static function (float $amount): string {
    return 'Rp' . number_format($amount, 0, ',', '.');
};

$formatDate = static function (?string $date): string {
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M Y', $timestamp) : $date;
};

$subscriptionStatusMeta = [
    'active' => ['label' => 'Aktif', 'class' => 'badge-soft-success'],
    'trial' => ['label' => 'Uji Coba', 'class' => 'badge-soft-info'],
    'pending' => ['label' => 'Menunggu', 'class' => 'badge-soft-warning text-dark'],
    'paused' => ['label' => 'Ditangguhkan', 'class' => 'badge-soft-secondary'],
    'cancelled' => ['label' => 'Dibatalkan', 'class' => 'badge-soft-danger'],
    'ended' => ['label' => 'Selesai', 'class' => 'badge-soft-secondary'],
];

$invoiceStatusMeta = [
    'paid' => ['label' => 'Lunas', 'class' => 'badge badge-success-transparent'],
    'pending' => ['label' => 'Menunggu', 'class' => 'badge badge-warning-transparent'],
    'due' => ['label' => 'Jatuh Tempo', 'class' => 'badge badge-info-transparent'],
    'overdue' => ['label' => 'Terlambat', 'class' => 'badge badge-danger-transparent'],
];
$paymentStatusMeta = [
    'completed' => ['label' => 'Berhasil', 'class' => 'badge badge-success-transparent'],
    'settled' => ['label' => 'Berhasil', 'class' => 'badge badge-success-transparent'],
    'partial' => ['label' => 'Sebagian', 'class' => 'badge badge-info-transparent'],
    'pending' => ['label' => 'Diproses', 'class' => 'badge badge-warning-transparent'],
    'failed' => ['label' => 'Gagal', 'class' => 'badge badge-danger-transparent'],
    'refunded' => ['label' => 'Dikembalikan', 'class' => 'badge badge-secondary-transparent'],
];

$activeSubscriptions = 0;
$expiringSoon = 0;
$nextRenewalDate = null;
$nextRenewalProduct = '-';

foreach ($subscriptions as $subscription) {
    $status = $subscription['status'] ?? '';
    if (in_array($status, ['active', 'trial'], true)) {
        $activeSubscriptions++;
    }
    $renewalTimestamp = isset($subscription['renewal_at']) ? strtotime((string) $subscription['renewal_at']) : null;
    if ($renewalTimestamp) {
        if ($nextRenewalDate === null || $renewalTimestamp < $nextRenewalDate) {
            $nextRenewalDate = $renewalTimestamp;
            $nextRenewalProduct = $subscription['name'] ?? '-';
        }
        // tandai expiring jika kurang dari 14 hari
        if ($renewalTimestamp - time() <= 14 * 86400 && $renewalTimestamp >= time()) {
            $expiringSoon++;
        }
    }
}

$nextRenewalLabel = $nextRenewalDate ? date('d M Y', $nextRenewalDate) : '-';

$invoiceStatusSummary = [];
foreach ($invoiceStatusMeta as $key => $meta) {
    $invoiceStatusSummary[$key] = ['count' => 0, 'amount' => 0];
}

$unpaidTotal = 0;
$pendingInvoicesCount = 0;
$monthlyTotal = 0;
$paidThisMonth = 0;
$currentYearMonth = date('Y-m');
$nowTs = time();

$bucketInvoice = static function (array $invoice, int $nowTs): string {
    $rawStatus = strtolower(trim((string) ($invoice['status'] ?? '')));
    $statusMap = [
        'paid' => ['paid', 'settled', 'lunas', 'completed', 'success'],
        'pending' => ['pending', 'menunggu', 'processing', 'draft', 'partial', 'unpaid', 'open', 'sent'],
        'due' => ['due', 'jatuh_tempo', 'due_today'],
        'overdue' => ['overdue', 'late', 'terlambat', 'past_due'],
    ];

    $bucket = 'pending';
    foreach ($statusMap as $key => $aliases) {
        if (in_array($rawStatus, $aliases, true)) {
            $bucket = $key;
            break;
        }
    }

    $total = (float) ($invoice['total_amount'] ?? $invoice['amount'] ?? 0);
    $paid = (float) ($invoice['amount_paid'] ?? 0);
    $dueAmount = (float) ($invoice['amount_due'] ?? max(0, $total - $paid));
    $dueAt = $invoice['due_at'] ?? $invoice['due_date'] ?? '';
    $dueTs = $dueAt ? strtotime((string) $dueAt) : null;

    if ($bucket !== 'paid' && $dueAmount <= 0) {
        $bucket = 'paid';
    }
    if ($bucket !== 'paid' && ($bucket === 'overdue' || ($dueTs && $dueTs < $nowTs))) {
        $bucket = 'overdue';
    }
    if ($bucket === 'pending' && $rawStatus === 'due') {
        $bucket = 'due';
    }
    if ($bucket === 'pending' && $rawStatus === '' && $dueTs && $dueTs < $nowTs) {
        $bucket = 'overdue';
    }

    return $bucket;
};

foreach ($invoices as $invoice) {
    $bucket = $bucketInvoice($invoice, $nowTs);
    $amount = (float) ($invoice['total_amount'] ?? $invoice['amount'] ?? $invoice['amount_due'] ?? 0);

    if (isset($invoiceStatusSummary[$bucket])) {
        $invoiceStatusSummary[$bucket]['count']++;
        $invoiceStatusSummary[$bucket]['amount'] += $amount;
    }

    if (in_array($bucket, ['pending', 'due', 'overdue'], true)) {
        $unpaidTotal += $amount;
        $pendingInvoicesCount++;
    }

    $dueAt = $invoice['due_at'] ?? '';
    $dueTimestamp = $dueAt ? strtotime((string) $dueAt) : null;
    if ($dueTimestamp && date('Y-m', $dueTimestamp) === $currentYearMonth) {
        $monthlyTotal += $amount;
        if ($bucket === 'paid') {
            $paidThisMonth += $amount;
        }
    }
}

// Jika ringkasan masih kosong sementara ada pembayaran, gunakan status invoice di pembayaran sebagai cadangan
$statusCountSum = 0;
foreach ($invoiceStatusSummary as $summary) {
    $statusCountSum += $summary['count'];
}
if ($statusCountSum === 0 && !empty($payments)) {
    foreach ($payments as $payment) {
        $invoiceStatus = strtolower(trim((string) ($payment['invoice_status'] ?? $payment['status'] ?? '')));
        $amount = (float) ($payment['invoice_amount'] ?? $payment['amount'] ?? 0);
        $bucket = $bucketInvoice([
            'status' => $invoiceStatus,
            'total_amount' => $payment['invoice_amount'] ?? $payment['amount'] ?? 0,
            'amount_due' => $payment['invoice_amount_due'] ?? 0,
            'due_at' => $payment['invoice_due_at'] ?? ($payment['due_at'] ?? ''),
        ], $nowTs);
        if (isset($invoiceStatusSummary[$bucket])) {
            $invoiceStatusSummary[$bucket]['count']++;
            $invoiceStatusSummary[$bucket]['amount'] += $amount;
        }
        if (in_array($bucket, ['pending', 'due', 'overdue'], true)) {
            $unpaidTotal += $amount;
            $pendingInvoicesCount++;
        }
    }
}

$invoiceList = $invoices;
usort($invoiceList, function (array $a, array $b): int {
    $aTime = isset($a['due_at']) ? strtotime((string) $a['due_at']) : PHP_INT_MAX;
    $bTime = isset($b['due_at']) ? strtotime((string) $b['due_at']) : PHP_INT_MAX;
    return $aTime <=> $bTime;
});

$nextDueInvoice = null;
foreach ($invoiceList as $invoice) {
    $bucket = $bucketInvoice($invoice, $nowTs);
    if (in_array($bucket, ['pending', 'due', 'overdue'], true)) {
        $invoice['status'] = $bucket;
        $nextDueInvoice = $invoice;
        break;
    }
}

$upcomingInvoices = array_values(array_filter($invoiceList, function ($invoice) use ($bucketInvoice, $nowTs) {
    $bucket = $bucketInvoice($invoice, $nowTs);
    return in_array($bucket, ['pending', 'due', 'overdue'], true);
}));
$upcomingInvoices = array_map(static function (array $invoice) use ($bucketInvoice, $nowTs): array {
    $invoice['status'] = $bucketInvoice($invoice, $nowTs);
    return $invoice;
}, $upcomingInvoices);
usort($upcomingInvoices, function ($a, $b) {
    $aTime = isset($a['due_at']) ? strtotime((string) $a['due_at']) : PHP_INT_MAX;
    $bTime = isset($b['due_at']) ? strtotime((string) $b['due_at']) : PHP_INT_MAX;
    return $aTime <=> $bTime;
});
$upcomingInvoices = array_slice($upcomingInvoices, 0, 5);

$recentPayments = $payments;
usort($recentPayments, function ($a, $b) {
    $aTime = isset($a['payment_date']) ? strtotime((string) $a['payment_date']) : (isset($a['created_at']) ? strtotime((string) $a['created_at']) : 0);
    $bTime = isset($b['payment_date']) ? strtotime((string) $b['payment_date']) : (isset($b['created_at']) ? strtotime((string) $b['created_at']) : 0);
    return $bTime <=> $aTime;
});
$recentPayments = array_slice($recentPayments, 0, 5);

$normalizeKey = static function (array $item): string {
    $candidates = [
        $item['product_id'] ?? null,
        $item['id'] ?? null,
        $item['slug'] ?? null,
        $item['name'] ?? null,
        $item['product'] ?? null,
    ];
    foreach ($candidates as $candidate) {
        if (!empty($candidate)) {
            return strtolower(trim((string) $candidate));
        }
    }
    return '';
};

$invoiceByProductKey = [];
foreach ($invoiceList as $invoice) {
    $key = $normalizeKey($invoice);
    if ($key === '') {
        continue;
    }
    if (!isset($invoiceByProductKey[$key])) {
        $invoiceByProductKey[$key] = $invoice;
        continue;
    }
    $existing = $invoiceByProductKey[$key];
    $existingDue = isset($existing['due_at']) ? strtotime((string) $existing['due_at']) : PHP_INT_MAX;
    $candidateDue = isset($invoice['due_at']) ? strtotime((string) $invoice['due_at']) : PHP_INT_MAX;
    if ($candidateDue < $existingDue) {
        $invoiceByProductKey[$key] = $invoice;
    }
}

$subscribedKeys = [];
foreach ($subscriptions as $subscription) {
    $key = $normalizeKey($subscription);
    if ($key !== '') {
        $subscribedKeys[$key] = true;
    }
}

$availableProducts = [];
foreach ($productCatalog as $product) {
    $key = $normalizeKey($product);
    if ($key === '' || isset($subscribedKeys[$key])) {
        continue;
    }
    $availableProducts[] = $product;
}

// Normalisasi untuk tabel Tagihan & Pembayaran (isi kolom kosong agar tidak blank)
$invoiceList = array_map(static function (array $inv) use ($invoiceStatusMeta, $bucketInvoice, $nowTs): array {
    $total = (float)($inv['total_amount'] ?? $inv['amount'] ?? 0);
    $due = (float)($inv['amount_due'] ?? max(0, $total - (float)($inv['amount_paid'] ?? 0)));
    $statusKey = $bucketInvoice($inv, $nowTs);
    $meta = $invoiceStatusMeta[$statusKey] ?? $invoiceStatusMeta['pending'];
    return [
        'number' => $inv['invoice_no'] ?? $inv['number'] ?? '-',
        'product' => $inv['product'] ?? $inv['invoice_title'] ?? ($inv['client_name'] ?? '-'),
        'due_at' => $inv['due_at'] ?? $inv['due_date'] ?? null,
        'amount' => $due > 0 ? $due : $total,
        'status' => $statusKey,
        'status_class' => $meta['class'],
        'status_label' => $meta['label'],
    ];
}, $invoiceList);
?>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content dashboard-customer">
            <style>
                .dashboard-customer .card {
                    border: 1px solid #e9edf4;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
                }
                .dashboard-customer .card-header h5 {
                    letter-spacing: 0.2px;
                }
                .dashboard-customer .table > :not(caption) > * > * {
                    vertical-align: middle;
                }
                .dashboard-customer .status-pill {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 104px;
                }
            </style>

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Dashboard Pelanggan</h2>
                    <p class="text-muted mb-1">Ringkasan langganan & tagihan <?php echo htmlspecialchars($customerName); ?></p>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="dashboard-pelanggan.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Pelanggan
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Ringkasan Layanan</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        <div class="input-icon w-120 position-relative">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar text-gray-9"></i>
                            </span>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($billingPeriod); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="row">
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-primary mb-2">
                                <i class="ti ti-broadcast fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Produk Aktif</h6>
                            <h3 class="mb-1"><?php echo $activeSubscriptions; ?></h3>
                            <p class="fs-12 text-gray-9 mb-0">
                                <?php echo $expiringSoon > 0 ? $expiringSoon . ' perlu perpanjangan' : 'Semua stabil'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-secondary mb-2">
                                <i class="ti ti-file-dollar fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Tagihan Belum Dibayar</h6>
                            <h3 class="mb-1"><?php echo $formatCurrency($unpaidTotal); ?></h3>
                            <p class="fs-12 text-gray-9 mb-0"><?php echo $pendingInvoicesCount; ?> tagihan menunggu</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-success mb-2">
                                <i class="ti ti-chart-arrows fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Tagihan Bulan Ini</h6>
                            <h3 class="mb-1"><?php echo $formatCurrency($monthlyTotal); ?></h3>
                            <p class="fs-12 text-gray-9 mb-0"><?php echo $paidThisMonth > 0 ? $formatCurrency($paidThisMonth) . ' sudah dibayar' : 'Belum ada pembayaran'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-info mb-2">
                                <i class="ti ti-alarm fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Perpanjangan Terdekat</h6>
                            <h3 class="mb-1"><?php echo htmlspecialchars($nextRenewalLabel); ?></h3>
                            <p class="fs-12 text-gray-9 mb-0"><?php echo htmlspecialchars($nextRenewalProduct); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-8 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Produk Langganan</h5>
                            <a href="produk-antara.php" class="btn btn-light btn-md mb-2">Produk lainnya</a>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <?php if (empty($subscriptions)): ?>
                                    <div class="col">
                                        <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center text-center">
                                            <p class="mb-2 fw-medium">Belum ada produk langganan</p>
                                            <p class="text-gray-7 mb-3">Tambahkan paket untuk pelanggan ini agar data langganan tampil di sini.</p>
                                            <a href="produk-antara.php" class="btn btn-primary btn-sm align-self-center">Cari Produk</a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($subscriptions as $subscription): ?>
                                        <?php
                                            $statusKey = $subscription['status'] ?? 'active';
                                            $statusMeta = $subscriptionStatusMeta[$statusKey] ?? $subscriptionStatusMeta['active'];
                                            $usage = $subscription['usage'] ?? [];
                                            $percent = isset($usage['percent']) ? (float) $usage['percent'] : 0;
                                            $percent = max(0, min(100, $percent));
                                        ?>
                                        <div class="col">
                                            <div class="border rounded p-3 h-100">
                                                <div class="d-flex align-items-start justify-content-between mb-2">
                                                    <div>
                                                        <p class="fs-12 text-gray-7 mb-1"><?php echo htmlspecialchars($subscription['cycle'] ?? ''); ?></p>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($subscription['name'] ?? 'Produk'); ?></h6>
                                                    </div>
                                                    <span class="badge <?php echo $statusMeta['class']; ?>"><?php echo htmlspecialchars($statusMeta['label']); ?></span>
                                                </div>
                                                <p class="fs-12 text-gray-7 mb-2">Perpanjangan: <?php echo $formatDate($subscription['renewal_at'] ?? null); ?></p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-medium"><?php echo $formatCurrency((float) ($subscription['amount'] ?? 0)); ?> / <?php echo htmlspecialchars(strtolower($subscription['cycle'] ?? '')); ?></span>
                                                <div class="d-flex align-items-center gap-1">
                                                    <?php
                                                        $matchKey = $normalizeKey($subscription);
                                                        $relatedInvoice = $matchKey ? ($invoiceByProductKey[$matchKey] ?? null) : null;
                                                    ?>
                                                    <?php if ($relatedInvoice): ?>
                                                        <a href="invoices.php?search=<?php echo urlencode($relatedInvoice['number'] ?? ($relatedInvoice['product'] ?? '')); ?>" class="btn btn-sm btn-light">Invoice</a>
                                                    <?php endif; ?>
                                                    <a href="produk-antara.php" class="btn btn-sm btn-outline-primary">Detail</a>
                                                </div>
                                            </div>
                                                <?php if (!empty($usage)): ?>
                                                    <div class="mb-2">
                                                        <div class="d-flex justify-content-between fs-12 text-gray-7">
                                                            <span><?php echo htmlspecialchars($usage['label'] ?? 'Pemakaian'); ?></span>
                                                            <span><?php echo htmlspecialchars($usage['value'] ?? ''); ?></span>
                                                        </div>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" style="width: <?php echo $percent; ?>%;"></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($subscription['tags']) && is_array($subscription['tags'])): ?>
                                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                                        <?php foreach ($subscription['tags'] as $tag): ?>
                                                            <span class="badge bg-light text-default border"><?php echo htmlspecialchars($tag); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Status Tagihan</h5>
                            <a href="invoices.php" class="btn btn-light btn-md mb-2">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($invoiceStatusSummary as $statusKey => $summary): ?>
                                    <?php $meta = $invoiceStatusMeta[$statusKey]; ?>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 fw-medium"><?php echo htmlspecialchars($meta['label']); ?></p>
                                            <span class="fs-12 text-gray-7"><?php echo $summary['count']; ?> dokumen</span>
                                        </div>
                                        <span class="<?php echo $meta['class']; ?>"><?php echo $formatCurrency((float) $summary['amount']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="border rounded p-3 mt-3">
                                <p class="mb-1 fw-medium">Tagihan aktif</p>
                                <p class="mb-2 text-gray-7">Total <?php echo $pendingInvoicesCount; ?> tagihan menunggu dengan nilai <?php echo $formatCurrency($unpaidTotal); ?></p>
                                <?php if ($nextDueInvoice): ?>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="mb-0"><?php echo htmlspecialchars($nextDueInvoice['product'] ?? ''); ?></p>
                                            <span class="fs-12 text-gray-7">Jatuh tempo <?php echo $formatDate($nextDueInvoice['due_at'] ?? null); ?></span>
                                        </div>
                                        <span class="fw-medium"><?php echo $formatCurrency((float) ($nextDueInvoice['amount'] ?? 0)); ?></span>
                                    </div>
                                <?php else: ?>
                                    <p class="mb-0 text-gray-7">Tidak ada tagihan yang menunggu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($availableProducts)): ?>
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Produk ANTARA</h5>
                            <a href="produk-antara.php" class="btn btn-light btn-md mb-2">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach (array_slice($availableProducts, 0, 4) as $product): ?>
                                    <li class="list-group-item px-0 d-flex align-items-start justify-content-between">
                                        <div class="me-2">
                                            <p class="mb-1 fw-medium"><?php echo htmlspecialchars($product['name'] ?? 'Produk'); ?></p>
                                            <span class="fs-12 text-gray-7"><?php echo htmlspecialchars($product['description'] ?? 'Produk ANTARA'); ?></span>
                                        </div>
                                        <div class="text-end">
                                            <p class="mb-1 fw-medium"><?php echo $formatCurrency((float) ($product['amount'] ?? 0)); ?></p>
                                            <a href="produk-antara.php" class="btn btn-sm btn-outline-primary">Langganan</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Tagihan & Pembayaran</h5>
                            <a href="payments.php" class="btn btn-light btn-md mb-2">Riwayat Pembayaran</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Produk</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($invoiceList)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <p class="mb-1 fw-medium">Belum ada tagihan tercatat.</p>
                                                    <p class="text-gray-7 mb-0">Buat tagihan baru atau sinkronkan data penagihan pelanggan ini.</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($invoiceList as $invoice): ?>
                                                    <?php
                                                        $statusKey = $invoice['status'] ?? 'pending';
                                                        $meta = $invoiceStatusMeta[$statusKey] ?? $invoiceStatusMeta['pending'];
                                                    ?>
                                                <tr>
                                                    <td class="fw-medium"><?php echo htmlspecialchars($invoice['number'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($invoice['product'] ?? '-'); ?></td>
                                                    <td><?php echo $formatDate($invoice['due_at'] ?? null); ?></td>
                                                    <td><?php echo $formatCurrency((float) ($invoice['amount'] ?? 0)); ?></td>
                                                    <td>
                                                        <span class="status-pill <?php echo $meta['class']; ?>"><?php echo htmlspecialchars($meta['label']); ?></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a class="btn btn-sm btn-outline-primary" href="invoices.php?search=<?php echo urlencode($invoice['number'] ?? ''); ?>">Lihat</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Jadwal Tagihan</h5>
                            <a href="invoices.php" class="btn btn-light btn-sm mb-2">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (empty($upcomingInvoices)): ?>
                                    <li class="list-group-item px-0 text-gray-7">Tidak ada tagihan menunggu.</li>
                                <?php else: ?>
                                    <?php foreach ($upcomingInvoices as $invoice): ?>
                                        <?php
                                            $statusKey = $invoice['status'] ?? 'pending';
                                            $meta = $invoiceStatusMeta[$statusKey] ?? $invoiceStatusMeta['pending'];
                                        ?>
                                        <li class="list-group-item px-0 d-flex align-items-start justify-content-between">
                                            <div class="me-2">
                                                <p class="mb-1 fw-medium"><?php echo htmlspecialchars($invoice['product'] ?? $invoice['invoice_title'] ?? 'Produk'); ?></p>
                                                <span class="fs-12 text-gray-7">Jatuh tempo: <?php echo $formatDate($invoice['due_at'] ?? null); ?></span>
                                            </div>
                                            <div class="text-end">
                                                <p class="mb-1 fw-medium"><?php echo $formatCurrency((float) ($invoice['amount'] ?? $invoice['amount_due'] ?? 0)); ?></p>
                                                <span class="<?php echo $meta['class']; ?>"><?php echo htmlspecialchars($meta['label']); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Pembayaran Terbaru</h5>
                            <a href="payments.php" class="btn btn-light btn-sm mb-2">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (empty($recentPayments)): ?>
                                    <li class="list-group-item px-0 text-gray-7">Belum ada pembayaran tercatat.</li>
                                <?php else: ?>
                                    <?php foreach ($recentPayments as $payment): ?>
                                        <?php
                                            $statusKey = $payment['status'] ?? 'completed';
                                            $meta = $paymentStatusMeta[$statusKey] ?? $paymentStatusMeta['completed'];
                                            $label = $payment['payment_method'] ?? $payment['method'] ?? $payment['channel'] ?? 'Pembayaran';
                                            $targetInvoice = $payment['invoice_number'] ?? $payment['invoice_no'] ?? '';
                                        ?>
                                        <li class="list-group-item px-0 d-flex align-items-start justify-content-between">
                                            <div class="me-2">
                                                <p class="mb-1 fw-medium"><?php echo htmlspecialchars($label); ?></p>
                                                <span class="fs-12 text-gray-7">
                                                    <?php echo $formatDate($payment['payment_date'] ?? $payment['created_at'] ?? null); ?>
                                                    <?php if ($targetInvoice): ?> &bull; Invoice <?php echo htmlspecialchars($targetInvoice); ?><?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <p class="mb-1 fw-medium"><?php echo $formatCurrency((float) ($payment['amount'] ?? 0)); ?></p>
                                                <span class="<?php echo $meta['class']; ?>"><?php echo htmlspecialchars($meta['label']); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Content -->   

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>
