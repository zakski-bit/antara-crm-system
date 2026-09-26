<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/functions.php';

ob_start();

$flashSuccess = null;
$flashError = null;

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$safeUserId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $userId);

$defaultProfile = [
    'name' => '',
    'type' => '',
    'email' => '',
    'phone' => '',
    'mobile' => '',
    'address' => '',
    'province' => '',
    'city' => '',
    'district' => '',
    'subdistrict' => '',
];

$defaultCompany = [
    'name' => '',
    'type' => '',
    'email' => '',
    'phone' => '',
    'mobile' => '',
    'address' => '',
    'province' => '',
    'city' => '',
    'district' => '',
    'subdistrict' => '',
    'categories' => [
        'jasa_perhotelan' => false,
        'kjpp' => false,
        'fnb' => false,
        'jasa_sewa' => false,
        'kontraktor' => false,
        'peralatan_komputer' => false,
        'jasa_transportasi' => false,
        'logistik_atk' => false,
        'foto_video' => false,
        'kap' => false,
        'konsultan' => false,
        'provider_internet' => false,
    ],
];

$defaultPic = [
    'name' => '',
    'role' => '',
    'email' => '',
    'phone' => '',
    'location' => '',
    'note' => '',
    'avatar' => '',
];

$defaultDocuments = [
    'ktp' => ['label' => 'KTP', 'status' => 'missing', 'file' => null],
    'npwp' => ['label' => 'NPWP', 'status' => 'missing', 'file' => null],
    'sertifikasi' => ['label' => 'Sertifikasi', 'status' => 'missing', 'file' => null],
    'pengalaman' => ['label' => 'List Pengalaman Kerja', 'status' => 'missing', 'file' => null],
];

$safe = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$assetsRoot = realpath(dirname(__DIR__, 2) . '/assets') ?: (dirname(__DIR__, 2) . '/assets');
$baseUrlConfig = trim((string) app_config('app.base_url', ''), '/');
$appBaseUrl = $baseUrlConfig === '' ? '' : '/' . $baseUrlConfig;
$publicUploadsBase = ($appBaseUrl === '' ? '' : $appBaseUrl) . '/assets/uploads/my-info';

