<?php

use App\Repositories\ActivityRepository;

$activityRepository = new ActivityRepository();
$flashKey = 'activity_flash';
$flashBag = $_SESSION[$flashKey] ?? ['success' => null, 'errors' => []];
unset($_SESSION[$flashKey]);

$activityTypes = [
    'meeting' => [
        'label' => 'Rapat',
        'badge' => 'badge badge-pink-transparent',
        'icon' => 'ti ti-device-computer-camera',
    ],
    'calls' => [
        'label' => 'Telepon',
        'badge' => 'badge badge-purple-transparent',
        'icon' => 'ti ti-phone',
    ],
    'email' => [
        'label' => 'Email',
        'badge' => 'badge badge-warning-transparent',
        'icon' => 'ti ti-mail',
    ],
    'task' => [
        'label' => 'Tugas',
        'badge' => 'badge badge-success-transparent',
        'icon' => 'ti ti-checklist',
    ],
];

$seedActivities = static function () use ($activityRepository): void {
    if ($activityRepository->count() > 0) {
        return;
    }

    $defaults = [
        [
            'title' => 'Briefing liputan bersama Dishub DKI',
            'activity_type' => 'meeting',
            'due_date' => date('Y-m-d', strtotime('+1 day')),
            'due_time' => '09:00:00',
            'owner' => 'Farah Laksmi',
            'notes' => 'Koordinasikan rundown liputan pagi dan daftar narasumber Dishub.',
        ],
        [
            'title' => 'Kirim proposal paket live streaming BTN',
            'activity_type' => 'email',
            'due_date' => date('Y-m-d', strtotime('+2 days')),
            'due_time' => '13:00:00',
            'owner' => 'Theresia S',
            'notes' => 'Lampirkan rate card terbaru dan contoh tayangan.',
        ],
        [
            'title' => 'Follow up sponsorship Telkomsel',
            'activity_type' => 'calls',
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'due_time' => '15:30:00',
            'owner' => 'Rudi Hartono',
            'notes' => 'Tanyakan kepastian jadwal negosiasi minggu depan.',
        ],
    ];

    foreach ($defaults as $activity) {
        $activityRepository->create($activity);
    }
};

$seedActivities();

$redirectToSelf = static function (): void {
    header('Location: activity.php');
    exit;
};

$parseDate = static function (string $value): ?DateTime {
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $acceptedFormats = ['Y-m-d', 'd/m/Y', 'd-m-Y'];

    foreach ($acceptedFormats as $format) {
        $date = DateTime::createFromFormat($format, $value);
        if ($date instanceof DateTime) {
            $errors = DateTime::getLastErrors();
            if (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
                return $date;
            }
        }
    }

    try {
        return new DateTime($value);
    } catch (Exception $exception) {
        return null;
    }
};

$parseTime = static function (string $value): ?string {
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $formats = ['H:i', 'H.i', 'g:i A', 'g:i a', 'g.i A', 'g.i a'];

    foreach ($formats as $format) {
        $time = DateTime::createFromFormat($format, $value);
        if ($time instanceof DateTime) {
            $errors = DateTime::getLastErrors();
            if (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
                return $time->format('H:i:s');
            }
        }
    }

    try {
        return (new DateTime($value))->format('H:i:s');
    } catch (Exception $exception) {
        return null;
    }
};

