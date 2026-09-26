<?php ob_start();

if (!function_exists('db') && file_exists(__DIR__ . '/../app/functions.php')) {
    require_once __DIR__ . '/../app/functions.php';
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
$userName = (string) ($_SESSION['user_name'] ?? 'Pengguna');
$flashKey = 'todo_flash_message';

$priorityMeta = [
    'high' => ['label' => 'Tinggi', 'class' => 'badge bg-danger-subtle text-danger border border-danger-subtle'],
    'medium' => ['label' => 'Menengah', 'class' => 'badge bg-warning-subtle text-warning border border-warning-subtle'],
    'low' => ['label' => 'Rendah', 'class' => 'badge bg-success-subtle text-success border border-success-subtle'],
];

$categoryOptions = [
    'Umum',
    'Operasional',
    'Produk',
    'Layanan',
    'Keuangan',
    'Personal',
];

$todayDate = date('Y-m-d');
$pdo = null;
$dbReady = false;
$dbIssue = null;

if (function_exists('db')) {
    try {
        $pdo = db();
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS crm_todo_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(180) NOT NULL,
                description TEXT NULL,
                priority ENUM('high','medium','low') NOT NULL DEFAULT 'medium',
                category VARCHAR(120) NULL,
                due_date DATE NULL,
                status ENUM('pending','completed') NOT NULL DEFAULT 'pending',
                completed_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_todo_user_status (user_id, status),
                INDEX idx_todo_user_due (user_id, due_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $dbReady = true;
    } catch (Throwable $e) {
        $dbIssue = $e->getMessage();
    }
} else {
    $dbIssue = 'Fungsi database belum tersedia.';
}

$selfPath = strtok($_SERVER['REQUEST_URI'] ?? 'todo.php', '?') ?: 'todo.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dbReady && $userId > 0) {
    $action = strtolower(trim((string) ($_POST['action'] ?? '')));

    try {
        if ($action === 'create') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $priority = strtolower(trim((string) ($_POST['priority'] ?? 'medium')));
            $category = trim((string) ($_POST['category'] ?? ''));
            $dueDate = trim((string) ($_POST['due_date'] ?? ''));

            if ($title === '') {
                throw new RuntimeException('Judul tugas wajib diisi.');
            }
            if (strlen($title) > 180) {
                throw new RuntimeException('Judul tugas maksimal 180 karakter.');
            }
            if (!isset($priorityMeta[$priority])) {
                $priority = 'medium';
            }
            if (!in_array($category, $categoryOptions, true)) {
                $category = 'Umum';
            }
            if ($dueDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
                throw new RuntimeException('Format tanggal jatuh tempo tidak valid.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO crm_todo_items (user_id, title, description, priority, category, due_date, status) VALUES (:uid, :title, :description, :priority, :category, :due_date, :status)'
            );
            $stmt->execute([
                'uid' => $userId,
                'title' => $title,
                'description' => $description !== '' ? $description : null,
                'priority' => $priority,
                'category' => $category,
                'due_date' => $dueDate !== '' ? $dueDate : null,
                'status' => 'pending',
            ]);

            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Tugas baru berhasil ditambahkan.'];
        }

        if ($action === 'toggle') {
            $taskId = (int) ($_POST['task_id'] ?? 0);
            $nextStatus = strtolower(trim((string) ($_POST['next_status'] ?? 'pending')));
            $nextStatus = $nextStatus === 'completed' ? 'completed' : 'pending';

            if ($taskId <= 0) {
                throw new RuntimeException('ID tugas tidak valid.');
            }

            $stmt = $pdo->prepare(
                'UPDATE crm_todo_items SET status = :status, completed_at = :completed_at WHERE id = :id AND user_id = :uid'
            );
            $stmt->execute([
                'status' => $nextStatus,
                'completed_at' => $nextStatus === 'completed' ? date('Y-m-d H:i:s') : null,
                'id' => $taskId,
                'uid' => $userId,
            ]);

            $_SESSION[$flashKey] = [
                'type' => 'success',
                'message' => $nextStatus === 'completed' ? 'Tugas ditandai selesai.' : 'Tugas dikembalikan ke daftar aktif.',
            ];
        }

        if ($action === 'delete') {
            $taskId = (int) ($_POST['task_id'] ?? 0);
            if ($taskId <= 0) {
                throw new RuntimeException('ID tugas tidak valid.');
            }

            $stmt = $pdo->prepare('DELETE FROM crm_todo_items WHERE id = :id AND user_id = :uid');
            $stmt->execute(['id' => $taskId, 'uid' => $userId]);
            $_SESSION[$flashKey] = ['type' => 'success', 'message' => 'Tugas berhasil dihapus.'];
        }
    } catch (Throwable $e) {
        $_SESSION[$flashKey] = ['type' => 'danger', 'message' => $e->getMessage()];
    }

    header('Location: ' . $selfPath, true, 303);
    exit;
}

$flash = $_SESSION[$flashKey] ?? null;
if ($flash !== null) {
    unset($_SESSION[$flashKey]);
}

$items = [];
if ($dbReady && $userId > 0) {
    $stmt = $pdo->prepare(
        'SELECT id, title, description, priority, category, due_date, status, completed_at, created_at, updated_at
         FROM crm_todo_items
         WHERE user_id = :uid
         ORDER BY (status = "completed") ASC, CASE WHEN due_date IS NULL THEN 1 ELSE 0 END ASC, due_date ASC, created_at DESC'
    );
    $stmt->execute(['uid' => $userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

$summary = [
    'total' => count($items),
    'pending' => 0,
    'completed' => 0,
    'overdue' => 0,
];
$pendingItems = [];
$completedItems = [];

foreach ($items as $item) {
    $status = strtolower((string) ($item['status'] ?? 'pending'));
    $dueDate = (string) ($item['due_date'] ?? '');

    if ($status === 'completed') {
        $summary['completed']++;
        $completedItems[] = $item;
    } else {
        $summary['pending']++;
        if ($dueDate !== '' && $dueDate < $todayDate) {
            $summary['overdue']++;
        }
        $pendingItems[] = $item;
    }
}

$formatDate = static function (?string $date, string $format = 'd M Y'): string {
    if (!$date) {
        return '-';
    }
    $time = strtotime($date);
    return $time ? date($format, $time) : $date;
};
?>

<div class="page-wrapper">
    <div class="content todo-newsroom">
        <style>
            .todo-newsroom .bg-danger-subtle { background-color: #fdebec !important; }
            .todo-newsroom .bg-warning-subtle { background-color: #fff6df !important; }
            .todo-newsroom .bg-success-subtle { background-color: #eaf8ef !important; }
            .todo-newsroom .border-danger-subtle { border-color: #f3c6ca !important; }
            .todo-newsroom .border-warning-subtle { border-color: #f0dfaa !important; }
            .todo-newsroom .border-success-subtle { border-color: #bfe5cc !important; }
            .todo-newsroom .card {
                border: 1px solid #e9edf4;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            }
            .todo-newsroom .overview-card {
                border: 0;
                background: linear-gradient(135deg, #ca1f27 0%, #8d1419 100%);
                color: #fff;
            }
            .todo-newsroom .stat-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 12px;
                border: 1px solid rgba(255, 255, 255, 0.35);
            }
            .todo-newsroom .task-item {
                border: 1px solid #e4e9f2;
                border-left: 4px solid #c4cddc;
                border-radius: 10px;
                padding: 14px;
                background-color: #fff;
            }
            .todo-newsroom .task-item.task-high { border-left-color: #dc3545; }
            .todo-newsroom .task-item.task-medium { border-left-color: #f59e0b; }
            .todo-newsroom .task-item.task-low { border-left-color: #16a34a; }
            .todo-newsroom .task-item + .task-item { margin-top: 12px; }
            .todo-newsroom .task-meta { font-size: 12px; color: #6c757d; }
            .todo-newsroom .empty-box {
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
                <h2 class="mb-1">To-Do Workspace</h2>
                <p class="text-muted mb-1">Agenda kerja harian untuk semua pengguna: pelanggan, user, dan tim internal.</p>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard-pelanggan.php"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item">Application</li>
                        <li class="breadcrumb-item active" aria-current="page">To-Do</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-light text-dark border">User: <?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="badge bg-light text-dark border">Update: <?php echo date('d M Y H:i'); ?></span>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!$dbReady): ?>
            <div class="alert alert-danger mb-3">To-Do belum bisa diproses: <?php echo htmlspecialchars((string) $dbIssue, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="card overview-card mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="text-white mb-1">Pusat Kendali Tugas Harian</h4>
                    <p class="mb-0 text-white-50">Pantau prioritas, deadline, dan progres tugas dalam satu halaman kerja.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="stat-badge">Total: <?php echo $summary['total']; ?></span>
                    <span class="stat-badge">Pending: <?php echo $summary['pending']; ?></span>
                    <span class="stat-badge">Selesai: <?php echo $summary['completed']; ?></span>
                    <span class="stat-badge">Overdue: <?php echo $summary['overdue']; ?></span>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Tambah Tugas Baru</h5></div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="action" value="create">
                    <div class="col-lg-4">
                        <label class="form-label">Judul Tugas</label>
                        <input type="text" name="title" class="form-control" maxlength="180" placeholder="Contoh: Follow up invoice bulan ini" required>
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
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <?php foreach ($categoryOptions as $category): ?>
                                <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger w-100"><i class="ti ti-plus me-1"></i>Simpan</button>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" maxlength="2000" placeholder="Detail pekerjaan, konteks, atau catatan tambahan."></textarea>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tugas Aktif</h5>
                        <span class="badge bg-light text-dark border"><?php echo count($pendingItems); ?> tugas</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingItems)): ?>
                            <div class="empty-box">Tidak ada tugas aktif. Tambahkan tugas baru untuk mulai mengelola agenda.</div>
                        <?php else: ?>
                            <?php foreach ($pendingItems as $item): ?>
                                <?php
                                    $priorityKey = strtolower((string) ($item['priority'] ?? 'medium'));
                                    if (!isset($priorityMeta[$priorityKey])) {
                                        $priorityKey = 'medium';
                                    }
                                    $isOverdue = !empty($item['due_date']) && $item['due_date'] < $todayDate;
                                ?>
                                <div class="task-item task-<?php echo htmlspecialchars($priorityKey, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h6>
                                            <?php if (!empty($item['description'])): ?>
                                                <p class="mb-2 text-muted"><?php echo nl2br(htmlspecialchars((string) $item['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                <span class="<?php echo $priorityMeta[$priorityKey]['class']; ?>"><?php echo $priorityMeta[$priorityKey]['label']; ?></span>
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars((string) ($item['category'] ?? 'Umum'), ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php if (!empty($item['due_date'])): ?>
                                                    <span class="badge <?php echo $isOverdue ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-dark border'; ?>">
                                                        <i class="ti ti-calendar me-1"></i><?php echo $formatDate((string) $item['due_date']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <form method="post">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="task_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                                <input type="hidden" name="next_status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Selesai</button>
                                            </form>
                                            <form method="post" onsubmit="return confirm('Hapus tugas ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="task_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tugas Selesai</h5>
                        <span class="badge bg-light text-dark border"><?php echo count($completedItems); ?> tugas</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($completedItems)): ?>
                            <div class="empty-box">Belum ada tugas selesai.</div>
                        <?php else: ?>
                            <?php foreach ($completedItems as $item): ?>
                                <?php
                                    $priorityKey = strtolower((string) ($item['priority'] ?? 'medium'));
                                    if (!isset($priorityMeta[$priorityKey])) {
                                        $priorityKey = 'medium';
                                    }
                                ?>
                                <div class="task-item task-<?php echo htmlspecialchars($priorityKey, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <div>
                                            <h6 class="mb-1 text-decoration-line-through"><?php echo htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h6>
                                            <div class="task-meta">
                                                Diselesaikan: <?php echo $formatDate((string) ($item['completed_at'] ?? ''), 'd M Y H:i'); ?>
                                            </div>
                                        </div>
                                        <form method="post">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="task_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                            <input type="hidden" name="next_status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Kembalikan</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
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
