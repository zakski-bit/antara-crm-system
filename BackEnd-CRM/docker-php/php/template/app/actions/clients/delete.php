<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;

/**
 * Handle request to delete a client by id.
 *
 * @param array $input Submitted form values (typically $_POST).
 */
function action_clients_delete(array $input): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $userId = (int) ($_SESSION['user_id'] ?? 0);

    $allowedRedirectPages = ['clients', 'clients-grid'];
    $requestedRedirect = isset($input['_redirect']) ? (string)$input['_redirect'] : '';
    $normalizedRedirect = strtolower(trim(pathinfo($requestedRedirect, PATHINFO_FILENAME)));
    if ($normalizedRedirect === '') {
        $normalizedRedirect = 'clients';
    }
    if (!in_array($normalizedRedirect, $allowedRedirectPages, true)) {
        $normalizedRedirect = 'clients';
    }

    $baseUrl = rtrim(app_config('app.base_url', ''), '/');
    $redirectBase = $baseUrl === '' ? '' : $baseUrl;

    $token = $input['_token'] ?? '';
    if (!csrf_validate($token)) {
        flash_set('client_error', 'Sesi kedaluwarsa. Silakan coba lagi.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    $clientId = isset($input['client_id']) ? (int)$input['client_id'] : 0;
    if ($clientId <= 0) {
        flash_set('client_error', 'Klien tidak ditemukan atau parameter tidak valid.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    $repository = new ClientRepository();

    try {
        $deleted = $repository->delete($userId, $clientId);
        if ($deleted) {
            flash_set('client_success', 'Klien berhasil dihapus.');
        } else {
            flash_set('client_error', 'Klien tidak ditemukan atau sudah dihapus.');
        }
    } catch (\Throwable $exception) {
        flash_set('client_error', 'Terjadi kesalahan saat menghapus klien.');
    }

    redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
}
