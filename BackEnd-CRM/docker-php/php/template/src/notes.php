<?php ob_start();

if (!function_exists('db') && file_exists(__DIR__ . '/../app/functions.php')) {
    require_once __DIR__ . '/../app/functions.php';
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = (string) ($_SESSION['user_name'] ?? 'Pengguna');
$flashKey = 'notes_flash_message';

$priorityMeta = [
    'high' => ['label' => 'Tinggi', 'class' => 'badge bg-danger-subtle text-danger border border-danger-subtle'],
    'medium' => ['label' => 'Menengah', 'class' => 'badge bg-warning-subtle text-warning border border-warning-subtle'],
    'low' => ['label' => 'Rendah', 'class' => 'badge bg-success-subtle text-success border border-success-subtle'],
];

$tagOptions = [
    'Umum',
    'Operasional',
    'Produk',
    'Layanan',
    'Keuangan',
    'Personal',
];

$view = strtolower(trim((string) ($_GET['view'] ?? 'all')));
if (!in_array($view, ['all', 'important', 'trash'], true)) {
    $view = 'all';
}

$pdo = null;
$dbReady = false;
$dbIssue = null;

if (function_exists('db')) {
    try {
        $pdo = db();
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS crm_notes_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(190) NOT NULL,
                content TEXT NOT NULL,
                tag VARCHAR(120) NULL,
                priority ENUM('high','medium','low') NOT NULL DEFAULT 'medium',
                is_important TINYINT(1) NOT NULL DEFAULT 0,
                is_deleted TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_notes_user_deleted (user_id, is_deleted),
                INDEX idx_notes_user_important (user_id, is_important)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $dbReady = true;
    } catch (Throwable $e) {
        $dbIssue = $e->getMessage();
    }
} else {
    $dbIssue = 'Fungsi database belum tersedia.';
}

$selfPath = strtok($_SERVER['REQUEST_URI'] ?? 'notes.php', '?') ?: 'notes.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dbReady && $userId > 0) {
    $action = strtolower(trim((string) ($_POST['action'] ?? '')));

    try {
        if ($action === 'create') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));
            $tag = trim((string) ($_POST['tag'] ?? ''));
            $priority = strtolower(trim((string) ($_POST['priority'] ?? 'medium')));
            $important = (int) ($_POST['is_important'] ?? 0) === 1 ? 1 : 0;

            if ($title === '' || $content === '') {
                throw new RuntimeException('Judul dan isi catatan wajib diisi.');
            }
            if (strlen($title) > 190) {
                throw new RuntimeException('Judul catatan maksimal 190 karakter.');
            }
            if (!isset($priorityMeta[$priority])) {
                $priority = 'medium';
            }
            if (!in_array($tag, $tagOptions, true)) {
                $tag = 'Internal';
            }

            $stmt = $pdo->prepare(
                'INSERT INTO crm_notes_items (user_id, title, content, tag, priority, is_important, is_deleted) VALUES (:uid, :title, :content, :tag, :priority, :important, 0)'
            );
            $stmt->execute([
                'uid' => $userId,
                'title' => $title,
                'content' => $content,
                'tag' => $tag,
                'priority' => $priority,
                'important' => $important,
            ]);

            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Catatan berhasil ditambahkan.'];
        }

        if ($action === 'toggle_important') {
            $noteId = (int) ($_POST['note_id'] ?? 0);
            $nextImportant = (int) ($_POST['next_important'] ?? 0) === 1 ? 1 : 0;
            if ($noteId <= 0) {
                throw new RuntimeException('ID catatan tidak valid.');
            }

            $stmt = $pdo->prepare('UPDATE crm_notes_items SET is_important = :important WHERE id = :id AND user_id = :uid');
            $stmt->execute([
                'important' => $nextImportant,
                'id' => $noteId,
                'uid' => $userId,
            ]);

            $_SESSION[$flashKey] = [
                'type' => 'success',
                'message' => $nextImportant === 1 ? 'Catatan ditandai penting.' : 'Catatan tidak lagi ditandai penting.',
            ];
        }

        if ($action === 'trash') {
            $noteId = (int) ($_POST['note_id'] ?? 0);
            if ($noteId <= 0) {
                throw new RuntimeException('ID catatan tidak valid.');
            }

            $stmt = $pdo->prepare('UPDATE crm_notes_items SET is_deleted = 1 WHERE id = :id AND user_id = :uid');
            $stmt->execute(['id' => $noteId, 'uid' => $userId]);
            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Catatan dipindahkan ke Trash.'];
        }

        if ($action === 'restore') {
            $noteId = (int) ($_POST['note_id'] ?? 0);
            if ($noteId <= 0) {
                throw new RuntimeException('ID catatan tidak valid.');
            }

            $stmt = $pdo->prepare('UPDATE crm_notes_items SET is_deleted = 0 WHERE id = :id AND user_id = :uid');
            $stmt->execute(['id' => $noteId, 'uid' => $userId]);
            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Catatan berhasil dipulihkan.'];
        }

        if ($action === 'delete_permanent') {
            $noteId = (int) ($_POST['note_id'] ?? 0);
            if ($noteId <= 0) {
                throw new RuntimeException('ID catatan tidak valid.');
            }

            $stmt = $pdo->prepare('DELETE FROM crm_notes_items WHERE id = :id AND user_id = :uid');
            $stmt->execute(['id' => $noteId, 'uid' => $userId]);
            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Catatan dihapus permanen.'];
        }
    } catch (Throwable $e) {
        $_SESSION[$flashKey] = ['type' => 'danger', 'message' => $e->getMessage()];
    }

    $targetView = strtolower(trim((string) ($_POST['redirect_view'] ?? $view)));
    if (!in_array($targetView, ['all', 'important', 'trash'], true)) {
        $targetView = 'all';
    }

    header('Location: ' . strtok($selfPath, '?') . '?view=' . urlencode($targetView), true, 303);
    exit;
}

