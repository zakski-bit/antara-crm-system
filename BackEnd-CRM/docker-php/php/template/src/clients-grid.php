<?php
if (!function_exists('flash_get')) {
    require_once dirname(__DIR__) . '/app/functions.php';
}

$isDataInjected = isset($clients);

ob_start();

$clients = isset($clients) && is_array($clients) ? $clients : [];
$clientsError = $clients_error ?? null;
$clientStats = isset($client_stats) && is_array($client_stats) ? array_merge([
    'total' => count($clients),
    'active' => 0,
    'inactive' => 0,
    'prospect' => 0,
    'new_this_month' => 0,
], $client_stats) : [
    'total' => count($clients),
    'active' => 0,
    'inactive' => 0,
    'prospect' => 0,
    'new_this_month' => 0,
];

$totalClients = (int) ($clientStats['total'] ?? count($clients));
$activeClients = (int) ($clientStats['active'] ?? 0);
$inactiveClients = (int) ($clientStats['inactive'] ?? 0);
$prospectClients = (int) ($clientStats['prospect'] ?? 0);
$newClientsThisMonth = (int) ($clientStats['new_this_month'] ?? 0);
$activeShare = $totalClients > 0 ? ($activeClients / $totalClients) * 100 : 0;
$inactiveShare = $totalClients > 0 ? ($inactiveClients / $totalClients) * 100 : 0;
$prospectShare = $totalClients > 0 ? ($prospectClients / $totalClients) * 100 : 0;

$formatPercent = static function (float $value): string {
    $formatted = number_format($value, 1);
    return rtrim(rtrim($formatted, '0'), '.');
};

$baseUrlPrefix = '';
if (class_exists('\App\Core\Config')) {
    $baseUrlPrefix = trim(\App\Core\Config::get('app.base_url', ''), '/');
    if ($baseUrlPrefix !== '') {
        $baseUrlPrefix = '/' . $baseUrlPrefix;
    }
}
$clientsListHref = 'clients.php';
$clientsGridHref = 'clients-grid.php';
if ($baseUrlPrefix !== '' && strpos($baseUrlPrefix, '/template') === false) {
    $clientsListHref = $baseUrlPrefix . '/crm/clients';
    $clientsGridHref = $baseUrlPrefix . '/crm/clients/grid';
}
$emailComposeBase = $baseUrlPrefix !== '' ? $baseUrlPrefix . '/dashboard/email?compose=' : '/dashboard/email?compose=';

if (!$isDataInjected) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $userId = (int) ($_SESSION['user_id'] ?? 0);

    try {
        $repository = new \App\Repositories\ClientRepository();
        $clients = $repository->all($userId, 100);
        $clientStats = $repository->stats($clients);
        $clientsError = null;
    } catch (\Throwable $exception) {
        $clients = [];
        $clientStats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'prospect' => 0,
            'new_this_month' => 0,
        ];
        $clientsError = 'Gagal memuat data klien: ' . $exception->getMessage();
    }

    $totalClients = (int) ($clientStats['total'] ?? count($clients));
    $activeClients = (int) ($clientStats['active'] ?? 0);
    $inactiveClients = (int) ($clientStats['inactive'] ?? 0);
    $prospectClients = (int) ($clientStats['prospect'] ?? 0);
    $newClientsThisMonth = (int) ($clientStats['new_this_month'] ?? 0);
    $activeShare = $totalClients > 0 ? ($activeClients / $totalClients) * 100 : 0;
    $inactiveShare = $totalClients > 0 ? ($inactiveClients / $totalClients) * 100 : 0;
    $prospectShare = $totalClients > 0 ? ($prospectClients / $totalClients) * 100 : 0;
}

$clientFormErrors = flash_get('client_form_errors', []);
if (!is_array($clientFormErrors)) {
    $clientFormErrors = [];
}

$clientFormOld = flash_get('client_form_old', []);
if (!is_array($clientFormOld)) {
    $clientFormOld = [];
}

