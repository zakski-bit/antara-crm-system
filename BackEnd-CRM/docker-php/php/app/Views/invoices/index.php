<?php
/** @var array $invoices */
/** @var array $stats */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var array $filters */
/** @var array $statusOptions */
/** @var array $statusLabels */
/** @var array $sortOptions */
/** @var array $perPageOptions */
/** @var string $baseUrl */
/** @var string $listUrl */
/** @var string $storeUrl */

$invoices = $invoices ?? [];
$stats = $stats ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$statusLabels = $statusLabels ?? [];
$sortOptions = $sortOptions ?? [];
$perPageOptions = $perPageOptions ?? [10, 25, 50];
$baseUrl = $baseUrl ?? '';
$listUrl = $listUrl ?? ($baseUrl === '' ? '/invoices.php' : $baseUrl . '/invoices.php');
$storeUrl = $storeUrl ?? (rtrim($baseUrl, '/') . '/crm/invoices');

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/invoices.php';

require BASE_PATH . '/template/src/invoices.php';