$flash = $_SESSION[$flashKey] ?? null;
if ($flash !== null) {
    unset($_SESSION[$flashKey]);
}

$counts = ['all' => 0, 'important' => 0, 'trash' => 0];
if ($dbReady && $userId > 0) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM crm_notes_items WHERE user_id = :uid AND is_deleted = 0');
    $stmt->execute(['uid' => $userId]);
    $counts['all'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM crm_notes_items WHERE user_id = :uid AND is_deleted = 0 AND is_important = 1');
    $stmt->execute(['uid' => $userId]);
    $counts['important'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM crm_notes_items WHERE user_id = :uid AND is_deleted = 1');
    $stmt->execute(['uid' => $userId]);
    $counts['trash'] = (int) $stmt->fetchColumn();
}

$notes = [];
if ($dbReady && $userId > 0) {
    $where = 'user_id = :uid';
    if ($view === 'important') {
        $where .= ' AND is_deleted = 0 AND is_important = 1';
    } elseif ($view === 'trash') {
        $where .= ' AND is_deleted = 1';
    } else {
        $where .= ' AND is_deleted = 0';
    }

    $stmt = $pdo->prepare(
        "SELECT id, title, content, tag, priority, is_important, is_deleted, created_at, updated_at
         FROM crm_notes_items
         WHERE {$where}
         ORDER BY is_important DESC, updated_at DESC"
    );
    $stmt->execute(['uid' => $userId]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

$formatDate = static function (?string $date, string $format = 'd M Y H:i'): string {
    if (!$date) {
        return '-';
    }
    $time = strtotime($date);
    return $time ? date($format, $time) : $date;
};

$excerpt = static function (string $text, int $max = 180): string {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if (strlen($text) <= $max) {
        return $text;
    }
    return substr($text, 0, $max - 3) . '...';
};
?>

<div class="page-wrapper">
    <div class="content notes-newsroom pb-4">
        <style>
            .notes-newsroom .bg-danger-subtle { background-color: #fdebec !important; }
            .notes-newsroom .bg-warning-subtle { background-color: #fff6df !important; }
            .notes-newsroom .bg-success-subtle { background-color: #eaf8ef !important; }
            .notes-newsroom .border-danger-subtle { border-color: #f3c6ca !important; }
            .notes-newsroom .border-warning-subtle { border-color: #f0dfaa !important; }
            .notes-newsroom .border-success-subtle { border-color: #bfe5cc !important; }
            .notes-newsroom .card {
                border: 1px solid #e9edf4;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            }
            .notes-newsroom .overview-card {
                border: 0;
                background: linear-gradient(135deg, #0f3c78 0%, #09284f 100%);
                color: #fff;
            }
            .notes-newsroom .filter-link {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                border-radius: 8px;
                color: #223047;
                text-decoration: none;
                border: 1px solid transparent;
            }
            .notes-newsroom .filter-link:hover,
            .notes-newsroom .filter-link.active {
                background-color: #f4f8ff;
                border-color: #d9e3f2;
                color: #102038;
            }
            .notes-newsroom .note-card {
                border: 1px solid #e1e8f3;
                border-top: 3px solid #c5d1e3;
                border-radius: 12px;
                padding: 14px;
                height: 100%;
                background: #fff;
            }
            .notes-newsroom .note-card.note-high { border-top-color: #dc3545; }
            .notes-newsroom .note-card.note-medium { border-top-color: #f59e0b; }
            .notes-newsroom .note-card.note-low { border-top-color: #16a34a; }
            .notes-newsroom .note-content {
                min-height: 68px;
                color: #4b5563;
                margin-bottom: 12px;
            }
            .notes-newsroom .empty-box {
                border: 1px dashed #ced7e5;
                border-radius: 10px;
                text-align: center;
                padding: 24px;
                color: #6c757d;
                background-color: #f8fbff;
            }
        </style>

        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Notes Workspace</h2>
                <p class="text-muted mb-1">Catatan kerja untuk semua pengguna: pelanggan, user, dan tim internal.</p>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard-pelanggan.php"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item">Application</li>
                        <li class="breadcrumb-item active" aria-current="page">Notes</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-light text-dark border">User: <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="badge bg-light text-dark border">View: <?php echo htmlspecialchars(strtoupper($view), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!$dbReady): ?>
            <div class="alert alert-danger mb-3">Notes belum bisa diproses: <?php echo htmlspecialchars((string) $dbIssue, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="card overview-card mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="text-white mb-1">Ruang Catatan Operasional</h4>
                    <p class="mb-0 text-white-50">Simpan poin penting aktivitas, tindak lanjut, dan keputusan kerja secara terstruktur.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge border border-light text-white">Semua: <?php echo $counts['all']; ?></span>
                    <span class="badge border border-light text-white">Penting: <?php echo $counts['important']; ?></span>
                    <span class="badge border border-light text-white">Trash: <?php echo $counts['trash']; ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 d-flex">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="mb-0">Filter Catatan</h5></div>
                    <div class="card-body">
                        <a class="filter-link <?php echo $view === 'all' ? 'active' : ''; ?>" href="notes.php?view=all">
                            <span><i class="ti ti-notes me-2"></i>Semua Catatan</span>
                            <span class="badge bg-light text-dark border"><?php echo $counts['all']; ?></span>
                        </a>
                        <a class="filter-link <?php echo $view === 'important' ? 'active' : ''; ?>" href="notes.php?view=important">
                            <span><i class="ti ti-star me-2"></i>Penting</span>
                            <span class="badge bg-light text-dark border"><?php echo $counts['important']; ?></span>
                        </a>
                        <a class="filter-link <?php echo $view === 'trash' ? 'active' : ''; ?>" href="notes.php?view=trash">
                            <span><i class="ti ti-trash me-2"></i>Trash</span>
                            <span class="badge bg-light text-dark border"><?php echo $counts['trash']; ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Tambah Catatan</h5></div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="redirect_view" value="<?php echo htmlspecialchars($view, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="col-lg-4">
                                <label class="form-label">Judul</label>
                                <input type="text" name="title" class="form-control" maxlength="190" placeholder="Contoh: Ringkasan follow up pembayaran" required>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Prioritas</label>
                                <select name="priority" class="form-select">
                                    <option value="high">Tinggi</option>
                                    <option value="medium" selected>Menengah</option>
                                    <option value="low">Rendah</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">Tag</label>
                                <select name="tag" class="form-select">
                                    <?php foreach ($tagOptions as $tag): ?>
                                        <option value="<?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_important" id="noteImportant">
                                    <label class="form-check-label" for="noteImportant">Tandai penting</label>
                                </div>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="ti ti-plus me-1"></i>Simpan</button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Isi Catatan</label>
                                <textarea name="content" class="form-control" rows="3" maxlength="6000" placeholder="Tulis poin penting, keputusan, atau tindak lanjut yang perlu dipantau." required></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3">
                    <?php if (empty($notes)): ?>
                        <div class="col-12">
                            <div class="empty-box">Belum ada catatan pada filter ini.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                            <?php
                                $priorityKey = strtolower((string) ($note['priority'] ?? 'medium'));
                                if (!isset($priorityMeta[$priorityKey])) {
                                    $priorityKey = 'medium';
                                }
                                $isImportant = (int) ($note['is_important'] ?? 0) === 1;
                                $isDeleted = (int) ($note['is_deleted'] ?? 0) === 1;
                            ?>
                            <div class="col-lg-6 d-flex">
                                <div class="note-card note-<?php echo htmlspecialchars($priorityKey, ENT_QUOTES, 'UTF-8'); ?> w-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="<?php echo $priorityMeta[$priorityKey]['class']; ?>"><?php echo $priorityMeta[$priorityKey]['label']; ?></span>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars((string) ($note['tag'] ?? 'Umum'), ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <?php if ($isImportant): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="ti ti-star-filled me-1"></i>Penting</span>
                                        <?php endif; ?>
                                    </div>

                                    <h6 class="mb-1"><?php echo htmlspecialchars((string) ($note['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h6>
                                    <p class="note-content mb-2"><?php echo htmlspecialchars($excerpt((string) ($note['content'] ?? ''), 220), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-muted fs-12 mb-3">Diperbarui: <?php echo $formatDate((string) ($note['updated_at'] ?? '')); ?></p>

                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if (!$isDeleted): ?>
                                            <form method="post">
                                                <input type="hidden" name="action" value="toggle_important">
                                                <input type="hidden" name="note_id" value="<?php echo (int) ($note['id'] ?? 0); ?>">
                                                <input type="hidden" name="next_important" value="<?php echo $isImportant ? '0' : '1'; ?>">
                                                <input type="hidden" name="redirect_view" value="<?php echo htmlspecialchars($view, ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-warning"><?php echo $isImportant ? 'Lepas Penting' : 'Tandai Penting'; ?></button>
                                            </form>
                                            <form method="post" onsubmit="return confirm('Pindahkan catatan ke Trash?');">
                                                <input type="hidden" name="action" value="trash">
                                                <input type="hidden" name="note_id" value="<?php echo (int) ($note['id'] ?? 0); ?>">
                                                <input type="hidden" name="redirect_view" value="<?php echo htmlspecialchars($view, ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Trash</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post">
                                                <input type="hidden" name="action" value="restore">
                                                <input type="hidden" name="note_id" value="<?php echo (int) ($note['id'] ?? 0); ?>">
                                                <input type="hidden" name="redirect_view" value="trash">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Pulihkan</button>
                                            </form>
                                            <form method="post" onsubmit="return confirm('Hapus permanen catatan ini?');">
                                                <input type="hidden" name="action" value="delete_permanent">
                                                <input type="hidden" name="note_id" value="<?php echo (int) ($note['id'] ?? 0); ?>">
                                                <input type="hidden" name="redirect_view" value="trash">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Permanen</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../partials/footer.php'; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../partials/main.php';
?>
