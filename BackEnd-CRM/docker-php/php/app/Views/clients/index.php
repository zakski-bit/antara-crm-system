<?php
/** @var array $clients */
/** @var array $client_stats */
/** @var string|null $clients_error */
/** @var string $baseUrl */

require_once BASE_PATH . '/template/app/functions.php';

$_SERVER['APP_BASE_URL'] = $baseUrl ?? '';
$_SERVER['PHP_SELF'] = '/clients.php';

$clients = $clients ?? [];
$client_stats = $client_stats ?? [
    'total' => count($clients),
    'active' => 0,
    'inactive' => 0,
    'prospect' => 0,
    'new_this_month' => 0,
];
$clients_error = $clients_error ?? null;

$currentPageSlug = 'clients';
$actionUrl = ($baseUrl === '' ? '' : $baseUrl) . '/crm/clients';

require BASE_PATH . '/template/src/clients.php';
