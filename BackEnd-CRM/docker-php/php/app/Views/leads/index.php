<?php
/** @var array $leads */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var array $stages */
/** @var string $baseUrl */

$baseUrl = $baseUrl ?? '';
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$stages = $stages ?? [];

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/leads-grid.php';

$storeUrl = rtrim($baseUrl, '/') . '/crm/leads';

require BASE_PATH . '/template/src/leads-grid.php';
