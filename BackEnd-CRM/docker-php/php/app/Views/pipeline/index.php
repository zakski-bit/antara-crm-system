<?php
/** @var array $entries */
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
$entries = $entries ?? [];

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/pipeline.php';

$storeUrl = rtrim($baseUrl, '/') . '/crm/pipeline';

require BASE_PATH . '/template/src/pipeline.php';
