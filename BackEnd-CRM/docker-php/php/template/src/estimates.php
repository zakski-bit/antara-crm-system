<?php ob_start();
$estimates = $estimates ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$statusOptions = $statusOptions ?? [];
$statusFilter = $statusFilter ?? '';
$sort = $sort ?? 'recent';
$baseUrl = $baseUrl ?? '';
$storeUrl = $storeUrl ?? (rtrim($baseUrl, '/') . '/crm/estimates');
$listUrl = $listUrl ?? ($baseUrl === '' ? '/estimates.php' : $baseUrl . '/estimates.php');

$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;

$statusLabels = [
    'draft' => 'Draft',
    'sent' => 'Dikirim',
    'accepted' => 'Diterima',
    'declined' => 'Ditolak',
    'expired' => 'Kedaluwarsa',
];

$sortLabels = [
    'recent' => 'Terbaru',
    'amount_desc' => 'Nominal Terbesar',
    'amount_asc' => 'Nominal Terkecil',
    'expiry' => 'Kedaluwarsa Terdekat',
];

$formatCurrency = static function (float $value): string {
    return 'Rp' . number_format($value, 0, ',', '.');
};

$formatDate = static function (?string $date): string {
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M Y', $timestamp) : $date;
};

$badgeClass = static function (string $status): string {
    switch ($status) {
        case 'accepted':
            return 'badge badge-soft-success';
        case 'sent':
            return 'badge badge-soft-purple';
        case 'expired':
            return 'badge badge-soft-warning text-dark';
        case 'declined':
            return 'badge badge-soft-danger';
        default:
            return 'badge badge-soft-secondary';
    }
};

