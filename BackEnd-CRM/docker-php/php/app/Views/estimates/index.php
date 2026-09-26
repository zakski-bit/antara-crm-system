<?php
/** @var array $estimates */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var array $statusOptions */
/** @var string $statusFilter */
/** @var string $sort */
/** @var string $baseUrl */
/** @var string $storeUrl */

$baseUrl = $baseUrl ?? '';
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$statusOptions = $statusOptions ?? [];
$statusFilter = $statusFilter ?? '';
$sort = $sort ?? 'recent';

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/estimates.php';

$storeUrl = rtrim($baseUrl, '/') . '/crm/estimates';
$listUrl = $baseUrl === '' ? '/estimates.php' : $baseUrl . '/estimates.php';

require BASE_PATH . '/template/src/estimates.php';
