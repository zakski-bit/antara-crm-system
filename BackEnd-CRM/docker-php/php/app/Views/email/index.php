<?php
/** @var string $folder */
/** @var string $search */
/** @var array $messages */
/** @var array|null $activeMessage */
/** @var array $counts */
/** @var array $filterCounts */
/** @var array $labelCounts */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var string $baseUrl */
/** @var string $currentPath */
/** @var string $filter */
/** @var string $label */
/** @var array $recipientOptions */
/** @var array $errorsCompose */
/** @var array $oldInput */

$folder = $folder ?? 'inbox';
$search = $search ?? '';
$messages = $messages ?? [];
$activeMessage = $activeMessage ?? null;
$counts = $counts ?? [];
$filterCounts = $filterCounts ?? [];
$labelCounts = $labelCounts ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$baseUrl = $baseUrl ?? '';
$currentPath = $currentPath ?? ($baseUrl . '/dashboard/email');
$filter = $filter ?? '';
$label = $label ?? '';
$recipientOptions = $recipientOptions ?? [];
$errorsCompose = $errorsCompose ?? [];
$oldInput = $oldInput ?? [];
$currentFilter = $filter;

if (!$activeMessage && !empty($messages)) {
    $activeMessage = $messages[0];
}

$folderDefinitions = [
    'inbox' => ['label' => 'Inbox', 'icon' => 'ti ti-inbox'],
    'starred' => ['label' => 'Starred', 'icon' => 'ti ti-star'],
    'sent' => ['label' => 'Sent', 'icon' => 'ti ti-rocket'],
    'deleted' => ['label' => 'Deleted', 'icon' => 'ti ti-trash'],
];

$quickFilters = [];

$labelColorMap = [
    'Team Events' => 'text-success',
    'Work' => 'text-warning',
    'External' => 'text-danger',
    'Projects' => 'text-info',
    'Applications' => 'text-info',
];

$labelColor = static function (string $labelName) use ($labelColorMap): string {
    return $labelColorMap[$labelName] ?? 'text-primary';
};

$currentFolderLabel = $folderDefinitions[$folder]['label'] ?? ucwords(str_replace('-', ' ', $folder));

$userName = $_SESSION['user_name'] ?? 'James Hong';
$userEmail = $_SESSION['user_email'] ?? 'jnh343@example.com';
$userInitials = strtoupper(substr($userName, 0, 1));

$mailboxUrl = static function (string $path, array $query = []) use ($baseUrl): string {
    $query = array_filter($query, static fn ($value) => $value !== null && $value !== '');
    $url = rtrim($baseUrl . $path, '/');
    if ($url === '') {
        $url = '/';
    }
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }
    return $url;
};

$folderUrl = static function (string $folderKey, string $search) use ($mailboxUrl): string {
    $query = ['folder' => $folderKey];
    if ($search !== '') {
        $query['q'] = $search;
    }
    return $mailboxUrl('/dashboard/email', $query);
};

$filterUrl = static function (string $filterKey, string $folder, string $search, string $label) use ($mailboxUrl): string {
    $query = [
        'filter' => $filterKey,
        'folder' => $folder,
    ];
    if ($search !== '') {
        $query['q'] = $search;
    }
    if ($label !== '') {
        $query['label'] = $label;
    }
    return $mailboxUrl('/dashboard/email', $query);
};

$messageUrl = static function (int $id, string $folder, string $search, string $filter) use ($mailboxUrl): string {
    $query = ['folder' => $folder];
    if ($search !== '') {
        $query['q'] = $search;
    }
    if ($filter !== '') {
        $query['filter'] = $filter;
    }
    $query['message'] = $id;
    return $mailboxUrl('/dashboard/email/' . $id, $query);
};

$labelUrl = static function (string $labelName, string $folder, string $filter, string $search) use ($mailboxUrl): string {
    $query = [
        'folder' => $folder,
        'label' => $labelName,
    ];
    if ($filter !== '') {
        $query['filter'] = $filter;
    }
    if ($search !== '') {
        $query['q'] = $search;
    }
    return $mailboxUrl('/dashboard/email', $query);
};

$clearFilterUrl = static function (string $folder, string $search, string $label) use ($mailboxUrl): string {
    $query = ['folder' => $folder];
    if ($search !== '') {
        $query['q'] = $search;
    }
    if ($label !== '') {
        $query['label'] = $label;
    }
    return $mailboxUrl('/dashboard/email', $query);
};

$clearLabelUrl = static function (string $folder, string $filter, string $search) use ($mailboxUrl): string {
    $query = ['folder' => $folder];
    if ($filter !== '') {
        $query['filter'] = $filter;
    }
    if ($search !== '') {
        $query['q'] = $search;
    }
    return $mailboxUrl('/dashboard/email', $query);
};

