<?php
/** @var bool $isEdit */
/** @var array|null $partner */
/** @var array $errors */
/** @var array $old */
/** @var array $flash */
/** @var array $statusOptions */
/** @var string $baseUrl */

$isEdit = $isEdit ?? false;
$partner = $partner ?? null;
$errors = $errors ?? [];
$old = $old ?? [];
$flash = $flash ?? [];
$baseUrl = $baseUrl ?? '';

$successMessage = $flash['success'] ?? null;
$errorMessage = $flash['error'] ?? null;

$formAction = $isEdit && $partner ? $baseUrl . '/crm/mitra/' . $partner['id'] : $baseUrl . '/crm/mitra';
$methodField = $isEdit ? '<input type="hidden" name="_method" value="PUT">' : '';

$backUrl = $baseUrl . '/crm/mitra';

function field_value(array $old, ?array $partner, string $key, bool $isEdit): string
{
    if (array_key_exists($key, $old) && $old[$key] !== '') {
        return (string)$old[$key];
    }

    if ($isEdit && $partner && isset($partner[$key]) && $partner[$key] !== null) {
        return (string)$partner[$key];
    }

    return '';
}

$_SERVER['APP_BASE_URL'] = $baseUrl ?? '';
$_SERVER['PHP_SELF'] = '/companies-grid.php';

ob_start();
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
                    <h2 class="mb-1"><?php echo $isEdit ? 'Edit Mitra' : 'Tambah Mitra'; ?></h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($baseUrl . '/'); ?>"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($baseUrl . '/crm/mitra'); ?>">CRM</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $isEdit ? 'Edit Mitra' : 'Tambah Mitra'; ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-outline-primary d-flex align-items-center"><i class="ti ti-arrow-left me-2"></i>Kembali ke daftar</a>
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
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?php echo $isEdit ? 'Informasi Mitra' : 'Form Mitra Baru'; ?></h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo $methodField; ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control <?php echo isset($errors['nama']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(field_value($old, $partner, 'nama', $isEdit)); ?>" placeholder="Contoh: Bloomberg">
                                        <?php if (isset($errors['nama'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['nama']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Industri / Segmen <span class="text-danger">*</span></label>
                                        <input type="text" name="industri" class="form-control <?php echo isset($errors['industri']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(field_value($old, $partner, 'industri', $isEdit)); ?>" placeholder="Contoh: Media & Data Finansial">
                                        <?php if (isset($errors['industri'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['industri']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Mitra <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>">
                                            <option value="">Pilih status</option>
                                            <?php foreach ($statusOptions as $option): ?>
                                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo field_value($old, $partner, 'status', $isEdit) === $option ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($option); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($errors['status'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kontak Utama</label>
                                        <input type="text" name="kontak" class="form-control" value="<?php echo htmlspecialchars(field_value($old, $partner, 'kontak', $isEdit)); ?>" placeholder="Nama PIC / kontak partner">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(field_value($old, $partner, 'email', $isEdit)); ?>" placeholder="contoh@domain.com">
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Telepon <span class="text-danger">*</span></label>
                                        <input type="text" name="telepon" class="form-control <?php echo isset($errors['telepon']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars(field_value($old, $partner, 'telepon', $isEdit)); ?>" placeholder="+62 ...">
                                        <?php if (isset($errors['telepon'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['telepon']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="alamat" rows="3" class="form-control <?php echo isset($errors['alamat']) ? 'is-invalid' : ''; ?>" placeholder="Alamat kantor pusat atau cabang utama"><?php echo htmlspecialchars(field_value($old, $partner, 'alamat', $isEdit)); ?></textarea>
                                        <?php if (isset($errors['alamat'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['alamat']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Path Logo (opsional)</label>
                                        <input type="text" name="logo_path" class="form-control" value="<?php echo htmlspecialchars(field_value($old, $partner, 'logo_path', $isEdit)); ?>" placeholder="assets/img/mitra/logo-nama.png">
                                        <span class="form-text">Isi jika logo sudah tersedia di folder assets. Jika ingin unggah file baru, gunakan opsi di bawah.</span>
                                        <?php if (isset($errors['logo'])): ?>
                                            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['logo']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Unggah Logo (opsional)</label>
                                        <input type="file" name="logo" class="form-control <?php echo isset($errors['logo']) ? 'is-invalid' : ''; ?>" accept=".png,.jpg,.jpeg,.svg,.webp">
                                        <span class="form-text">Format yang didukung: PNG, JPG, JPEG, SVG, WEBP.</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-end border-top mt-4 pt-3">
                                    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-light me-2">Batal</a>
                                    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Simpan Perubahan' : 'Simpan Mitra'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
<?php if ($isEdit && $partner): ?>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Pratinjau Logo</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="partner-logo-wrapper mx-auto mb-3" style="max-width: 220px;">
                                <img src="<?php echo htmlspecialchars($partner['logo'] ?: 'assets/img/logo.png'); ?>" alt="Logo <?php echo htmlspecialchars($partner['nama']); ?>">
                            </div>
                            <p class="text-muted mb-0">Logo saat ini akan tetap digunakan jika Anda tidak mengganti file atau path logo.</p>
                        </div>
                    </div>
                </div>
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
