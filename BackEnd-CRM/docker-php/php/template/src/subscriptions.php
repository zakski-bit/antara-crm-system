<?php
ob_start();

$subscriptions = $subscriptions ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$cycleOptions = $cycleOptions ?? [];
$clients = $clients ?? [];
$customers = $customers ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$baseUrl = $baseUrl ?? '';
$listUrl = $listUrl ?? ($baseUrl === '' ? '/subscriptions.php' : $baseUrl . '/subscriptions.php');
$storeUrl = $storeUrl ?? ($baseUrl === '' ? '/crm/subscriptions' : $baseUrl . '/crm/subscriptions');

$search = $filters['search'] ?? '';
$status = $filters['status'] ?? '';
$cycle = $filters['cycle'] ?? '';
$page = max(1, (int) ($filters['page'] ?? 1));
$perPage = max(1, (int) ($filters['per_page'] ?? 25));
$total = (int) ($filters['total'] ?? count($subscriptions));
$totalPages = max(1, (int) ($filters['total_pages'] ?? ceil($total / $perPage)));

$formatCurrency = static function (float $value, string $currency = 'IDR'): string {
    $prefix = strtoupper($currency) === 'IDR' ? 'Rp' : strtoupper($currency) . ' ';
    return $prefix . number_format($value, 0, ',', '.');
};

$formatDate = static function (?string $value): string {
    if (!$value) return '-';
    $ts = strtotime($value);
    return $ts ? date('d M Y', $ts) : $value;
};

$statusClass = static function (string $status): string {
    switch ($status) {
        case 'active':
            return 'badge badge-soft-success';
        case 'trial':
            return 'badge badge-soft-info';
        case 'pending':
            return 'badge badge-soft-warning text-dark';
        case 'cancelled':
        case 'ended':
            return 'badge badge-soft-danger';
        default:
            return 'badge badge-soft-secondary';
    }
};
?>