$clientSuccessMessage = flash_get('client_success');
$clientErrorMessage = flash_get('client_error');
?>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Clients</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Employee
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Client Grid</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                            <a href="<?= htmlspecialchars($clientsListHref, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-icon btn-sm me-1"><i class="ti ti-list-tree"></i></a>
                            <a href="<?= htmlspecialchars($clientsGridHref, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-icon btn-sm active bg-primary text-white"><i class="ti ti-layout-grid"></i></a>
                        </div>
                    </div>
                    <div class="mb-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_client" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Add Client</a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <?php if ($clientSuccessMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>
                <span><?= htmlspecialchars($clientSuccessMessage, ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if ($clientErrorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>
                <span><?= htmlspecialchars($clientErrorMessage, ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                        <h5 class="mb-0">Client Grid</h5>
                        <div class="d-flex align-items-center flex-wrap row-gap-3">
                            <div class="dropdown me-2">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="client-status-filter-label" data-selected-status="all">
                                    Status : All Statuses
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="all">All Statuses</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="active">Active</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="inactive">Inactive</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="prospect">Prospect</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="pending">Pending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-status-filter" data-status="archived">Archived</a></li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="client-sort-filter-label" data-selected-sort="recent">
                                    Sort By : Recently Added
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-sort-filter" data-sort="recent">Recently Added</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-sort-filter" data-sort="oldest">Oldest First</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-sort-filter" data-sort="last7">Last 7 Days</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 client-sort-filter" data-sort="last30">Last 30 Days</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($clientsError): ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>
                <span><?= htmlspecialchars($clientsError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>

            <!-- Clients Info -->
            <div class="row" id="clients-grid-row">
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span class="p-2 br-10 bg-pink-transparent border border-pink d-flex align-items-center justify-content-center">
                                            <i class="ti ti-users-group text-pink fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Total Clients</p>
                                        <h4><?= number_format($totalClients) ?></h4>
                                    </div>
                                </div>
                                <span class="badge bg-transparent-purple d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-square-rounded-plus me-1"></i>
                                    <?= number_format($newClientsThisMonth) ?> bulan ini
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span class="p-2 br-10 bg-success-transparent border border-success d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-share fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Active Clients</p>
                                        <h4><?= number_format($activeClients) ?></h4>
                                    </div>
                                </div>
                                <span class="badge bg-transparent-primary text-primary d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-percentage me-1"></i>
                                    <?= $formatPercent($activeShare) ?>% dari total
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span class="p-2 br-10 bg-danger-transparent border border-danger d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-pause fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Inactive Clients</p>
                                        <h4><?= number_format($inactiveClients) ?></h4>
                                    </div>
                                </div>
                                <span class="badge bg-transparent-dark text-dark d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-chart-arrows me-1"></i>
                                    <?= $formatPercent($inactiveShare) ?>% dari total
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <span class="p-2 br-10 bg-info-transparent border border-info d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-plus fs-18"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="fs-12 fw-medium mb-0 text-gray-5 mb-1">Prospect Clients</p>
                                        <h4><?= number_format($prospectClients) ?></h4>
                                    </div>
                                </div>
                                <span class="badge bg-transparent-secondary text-dark d-inline-flex align-items-center fw-normal">
                                    <i class="ti ti-user-star me-1"></i>
                                    <?= $formatPercent($prospectShare) ?>% dari total
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Clients Info -->

            <!-- Clients Grid -->
            <div class="row">
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $client): ?>
                        <?php
                        $clientId = isset($client['id']) ? (int) $client['id'] : 0;
                        $clientCode = $client['code'] ?? ($clientId > 0 ? sprintf('CLI-%03d', $clientId) : 'CLI');
                        $clientName = trim((string) ($client['name'] ?? 'Tidak diketahui'));
                        $jobTitle = trim((string) ($client['job_title'] ?? $client['position'] ?? 'Client'));
                        $status = strtolower((string) ($client['status'] ?? 'active'));
                        $avatarPath = $client['avatar_path'] ?? 'assets/img/users/user-01.jpg';
                        $email = trim((string) ($client['email'] ?? ''));
                        $phone = trim((string) ($client['phone'] ?? ''));
                        $projectName = '';
                        $projectProgress = null;
                        $createdAtRaw = $client['created_at'] ?? null;
                        $createdAt = null;
                        $createdTs = null;
                        if ($createdAtRaw) {
                            try {
                                $dt = new \DateTimeImmutable((string)$createdAtRaw);
                                $createdAt = $dt->format('Y-m-d H:i:s');
                                $createdTs = $dt->getTimestamp();
                            } catch (\Throwable $ignored) {
                                $createdAt = null;
                                $createdTs = null;
                            }
                        }
                        if ($createdTs === null) {
                            $createdTs = time();
                            $createdAt = date('Y-m-d H:i:s', $createdTs);
                        }

                        $avatarStatusClass = 'online';
                        $statusBadgeClass = 'badge bg-success-transparent text-success';
                        $statusLabel = 'Active';

                        if ($status === 'inactive') {
                            $avatarStatusClass = 'offline';
                            $statusBadgeClass = 'badge bg-danger-transparent text-danger';
                            $statusLabel = 'Inactive';
                        } elseif (in_array($status, ['prospect', 'pending'], true)) {
                            $avatarStatusClass = 'away';
                            $statusBadgeClass = 'badge bg-warning-transparent text-warning';
                            $statusLabel = ucfirst($status);
                        } elseif ($status !== '' && $status !== 'active') {
                            $statusBadgeClass = 'badge bg-secondary text-dark';
                            $statusLabel = ucfirst($status);
                        }

                        $messageHref = $email !== '' ? $emailComposeBase . rawurlencode($email) : 'javascript:void(0);';
                        $messageClass = 'avatar avatar-rounded avatar-sm bg-light';
                        if ($email === '') {
                            $messageClass .= ' opacity-50 pe-none';
                        }
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 client-card-wrapper" data-client-card data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" data-created-at="<?= htmlspecialchars($createdAt ?? '', ENT_QUOTES, 'UTF-8') ?>" data-created-ts="<?= (int)$createdTs ?>">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column client-card-body text-center position-relative">
                                    <div class="position-absolute end-0 top-0">
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                           <ul class="dropdown-menu dropdown-menu-end p-3">
                                                <li>
                                                    <a class="dropdown-item rounded-1 client-edit-trigger"
                                                       href="javascript:void(0);"
                                                       data-client-id="<?= $clientId ?>"
                                                       data-client-first-name="<?= htmlspecialchars($client['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-last-name="<?= htmlspecialchars($client['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-username="<?= htmlspecialchars($client['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-email="<?= htmlspecialchars($client['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-phone="<?= htmlspecialchars($client['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-company="<?= htmlspecialchars($client['company'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-status="<?= htmlspecialchars($client['status'] ?? 'active', ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-job="<?= htmlspecialchars($jobTitle, ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-avatar="<?= htmlspecialchars($avatarPath, ENT_QUOTES, 'UTF-8') ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#edit_client">
                                                        <i class="ti ti-edit me-1"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item rounded-1 client-delete-trigger"
                                                       href="#"
                                                       data-client-id="<?= $clientId ?>"
                                                       data-client-name="<?= htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8') ?>"
                                                       data-client-code="<?= htmlspecialchars($clientCode, ENT_QUOTES, 'UTF-8') ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#grid_delete_client_modal"><i class="ti ti-trash me-1"></i>Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex justify-content-center">
                                        <a href="client-details.php" class="avatar avatar-xl client-avatar-square <?= $avatarStatusClass ?> border p-1 border-primary" style="width: 96px; height: 96px;">
                                            <img src="<?= htmlspecialchars($avatarPath, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid h-100 w-100" alt="Avatar">
                                        </a>
                                    </div>
                                    <div class="text-center mb-3">
                                        <h6 class="mb-1"><a href="client-details.php"><?= htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8') ?></a></h6>
                                        <div class="d-flex justify-content-center flex-wrap gap-1">
                                            <span class="badge bg-pink-transparent fs-10 fw-medium"><?= htmlspecialchars($jobTitle, ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="<?= $statusBadgeClass ?> fs-10 fw-medium"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                    <?php if ($projectName !== '' || $projectProgress !== null): ?>
                                    <div class="mb-3">
                                        <?php if ($projectName !== ''): ?>
                                        <p class="mb-2 text-truncate">Project : <?= htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php endif; ?>
                                        <?php if ($projectProgress !== null): ?>
                                        <div class="progress progress-xs mb-2">
                                            <div class="progress-bar bg-purple" role="progressbar" style="width: <?= $projectProgress ?>%"></div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="text-purple"><?= $projectProgress ?>%</span>
                                            <span class="small text-gray-6">Progress</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    <ul class="list-unstyled small text-gray-6 mb-3 text-center">
                                        <?php if ($email !== ''): ?>
                                        <li class="mb-1"><i class="ti ti-mail me-2"></i><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></li>
                                        <?php endif; ?>
                                        <li><i class="ti ti-id-badge me-2"></i><?= htmlspecialchars($clientCode, ENT_QUOTES, 'UTF-8') ?></li>
                                    </ul>
                                    <div class="mt-auto d-flex align-items-center justify-content-between border-top pt-3">
                                        <div>
                                            <p class="mb-1 fs-12">Company</p>
                                            <h6 class="fw-normal text-truncate mb-0"><?= htmlspecialchars($client['company'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h6>
                                        </div>
                                        <div class="icons-social d-flex align-items-center">
                                            <a href="<?= htmlspecialchars($messageHref, ENT_QUOTES, 'UTF-8') ?>" class="<?= htmlspecialchars($messageClass, ENT_QUOTES, 'UTF-8') ?>" title="Compose Email">
                                                <i class="ti ti-mail"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5 border border-dashed rounded">
                            <i class="ti ti-database-off d-block fs-24 mb-2"></i>
                            <p class="mb-1">Belum ada data klien.</p>
                            <small class="text-muted">Tambah data melalui tombol "Add Client" atau pastikan konfigurasi database sudah benar.</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- /Clients Grid -->

        </div>
        <!-- End Content -->   

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

        <div class="modal fade" id="grid_delete_client_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <?php
                    $currentPageSlug = 'clients-grid';
                    $gridActionUrl = rtrim($baseUrlPrefix, '/') . '/crm/clients';
                    ?>
                    <form id="grid-delete-client-form" action="<?= htmlspecialchars($gridActionUrl, ENT_QUOTES, 'UTF-8') ?>?page=<?= htmlspecialchars($currentPageSlug, ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <input type="hidden" name="_action" value="client_delete">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="_redirect" value="<?= htmlspecialchars($currentPageSlug, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="client_id" id="grid-delete-client-id" value="">
                        <div class="modal-body text-center">
                            <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                                <i class="ti ti-trash-x fs-36"></i>
                            </span>
                            <h4 class="mb-1">Konfirmasi Hapus</h4>
                            <p class="mb-3">Anda yakin ingin menghapus klien <span class="fw-semibold" id="grid-delete-client-label"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <script>
        (function () {
            function uniqueModals() {
                ['add_client', 'edit_client', 'delete_client_modal'].forEach(function (id) {
                    var nodes = document.querySelectorAll('#' + id);
                    nodes.forEach(function (node, index) {
                        if (index > 0) {
                            node.remove();
                        }
                    });
                });
            }

            function setupGridFilters() {
                var cards = Array.prototype.slice.call(document.querySelectorAll('[data-client-card]'));
                var statusLabel = document.getElementById('client-status-filter-label');
                var sortLabel = document.getElementById('client-sort-filter-label');
                var statusFilterLinks = document.querySelectorAll('.client-status-filter');
                var sortFilterLinks = document.querySelectorAll('.client-sort-filter');
                var deleteModal = document.getElementById('grid_delete_client_modal');
                var deleteForm = document.getElementById('grid-delete-client-form');
                var deleteInput = document.getElementById('grid-delete-client-id');
                var deleteLabel = document.getElementById('grid-delete-client-label');
                if (!cards.length || !statusLabel || !sortLabel) {
                    return;
                }

                function ensureJobInputs() {
                    function createJobInput(form, id) {
                        if (!form) return null;
                        var existing = document.getElementById(id);
                        if (existing) return existing;
                        var phoneInput = form.querySelector('input[name="phone"]');
                        var phoneCol = phoneInput ? phoneInput.closest('.col-md-6, .col-md-4, .col-12') : null;
                        var row = phoneCol ? phoneCol.parentElement : form.querySelector('.row') || form;
                        var col = document.createElement('div');
                        col.className = phoneCol ? phoneCol.className : 'col-md-6';
                        var wrap = document.createElement('div');
                        wrap.className = 'mb-3';
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.setAttribute('for', id);
                        label.textContent = 'Job Title';
                        var input = document.createElement('input');
                        input.className = 'form-control';
                        input.type = 'text';
                        input.name = 'job_title';
                        input.id = id;
                        wrap.appendChild(label);
                        wrap.appendChild(input);
                        col.appendChild(wrap);
                        if (phoneCol && phoneCol.parentElement) {
                            phoneCol.parentElement.insertBefore(col, phoneCol);
                        } else {
                            row.appendChild(col);
                        }
                        return input;
                    }

                    return {
                        add: createJobInput(document.getElementById('add-client-form'), 'add-client-job-title'),
                        edit: createJobInput(document.getElementById('edit-client-form'), 'edit-client-job-title'),
                    };
                }

                var jobInputs = ensureJobInputs();
                var addJobInput = jobInputs.add;
                var editJobInput = jobInputs.edit;

                function normalizeAvatarInputs() {
                    document.querySelectorAll('#add_client input.image-sign, #edit_client input.image-sign').forEach(function (input) {
                        input.setAttribute('name', 'avatar');
                        input.setAttribute('accept', 'image/*');
                        input.removeAttribute('multiple');
                    });

                    function ensurePreview(input, defaultSrc) {
                        if (!input) return null;
                        var uploadBox = input.closest('.profile-upload');
                        if (!uploadBox) return null;
                        var avatarBox = uploadBox.previousElementSibling;
                        if (!avatarBox) return null;
                        avatarBox.classList.add('client-avatar-square');
                        var img = avatarBox.querySelector('img');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'img-fluid w-100 h-100';
                            avatarBox.innerHTML = '';
                            avatarBox.appendChild(img);
                        }
                        img.src = defaultSrc;
                        input.addEventListener('change', function () {
                            var file = input.files && input.files[0];
                            if (!file) return;
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        });
                        return img;
                    }

                    var addInput = document.querySelector('#add_client input[name="avatar"]');
                    var editInput = document.querySelector('#edit_client input[name="avatar"]');
                    ensurePreview(addInput, 'assets/img/users/user-01.jpg');
                    ensurePreview(editInput, 'assets/img/users/user-01.jpg');

                    // Remove permissions/secondary tabs not used
                    ['#address', '#address2', '[data-bs-target="#address"]', '[data-bs-target="#address2"]'].forEach(function (sel) {
                        document.querySelectorAll(sel).forEach(function (el) { el.remove(); });
                    });
                }

                function removePasswordFields() {
                    var selectors = [
                        '#add_client input[name=\"password\"]',
                        '#add_client input[name=\"confirm_password\"]',
                        '#edit_client input[name=\"password\"]',
                        '#edit_client input[name=\"confirm_password\"]'
                    ];
                    selectors.forEach(function (sel) {
                        document.querySelectorAll(sel).forEach(function (input) {
                            var col = input.closest('.col-md-6');
                            if (col) {
                                col.remove();
                            } else {
                                input.remove();
                            }
                        });
                    });
                }

                normalizeAvatarInputs();
                removePasswordFields();

                var statusMap = {
                    all: 'All Statuses',
                    active: 'Active',
                    inactive: 'Inactive',
                    prospect: 'Prospect',
                    pending: 'Pending',
                    archived: 'Archived'
                };

                var sortMap = {
                    recent: 'Recently Added',
                    oldest: 'Oldest First',
                    last7: 'Last 7 Days',
                    last30: 'Last 30 Days'
                };

                function applyFilters() {
                    var statusValue = statusLabel.dataset.selectedStatus || 'all';
                    var sortValue = sortLabel.dataset.selectedSort || 'recent';
                    var now = Date.now() / 1000;
                    var horizonSeconds = null;
                    if (sortValue === 'last7') {
                        horizonSeconds = 7 * 24 * 60 * 60;
                    } else if (sortValue === 'last30') {
                        horizonSeconds = 30 * 24 * 60 * 60;
                    }

                    var filtered = cards.filter(function (card) {
                        var cardStatus = (card.dataset.status || '').toLowerCase();
                        var cardTs = parseInt(card.dataset.createdTs || '0', 10);
                        var statusOk = statusValue === 'all' || cardStatus === statusValue;
                        var timeOk = true;
                        if (horizonSeconds !== null) {
                            timeOk = (now - cardTs) <= horizonSeconds;
                        }
                        return statusOk && timeOk;
                    });

                    if (sortValue === 'recent' || sortValue === 'last7' || sortValue === 'last30') {
                        filtered.sort(function (a, b) {
                            return parseInt(b.dataset.createdTs || '0', 10) - parseInt(a.dataset.createdTs || '0', 10);
                        });
                    } else if (sortValue === 'oldest') {
                        filtered.sort(function (a, b) {
                            return parseInt(a.dataset.createdTs || '0', 10) - parseInt(b.dataset.createdTs || '0', 10);
                        });
                    }

                    var gridRow = document.getElementById('clients-grid-row');
                    if (gridRow) {
                        filtered.forEach(function (card) {
                            gridRow.appendChild(card);
                        });
                        cards.forEach(function (card) {
                            card.style.display = filtered.indexOf(card) !== -1 ? '' : 'none';
                        });
                    }
                }

                statusFilterLinks.forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        var selected = this.dataset.status || 'all';
                        statusLabel.dataset.selectedStatus = selected;
                        statusLabel.textContent = 'Status : ' + (statusMap[selected] || 'All');
                        applyFilters();
                    });
                });

                sortFilterLinks.forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        var selected = this.dataset.sort || 'recent';
                        sortLabel.dataset.selectedSort = selected;
                        sortLabel.textContent = 'Sort By : ' + (sortMap[selected] || 'Recently Added');
                        applyFilters();
                    });
                });

                function setEditAvatar(trigger) {
                    var src = trigger ? (trigger.dataset.clientAvatar || 'assets/img/users/user-01.jpg') : 'assets/img/users/user-01.jpg';
                    var uploadBox = document.querySelector('#edit_client input[name="avatar"]')?.closest('.profile-upload');
                    if (uploadBox && uploadBox.previousElementSibling) {
                        var img = uploadBox.previousElementSibling.querySelector('img');
                        if (img) {
                            img.src = src;
                        }
                    }
                }

                function setEditJob(trigger) {
                    if (!editJobInput) return;
                    var job = trigger ? (trigger.dataset.clientJob || '') : '';
                    editJobInput.value = job;
                }

                document.addEventListener('click', function (event) {
                    var deleteTrigger = event.target.closest('.client-delete-trigger');
                    if (deleteTrigger && deleteModal && deleteForm && deleteInput) {
                        event.preventDefault();
                        var id = deleteTrigger.dataset.clientId || '';
                        var name = deleteTrigger.dataset.clientName || 'klien ini';
                        var code = deleteTrigger.dataset.clientCode || '';
                        deleteInput.value = id;
                        deleteLabel.textContent = code ? name + ' (' + code + ')' : name;
                    }

                    var editTrigger = event.target.closest('.client-edit-trigger');
                    if (editTrigger) {
                        setEditAvatar(editTrigger);
                        setEditJob(editTrigger);
                    }
                });

                applyFilters();
            }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        uniqueModals();
                        setupGridFilters();
                    }, { once: true });
                } else {
                    uniqueModals();
                    setupGridFilters();
                }
            })();
    </script>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>   