$extractInput = static function (string $key, bool $trim = true): string {
    $value = $_POST[$key] ?? '';
    return $trim ? trim((string)$value) : (string)$value;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? '';
    $errors = [];
    $successMessage = null;

    try {
        if ($action === 'create' || $action === 'update') {
            $title = $extractInput('title');
            $type = $extractInput('activity_type');
            $dueDateInput = $extractInput('due_date');
            $dueTimeInput = $extractInput('due_time');
            $owner = $extractInput('owner');
            $notes = $extractInput('notes', false);

            if ($title === '') {
                $errors[] = 'Judul aktivitas wajib diisi.';
            }
            if (!array_key_exists($type, $activityTypes)) {
                $errors[] = 'Jenis aktivitas tidak valid.';
            }
            $dueDate = $parseDate($dueDateInput);
            if (!$dueDate) {
                $errors[] = 'Tanggal jatuh tempo tidak valid.';
            }

            $dueTime = null;
            if ($dueTimeInput !== '') {
                $dueTime = $parseTime($dueTimeInput);
                if ($dueTime === null) {
                    $errors[] = 'Format waktu tidak valid.';
                }
            }

            if ($owner === '') {
                $errors[] = 'Penanggung jawab wajib diisi.';
            }

            if ($action === 'update') {
                $activityId = (int)($_POST['activity_id'] ?? 0);
                if ($activityId <= 0) {
                    $errors[] = 'Aktivitas tidak ditemukan.';
                } elseif (!$activityRepository->find($activityId)) {
                    $errors[] = 'Aktivitas tidak tersedia atau sudah dihapus.';
                }
            }

            if (empty($errors) && $dueDate instanceof DateTime) {
                $payload = [
                    'title' => $title,
                    'activity_type' => $type,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'due_time' => $dueTime,
                    'owner' => $owner,
                    'notes' => $notes !== '' ? $notes : null,
                ];

                if ($action === 'create') {
                    $activityRepository->create($payload);
                    $successMessage = 'Aktivitas baru berhasil ditambahkan.';
                } else {
                    $activityId = (int)($_POST['activity_id'] ?? 0);
                    $activityRepository->update($activityId, $payload);
                    $successMessage = 'Aktivitas berhasil diperbarui.';
                }
            }
        } elseif ($action === 'delete') {
            $activityId = (int)($_POST['activity_id'] ?? 0);
            if ($activityId <= 0) {
                $errors[] = 'Aktivitas tidak valid.';
            } elseif (!$activityRepository->find($activityId)) {
                $errors[] = 'Aktivitas tidak ditemukan atau sudah dihapus.';
            } else {
                $activityRepository->delete($activityId);
                $successMessage = 'Aktivitas berhasil dihapus.';
            }
        } else {
            $errors[] = 'Aksi tidak dikenal.';
        }
    } catch (Exception $exception) {
        $errors[] = 'Terjadi kesalahan pada server. Silakan coba kembali.';
    }

    $_SESSION[$flashKey] = [
        'success' => empty($errors) ? $successMessage : null,
        'errors' => $errors,
    ];

    $redirectToSelf();
}

 $rangeInput = isset($_GET['range']) ? trim((string)($_GET['range'])) : '';
 $rangeStart = $rangeEnd = null;
 if ($rangeInput !== '') {
     $parts = preg_split('/\s*-\s*/', $rangeInput);
     if (count($parts) === 2) {
         $rangeStart = $parseDate($parts[0]);
         $rangeEnd = $parseDate($parts[1]);
     }
 }

 $typeFilter = $_GET['type'] ?? '';
 if (!array_key_exists($typeFilter, $activityTypes)) {
     $typeFilter = '';
 }

 $sortOptions = [
     'recent' => 'Urut : Terbaru',
     'oldest' => 'Urut : Terlama',
     'title_asc' => 'Judul A-Z',
     'title_desc' => 'Judul Z-A',
 ];

 $sortFilter = $_GET['sort'] ?? 'recent';
 if (!array_key_exists($sortFilter, $sortOptions)) {
     $sortFilter = 'recent';
 }

 $activities = $activityRepository->filtered([
     'start_date' => $rangeStart ? $rangeStart->format('Y-m-d') : null,
     'end_date' => $rangeEnd ? $rangeEnd->format('Y-m-d') : null,
     'type' => $typeFilter,
     'sort' => $sortFilter,
 ]);

