<?php
/** @var array $subscriptions */
/** @var array $filters */
/** @var array $statusOptions */
/** @var array $cycleOptions */
/** @var array $clients */
/** @var array $customers */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var string $baseUrl */

$_SERVER['PHP_SELF'] = '/subscriptions.php';
$baseUrl = $baseUrl ?? '';
$listUrl = $baseUrl === '' ? '/subscriptions.php' : $baseUrl . '/subscriptions.php';
$storeUrl = $baseUrl === '' ? '/crm/subscriptions' : $baseUrl . '/crm/subscriptions';

require BASE_PATH . '/template/src/subscriptions.php';