$ensureTables = static function (): void {
    $pdo = db();
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_profiles (
        user_id INT NOT NULL PRIMARY KEY,
        name VARCHAR(190) DEFAULT '',
        type VARCHAR(120) DEFAULT '',
        email VARCHAR(190) DEFAULT '',
        phone VARCHAR(50) DEFAULT '',
        mobile VARCHAR(50) DEFAULT '',
        address TEXT,
        province VARCHAR(120) DEFAULT '',
        city VARCHAR(120) DEFAULT '',
        district VARCHAR(120) DEFAULT '',
        subdistrict VARCHAR(120) DEFAULT '',
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS company_profiles (
        user_id INT NOT NULL PRIMARY KEY,
        name VARCHAR(190) DEFAULT '',
        type VARCHAR(120) DEFAULT '',
        email VARCHAR(190) DEFAULT '',
        phone VARCHAR(50) DEFAULT '',
        mobile VARCHAR(50) DEFAULT '',
        address TEXT,
        province VARCHAR(120) DEFAULT '',
        city VARCHAR(120) DEFAULT '',
        district VARCHAR(120) DEFAULT '',
        subdistrict VARCHAR(120) DEFAULT '',
        categories_json JSON NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS company_pic (
        user_id INT NOT NULL PRIMARY KEY,
        name VARCHAR(190) DEFAULT '',
        role VARCHAR(190) DEFAULT '',
        email VARCHAR(190) DEFAULT '',
        phone VARCHAR(50) DEFAULT '',
        location VARCHAR(190) DEFAULT '',
        note TEXT,
        avatar_url VARCHAR(255) DEFAULT '',
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS user_documents (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        doc_key VARCHAR(40) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_doc_unique (user_id, doc_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
};

$ensureTables();

$pdo = db();

$myInfo = [
    'profile' => $defaultProfile,
    'company' => $defaultCompany,
    'pic' => $defaultPic,
    'documents' => $defaultDocuments,
];

$ensureUserRows = static function () use ($pdo, $userId, $defaultProfile, $defaultCompany, $defaultPic): void {
    if ($userId <= 0) {
        return;
    }

    $insertIfMissing = static function (string $table, array $data) use ($pdo, $userId): void {
        $existsStmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE user_id = ? LIMIT 1");
        $existsStmt->execute([$userId]);
        if ($existsStmt->fetchColumn()) {
            return;
        }
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        $sql = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    };

    $insertIfMissing('user_profiles', array_merge(['user_id' => $userId], $defaultProfile));
    $companyData = $defaultCompany;
    $companyData['categories_json'] = json_encode($defaultCompany['categories']);
    unset($companyData['categories']);
    $insertIfMissing('company_profiles', array_merge(['user_id' => $userId], $companyData));
    $picPayload = $defaultPic;
    unset($picPayload['avatar']);
    $picPayload['avatar_url'] = $defaultPic['avatar'] ?? '';
    $insertIfMissing('company_pic', array_merge(['user_id' => $userId], $picPayload));
};

$ensureUserRows();

if ($userId > 0) {
    // Profile
    $stmt = $pdo->prepare("SELECT name, type, email, phone, mobile, address, province, city, district, subdistrict FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row) {
        $myInfo['profile'] = array_merge($defaultProfile, $row);
    }

    // Company
    $stmt = $pdo->prepare("SELECT name, type, email, phone, mobile, address, province, city, district, subdistrict, categories_json FROM company_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row) {
        $categories = $defaultCompany['categories'];
        if (!empty($row['categories_json'])) {
            $decoded = json_decode((string) $row['categories_json'], true);
            if (is_array($decoded)) {
                $categories = array_merge($categories, $decoded);
            }
        }
        unset($row['categories_json']);
        $myInfo['company'] = array_merge($defaultCompany, $row);
        $myInfo['company']['categories'] = $categories;
    }

    // PIC
    $stmt = $pdo->prepare("SELECT name, role, email, phone, location, note, avatar_url FROM company_pic WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row) {
        $myInfo['pic'] = array_merge($defaultPic, $row);
        if (!empty($row['avatar_url'])) {
            $myInfo['pic']['avatar'] = $row['avatar_url'];
        }
    }

    // Documents
    $stmt = $pdo->prepare("SELECT doc_key, file_path, original_name, uploaded_at FROM user_documents WHERE user_id = ?");
    $stmt->execute([$userId]);
    $docRows = $stmt->fetchAll();
    if ($docRows) {
        foreach ($docRows as $docRow) {
            $key = $docRow['doc_key'];
            if (!isset($myInfo['documents'][$key])) {
                continue;
            }
            $myInfo['documents'][$key] = [
                'label' => $myInfo['documents'][$key]['label'],
                'status' => 'uploaded',
                'file' => $docRow['file_path'],
                'original' => $docRow['original_name'],
                'uploaded_at' => date('d M Y, H:i', strtotime($docRow['uploaded_at'] ?? 'now')),
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($userId <= 0) {
        $flashError = 'Sesi tidak valid. Silakan login kembali.';
    } elseif ($formType === 'profile') {
        $profileFields = array_keys($defaultProfile);
        foreach ($profileFields as $field) {
            $myInfo['profile'][$field] = trim($_POST[$field] ?? '');
        }
        $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, name, type, email, phone, mobile, address, province, city, district, subdistrict)
            VALUES (:user_id, :name, :type, :email, :phone, :mobile, :address, :province, :city, :district, :subdistrict)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                type = VALUES(type),
                email = VALUES(email),
                phone = VALUES(phone),
                mobile = VALUES(mobile),
                address = VALUES(address),
                province = VALUES(province),
                city = VALUES(city),
                district = VALUES(district),
                subdistrict = VALUES(subdistrict)");
        $stmt->execute(array_merge($myInfo['profile'], ['user_id' => $userId]));
        $flashSuccess = 'Informasi pribadi berhasil diperbarui.';
    } elseif ($formType === 'company') {
        $companyScalarFields = ['name', 'type', 'email', 'phone', 'mobile', 'address', 'province', 'city', 'district', 'subdistrict'];
        foreach ($companyScalarFields as $field) {
            $myInfo['company'][$field] = trim($_POST[$field] ?? '');
        }
        $categoryKeys = array_keys($defaultCompany['categories']);
        $selectedCategories = [];
        foreach ($categoryKeys as $catKey) {
            $selectedCategories[$catKey] = isset($_POST['categories'][$catKey]);
        }
        $myInfo['company']['categories'] = $selectedCategories;
        $stmt = $pdo->prepare("INSERT INTO company_profiles (user_id, name, type, email, phone, mobile, address, province, city, district, subdistrict, categories_json)
            VALUES (:user_id, :name, :type, :email, :phone, :mobile, :address, :province, :city, :district, :subdistrict, :categories_json)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                type = VALUES(type),
                email = VALUES(email),
                phone = VALUES(phone),
                mobile = VALUES(mobile),
                address = VALUES(address),
                province = VALUES(province),
                city = VALUES(city),
                district = VALUES(district),
                subdistrict = VALUES(subdistrict),
                categories_json = VALUES(categories_json)");
        $stmt->execute([
            'user_id' => $userId,
            'name' => $myInfo['company']['name'],
            'type' => $myInfo['company']['type'],
            'email' => $myInfo['company']['email'],
            'phone' => $myInfo['company']['phone'],
            'mobile' => $myInfo['company']['mobile'],
            'address' => $myInfo['company']['address'],
            'province' => $myInfo['company']['province'],
            'city' => $myInfo['company']['city'],
            'district' => $myInfo['company']['district'],
            'subdistrict' => $myInfo['company']['subdistrict'],
            'categories_json' => json_encode($selectedCategories),
        ]);
        $flashSuccess = 'Data perusahaan berhasil diperbarui.';
    } elseif ($formType === 'pic') {
        $picFields = array_keys($defaultPic);
        foreach ($picFields as $field) {
            $myInfo['pic'][$field] = trim($_POST[$field] ?? '');
        }
        $avatarUrl = $myInfo['pic']['avatar'];
        // Upload avatar jika ada
        if (isset($_FILES['avatar_file']) && ($_FILES['avatar_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploadDir = $assetsRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'my-info' . DIRECTORY_SEPARATOR . 'user-' . ($safeUserId === '' ? 'guest' : $safeUserId) . DIRECTORY_SEPARATOR . 'pic';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $originalName = $_FILES['avatar_file']['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = 'pic-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
            $targetFile = $uploadDir . DIRECTORY_SEPARATOR . $safeName . ($extension ? '.' . $extension : '');
            if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], $targetFile)) {
                $relativePic = 'assets/uploads/my-info/user-' . ($safeUserId === '' ? 'guest' : $safeUserId) . '/pic/' . basename($targetFile);
                $avatarUrl = $relativePic;
            } else {
                $flashError = 'Gagal mengunggah foto PIC.';
            }
        }
        $myInfo['pic']['avatar'] = $avatarUrl;
        $stmt = $pdo->prepare("INSERT INTO company_pic (user_id, name, role, email, phone, location, note, avatar_url)
            VALUES (:user_id, :name, :role, :email, :phone, :location, :note, :avatar_url)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                role = VALUES(role),
                email = VALUES(email),
                phone = VALUES(phone),
                location = VALUES(location),
                note = VALUES(note),
                avatar_url = VALUES(avatar_url)");
        $stmt->execute([
            'user_id' => $userId,
            'name' => $myInfo['pic']['name'],
            'role' => $myInfo['pic']['role'],
            'email' => $myInfo['pic']['email'],
            'phone' => $myInfo['pic']['phone'],
            'location' => $myInfo['pic']['location'],
            'note' => $myInfo['pic']['note'],
            'avatar_url' => $avatarUrl,
        ]);
        $flashSuccess = 'Data PIC berhasil diperbarui.';
    } elseif ($formType === 'upload_doc') {
        $docKey = $_POST['doc_key'] ?? '';
        if (!isset($myInfo['documents'][$docKey])) {
            $flashError = 'Dokumen tidak dikenali.';
        } elseif (!isset($_FILES['document_file']) || ($_FILES['document_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $flashError = 'Gagal mengunggah dokumen. Pastikan file dipilih.';
        } else {
            $uploadDir = $assetsRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'my-info' . DIRECTORY_SEPARATOR . 'user-' . ($safeUserId === '' ? 'guest' : $safeUserId);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $originalName = $_FILES['document_file']['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = $docKey . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
            $targetFile = $uploadDir . DIRECTORY_SEPARATOR . $safeName . ($extension ? '.' . $extension : '');
            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $targetFile)) {
                $relativePath = 'assets/uploads/my-info/user-' . ($safeUserId === '' ? 'guest' : $safeUserId) . '/' . basename($targetFile);
                $publicPath = ($appBaseUrl === '' ? '' : $appBaseUrl . '/') . $relativePath;
                $myInfo['documents'][$docKey] = [
                    'label' => $myInfo['documents'][$docKey]['label'],
                    'status' => 'uploaded',
                    'file' => $relativePath,
                    'original' => $originalName,
                    'uploaded_at' => date('d M Y, H:i'),
                ];
                $stmt = $pdo->prepare("INSERT INTO user_documents (user_id, doc_key, file_path, original_name, uploaded_at)
                    VALUES (:user_id, :doc_key, :file_path, :original_name, NOW())
                    ON DUPLICATE KEY UPDATE
                        file_path = VALUES(file_path),
                        original_name = VALUES(original_name),
                        uploaded_at = VALUES(uploaded_at)");
                $stmt->execute([
                    'user_id' => $userId,
                    'doc_key' => $docKey,
                    'file_path' => $relativePath,
                    'original_name' => $originalName,
                ]);
                $flashSuccess = 'Dokumen ' . $myInfo['documents'][$docKey]['label'] . ' berhasil diunggah.';
            } else {
                $flashError = 'Gagal memindahkan file unggahan.';
            }
        }
    }
}

$profileData = $myInfo['profile'];
$companyData = $myInfo['company'];
$picData = $myInfo['pic'];
$documents = $myInfo['documents'];
$allDocumentsUploaded = !empty($documents) && count(array_filter($documents, fn($doc) => ($doc['status'] ?? '') === 'uploaded')) === count($documents);
?>
<style>
    .js-tab-panel { display: none; }
    .js-tab-panel.active { display: block; }
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
                    <h2 class="mb-1">My Info</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Pengaturan
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">My Info</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <?php if ($flashSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $safe($flashSuccess); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $safe($flashError); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs nav-tabs-solid bg-transparent border-bottom mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="my-info.php"><i class="ti ti-user me-2"></i>My Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="security-settings.php"><i class="ti ti-shield-check me-2"></i>Security Settings</a>
                </li>
            </ul>

            <div class="row">
                <div class="col-xl-3 theiaStickySidebar">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column list-group settings-list">
                                <a href="my-info.php" class="d-inline-flex align-items-center rounded active py-2 px-3"><i class="ti ti-arrow-badge-right me-2"></i>My Info</a>
                                <a href="security-settings.php" class="d-inline-flex align-items-center rounded py-2 px-3">Security Settings</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                <div>
                                    <p class="text-uppercase text-muted fs-12 mb-1">Vendor</p>
                                    <h3 class="mb-1"><?php echo $safe($companyData['name']); ?></h3>
                                    <p class="mb-0 text-muted">Data perusahaan klien Berita Antara</p>
                                </div>
                                <div class="text-end">
                                    <?php if ($allDocumentsUploaded): ?>
                                        <span class="badge bg-soft-success text-success px-3 py-2">Terverifikasi</span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-warning text-dark px-3 py-2">Terverifikasi sebagian</span>
                                    <?php endif; ?>
                                    <p class="text-muted fs-12 mb-0 mt-2">Update terakhir: <?php echo date('d M Y, H:i'); ?></p>
                                </div>
                            </div>
                            <ul class="nav nav-pills nav-justified bg-light rounded-2 p-1">
                                <li class="nav-item">
                                    <a class="nav-link active js-tab-link" data-target="#tab-profile" href="javascript:void(0);">Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-tab-link" data-target="#tab-company" href="javascript:void(0);">Data Perusahaan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-tab-link" data-target="#tab-documents" href="javascript:void(0);">Dokumen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-tab-link" data-target="#tab-pic" href="javascript:void(0);">PIC Perusahaan</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="js-tab-panel active" id="tab-profile">
                        <div class="card mb-3" id="profile-info">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Informasi Pribadi</h5>
                            </div>
                            <div class="card-body">
                                <form id="form-profile" method="post" class="row gy-3">
                                    <input type="hidden" name="form_type" value="profile">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $safe($profileData['name']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" name="address" class="form-control" value="<?php echo $safe($profileData['address']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipe</label>
                                        <input type="text" name="type" class="form-control" value="<?php echo $safe($profileData['type']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" name="province" class="form-control" value="<?php echo $safe($profileData['province']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $safe($profileData['email']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kecamatan</label>
                                        <input type="text" name="district" class="form-control" value="<?php echo $safe($profileData['district']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Tlp</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo $safe($profileData['phone']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kota/Kabupaten</label>
                                        <input type="text" name="city" class="form-control" value="<?php echo $safe($profileData['city']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Hp</label>
                                        <input type="text" name="mobile" class="form-control" value="<?php echo $safe($profileData['mobile']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kelurahan</label>
                                        <input type="text" name="subdistrict" class="form-control" value="<?php echo $safe($profileData['subdistrict']); ?>">
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2 mt-2">
                                            <span class="text-muted">Status:</span>
                                            <?php if ($allDocumentsUploaded): ?>
                                                <span class="text-success fw-semibold">Lengkap</span>
                                            <?php else: ?>
                                                <span class="text-danger fw-semibold">Perlu unggah dokumen</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="d-flex justify-content-end px-3 pb-3">
                                <button class="btn btn-primary" type="submit" form="form-profile">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <div class="js-tab-panel" id="tab-company">
                        <div class="card mb-3" id="company-info">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Data Perusahaan</h5>
                            </div>
                            <div class="card-body">
                                <form id="form-company" method="post" class="row gy-3">
                                    <input type="hidden" name="form_type" value="company">
                                    <div class="col-md-4">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $safe($companyData['name']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tipe Perusahaan</label>
                                        <select name="type" class="form-select">
                                            <option value="">Pilih Tipe Perusahaan</option>
                                            <?php
                                            $companyTypes = ['BUMN', 'Swasta', 'BUMD', 'Koperasi', 'CV', 'PT', 'Perorangan'];
                                            foreach ($companyTypes as $type):
                                                $selected = ($companyData['type'] ?? '') === $type ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $safe($type); ?>" <?php echo $selected; ?>><?php echo $safe($type); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Email Perusahaan</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $safe($companyData['email']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Tlp Perusahaan</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo $safe($companyData['phone']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Hp Perusahaan</label>
                                        <input type="text" name="mobile" class="form-control" value="<?php echo $safe($companyData['mobile']); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="address" class="form-control" rows="2"><?php echo $safe($companyData['address']); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" name="province" class="form-control" value="<?php echo $safe($companyData['province']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kota/Kabupaten</label>
                                        <input type="text" name="city" class="form-control" value="<?php echo $safe($companyData['city']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kecamatan</label>
                                        <input type="text" name="district" class="form-control" value="<?php echo $safe($companyData['district']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kelurahan/Desa</label>
                                        <input type="text" name="subdistrict" class="form-control" value="<?php echo $safe($companyData['subdistrict']); ?>">
                                    </div>

                                    <div class="col-12 mt-4 pt-1">
                                        <h5 class="mb-3 text-center">Kategori Perusahaan</h5>
                                        <div class="row">
                                            <?php
                                                $categoryLabels = [
                                                    'jasa_perhotelan' => 'Jasa Perhotelan',
                                                    'kjpp' => 'Kantor Jasa Penilai Publik (KJPP)',
                                                    'fnb' => 'Penyedia Makanan dan Minuman',
                                                    'jasa_sewa' => 'Jasa Sewa',
                                                    'kontraktor' => 'Kontraktor Pelaksana Pembangunan',
                                                    'peralatan_komputer' => 'Peralatan Komputer',
                                                    'jasa_transportasi' => 'Jasa Transportasi',
                                                    'logistik_atk' => 'Logistik dan ATK',
                                                    'foto_video' => 'Perlengkapan Fotografi dan Video',
                                                    'kap' => 'Kantor Akuntan Publik (KAP)',
                                                    'konsultan' => 'Penyedia Jasa Konsultan',
                                                    'provider_internet' => 'Provider Internet & Data Center',
                                                ];
                                                $colCount = 4;
                                                $categories = $companyData['categories'] ?? [];
                                                $i = 0;
                                                foreach ($categoryLabels as $key => $label):
                                                    $checked = !empty($categories[$key]) ? 'checked' : '';
                                            ?>
                                                <div class="col-md-3 col-sm-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="cat-<?php echo $safe($key); ?>" name="categories[<?php echo $safe($key); ?>]" <?php echo $checked; ?>>
                                                        <label class="form-check-label" for="cat-<?php echo $safe($key); ?>"><?php echo $safe($label); ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit" form="form-company">Simpan</button>
                        </div>
                    </div>

                    <div class="js-tab-panel" id="tab-documents">
                        <div class="row" id="documents">
                            <div class="col-lg-12">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h5 class="mb-0">Dokumen</h5>
                                        <?php if ($allDocumentsUploaded): ?>
                                            <span class="badge bg-soft-success text-success px-3 py-2">Lengkap</span>
                                        <?php else: ?>
                                            <span class="badge bg-soft-danger text-danger px-3 py-2">Perlu unggah</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table align-middle mb-0">
                                                <tbody>
                                                <?php foreach ($documents as $key => $doc): ?>
                                                    <tr>
                                                        <td class="w-25 fw-semibold"><?php echo $safe(($doc['label'] ?? 'Dokumen')); ?></td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-2">
                                                                <?php
                                                                    $docUrl = $doc['file'] ?? '';
                                                                    if ($docUrl !== '' && !preg_match('/^https?:\\/\\//', $docUrl)) {
                                                                        $docUrl = '/' . ltrim($docUrl, '/');
                                                                        $basePrefix = $appBaseUrl === '' ? '' : $appBaseUrl;
                                                                        if ($basePrefix !== '' && strpos($docUrl, $basePrefix . '/assets/') !== 0 && strpos($docUrl, '/assets/') === 0) {
                                                                            $docUrl = $basePrefix . $docUrl;
                                                                        }
                                                                        if ($basePrefix !== '' && strpos($docUrl, $basePrefix . $basePrefix) === 0) {
                                                                            $docUrl = $basePrefix . substr($docUrl, strlen($basePrefix) * 2);
                                                                        }
                                                                    }
                                                                    $ext = strtolower(pathinfo($doc['original'] ?? '', PATHINFO_EXTENSION));
                                                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                                                ?>
                                                                <?php if (($doc['status'] ?? '') === 'uploaded' && !empty($doc['file'])): ?>
                                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                            <?php if ($isImage): ?>
                                                                                <div class="border rounded" style="width: 120px; height: 80px; overflow: hidden;">
                                                                                    <img src="<?php echo $safe($docUrl); ?>" alt="preview" style="width:100%; height:100%; object-fit: cover;">
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div>
                                                                                <a href="<?php echo $safe($docUrl); ?>" class="fw-semibold" target="_blank" rel="noopener">
                                                                                    <?php echo $safe($doc['original'] ?? basename((string) $doc['file'])); ?>
                                                                                </a>
                                                                                <p class="text-muted fs-12 mb-0">Diupload: <?php echo $safe($doc['uploaded_at'] ?? ''); ?></p>
                                                                            </div>
                                                                        </div>
                                                                        <span class="badge bg-soft-success text-success">Terverifikasi</span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <form method="post" enctype="multipart/form-data" class="d-flex align-items-center flex-wrap gap-2">
                                                                    <input type="hidden" name="form_type" value="upload_doc">
                                                                    <input type="hidden" name="doc_key" value="<?php echo $safe($key); ?>">
                                                                    <div class="d-inline-flex align-items-center border rounded px-2 py-1 bg-white flex-grow-1" style="min-width: 220px;">
                                                                        <input type="file" name="document_file" class="form-control form-control-sm border-0 p-0" accept=".pdf,.jpg,.jpeg,.png">
                                                                    </div>
                                                                    <button class="btn btn-sm btn-primary" type="submit">Upload</button>
                                                                    <span class="text-muted fs-12">Maks. 10 Mb</span>
                                                                </form>
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
                        </div>
                    </div>

                    <div class="js-tab-panel" id="tab-pic">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase text-muted fs-12 mb-1">PIC Perusahaan</p>
                                    <h5 class="mb-0"><?php echo $safe($picData['name']); ?></h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="form-pic" method="post" enctype="multipart/form-data" class="row gy-3">
                                    <input type="hidden" name="form_type" value="pic">
                                    <div class="col-md-4">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $safe($picData['name']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" name="role" class="form-control" value="<?php echo $safe($picData['role']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo $safe($picData['email']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">No. Hp</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo $safe($picData['phone']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" name="location" class="form-control" value="<?php echo $safe($picData['location']); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Catatan PIC</label>
                                        <textarea name="note" class="form-control" rows="3"><?php echo $safe($picData['note']); ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Foto Profil (Upload)</label>
                                        <input type="file" name="avatar_file" class="form-control mb-2" accept=".jpg,.jpeg,.png,.gif,.webp">
                                        <p class="text-muted fs-12 mb-0 mt-1">Unggah foto dari komputer Anda.</p>
                                    </div>
                                </form>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="avatar avatar-lg me-3">
                                        <img src="<?php echo $safe($picData['avatar']); ?>" class="rounded-circle" alt="PIC">
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?php echo $safe($picData['name']); ?></h6>
                                        <span class="badge bg-soft-primary text-primary"><?php echo $safe($picData['role']); ?></span>
                                        <div class="mt-2 text-muted">
                                            <div><i class="ti ti-mail me-1"></i><?php echo $safe($picData['email']); ?></div>
                                            <div><i class="ti ti-phone me-1"></i><?php echo $safe($picData['phone']); ?></div>
                                            <div><i class="ti ti-building me-1"></i><?php echo $safe($picData['location']); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-muted fs-12 mb-1">Catatan PIC</p>
                                <p class="mb-0"><?php echo $safe($picData['note']); ?></p>
                                <div class="d-flex justify-content-end pt-3">
                                    <button class="btn btn-primary" type="submit" form="form-pic">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Content -->   

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var links = document.querySelectorAll('.js-tab-link');
                var panels = document.querySelectorAll('.js-tab-panel');

                function setActive(target) {
                    if (!target) {
                        return;
                    }
                    links.forEach(function (link) {
                        var same = link.getAttribute('data-target') === target;
                        link.classList.toggle('active', same);
                    });
                    panels.forEach(function (panel) {
                        var isMatch = ('#' + panel.id) === target || panel.id === target.replace('#', '');
                        panel.classList.toggle('active', isMatch);
                    });
                }

                links.forEach(function (link) {
                    link.addEventListener('click', function () {
                        setActive(link.getAttribute('data-target'));
                    });
                });

                var initial = document.querySelector('.js-tab-link.active');
                if (initial && initial.getAttribute('data-target')) {
                    setActive(initial.getAttribute('data-target'));
                } else if (links.length) {
                    setActive(links[0].getAttribute('data-target'));
                }
            });
        </script>

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>   