$formatDate = static function (?string $value, string $format = 'd M Y'): string {
    if (!$value) {
        return '-';
    }
    try {
        return (new DateTime($value))->format($format);
    } catch (Exception $exception) {
        return '-';
    }
};

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
                    <h2 class="mb-1">Aktivitas Relasi</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                CRM
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Aktivitas</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#add_activity" class="btn btn-primary d-flex align-items-center"><i class="ti ti-circle-plus me-2"></i>Tambah Aktivitas</a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <?php if (!empty($flashBag['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flashBag['success'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashBag['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($flashBag['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Activity List -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <h5>Daftar Aktivitas</h5>
                    <form id="activity-filter-form" method="get" class="d-flex align-items-center flex-wrap row-gap-3">
                        <div class="me-3">
                            <div class="input-icon-end position-relative">
                                <input type="text" class="form-control date-range bookingrange" name="range" value="<?= htmlspecialchars($rangeInput, ENT_QUOTES, 'UTF-8'); ?>" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                                <span class="input-icon-addon">
                                    <i class="ti ti-chevron-down"></i>
                                </span>
                            </div>
                        </div>
                        <div class="me-3">
                            <select class="form-select" name="type" onchange="this.form.submit()">
                                <option value=""><?= htmlspecialchars('Semua Jenis', ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php foreach ($activityTypes as $key => $type): ?>
                                    <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?= $typeFilter === $key ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($type['label'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="me-3">
                            <select class="form-select" name="sort" onchange="this.form.submit()">
                                <?php foreach ($sortOptions as $key => $label): ?>
                                    <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?= $sortFilter === $key ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-primary me-2">Terapkan</button>
                            <?php if (!empty($_GET)): ?>
                                <a href="activity.php" class="btn btn-light">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="custom-datatable-filter table-responsive">
                        <table id="activity-table" class="table datatable datatable-export">
                            <thead class="thead-light">
                                <tr>
                                    <th>Aktivitas</th>
                                    <th>Jenis</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Dibuat</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activities)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada aktivitas yang tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                <?php foreach ($activities as $activity): ?>
                                    <?php
                                        $typeKey = $activity['activity_type'] ?? 'meeting';
                                        $typeMeta = $activityTypes[$typeKey] ?? $activityTypes['meeting'];
                                        $dueDateLabel = $formatDate($activity['due_date']);
                                        $dueTimeLabel = $activity['due_time'] ? $formatDate($activity['due_date'] . ' ' . $activity['due_time'], 'H:i') : '';
                                        $createdLabel = $formatDate($activity['created_at']);
                                        $notes = $activity['notes'] ?? '';
                                    ?>
                                    <tr>
                                        <td>
                                            <p class="fs-14 text-dark fw-medium mb-1"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php if ($notes): ?>
                                                <small class="text-muted"><?= htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= $typeMeta['badge']; ?> d-inline-flex align-items-center">
                                                <i class="<?= $typeMeta['icon']; ?> me-1"></i>
                                                <?= htmlspecialchars($typeMeta['label'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($dueDateLabel, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if ($dueTimeLabel): ?>
                                                <span class="d-block text-muted"><?= htmlspecialchars($dueTimeLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($activity['owner'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($createdLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div class="action-icon d-inline-flex">
                                                <button type="button"
                                                    class="btn btn-link text-primary p-0 me-2 btn-edit-activity"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#edit_activity"
                                                    data-id="<?= (int)$activity['id']; ?>"
                                                    data-title="<?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-type="<?= htmlspecialchars($typeKey, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-due-date="<?= htmlspecialchars($activity['due_date'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-due-time="<?= htmlspecialchars($activity['due_time'] ? substr($activity['due_time'], 0, 5) : '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-owner="<?= htmlspecialchars($activity['owner'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-notes="<?= htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-link text-danger p-0 btn-delete-activity"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#delete_activity_modal"
                                                    data-id="<?= (int)$activity['id']; ?>"
                                                    data-title="<?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Activity List -->

        </div>
        <!-- End Content -->

        <!-- Delete Activity Modal -->
        <div class="modal fade" id="delete_activity_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="modal-title">Hapus Aktivitas</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form method="post" action="activity.php">
                        <input type="hidden" name="_action" value="delete">
                        <input type="hidden" name="activity_id" id="delete-activity-id">
                        <div class="modal-body text-center">
                            <span class="avatar avatar-xl bg-transparent-danger text-danger mb-3">
                                <i class="ti ti-trash-x fs-36"></i>
                            </span>
                            <h4 class="mb-1">Yakin ingin menghapus?</h4>
                            <p class="mb-3">Aktivitas <span class="fw-semibold" id="delete-activity-title"></span> akan dihapus permanen.</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Activity Modal -->

        <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = document.getElementById('edit_activity');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }
                    editModal.querySelector('[name="activity_id"]').value = trigger.getAttribute('data-id') || '';
                    editModal.querySelector('[name="title"]').value = trigger.getAttribute('data-title') || '';
                    var activityTypeField = editModal.querySelector('[name="activity_type"]');
                    var typeValue = trigger.getAttribute('data-type') || 'meeting';
                    if (activityTypeField) {
                        activityTypeField.value = typeValue;
                        activityTypeField.dispatchEvent(new Event('change'));
                    }
                    editModal.querySelector('[name="due_date"]').value = trigger.getAttribute('data-due-date') || '';
                    editModal.querySelector('[name="due_time"]').value = trigger.getAttribute('data-due-time') || '';
                    editModal.querySelector('[name="owner"]').value = trigger.getAttribute('data-owner') || '';
                    editModal.querySelector('[name="notes"]').value = trigger.getAttribute('data-notes') || '';
                });
            }

            var deleteModal = document.getElementById('delete_activity_modal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }
                    deleteModal.querySelector('#delete-activity-id').value = trigger.getAttribute('data-id') || '';
                    deleteModal.querySelector('#delete-activity-title').textContent = trigger.getAttribute('data-title') || '';
                });
            }

            var filterForm = document.getElementById('activity-filter-form');
            if (filterForm) {
                var bookingRange = filterForm.querySelector('.bookingrange');
                if (bookingRange) {
                    bookingRange.addEventListener('change', function () {
                        filterForm.submit();
                    });
                }
            }

        });
    </script>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php';
