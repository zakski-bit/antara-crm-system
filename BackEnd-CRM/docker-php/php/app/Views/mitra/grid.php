<?php
/** @var array $partners */
/** @var array $flash */
/** @var string $baseUrl */

$baseUrl = $baseUrl ?? '';
$flash = $flash ?? [];
$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;

$_SERVER['APP_BASE_URL'] = $baseUrl ?? '';
$_SERVER['PHP_SELF'] = '/companies-grid.php';

ob_start();

include_once BASE_PATH . '/template/partials/partner-styles.php';

$gridUrl = $baseUrl . '/crm/mitra';
$tableUrl = $baseUrl . '/crm/mitra/table';
$createUrl = $baseUrl . '/crm/mitra/create';
$editUrl = static function (int $id) use ($baseUrl): string {
    return $baseUrl . '/crm/mitra/' . $id . '/edit';
};
$deleteUrl = static function (int $id) use ($baseUrl): string {
    return $baseUrl . '/crm/mitra/' . $id;
};
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
                    <h2 class="mb-1">Mitra & Korporasi</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($baseUrl . '/'); ?>"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                CRM
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Grid Mitra</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                            <a href="<?php echo htmlspecialchars($tableUrl); ?>" class="btn btn-icon btn-sm me-1"><i class="ti ti-list-tree"></i></a>
                            <a href="<?php echo htmlspecialchars($gridUrl); ?>" class="btn btn-icon btn-sm active bg-primary text-white"><i class="ti ti-layout-grid"></i></a>
                        </div>
                    </div>
                    <div class="mb-2">
                        <a href="<?php echo htmlspecialchars($createUrl); ?>" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Tambah Mitra</a>
                    </div>
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

            <div class="row">
<?php if (empty($partners)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-xl bg-light-primary rounded-circle mb-3">
                                <i class="ti ti-building text-primary fs-30"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Belum ada data mitra</h5>
                            <p class="text-muted mb-3">Mulai dengan menambahkan mitra strategis pertama Anda.</p>
                            <a href="<?php echo htmlspecialchars($createUrl); ?>" class="btn btn-primary"><i class="ti ti-circle-plus me-2"></i>Tambah Mitra</a>
                        </div>
                    </div>
                </div>
<?php else: ?>
<?php foreach ($partners as $item): ?>
<?php
    $logoPath = $item['logo'] !== '' ? $item['logo'] : 'assets/img/logo.png';
    $status = $item['status'] ?? '';
    $statusClass = 'bg-secondary-transparent';
    if (stripos($status, 'strategic') !== false || stripos($status, 'security') !== false || stripos($status, 'technology') !== false) {
        $statusClass = 'bg-success-transparent';
    } elseif (stripos($status, 'media') !== false || stripos($status, 'network') !== false || stripos($status, 'editorial') !== false) {
        $statusClass = 'bg-info-transparent';
    }
?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-end align-items-start mb-2">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-3">
                                        <li><a class="dropdown-item rounded-1 d-flex align-items-center" href="<?php echo htmlspecialchars($editUrl((int)$item['id'])); ?>"><i class="ti ti-edit me-1"></i>Edit</a></li>
<?php $confirmMessage = 'Hapus mitra ' . $item['nama'] . '?'; ?>
                                        <li>
                                            <form method="POST" action="<?php echo htmlspecialchars($deleteUrl((int)$item['id'])); ?>" onsubmit="return confirm('<?php echo htmlspecialchars($confirmMessage, ENT_QUOTES, 'UTF-8'); ?>');">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="dropdown-item rounded-1 text-danger d-flex align-items-center">
                                                    <i class="ti ti-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <div class="partner-logo-wrapper">
                                    <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo <?php echo htmlspecialchars($item['nama']); ?>">
                                </div>
                                <h6 class="mb-1"><a href="<?php echo htmlspecialchars($editUrl((int)$item['id'])); ?>"><?php echo htmlspecialchars($item['nama']); ?></a></h6>
                                <span class="badge <?php echo htmlspecialchars($statusClass); ?> fs-10 fw-medium"><?php echo htmlspecialchars($item['industri']); ?></span>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <p class="text-dark d-inline-flex align-items-center mb-2">
                                    <i class="ti ti-user text-gray-5 me-2"></i>
                                    <?php echo htmlspecialchars($item['kontak'] ?: '-'); ?>
                                </p>
                                <p class="text-dark d-inline-flex align-items-center mb-2">
                                    <i class="ti ti-mail-forward text-gray-5 me-2"></i>
                                    <?php echo htmlspecialchars($item['email']); ?>
                                </p>
                                <p class="text-dark d-inline-flex align-items-center mb-2">
                                    <i class="ti ti-phone text-gray-5 me-2"></i>
                                    <?php echo htmlspecialchars($item['telepon']); ?>
                                </p>
                                <p class="text-dark d-inline-flex align-items-center mb-0">
                                    <i class="ti ti-map-pin text-gray-5 me-2"></i>
                                    <?php echo htmlspecialchars($item['alamat']); ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                                <span class="badge bg-secondary-transparent"><?php echo htmlspecialchars($status); ?></span>
                                <a href="<?php echo htmlspecialchars($editUrl((int)$item['id'])); ?>" class="link-default fs-12">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
<?php endforeach; ?>
<?php endif; ?>
            </div>

        </div>
        <!-- End Content -->   

        <?php include BASE_PATH . '/template/partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

<?php
$content = ob_get_clean();

require BASE_PATH . '/template/partials/main.php';
