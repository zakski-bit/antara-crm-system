<?php ob_start(); ?>

<?php
/**
 * Standalone grid page for Mitra & Korporasi (template preview mode).
 * Rehydrates the same data used by the router-based view so modal editing still works.
 */

$_SERVER['PHP_SELF'] = 'companies-grid.php';

$rootPath = dirname(__DIR__, 2);
$composerAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function (string $class) use ($rootPath): void {
        $prefix = 'App\\';
        $baseDir = $rootPath . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

\App\Core\Config::loadFromDirectory($rootPath . '/app/Config');

$statusOptions = [
    'Strategic Alliance',
    'Editorial Partner',
    'Media Distribution',
    'Media Partner',
    'Commercial Partner',
    'Network Member',
    'Technology Partner',
    'Security Partner',
    'Government Relations',
];

$baseUrlConfig = trim(\App\Core\Config::get('app.base_url', ''), '/');
$assetBaseUrl = ($baseUrlConfig === '' ? '' : '/' . $baseUrlConfig) . '/';

$resolveLogoUrl = static function (?string $path) use ($assetBaseUrl): string {
    $path = trim((string) $path);
    if ($path === '') {
        $path = 'assets/img/logo.png';
    }

    if (preg_match('#^(https?:)?//#i', $path)) {
        return $path;
    }

    $path = ltrim($path, '/');
    if (stripos($path, 'template/') === 0) {
        $path = substr($path, 9);
    }

    return $assetBaseUrl . $path;
};

$defaultLogoUrl = $resolveLogoUrl('assets/img/logo.png');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alertSuccess = $_SESSION['flash']['success'] ?? null;
$alertError = $_SESSION['flash']['error'] ?? null;
unset($_SESSION['flash']);

$partners = [];
$dbError = null;

try {
    $repository = new \App\Repositories\MitraRepository();
    $partners = $repository->all();
} catch (\Throwable $e) {
    $dbError = $e->getMessage();
}

$formTarget = 'companies-grid.php';
$gridUrl = 'companies-grid.php';
$tableUrl = 'companies-crm.php';
$createUrl = 'companies-crm.php';

?>

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Mitra &amp; Korporasi</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">CRM</li>
                            <li class="breadcrumb-item active" aria-current="page">Grid Mitra</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="d-flex align-items-center border bg-white rounded p-1 me-2 icon-list">
                            <a href="<?php echo htmlspecialchars($tableUrl); ?>" class="btn btn-icon btn-sm me-1">
                                <i class="ti ti-list-tree"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($gridUrl); ?>" class="btn btn-icon btn-sm active bg-primary text-white">
                                <i class="ti ti-layout-grid"></i>
                            </a>
                        </div>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                            <i class="ti ti-circle-plus me-2"></i>Tambah Mitra
                        </button>
                    </div>
                </div>
            </div>

            <?php if ($alertSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($alertSuccess); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($alertError || $dbError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($alertError ?? $dbError ?? 'Terjadi kesalahan.'); ?>
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
                                <a href="<?php echo htmlspecialchars($createUrl); ?>" class="btn btn-primary">
                                    <i class="ti ti-circle-plus me-2"></i>Tambah Mitra
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($partners as $item): ?>
                        <?php
                            $status = $item['status'] ?? '';
                            $statusClass = 'bg-secondary-transparent';
                            if (stripos($status, 'strategic') !== false || stripos($status, 'security') !== false || stripos($status, 'technology') !== false) {
                                $statusClass = 'bg-success-transparent';
                            } elseif (stripos($status, 'media') !== false || stripos($status, 'network') !== false || stripos($status, 'editorial') !== false) {
                                $statusClass = 'bg-info-transparent';
                            }

                            $logoPath = $item['logo_path'] ?? ($item['logo'] ?? '');
                            $logoUrl = $resolveLogoUrl($logoPath);

                            $dataAttrs = [
                                'data-partner-id' => (int)($item['id'] ?? 0),
                                'data-partner-nama' => $item['nama'] ?? '',
                                'data-partner-industri' => $item['industri'] ?? '',
                                'data-partner-kontak' => $item['kontak'] ?? '',
                                'data-partner-email' => $item['email'] ?? '',
                                'data-partner-telepon' => $item['telepon'] ?? '',
                                'data-partner-status' => $status,
                                'data-partner-alamat' => $item['alamat'] ?? '',
                                'data-partner-logo-path' => $logoPath,
                                'data-partner-logo-url' => $logoUrl,
                            ];
                            $attrString = '';
                            foreach ($dataAttrs as $attr => $value) {
                                $attrString .= sprintf(' %s="%s"', $attr, htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));
                            }
                            $confirmMessage = 'Hapus mitra ' . ($item['nama'] ?? '') . '?';
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
                                                <li>
                                                    <button type="button" class="dropdown-item rounded-1 d-flex align-items-center btn-edit-partner" data-bs-toggle="modal" data-bs-target="#editPartnerModal"<?php echo $attrString; ?>>
                                                        <i class="ti ti-edit me-1"></i>Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <form method="POST" action="<?php echo htmlspecialchars($formTarget); ?>" onsubmit="return confirm('<?php echo htmlspecialchars($confirmMessage, ENT_QUOTES, 'UTF-8'); ?>');">
                                                        <input type="hidden" name="form_action" value="delete">
                                                        <input type="hidden" name="partner_id" value="<?php echo (int)($item['id'] ?? 0); ?>">
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
                                            <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo <?php echo htmlspecialchars($item['nama'] ?? ''); ?>">
                                        </div>
                                        <h6 class="mb-1">
                                            <a href="javascript:void(0);" class="text-decoration-none btn-edit-partner" data-bs-toggle="modal" data-bs-target="#editPartnerModal"<?php echo $attrString; ?>>
                                                <?php echo htmlspecialchars($item['nama'] ?? ''); ?>
                                            </a>
                                        </h6>
                                        <span class="badge <?php echo htmlspecialchars($statusClass); ?> fs-10 fw-medium">
                                            <?php echo htmlspecialchars($item['industri'] ?? ''); ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <p class="text-dark d-inline-flex align-items-center mb-2">
                                            <i class="ti ti-user text-gray-5 me-2"></i>
                                            <?php echo htmlspecialchars($item['kontak'] ?: '-'); ?>
                                        </p>
                                        <p class="text-dark d-inline-flex align-items-center mb-2">
                                            <i class="ti ti-mail-forward text-gray-5 me-2"></i>
                                            <?php echo htmlspecialchars($item['email'] ?? ''); ?>
                                        </p>
                                        <p class="text-dark d-inline-flex align-items-center mb-2">
                                            <i class="ti ti-phone text-gray-5 me-2"></i>
                                            <?php echo htmlspecialchars($item['telepon'] ?? ''); ?>
                                        </p>
                                        <p class="text-dark d-inline-flex align-items-center mb-0">
                                            <i class="ti ti-map-pin text-gray-5 me-2"></i>
                                            <?php echo htmlspecialchars($item['alamat'] ?? ''); ?>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                                        <span class="badge bg-secondary-transparent"><?php echo htmlspecialchars($status); ?></span>
                                        <a href="javascript:void(0);" class="link-default fs-12 btn-edit-partner" data-bs-toggle="modal" data-bs-target="#editPartnerModal"<?php echo $attrString; ?>>
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php
            $modalStatusOptions = $statusOptions;
            include __DIR__ . '/../partials/mitra-modals.php';
        ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var defaultLogoSrc = "<?php echo htmlspecialchars($defaultLogoUrl, ENT_QUOTES, 'UTF-8'); ?>";

                var editModal = document.getElementById('editPartnerModal');
                if (!editModal) {
                    return;
                }

                var editPreviewImg = editModal.querySelector('img[data-logo-preview="edit"]');
                var editFileInput = editModal.querySelector('input[name="logo_file"]');
                var currentLogoTextElement = editModal.querySelector('[data-current-logo]');

                if (editPreviewImg) {
                    editPreviewImg.dataset.defaultSrc = defaultLogoSrc;
                    editPreviewImg.src = defaultLogoSrc;
                }

                if (editFileInput && editPreviewImg) {
                    editFileInput.addEventListener('change', function () {
                        if (editFileInput.files && editFileInput.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function (event) {
                                editPreviewImg.src = event.target.result;
                            };
                            reader.readAsDataURL(editFileInput.files[0]);
                        } else {
                            editPreviewImg.src = editPreviewImg.dataset.defaultSrc || defaultLogoSrc;
                        }
                    });
                }

                editModal.addEventListener('show.bs.modal', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }

                    var readAttr = function (suffix) {
                        return trigger.getAttribute('data-partner-' + suffix) || '';
                    };

                    if (editFileInput) {
                        editFileInput.value = '';
                    }

                    editModal.querySelector('input[name="partner_id"]').value = readAttr('id');
                    editModal.querySelector('input[name="nama"]').value = readAttr('nama');
                    editModal.querySelector('input[name="industri"]').value = readAttr('industri');
                    editModal.querySelector('input[name="kontak"]').value = readAttr('kontak');
                    editModal.querySelector('input[name="email"]').value = readAttr('email');
                    editModal.querySelector('input[name="telepon"]').value = readAttr('telepon');
                    editModal.querySelector('select[name="status"]').value = readAttr('status');
                    editModal.querySelector('textarea[name="alamat"]').value = readAttr('alamat');

                    var currentLogoPath = readAttr('logo-path');
                    var currentLogoUrl = readAttr('logo-url') || defaultLogoSrc;

                    var logoPathInput = editModal.querySelector('input[name="logo_path"]');
                    if (logoPathInput) {
                        logoPathInput.value = currentLogoPath;
                    }

                    if (editPreviewImg) {
                        editPreviewImg.src = currentLogoUrl;
                        editPreviewImg.dataset.defaultSrc = currentLogoUrl;
                    }

                    if (currentLogoTextElement) {
                        currentLogoTextElement.textContent = currentLogoPath !== '' ? currentLogoPath : 'Belum ada logo tersimpan.';
                    }
                });

                editModal.addEventListener('hidden.bs.modal', function () {
                    var form = editModal.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                    if (editPreviewImg) {
                        editPreviewImg.src = editPreviewImg.dataset.defaultSrc || defaultLogoSrc;
                    }
                    if (currentLogoTextElement) {
                        currentLogoTextElement.textContent = 'Belum ada logo tersimpan.';
                    }
                });
            });
        </script>

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>
    </div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../partials/main.php';
?>