$buildUrl = static function (?string $status, ?string $sort) use ($listUrl): string {
    $query = [];
    if ($status) {
        $query['status'] = $status;
    }
    if ($sort) {
        $query['sort'] = $sort;
    }
    $queryString = http_build_query($query);
    return $listUrl . ($queryString ? ('?' . $queryString) : '');
};
?>
<style>
    /* Peret banyak kolom supaya tidak perlu scroll horizontal */
    .estimates-table {
        table-layout: auto;
        width: 100%;
    }
    .estimates-table th,
    .estimates-table td {
        white-space: normal;
    }
    .estimates-table .col-client { min-width: 180px; }
    .estimates-table .col-company { min-width: 160px; }
    .estimates-table .col-date { width: 130px; }
    .estimates-table .col-amount { width: 110px; }
    .estimates-table .col-status { width: 120px; }
    .estimates-table .col-actions { width: 90px; }
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
                    <h2 class="mb-1">Draft Penawaran</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                CRM
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Draft Penawaran</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    
                    <div class="mb-2">
                        <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_estimate"><i class="ti ti-circle-plus me-2"></i>Tambah Penawaran</a>
                    </div>
                    <div class="head-icons ms-2">
                        <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                            <i class="ti ti-chevrons-up"></i>
                        </a>
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

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <h5>Daftar Draft Penawaran</h5>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                        
                        <div class="dropdown me-3">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <?php echo $statusFilter ? ($statusLabels[$statusFilter] ?? ucfirst($statusFilter)) : 'Semua Status'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li><a href="<?php echo htmlspecialchars($buildUrl('', $sort)); ?>" class="dropdown-item rounded-1">Semua Status</a></li>
                                <?php foreach ($statusOptions as $status): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($buildUrl($status, $sort)); ?>" class="dropdown-item rounded-1<?php echo $statusFilter === $status ? ' active' : ''; ?>">
                                            <?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                Urutkan: <?php echo htmlspecialchars($sortLabels[$sort] ?? 'Terbaru'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <?php foreach ($sortLabels as $sortKey => $label): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($buildUrl($statusFilter, $sortKey)); ?>" class="dropdown-item rounded-1<?php echo $sort === $sortKey ? ' active' : ''; ?>">
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
                        <table class="table datatable estimates-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="col-client">Kontak</th>
                                    <th class="col-company">Perusahaan</th>
                                    <th class="col-date">Tanggal Penawaran</th>
                                    <th class="col-date">Tanggal Kedaluwarsa</th>
                                    <th class="col-amount">Nominal</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
<?php if (empty($estimates)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar avatar-lg bg-light-primary avatar-rounded mb-2">
                                                <i class="ti ti-file-invoice text-primary fs-20"></i>
                                            </div>
                                            <h6 class="mb-1">Belum ada draft penawaran</h6>
                                            <p class="text-muted mb-3">Tambah penawaran baru untuk layanan CRM Antara.</p>
                                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_estimate"><i class="ti ti-circle-plus me-2"></i>Tambah Penawaran</a>
                                        </div>
                                    </td>
                                </tr>
<?php endif; ?>
<?php foreach ($estimates as $estimate):
    $encoded = htmlspecialchars(json_encode([
        'id' => (int) ($estimate['id'] ?? 0),
        'ref_no' => $estimate['ref_no'] ?? '',
        'client_name' => $estimate['client_name'] ?? '',
        'client_title' => $estimate['client_title'] ?? '',
        'company_name' => $estimate['company_name'] ?? '',
        'project_title' => $estimate['project_title'] ?? '',
        'contact_email' => $estimate['contact_email'] ?? '',
        'estimate_date' => $estimate['estimate_date'] ?? '',
        'expiry_date' => $estimate['expiry_date'] ?? '',
        'amount' => $estimate['amount'] ?? 0,
        'status' => $estimate['status'] ?? 'draft',
        'notes' => $estimate['notes'] ?? '',
        'avatar_path' => $estimate['avatar_path'] ?? '',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center file-name-icon">
                                            <a href="#" class="avatar avatar-sm avatar-rounded">
                                                <img src="<?php echo htmlspecialchars($estimate['avatar_path']); ?>" class="img-fluid" alt="avatar">
                                            </a>
                                            <div class="ms-2">
                                                <h6 class="fw-medium mb-0"><a href="#"><?php echo htmlspecialchars($estimate['client_name']); ?></a></h6>
                                                <span class="d-block mt-1 text-muted fs-12"><?php echo htmlspecialchars($estimate['client_title']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium"><?php echo htmlspecialchars($estimate['company_name']); ?></span>
                                            <?php if (!empty($estimate['project_title'])): ?>
                                                <span class="text-muted fs-12"><?php echo htmlspecialchars($estimate['project_title']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo $formatDate($estimate['estimate_date']); ?></td>
                                    <td><?php echo $formatDate($estimate['expiry_date']); ?></td>
                                    <td><?php echo $formatCurrency((float) $estimate['amount']); ?></td>
                                    <td><span class="<?php echo $badgeClass($estimate['status']); ?>"><?php echo htmlspecialchars($statusLabels[$estimate['status']] ?? ucfirst($estimate['status'])); ?></span></td>
                                    <td>
                                        <div class="action-icon d-inline-flex">
                                            <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_estimate" data-estimate="<?php echo $encoded; ?>" aria-label="Edit penawaran"><i class="ti ti-edit"></i></a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#delete_estimate" data-estimate="<?php echo $encoded; ?>" aria-label="Hapus penawaran"><i class="ti ti-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
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

    <div class="modal fade" id="add_estimate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Draft Penawaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" name="ref_no" class="form-control" value="<?php echo htmlspecialchars($old['ref_no'] ?? ''); ?>" placeholder="Opsional, kosongkan untuk otomatis">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>" <?php echo (($old['status'] ?? '') === $status) ? 'selected' : ''; ?>><?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['status']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Klien<span class="text-danger"> *</span></label>
                            <input type="text" name="client_name" class="form-control" value="<?php echo htmlspecialchars($old['client_name'] ?? ''); ?>" required>
                            <?php if (isset($errors['client_name'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['client_name']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan Kontak</label>
                            <input type="text" name="client_title" class="form-control" value="<?php echo htmlspecialchars($old['client_title'] ?? ''); ?>" placeholder="Contoh: Kepala Humas">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan<span class="text-danger"> *</span></label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($old['company_name'] ?? ''); ?>" required>
                            <?php if (isset($errors['company_name'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['company_name']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Kegiatan / Layanan</label>
                            <input type="text" name="project_title" class="form-control" value="<?php echo htmlspecialchars($old['project_title'] ?? ''); ?>" placeholder="Misal: Distribusi rilis pers">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Kontak</label>
                            <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($old['contact_email'] ?? ''); ?>">
                            <?php if (isset($errors['contact_email'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['contact_email']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Penawaran</label>
                            <input type="date" name="estimate_date" class="form-control" value="<?php echo htmlspecialchars($old['estimate_date'] ?? ''); ?>">
                            <?php if (isset($errors['estimate_date'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['estimate_date']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Kedaluwarsa</label>
                            <input type="date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($old['expiry_date'] ?? ''); ?>">
                            <?php if (isset($errors['expiry_date'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['expiry_date']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal Penawaran (Rp)<span class="text-danger"> *</span></label>
                            <input type="text" name="amount" class="form-control" value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>" required>
                            <?php if (isset($errors['amount'])): ?><small class="text-danger"><?php echo htmlspecialchars($errors['amount']); ?></small><?php endif; ?>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Rincian layanan, deliverable, atau catatan untuk tim."><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
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

    <div class="modal fade" id="edit_estimate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbarui Draft Penawaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body" id="edit-estimate-form">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" name="ref_no" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status<span class="text-danger"> *</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <?php foreach ($statusOptions as $status): ?>
                                    <option value="<?php echo htmlspecialchars($status); ?>"><?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Klien<span class="text-danger"> *</span></label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan Kontak</label>
                            <input type="text" name="client_title" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perusahaan<span class="text-danger"> *</span></label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Kegiatan / Layanan</label>
                            <input type="text" name="project_title" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Kontak</label>
                            <input type="email" name="contact_email" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Penawaran</label>
                            <input type="date" name="estimate_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Kedaluwarsa</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal Penawaran (Rp)<span class="text-danger"> *</span></label>
                            <input type="text" name="amount" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-1">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_estimate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Hapus Draft Penawaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" id="delete-estimate-form">
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="modal-body pt-1">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus draft penawaran ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = document.getElementById('edit_estimate');
            var deleteModal = document.getElementById('delete_estimate');

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var estimateData = button ? button.getAttribute('data-estimate') : null;
                    if (!estimateData) {
                        return;
                    }
                    try {
                        var estimate = JSON.parse(estimateData);
                        var form = editModal.querySelector('form');
                        form.action = "<?php echo htmlspecialchars($storeUrl); ?>" + '/' + estimate.id;
                        form.querySelector('[name=ref_no]').value = estimate.ref_no || '';
                        form.querySelector('[name=client_name]').value = estimate.client_name || '';
                        form.querySelector('[name=client_title]').value = estimate.client_title || '';
                        form.querySelector('[name=company_name]').value = estimate.company_name || '';
                        form.querySelector('[name=project_title]').value = estimate.project_title || '';
                        form.querySelector('[name=contact_email]').value = estimate.contact_email || '';
                        form.querySelector('[name=estimate_date]').value = estimate.estimate_date || '';
                        form.querySelector('[name=expiry_date]').value = estimate.expiry_date || '';
                        form.querySelector('[name=amount]').value = estimate.amount || '';
                        form.querySelector('[name=status]').value = estimate.status || '';
                        form.querySelector('[name=notes]').value = estimate.notes || '';
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var estimateData = button ? button.getAttribute('data-estimate') : null;
                    if (!estimateData) {
                        return;
                    }
                    try {
                        var estimate = JSON.parse(estimateData);
                        var form = deleteModal.querySelector('form');
                        form.action = "<?php echo htmlspecialchars($storeUrl); ?>" + '/' + estimate.id;
                    } catch (error) {
                        console.error(error);
                    }
                });
            }
        });
    </script>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>   
