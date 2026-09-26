<?php
/** @var array $partners */
/** @var array $flash */
/** @var string $baseUrl */

$baseUrl = $baseUrl ?? '';
$flash = $flash ?? [];
$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;

$_SERVER['APP_BASE_URL'] = $baseUrl ?? '';
$_SERVER['PHP_SELF'] = '/companies-crm.php';

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
                    <h2 class="mb-1">Daftar Mitra &amp; Korporasi</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($baseUrl . '/'); ?>"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                CRM
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Mitra</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <a href="<?php echo htmlspecialchars($gridUrl); ?>" class="btn btn-white btn-md"><i class="ti ti-layout-grid me-1"></i>Tampilan Grid</a>
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

            <!-- Company List -->
            <div class="card">
                <div class="card-body p-0">
<?php if (empty($partners)): ?>
                    <div class="text-center py-5">
                        <h6 class="fw-semibold mb-2">Belum ada data mitra.</h6>
                        <p class="text-muted mb-3">Klik tombol tambah untuk mulai mengisi daftar mitra.</p>
                        <a href="<?php echo htmlspecialchars($createUrl); ?>" class="btn btn-primary btn-sm"><i class="ti ti-circle-plus me-2"></i>Tambah Mitra</a>
                    </div>
<?php else: ?>
                    <div class="table-responsive">
                        <table class="table datatable align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th style="min-width: 260px;">Mitra</th>
                                    <th>Sektor</th>
                                    <th>Kontak Utama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($partners as $row): ?>
<?php
    $logoPath = $row['logo'] !== '' ? $row['logo'] : 'assets/img/logo.png';
    $status = $row['status'] ?? '';
    $badgeClass = 'bg-secondary-transparent';
    if (stripos($status, 'strategic') !== false || stripos($status, 'security') !== false || stripos($status, 'technology') !== false) {
        $badgeClass = 'bg-success-transparent';
    } elseif (stripos($status, 'media') !== false || stripos($status, 'network') !== false || stripos($status, 'editorial') !== false) {
        $badgeClass = 'bg-info-transparent';
    }
    $confirmMessage = 'Hapus mitra ' . ($row['nama'] ?? '') . '?';
?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="partner-logo-wrapper partner-logo-wrapper--small me-2">
                                                <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo <?php echo htmlspecialchars($row['nama']); ?>">
                                            </div>
                                            <div>
                                                <h6 class="fw-medium mb-1"><a href="<?php echo htmlspecialchars($editUrl((int)$row['id'])); ?>"><?php echo htmlspecialchars($row['nama']); ?></a></h6>
                                                <span class="fs-12 text-muted"><?php echo htmlspecialchars($row['alamat']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['industri']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kontak'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['telepon']); ?></td>
                                    <td>
                                        <span class="badge <?php echo htmlspecialchars($badgeClass); ?>"><?php echo htmlspecialchars($status); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                                <li><a class="dropdown-item rounded-1 d-flex align-items-center" href="<?php echo htmlspecialchars($editUrl((int)$row['id'])); ?>"><i class="ti ti-edit me-1"></i>Edit</a></li>
                                                <li>
                                                    <form method="POST" action="<?php echo htmlspecialchars($deleteUrl((int)$row['id'])); ?>" onsubmit="return confirm('<?php echo htmlspecialchars($confirmMessage, ENT_QUOTES, 'UTF-8'); ?>');">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="dropdown-item rounded-1 text-danger d-flex align-items-center">
                                                            <i class="ti ti-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
<?php endif; ?>
                </div>
            </div>
            <!-- /Company List -->

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