<div class="page-wrapper">
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Langganan Klien</h2>
                <p class="text-muted mb-0">Kelola paket/layanan yang diaktifkan untuk klien atau akun pelanggan.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createSubscriptionModal"><i class="ti ti-circle-plus me-2"></i>Tambah Langganan</a>
            </div>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0">Daftar Langganan</h5>
                <form class="d-flex align-items-center flex-wrap gap-2" action="<?php echo htmlspecialchars($listUrl); ?>" method="get">
                    <input type="hidden" name="page" value="1">
                    <div class="input-icon-end position-relative">
                        <input type="search" name="search" class="form-control" placeholder="Cari klien / paket" value="<?php echo htmlspecialchars($search); ?>">
                        <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    </div>
                    <select class="form-select" name="status" onchange="this.form.submit();">
                        <option value="">Semua Status</option>
                        <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?php echo htmlspecialchars($opt); ?>"<?php echo $status === $opt ? ' selected' : ''; ?>><?php echo ucfirst($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select" name="cycle" onchange="this.form.submit();">
                        <option value="">Semua Siklus</option>
                        <?php foreach ($cycleOptions as $opt): ?>
                            <option value="<?php echo htmlspecialchars($opt); ?>"<?php echo $cycle === $opt ? ' selected' : ''; ?>><?php echo ucfirst($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select" name="per_page" onchange="this.form.submit();">
                        <?php foreach ([10,25,50,100] as $opt): ?>
                            <option value="<?php echo $opt; ?>"<?php echo $perPage === $opt ? ' selected' : ''; ?>><?php echo $opt; ?>/hal</option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary d-flex align-items-center"><i class="ti ti-filter me-2"></i>Terapkan</button>
                    <a href="<?php echo htmlspecialchars($listUrl); ?>" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Klien / Akun</th>
                                <th>Paket</th>
                                <th>Siklus</th>
                                <th>Nominal</th>
                                <th>Periode</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($subscriptions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar avatar-lg bg-light-primary avatar-rounded mb-2"><i class="ti ti-packages text-primary fs-20"></i></div>
                                        <h6 class="mb-1">Belum ada langganan</h6>
                                        <p class="text-muted mb-3">Tambah paket untuk klien/pelanggan agar terlihat di dashboard mereka.</p>
                                        <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubscriptionModal"><i class="ti ti-circle-plus me-2"></i>Tambah Langganan</a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscriptions as $sub): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold"><?php echo htmlspecialchars($sub['customer_name'] ?? $sub['client_name'] ?? '—'); ?></span>
                                            <span class="text-muted small"><?php echo htmlspecialchars($sub['customer_email'] ?? $sub['client_email'] ?? '-'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold"><?php echo htmlspecialchars($sub['plan_name']); ?></span>
                                            <?php if (!empty($sub['plan_code'])): ?>
                                                <span class="text-muted small"><?php echo htmlspecialchars($sub['plan_code']); ?></span>
                                            <?php endif; ?>
                                            <span class="<?php echo $statusClass((string)($sub['status'] ?? '')); ?>"><?php echo htmlspecialchars($sub['status'] ?? '-'); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-capitalize"><?php echo htmlspecialchars($sub['cycle'] ?? '-'); ?></td>
                                    <td><?php echo $formatCurrency((float)($sub['amount'] ?? 0), $sub['currency_code'] ?? 'IDR'); ?></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>Mulai: <?php echo $formatDate($sub['started_at'] ?? null); ?></span>
                                            <span>Renewal: <?php echo $formatDate($sub['renewal_at'] ?? null); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <form action="<?php echo htmlspecialchars($storeUrl . '/' . (int)($sub['id'] ?? 0)); ?>" method="post" onsubmit="return confirm('Hapus langganan ini?');">
                                            <input type="hidden" name="_method" value="delete">
                                            <button type="submit" class="btn btn-link text-danger p-0"><i class="ti ti-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <span class="text-muted">Menampilkan <?php echo $total === 0 ? 0 : (($page - 1) * $perPage + 1); ?> - <?php echo min($total, $page * $perPage); ?> dari <?php echo $total; ?> langganan</span>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-white btn-sm<?php echo $page <= 1 ? ' disabled' : ''; ?>" href="<?php echo $page <= 1 ? '#' : htmlspecialchars($listUrl . '?' . http_build_query(array_merge($filters, ['page' => $page - 1]))); ?>"><i class="ti ti-chevron-left"></i></a>
                    <span class="text-muted">Hal <?php echo $page; ?> / <?php echo $totalPages; ?></span>
                    <a class="btn btn-white btn-sm<?php echo $page >= $totalPages ? ' disabled' : ''; ?>" href="<?php echo $page >= $totalPages ? '#' : htmlspecialchars($listUrl . '?' . http_build_query(array_merge($filters, ['page' => $page + 1]))); ?>"><i class="ti ti-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Langganan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo htmlspecialchars($storeUrl); ?>" method="post" class="modal-body" id="createSubscriptionForm">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Pilih Akun Pelanggan (users) <span class="text-danger">*</span></label>
                        <select name="customer_user_id" class="form-select">
                            <option value="">- Pilih akun pelanggan -</option>
                            <?php foreach ($customers as $cust): ?>
                                <option value="<?php echo (int)$cust['id']; ?>"<?php echo (int)($old['customer_user_id'] ?? 0) === (int)$cust['id'] ? ' selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cust['name'] ?? $cust['email']); ?> (<?php echo htmlspecialchars($cust['email'] ?? ''); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['customer_user_id'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['customer_user_id']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="plan_name" class="form-control<?php echo !empty($errors['plan_name']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($old['plan_name'] ?? ''); ?>" placeholder="Paket Data Premium">
                        <?php if (!empty($errors['plan_name'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['plan_name']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode Paket</label>
                        <input type="text" name="plan_code" class="form-control" value="<?php echo htmlspecialchars($old['plan_code'] ?? ''); ?>" placeholder="ANTARA-DATA">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Siklus</label>
                        <select name="cycle" class="form-select<?php echo !empty($errors['cycle']) ? ' is-invalid' : ''; ?>">
                            <?php foreach ($cycleOptions as $opt): ?>
                                <option value="<?php echo htmlspecialchars($opt); ?>"<?php echo ($old['cycle'] ?? 'monthly') === $opt ? ' selected' : ''; ?>><?php echo ucfirst($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['cycle'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['cycle']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nominal</label>
                        <input type="number" min="0" step="1000" name="amount" class="form-control<?php echo !empty($errors['amount']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($old['amount'] ?? '0'); ?>">
                        <?php if (!empty($errors['amount'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['amount']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mata Uang</label>
                        <input type="text" name="currency_code" class="form-control" value="<?php echo htmlspecialchars($old['currency_code'] ?? 'IDR'); ?>" placeholder="IDR">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select<?php echo !empty($errors['status']) ? ' is-invalid' : ''; ?>">
                            <?php foreach ($statusOptions as $opt): ?>
                                <option value="<?php echo htmlspecialchars($opt); ?>"<?php echo ($old['status'] ?? 'active') === $opt ? ' selected' : ''; ?>><?php echo ucfirst($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['status'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mulai</label>
                        <input type="datetime-local" name="started_at" class="form-control" value="<?php echo htmlspecialchars($old['started_at'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Renewal</label>
                        <input type="datetime-local" name="renewal_at" class="form-control" value="<?php echo htmlspecialchars($old['renewal_at'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berakhir</label>
                        <input type="datetime-local" name="ends_at" class="form-control" value="<?php echo htmlspecialchars($old['ends_at'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan khusus"><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="createSubscriptionForm" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../partials/main.php';
