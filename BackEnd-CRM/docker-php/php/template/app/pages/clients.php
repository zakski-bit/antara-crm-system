<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;

$repository = new ClientRepository();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = (int) ($_SESSION['user_id'] ?? 0);

$result = [
    'clients' => [],
    'clients_error' => null,
    'client_stats' => [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'prospect' => 0,
        'new_this_month' => 0,
    ],
];

try {
    $clients = $repository->all($userId, 50);
    $result['clients'] = $clients;
    $result['client_stats'] = $repository->stats($clients);
} catch (\Throwable $exception) {
    $result['clients_error'] = 'Gagal memuat data klien: ' . $exception->getMessage();
}

return $result;
