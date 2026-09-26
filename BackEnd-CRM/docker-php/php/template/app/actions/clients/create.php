<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;

/**
 * Handle form submission for creating a new client.
 *
 * @param array $input Submitted form values (typically $_POST).
 */
function action_clients_create(array $input): void
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
    $redirectPage = $normalizedRedirect;
    $baseUrl = rtrim(app_config('app.base_url', ''), '/');
    $redirectBase = $baseUrl === '' ? '' : $baseUrl;

    $token = $input['_token'] ?? '';
    if (!csrf_validate($token)) {
        flash_set('client_error', 'Sesi kedaluwarsa. Silakan coba lagi.');
        redirect($redirectBase . '/index.php?page=' . $redirectPage);
    }

    $firstName = trim((string)($input['first_name'] ?? ''));
    $lastName = trim((string)($input['last_name'] ?? ''));
    $username = trim((string)($input['username'] ?? ''));
    $email = trim((string)($input['email'] ?? ''));
    $password = (string)($input['password'] ?? '');
    $confirmPassword = (string)($input['confirm_password'] ?? '');
    $phone = trim((string)($input['phone'] ?? ''));
    $company = trim((string)($input['company'] ?? ''));
    $status = strtolower(trim((string)($input['status'] ?? 'active')));

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

    if ($password === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($confirmPassword === '') {
        $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
    } elseif ($confirmPassword !== $password) {
        $errors['confirm_password'] = 'Konfirmasi password tidak cocok.';
    }

    if ($phone === '') {
        $errors['phone'] = 'Nomor telepon wajib diisi.';
    }

    $oldInput = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'status' => $status,
    ];

    $repository = new ClientRepository();

    if ($repository->emailExists($userId, $email)) {
        $errors['email'] = 'Email sudah digunakan.';
    }

    if ($repository->usernameExists($userId, $username)) {
        $errors['username'] = 'Username sudah digunakan.';
    }

    if (!empty($errors)) {
        $firstError = reset($errors);
        flash_set('client_error', $firstError ?: 'Periksa kembali data yang Anda masukkan.');
        flash_set('client_form_errors', $errors);
        flash_set('client_form_old', $oldInput);
        redirect($redirectBase . '/index.php?page=' . $redirectPage);
    }

    $fullName = trim($firstName . ' ' . $lastName);
    if ($fullName === '') {
        $fullName = $firstName;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    $generatedCode = 'CLI-' . strtoupper(bin2hex(random_bytes(2))) . '-' . $initials;

    try {
        $repository->create($userId, [
            'code' => $generatedCode,
            'first_name' => $firstName,
            'last_name' => $lastName !== '' ? $lastName : null,
            'name' => $fullName,
            'username' => $username,
            'email' => $email,
            'company' => $company !== '' ? $company : null,
            'phone' => $phone,
            'status' => $status,
            'job_title' => 'Client',
            'position' => 'Client',
            'password_hash' => $passwordHash,
            'avatar_path' => 'assets/img/users/user-01.jpg',
            'notes' => json_encode([
                'created_via_form' => true,
                'submitted_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM),
            ], JSON_THROW_ON_ERROR),
        ]);
    } catch (\Throwable $exception) {
        flash_set('client_error', 'Terjadi kesalahan saat menyimpan data klien.');
        flash_set('client_form_old', $oldInput);
        redirect($redirectBase . '/index.php?page=' . $redirectPage);
    }

    flash_set('client_success', 'Client berhasil ditambahkan.');
    redirect($redirectBase . '/index.php?page=' . $redirectPage);
}
