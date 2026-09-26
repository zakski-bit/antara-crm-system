<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;

/**
 * Handle update request for a client.
 *
 * @param array $input Submitted form values (typically $_POST).
 */
function action_clients_update(array $input): void
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

    $firstName = trim((string)($input['first_name'] ?? ''));
    $lastName = trim((string)($input['last_name'] ?? ''));
    $username = trim((string)($input['username'] ?? ''));
    $email = trim((string)($input['email'] ?? ''));
    $phone = trim((string)($input['phone'] ?? ''));
    $company = trim((string)($input['company'] ?? ''));
    $status = strtolower(trim((string)($input['status'] ?? 'active')));
    $password = (string)($input['password'] ?? '');
    $confirmPassword = (string)($input['confirm_password'] ?? '');

    $allowedStatuses = ['active', 'inactive', 'prospect', 'pending', 'archived'];
    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'active';
    }

    $errors = [];

    if ($firstName === '') {
        $errors['first_name'] = 'First name wajib diisi.';
    }

    if ($username === '') {
        $errors['username'] = 'Username wajib diisi.';
    }

    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Nomor telepon wajib diisi.';
    }

    if ($password !== '' && strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($password !== '' || $confirmPassword !== '') {
        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama.';
        }
    }

    if (!empty($errors)) {
        $firstError = reset($errors);
        flash_set('client_error', $firstError ?: 'Periksa kembali data yang Anda masukkan.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    $repository = new ClientRepository();
    $existingClient = $repository->find($userId, $clientId);

    if (!$existingClient) {
        flash_set('client_error', 'Klien tidak ditemukan.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    if ($repository->emailExists($userId, $email, $clientId)) {
        $errors['email'] = 'Email sudah digunakan.';
    }

    if ($repository->usernameExists($userId, $username, $clientId)) {
        $errors['username'] = 'Username sudah digunakan.';
    }

    if (!empty($errors)) {
        $firstError = reset($errors);
        flash_set('client_error', $firstError ?: 'Periksa kembali data yang Anda masukkan.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    $fullName = trim($firstName . ' ' . $lastName);
    if ($fullName === '') {
        $fullName = $firstName;
    }

    $updateFields = [
        'first_name' => $firstName,
        'last_name' => $lastName !== '' ? $lastName : null,
        'name' => $fullName,
        'username' => $username,
        'email' => $email,
        'company' => $company !== '' ? $company : null,
        'phone' => $phone,
        'status' => $status,
    ];

    if ($password !== '') {
        $updateFields['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    try {
        $repository->update($userId, $clientId, $updateFields);
    } catch (\Throwable $exception) {
        flash_set('client_error', 'Terjadi kesalahan saat memperbarui data klien.');
        redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
    }

    flash_set('client_success', 'Data klien berhasil diperbarui.');
    redirect($redirectBase . '/index.php?page=' . $normalizedRedirect);
}