$composeAction = $mailboxUrl('/dashboard/email/compose');
$redirectValue = htmlspecialchars($currentPath, ENT_QUOTES, 'UTF-8');
$composeShouldOpen = !empty($errorsCompose);

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/email.php';
$_SERVER['REQUEST_URI'] = $currentPath;

ob_start();
?>

    <style>
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .page-wrapper > .content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .content .d-md-flex {
            flex: 1 1 auto;
            min-height: 0;
        }
        .email-sidebar {
            flex: 0 0 300px;
            max-width: 300px;
        }
        .email-sidebar .slimscroll-active-sidebar {
            height: auto;
        }
        .email-content {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }
        .email-split-container {
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
        }
        .email-content .email-menu {
            flex: 0 0 360px;
            border-right: 1px solid rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .email-content .email-menu-header {
            flex-shrink: 0;
        }
        .email-content .email-list {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }
        .email-content .email-details {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
        }
        @media (max-width: 991.98px) {
            .email-split-container {
                flex-direction: column;
            }
            .email-content .email-menu {
                flex: 0 0 auto;
                border-right: none;
            }
        }
        .email-content .email-list .list-group-item.unread {
            background: #f5f8ff;
            border-left: 3px solid #0d6efd;
            cursor: pointer;
        }
        .email-content .email-list .list-group-item {
            cursor: pointer;
        }
        .email-content .email-list .list-group-item.unread:hover {
            background: #eff4ff;
        }
        .search-highlight {
            background: #fff3cd;
            padding: 0 2px;
            border-radius: 2px;
        }
        /* Sembunyikan composer email lama */
        #compose-view {
            display: none !important;
        }
        .correspondence-editor {
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        .correspondence-toolbar {
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            padding: 6px;
            gap: 6px;
        }
        .correspondence-toolbar button {
            border: 0;
            background: transparent;
            padding: 6px;
            border-radius: 4px;
        }
        .correspondence-toolbar button:hover {
            background: #e9ecef;
        }
        .correspondence-body {
            min-height: 160px;
            padding: 10px;
            outline: none;
        }
        .correspondence-body:empty:before {
            content: attr(data-placeholder);
            color: #adb5bd;
        }
    </style>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content p-0">
            <div class="d-md-flex">
                <div class="email-sidebar border-end border-bottom">
                    <div class="active slimscroll h-100">
                        <div class="slimscroll-active-sidebar">
                            <div class="p-3">
                                <div class="shadow-md bg-white rounded p-2 mb-4">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
                                            <span class="avatar-title rounded-circle bg-primary text-white"><?php echo htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </a>
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></h6>
                                            <p class="mb-0"><?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary w-100" id="compose_mail"><i class="ti ti-edit me-2"></i>Korespondensi Baru</a>
                                <a href="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email', ['folder' => 'inbox']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary w-100 mt-2"><i class="ti ti-list-check me-2"></i>Daftar Korespondensi</a>
                                <div class="mt-4">
                                    <h5 class="mb-2">Emails</h5>
                                <div class="d-block mb-3 pb-3 border-bottom email-tags">
<?php foreach ($folderDefinitions as $key => $definition): ?>
<?php
    $isActive = $folder === $key;
    $total = $counts[$key]['total'] ?? 0;
    $unread = $counts[$key]['unread'] ?? 0;
    $display = $total;
    $badgeClass = $key === 'inbox' ? 'badge badge-danger rounded-pill badge-xs' : 'badge text-gray rounded-pill';
?>
                                        <a href="<?php echo htmlspecialchars($folderUrl($key, $search), ENT_QUOTES, 'UTF-8'); ?>" class="d-flex align-items-center justify-content-between p-2 rounded <?php echo $isActive ? 'active' : ''; ?>">
                                            <span class="d-flex align-items-center fw-medium">
                                                <i class="<?php echo htmlspecialchars($definition['icon']); ?> text-gray me-2"></i>
                                                <?php echo htmlspecialchars($definition['label']); ?>
                                            </span>
                                            <span class="<?php echo $badgeClass; ?>"><?php echo (int)$display; ?></span>
                                        </a>
<?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h5 class="mb-2">Labels</h5>
                                    <div class="email-tags">
<?php if (empty($labelCounts)): ?>
                                        <span class="text-muted fs-12">Belum ada label untuk difilter.</span>
<?php else: ?>
<?php foreach ($labelCounts as $labelName => $labelCount): ?>
<?php
    $isActiveLabel = $label === $labelName;
    $colorClass = $labelColor($labelName);
?>
                                        <a href="<?php echo htmlspecialchars($labelUrl($labelName, $folder, $filter, $search), ENT_QUOTES, 'UTF-8'); ?>" class="d-flex align-items-center justify-content-between p-2 rounded <?php echo $isActiveLabel ? 'active' : ''; ?>">
                                            <span class="d-flex align-items-center fw-medium"><i class="ti ti-tag <?php echo $colorClass; ?> me-2"></i><?php echo htmlspecialchars($labelName, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="badge text-gray rounded-pill"><?php echo (int)$labelCount; ?></span>
                                        </a>
<?php endforeach; ?>
<?php if ($label !== ''): ?>
                                        <a href="<?php echo htmlspecialchars($clearLabelUrl($folder, $filter, $search), ENT_QUOTES, 'UTF-8'); ?>" class="d-flex align-items-center justify-content-between p-2 rounded text-danger">
                                            <span class="d-flex align-items-center fw-medium"><i class="ti ti-x text-danger me-2"></i>Hapus filter label</span>
                                        </a>
<?php endif; ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="email-content">
                        <div class="email-top-head border-bottom p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <form class="position-relative" method="GET" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email')); ?>">
                                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control ps-5" name="q" placeholder="Search Email" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="folder" value="<?php echo htmlspecialchars($folder, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="label" value="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>">
                                </form>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0);" class="btn btn-icon btn-sm bg-light border"><i class="ti ti-refresh"></i></a>
                                <a href="javascript:void(0);" class="btn btn-icon btn-sm bg-light border"><i class="ti ti-settings"></i></a>
                            </div>
                        </div>
                        <div class="d-lg-flex email-split-container">
                                <div class="email-menu border-end flex-shrink-0">
                                <div class="email-menu-header d-flex align-items-center justify-content-between p-3 border-bottom">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($currentFolderLabel, ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <span class="text-muted fs-12"><?php echo count($messages); ?> Email<?php echo count($messages) === 1 ? "" : "s"; ?></span>
                                </div>
                                    <div class="email-list slimscroll">
<?php if (!empty($flash['success'])): ?>
                                    <div class="alert alert-success border-0 rounded-0">
                                        <?php echo htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
                                    <div class="alert alert-danger border-0 rounded-0">
                                        <?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
<?php endif; ?>
<?php if (empty($messages)): ?>
                                    <div class="p-4 text-center">
                                        <div class="avatar avatar-lg bg-light-primary rounded-circle mb-3">
                                            <i class="ti ti-mail"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-1">No conversations yet</h6>
                                        <p class="text-muted mb-0">Try selecting another folder or compose a new email.</p>
                                    </div>
<?php else: ?>
<?php foreach ($messages as $item): ?>
<?php
    $isActive = $activeMessage && (int)$activeMessage['id'] === (int)$item['id'];
    $itemUrl = $messageUrl((int)$item['id'], $folder, $search, $currentFilter);
    $initials = htmlspecialchars($item['initials'], ENT_QUOTES, 'UTF-8');
    $senderEmailEsc = htmlspecialchars($item['sender_email'], ENT_QUOTES, 'UTF-8');
    $toEsc = htmlspecialchars($item['to_addresses'] ?? '', ENT_QUOTES, 'UTF-8');
    $ccEsc = htmlspecialchars($item['cc_addresses'] ?? '', ENT_QUOTES, 'UTF-8');
    $subjectEsc = htmlspecialchars($item['subject'], ENT_QUOTES, 'UTF-8');
    $bodyEncoded = htmlspecialchars(base64_encode((string)($item['body_text'] ?? '')), ENT_QUOTES, 'UTF-8');
    $senderId = isset($item['sender_user_id']) ? (int)$item['sender_user_id'] : null;
    $itemClasses = 'list-group-item border-bottom p-3';
    if ($isActive) {
        $itemClasses .= ' active';
    }
    if (empty($item['is_read'])) {
        $itemClasses .= ' unread';
    }
?>
                                    <div class="<?php echo $itemClasses; ?>" data-url="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="d-flex align-items-center flex-wrap row-gap-2 flex-fill">
                                                <a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8'); ?>" class="avatar avatar-md avatar-rounded me-2 <?php echo $item['has_attachments'] ? 'bg-success' : 'bg-primary'; ?>">
                                                    <span class="avatar-title"><?php echo $initials; ?></span>
                                                </a>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8'); ?>" class="text-reset <?php echo $item['is_read'] ? '' : 'fw-semibold'; ?>">
                                                    <?php echo htmlspecialchars($item['sender'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                </h6>
                                                <span class="fw-semibold d-block text-dark"><?php echo htmlspecialchars($item['subject'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="dropdown me-2">
                                                    <button class="btn btn-icon btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ti ti-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end p-3">
                                                        <li>
                                                            <a class="dropdown-item rounded-1 compose-trigger" href="#" data-mode="reply" data-sender="<?php echo $senderEmailEsc; ?>" data-sender-id="<?php echo $senderId ?: ''; ?>" data-to="<?php echo $toEsc; ?>" data-cc="<?php echo $ccEsc; ?>" data-subject="<?php echo $subjectEsc; ?>" data-body="<?php echo $bodyEncoded; ?>">Reply</a>
                                                        </li>
                                                                    <li>
                                                                        <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . (int)$item['id'] . '/move'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                                            <input type="hidden" name="target" value="deleted">
                                                                            <button type="submit" class="dropdown-item rounded-1 text-danger">Delete</button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <span class="text-muted fs-12"><?php echo htmlspecialchars($item['time_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </div>
                                                    </div>
                                                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($item['snippet'], ENT_QUOTES, 'UTF-8'); ?></p>
<?php if (!empty($item['labels'])): ?>
                                                    <div class="d-flex flex-wrap gap-2 mt-2">
<?php foreach ($item['labels'] as $label): ?>
                                                        <span class="badge badge-soft-secondary"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
<?php endforeach; ?>
                                                    </div>
<?php endif; ?>
<?php if (!empty($item['scheduled_label'])): ?>
                                                    <div class="text-muted fs-12 mt-1"><?php echo htmlspecialchars($item['scheduled_label'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
<?php if ($item['has_attachments']): ?>
                                                <span class="d-flex align-items-center btn btn-sm bg-transparent-dark"><i class="ti ti-paperclip me-2"></i>Files</span>
<?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . (int)$item['id'] . ($item['is_starred'] ? '/unstar' : '/star')), ENT_QUOTES, 'UTF-8'); ?>" class="d-inline">
                                                    <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                    <button type="submit" class="btn btn-link p-0 border-0 <?php echo $item['is_starred'] ? 'text-warning' : 'text-muted'; ?>" title="<?php echo $item['is_starred'] ? 'Remove star' : 'Add star'; ?>">
                                                        <i class="ti ti-star<?php echo $item['is_starred'] ? '-filled' : ''; ?>"></i>
                                                    </button>
                                                </form>
<?php if ($item['is_read']): ?>
                                                <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . (int)$item['id'] . '/unread'), ENT_QUOTES, 'UTF-8'); ?>" class="d-inline ms-1">
                                                    <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                    <button type="submit" class="btn btn-link p-0 border-0 text-muted" title="Mark as unread">
                                                        <i class="ti ti-mail"></i>
                                                    </button>
                                                </form>
<?php else: ?>
                                                <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . (int)$item['id'] . '/read'), ENT_QUOTES, 'UTF-8'); ?>" class="d-inline ms-1">
                                                    <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                    <button type="submit" class="btn btn-link p-0 border-0 text-muted" title="Mark as read">
                                                        <i class="ti ti-mail-opened"></i>
                                                    </button>
                                                </form>
<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
<?php endforeach; ?>
<?php endif; ?>
                                </div>
                            </div>
                            <div class="email-details flex-grow-1">
<?php if ($activeMessage): ?>
                                <div class="border-bottom p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="mb-1"><?php echo htmlspecialchars($activeMessage['subject'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                        <div class="text-muted fs-12">
                                            <?php echo htmlspecialchars($activeMessage['sender_name'], ENT_QUOTES, 'UTF-8'); ?> &lt;<?php echo htmlspecialchars($activeMessage['sender_email'], ENT_QUOTES, 'UTF-8'); ?>&gt;
                                        </div>
                                        <div class="text-muted fs-12">
                                            To: <?php echo htmlspecialchars($activeMessage['to_addresses'], ENT_QUOTES, 'UTF-8'); ?>
<?php if (!empty($activeMessage['cc_addresses'])): ?>
                                            <span class="ms-2">Cc: <?php echo htmlspecialchars($activeMessage['cc_addresses'], ENT_QUOTES, 'UTF-8'); ?></span>
<?php endif; ?>
                                        </div>
<?php if (!empty($activeMessage['labels'])): ?>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
<?php foreach ($activeMessage['labels'] as $label): ?>
                                            <span class="badge badge-soft-primary"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
<?php endforeach; ?>
                                        </div>
<?php endif; ?>
<?php if (!empty($activeMessage['scheduled_label'])): ?>
                                        <div class="text-muted fs-12 mt-1"><?php echo htmlspecialchars($activeMessage['scheduled_label'], ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted fs-12"><?php echo htmlspecialchars($activeMessage['time_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <div class="d-flex align-items-center justify-content-end gap-2 mt-2">
<?php if (!empty($activeMessage['sender_user_id'])): ?>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm"
                                                    id="reply_btn"
                                                    data-sender-id="<?php echo (int)$activeMessage['sender_user_id']; ?>"
                                                    data-sender-name="<?php echo htmlspecialchars($activeMessage['sender_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-subject="<?php echo htmlspecialchars($activeMessage['subject'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-body="<?php echo htmlspecialchars(base64_encode($activeMessage['body_html'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="ti ti-arrow-back-up me-1"></i>Balas
                                            </button>
<?php endif; ?>
                                            <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . $activeMessage['id'] . ($activeMessage['is_starred'] ? '/unstar' : '/star'))); ?>">
                                                <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                <button type="submit" class="btn btn-light btn-sm">
                                                    <i class="ti ti-star<?php echo $activeMessage['is_starred'] ? '-filled text-warning' : ''; ?> me-1"></i>
                                                    <?php echo $activeMessage['is_starred'] ? 'Starred' : 'Star'; ?>
                                                </button>
                                            </form>
                                            <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . $activeMessage['id'] . '/unread')); ?>">
                                                <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                <button type="submit" class="btn btn-light btn-sm"><i class="ti ti-mail me-1"></i>Mark Unread</button>
                                            </form>
                                            <form method="POST" action="<?php echo htmlspecialchars($mailboxUrl('/dashboard/email/' . $activeMessage['id'] . '/move')); ?>">
                                                <input type="hidden" name="redirect" value="<?php echo $redirectValue; ?>">
                                                <input type="hidden" name="target" value="<?php echo $activeMessage['folder'] === 'deleted' ? 'inbox' : 'deleted'; ?>">
                                                <button type="submit" class="btn btn-light btn-sm"><i class="ti ti-trash me-1"></i><?php echo $activeMessage['folder'] === 'deleted' ? 'Restore' : 'Delete'; ?></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="mb-4">
                                        <?php echo $activeMessage['body_html']; ?>
                                    </div>
<?php if (!empty($activeMessage['attachment_path'])): ?>
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark"><i class="ti ti-paperclip me-1"></i>Lampiran</span>
                                        <?php
                                            $attachmentLabel = $activeMessage['attachment_name'] ?? basename($activeMessage['attachment_path']);
                                        ?>
                                        <a href="<?php echo htmlspecialchars($activeMessage['attachment_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($attachmentLabel, ENT_QUOTES, 'UTF-8'); ?></a>
                                    </div>
<?php endif; ?>
                                    </div>
<?php else: ?>
                                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                                    <div class="avatar avatar-xl bg-light-primary rounded-circle mb-3">
                                        <i class="ti ti-mail-opened text-primary fs-26"></i>
                                    </div>
                                    <h5 class="fw-semibold mb-1">Select an email</h5>
                                    <p class="text-muted mb-0">Pick a conversation from the list to view details.</p>
                                </div>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content -->

        <?php include BASE_PATH . '/template/partials/footer.php'; ?>

    </div>

    <div class="modal fade" id="correspondenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Korespondensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($composeAction, ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data" id="correspondenceForm">
                    <div class="modal-body">
                        <?php if (!empty($flash['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Kepada <span class="text-danger">*</span></label>
                            <select name="to_user_id" id="recipient_user_id" class="form-select<?php echo !empty($errorsCompose['to_user_id']) ? ' is-invalid' : ''; ?>" required>
                                <option value="">Pilih pengguna</option>
                                <?php foreach ($recipientOptions as $recipient): ?>
                                    <option value="<?php echo (int)($recipient['id'] ?? 0); ?>" data-email="<?php echo htmlspecialchars($recipient['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($oldInput['to_user_id']) && (int)$oldInput['to_user_id'] === (int)$recipient['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(($recipient['name'] ?? 'Pengguna') . ' - ' . ($recipient['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errorsCompose['to_user_id'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errorsCompose['to_user_id'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subjek <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control<?php echo !empty($errorsCompose['subject']) ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($oldInput['subject'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            <?php if (!empty($errorsCompose['subject'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errorsCompose['subject'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi <span class="text-danger">*</span></label>
                            <input type="hidden" name="body_html" id="correspondence_body_html" value="<?php echo htmlspecialchars($oldInput['body_html'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="correspondence-editor">
                                <div class="correspondence-toolbar d-flex flex-wrap">
                                    <button type="button" data-command="bold"><i class="ti ti-bold"></i></button>
                                    <button type="button" data-command="italic"><i class="ti ti-italic"></i></button>
                                    <button type="button" data-command="underline"><i class="ti ti-underline"></i></button>
                                    <button type="button" data-command="insertUnorderedList"><i class="ti ti-list"></i></button>
                                    <button type="button" data-command="insertOrderedList"><i class="ti ti-list-numbers"></i></button>
                                    <button type="button" data-command="formatBlock" data-value="blockquote"><i class="ti ti-quote"></i></button>
                                    <button type="button" data-command="createLink" data-value="prompt"><i class="ti ti-link"></i></button>
                                    <button type="button" data-command="removeFormat"><i class="ti ti-eraser"></i></button>
                                </div>
                                <div id="correspondence_body" class="correspondence-body" contenteditable="true" data-placeholder="Tulis pesan di sini..."><?php echo $oldInput['body_html'] ?? ''; ?></div>
                            </div>
                            <?php if (!empty($errorsCompose['body_html'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errorsCompose['body_html'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lampiran</label>
                            <input type="file" name="attachment" class="form-control<?php echo !empty($errorsCompose['attachment']) ? ' is-invalid' : ''; ?>" accept=".png,.jpg,.jpeg,.webp,.pdf,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">Maks 8MB. Format: gambar, PDF, DOC/X, XLS/X.</small>
                            <?php if (!empty($errorsCompose['attachment'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errorsCompose['attachment'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function clearBackdrops() {
            document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                if (backdrop && backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            });
            document.body.classList.remove('modal-open');
        }
        // Bersihkan jika ada backdrop tersisa dari session sebelumnya
        clearBackdrops();

        var legacyCompose = document.getElementById('compose-view');
        if (legacyCompose) {
            legacyCompose.classList.remove('show');
            legacyCompose.style.display = 'none';
        }

        var composeBtn = document.getElementById('compose_mail');
        var correspondenceModal = document.getElementById('correspondenceModal');
        var correspondenceInstance = null;
        if (composeBtn && correspondenceModal && typeof bootstrap !== 'undefined') {
            correspondenceInstance = new bootstrap.Modal(correspondenceModal);
            composeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                correspondenceInstance.show();
            });
            correspondenceModal.addEventListener('hidden.bs.modal', function () {
                clearBackdrops();
            });
        }

        var moreMenu = document.querySelector('.more-menu');
        var viewAllButton = document.querySelector('.viewall-button');
        if (<?php echo $currentFilter !== '' ? 'true' : 'false'; ?>) {
            if (moreMenu) {
                moreMenu.style.display = 'block';
            }
            if (viewAllButton) {
                viewAllButton.textContent = 'Less';
            }
        }

        // Composer lama dinonaktifkan
        var composeView = null;
        var composeForm = null;
        var clickableItems = document.querySelectorAll('.email-list .list-group-item');

        // Rich text editor & lampiran
        var richForm = document.getElementById('correspondenceForm');
        var bodyHidden = document.getElementById('correspondence_body_html');
        var bodyEditable = document.getElementById('correspondence_body');
        var toolbar = document.querySelector('.correspondence-toolbar');

        if (bodyEditable && bodyHidden) {
            if (bodyHidden.value) {
                bodyEditable.innerHTML = bodyHidden.value;
            }
        }

        if (toolbar && bodyEditable) {
            toolbar.querySelectorAll('button[data-command]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var cmd = btn.getAttribute('data-command');
                    var val = btn.getAttribute('data-value') || null;
                    if (cmd === 'createLink') {
                        if (val === 'prompt') {
                            var url = window.prompt('Masukkan URL');
                            if (url) {
                                document.execCommand(cmd, false, url);
                            }
                            return;
                        }
                    }
                    document.execCommand(cmd, false, val);
                    bodyEditable.focus();
                });
            });
        }

        if (richForm && bodyHidden && bodyEditable) {
            richForm.addEventListener('submit', function () {
                bodyHidden.value = bodyEditable.innerHTML;
            });
        }

        var recipientSelect = document.getElementById('recipient_user_id');
        var subjectInput = document.querySelector('input[name=\"subject\"]');

        function selectRecipient(senderId, senderEmail) {
            if (!recipientSelect) return;
            var options = recipientSelect.options;
            var matched = false;
            if (senderId) {
                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === String(senderId)) {
                        options[i].selected = true;
                        matched = true;
                        break;
                    }
                }
            }
            if (!matched && senderEmail) {
                var lower = senderEmail.toLowerCase();
                for (var j = 0; j < options.length; j++) {
                    var dataEmail = (options[j].getAttribute('data-email') || '').toLowerCase();
                    if (dataEmail && dataEmail === lower) {
                        options[j].selected = true;
                        matched = true;
                        break;
                    }
                }
            }
            if (!matched) {
                recipientSelect.value = '';
            }
        }

        function openReply(senderId, senderEmail, subjectOriginal, bodyEncoded) {
            if (!correspondenceInstance || !bodyEditable || !bodyHidden) return;
            selectRecipient(senderId, senderEmail);

            if (subjectInput) {
                if (/^Re:/i.test(subjectOriginal)) {
                    subjectInput.value = subjectOriginal;
                } else {
                    subjectInput.value = 'Re: ' + subjectOriginal;
                }
            }

            var decodedBody = '';
            try { decodedBody = atob(bodyEncoded || ''); } catch (e) { decodedBody = ''; }
            var quote = decodedBody ? '<blockquote style=\"border-left:3px solid #e9ecef; padding-left:8px; margin-top:8px;\">' + decodedBody + '</blockquote>' : '';
            bodyEditable.innerHTML = '<p></p>' + quote;
            bodyHidden.value = bodyEditable.innerHTML;

            correspondenceInstance.show();
        }

        var replyBtn = document.getElementById('reply_btn');
        if (replyBtn) {
            replyBtn.addEventListener('click', function () {
                openReply(
                    replyBtn.getAttribute('data-sender-id'),
                    replyBtn.getAttribute('data-sender-email'),
                    replyBtn.getAttribute('data-subject') || '',
                    replyBtn.getAttribute('data-body') || ''
                );
            });
        }

        document.querySelectorAll('.compose-trigger[data-mode=\"reply\"]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openReply(
                    btn.getAttribute('data-sender-id'),
                    btn.getAttribute('data-sender'),
                    btn.getAttribute('data-subject') || '',
                    btn.getAttribute('data-body') || ''
                );
            });
        });

        clickableItems.forEach(function (item) {
            item.addEventListener('click', function (event) {
                var target = event.target;
                if (target.closest('.dropdown') || target.tagName === 'A' || target.closest('button')) {
                    return;
                }
                var url = item.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });

        var currentUserEmail = <?php echo json_encode($userEmail); ?>;
        var currentUserEmailLower = (currentUserEmail || '').toLowerCase();

        if (composeForm) {
            composeForm.setAttribute('action', <?php echo json_encode($composeAction); ?>);
            composeForm.setAttribute('method', 'POST');

            var toInput = composeForm.querySelector('[name="Label"]');
            if (toInput) {
                toInput.setAttribute('name', 'to');
                toInput.value = <?php echo json_encode($old['to'] ?? ''); ?>;
            }

            var ccToggle = composeForm.querySelector('.email-cc');
            if (ccToggle) {
                var ccField = composeForm.querySelector('input[name="cc"]');
                if (!ccField) {
                    ccField = document.createElement('input');
                    ccField.type = 'hidden';
                    ccField.name = 'cc';
                    composeForm.appendChild(ccField);
                }
                ccField.value = <?php echo json_encode($old['cc'] ?? ''); ?>;
            }

            var subjectInput = composeForm.querySelector('input[placeholder="Subject"]');
            if (subjectInput) {
                subjectInput.setAttribute('name', 'subject');
                subjectInput.value = <?php echo json_encode($old['subject'] ?? ''); ?>;
            }

            var bodyTextarea = composeForm.querySelector('textarea');
            if (bodyTextarea) {
                bodyTextarea.setAttribute('name', 'body');
                bodyTextarea.value = <?php echo json_encode($old['body'] ?? ''); ?>;
            }
        }

        function showComposeModal() {
            if (!composeView || !composeForm) {
                return;
            }
            if (typeof window.jQuery !== 'undefined') {
                if (!window.jQuery('.modal-backdrop').length) {
                    window.jQuery('body').append('<div class="modal-backdrop fade show"></div>');
                }
                window.jQuery('#compose-view').addClass('show');
            } else {
                if (!document.querySelector('.modal-backdrop')) {
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
                composeView.classList.add('show');
            }
        }

        function uniqueList(list) {
            var map = {};
            list.forEach(function (item) {
                var trimmed = item.trim();
                if (trimmed !== '') {
                    var lower = trimmed.toLowerCase();
                    if (lower === currentUserEmailLower) {
                        return;
                    }
                    map[lower] = trimmed;
                }
            });
            return Object.keys(map).map(function (key) {
                return map[key];
            });
        }

        function updateTagsInput(input, value) {
            if (!input) {
                return;
            }
            var normalized = (value || '').split(',').map(function (item) {
                return item.trim();
            }).filter(function (item) {
                return item !== '';
            });

            if (typeof window.jQuery !== 'undefined' && window.jQuery(input).data('tagsinput')) {
                var plugin = window.jQuery(input);
                plugin.tagsinput('removeAll');
                normalized.forEach(function (item) {
                    plugin.tagsinput('add', item);
                });
            } else {
                input.value = normalized.join(', ');
            }
        }

        function populateCompose(mode, sender, toList, ccList, subject, bodyEncoded) {
            if (!composeForm) {
                return;
            }

            var toField = composeForm.querySelector('input[name="to"]');
            var ccField = composeForm.querySelector('input[name="cc"]');
            var subjectField = composeForm.querySelector('input[name="subject"]');
            var bodyField = composeForm.querySelector('textarea[name="body"]');

            var originalBody = '';
            try {
                originalBody = bodyEncoded ? atob(bodyEncoded) : '';
            } catch (error) {
                originalBody = '';
            }

            var finalTo = '';
            var finalCc = '';
            var finalSubject = subject || '';
            var finalBody = '';

            if (mode === 'reply') {
                finalTo = sender;
                if (subject && !/^Re:/i.test(subject)) {
                    finalSubject = 'Re: ' + subject;
                }
                finalBody = '\n\n--- Pesan sebelumnya ---\n' + originalBody;
            } else if (mode === 'reply-all') {
                var recipients = uniqueList((sender ? [sender] : []).concat((toList || '').split(',')));
                finalTo = recipients.join(', ');
                var ccRecipients = uniqueList((ccList || '').split(','));
                finalCc = ccRecipients.join(', ');
                if (subject && !/^Re:/i.test(subject)) {
                    finalSubject = 'Re: ' + subject;
                }
                finalBody = '\n\n--- Pesan sebelumnya ---\n' + originalBody;
            } else if (mode === 'forward') {
                finalTo = '';
                if (subject && !/^Fwd:/i.test(subject)) {
                    finalSubject = 'Fwd: ' + subject;
                }
                finalBody = '\n\n--- Pesan diteruskan ---\n' + originalBody;
            } else if (mode === 'forward-attachment') {
                finalTo = '';
                if (subject && !/^Fwd:/i.test(subject)) {
                    finalSubject = 'Fwd: ' + subject;
                }
                finalBody = 'Silakan temukan pesan terlampir.\n\n--- Pesan asli ---\n' + originalBody;
            }

            updateTagsInput(toField, finalTo);
            updateTagsInput(ccField, finalCc);
            if (subjectField) {
                subjectField.value = finalSubject;
            }
            if (bodyField) {
                bodyField.value = finalBody;
            }

            showComposeModal();
        }

        var composeActions = document.querySelectorAll('.compose-trigger');
        composeActions.forEach(function (trigger) {
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                var mode = trigger.getAttribute('data-mode');
                var sender = trigger.getAttribute('data-sender') || '';
                var toRecipients = trigger.getAttribute('data-to') || '';
                var ccRecipients = trigger.getAttribute('data-cc') || '';
                var subjectValue = trigger.getAttribute('data-subject') || '';
                var bodyValue = trigger.getAttribute('data-body') || '';
        populateCompose(mode, sender, toRecipients, ccRecipients, subjectValue, bodyValue);
    });
});

<?php if ($composeShouldOpen): ?>
        if (correspondenceInstance) {
            correspondenceInstance.show();
        }
<?php endif; ?>
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.querySelector('input[name="q"]');
        var term = searchInput ? searchInput.value.trim() : '';
        if (term === '') {
            return;
        }

        function highlightWithin(container, keyword) {
            if (!container) {
                return;
            }
            var safePattern = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            if (safePattern === '') {
                return;
            }
            var regex = new RegExp(safePattern, 'gi');
            var walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
            var nodes = [];
            while (walker.nextNode()) {
                nodes.push(walker.currentNode);
            }

            nodes.forEach(function (node) {
                var text = node.textContent;
                if (!regex.test(text)) {
                    regex.lastIndex = 0;
                    return;
                }
                regex.lastIndex = 0;
                var frag = document.createDocumentFragment();
                var lastIndex = 0;
                var match;
                while ((match = regex.exec(text)) !== null) {
                    if (match.index > lastIndex) {
                        frag.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
                    }
                    var mark = document.createElement('mark');
                    mark.className = 'search-highlight';
                    mark.textContent = match[0];
                    frag.appendChild(mark);
                    lastIndex = match.index + match[0].length;
                }
                if (lastIndex < text.length) {
                    frag.appendChild(document.createTextNode(text.slice(lastIndex)));
                }
                node.parentNode.replaceChild(frag, node);
            });
        }

        highlightWithin(document.querySelector('.email-list'), term);
        highlightWithin(document.querySelector('.email-details'), term);
    });
    </script>

<?php
$content = ob_get_clean();

require BASE_PATH . '/template/partials/main.php';
