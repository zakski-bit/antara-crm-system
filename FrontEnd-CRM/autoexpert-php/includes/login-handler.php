<?php
/**
 * Login handler with 2-step authentication:
 * 1) Validate email + password
 * 2) Verify OTP sent to email
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../page-login.php');
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/otp-mailer.php';

/** @var array<string, mixed> $authConfig */
$authConfig = require __DIR__ . '/auth-config.php';
$otpConfig = is_array($authConfig['otp'] ?? null) ? $authConfig['otp'] : [];
$otpBypassConfig = is_array($authConfig['otp_bypass'] ?? null) ? $authConfig['otp_bypass'] : [];
$mailConfig = is_array($authConfig['mail'] ?? null) ? $authConfig['mail'] : [];

$step = (string)($_POST['auth_step'] ?? 'password');

if ($step === 'verify_otp') {
    handleOtpVerification($otpConfig);
}

if ($step === 'resend_otp') {
    handleOtpResend($otpConfig, $mailConfig);
}

if ($step === 'cancel_otp') {
    unset($_SESSION['auth_pending']);
    authAddMessage('success', 'Verifikasi OTP dibatalkan.');
    authRedirect('../page-login.php');
}

handlePasswordLogin($otpConfig, $mailConfig, $otpBypassConfig);

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $mailConfig
 * @param array<string, mixed> $otpBypassConfig
 */
function handlePasswordLogin(array $otpConfig, array $mailConfig, array $otpBypassConfig): void
{
    $email = strtolower(trim((string)($_POST['username'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    $_SESSION['auth_old'] = [
        'login_email' => $email,
    ];

    if ($email === '' || $password === '') {
        authAddMessage('error', 'Mohon masukkan email dan kata sandi.');
        authRedirect('../page-login.php');
    }

    try {
        $pdo = getDb();

        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $matchedUser = $stmt->fetch();
    } catch (PDOException $e) {
        authAddMessage('error', 'Koneksi database gagal. Silakan coba lagi nanti.');
        authRedirect('../page-login.php');
    }

    if (!$matchedUser || !password_verify($password, $matchedUser['password'])) {
        authAddMessage('error', 'Email atau kata sandi tidak sesuai.');
        authRedirect('../page-login.php');
    }

    if (shouldBypassOtpForUser($matchedUser, $otpBypassConfig)) {
        completeLoginWithoutOtp($matchedUser, $remember);
        authAddMessage('success', 'Login akun demo berhasil tanpa OTP.');
        authRedirect('../page-login.php');
    }

    $otpResult = createLoginOtp($pdo, $matchedUser, $otpConfig);
    if (!$otpResult['ok']) {
        authAddMessage('error', $otpResult['error']);
        authRedirect('../page-login.php');
    }

    $sendResult = sendOtpEmail(
        $mailConfig,
        (string)$matchedUser['email'],
        (string)$matchedUser['name'],
        $otpResult['otp_code'],
        $otpResult['ttl_minutes']
    );

    if (!$sendResult['ok']) {
        // Invalidate OTP row if email sending fails.
        $invalidate = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $invalidate->execute(['id' => $otpResult['otp_id']]);

        authAddMessage('error', $sendResult['error']);
        authRedirect('../page-login.php');
    }

    $_SESSION['auth_pending'] = [
        'user_id' => (int)$matchedUser['id'],
        'full_name' => (string)$matchedUser['name'],
        'email' => (string)$matchedUser['email'],
        'role' => (string)($matchedUser['role'] ?? 'customer'),
        'remember' => $remember,
        'otp_id' => (int)$otpResult['otp_id'],
        'last_otp_sent_at' => time(),
    ];

    unset($_SESSION['auth_old']);

    authAddMessage('success', 'Kode OTP sudah dikirim ke email Anda. Silakan cek inbox/spam.');
    authRedirect('../page-login.php?step=otp');
}

/**
 * @param array<string, mixed> $otpConfig
 */
function handleOtpVerification(array $otpConfig): void
{
    $pending = $_SESSION['auth_pending'] ?? null;
    if (!is_array($pending) || empty($pending['user_id']) || empty($pending['otp_id'])) {
        authAddMessage('error', 'Sesi OTP tidak ditemukan. Silakan login ulang.');
        authRedirect('../page-login.php');
    }

    $otp = preg_replace('/\D+/', '', (string)($_POST['otp_code'] ?? ''));
    if ($otp === '') {
        authAddMessage('error', 'Kode OTP wajib diisi.');
        authRedirect('../page-login.php?step=otp');
    }

    $otpLength = max(4, (int)($otpConfig['length'] ?? 6));
    if (strlen($otp) !== $otpLength) {
        authAddMessage('error', "Kode OTP harus {$otpLength} digit.");
        authRedirect('../page-login.php?step=otp');
    }

    try {
        $pdo = getDb();
        $stmt = $pdo->prepare(
            'SELECT id, otp_hash, attempt_count, max_attempts, expires_at, used_at
             FROM login_email_otps
             WHERE id = :id AND user_id = :user_id AND purpose = :purpose
             LIMIT 1'
        );
        $stmt->execute([
            'id' => (int)$pending['otp_id'],
            'user_id' => (int)$pending['user_id'],
            'purpose' => 'login',
        ]);
        $otpRow = $stmt->fetch();
    } catch (PDOException $e) {
        authAddMessage('error', 'Gagal memverifikasi OTP. Silakan coba lagi.');
        authRedirect('../page-login.php?step=otp');
    }

    if (!$otpRow) {
        authAddMessage('error', 'Kode OTP tidak ditemukan. Silakan kirim ulang.');
        authRedirect('../page-login.php?step=otp');
    }

    if (!empty($otpRow['used_at'])) {
        authAddMessage('error', 'Kode OTP sudah digunakan. Silakan kirim ulang.');
        authRedirect('../page-login.php?step=otp');
    }

    $now = new DateTimeImmutable('now');
    $expiresAt = new DateTimeImmutable((string)$otpRow['expires_at']);
    if ($expiresAt <= $now) {
        $stmt = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => (int)$otpRow['id']]);
        authAddMessage('error', 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.');
        authRedirect('../page-login.php?step=otp');
    }

    $attemptCount = (int)$otpRow['attempt_count'];
    $maxAttempts = (int)$otpRow['max_attempts'];
    if ($attemptCount >= $maxAttempts) {
        $stmt = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => (int)$otpRow['id']]);
        authAddMessage('error', 'Batas percobaan OTP tercapai. Silakan kirim ulang.');
        authRedirect('../page-login.php?step=otp');
    }

    $otpSecret = (string)($otpConfig['secret'] ?? '');
    $candidateHash = hash_hmac('sha256', $otp, $otpSecret);
    $isValid = hash_equals((string)$otpRow['otp_hash'], $candidateHash);

    if (!$isValid) {
        $stmt = $pdo->prepare(
            'UPDATE login_email_otps
             SET attempt_count = attempt_count + 1
             WHERE id = :id'
        );
        $stmt->execute(['id' => (int)$otpRow['id']]);

        $remaining = max(0, $maxAttempts - ($attemptCount + 1));
        authAddMessage('error', "Kode OTP tidak valid. Sisa percobaan: {$remaining}.");
        authRedirect('../page-login.php?step=otp');
    }

    $stmt = $pdo->prepare(
        'UPDATE login_email_otps
         SET used_at = NOW()
         WHERE id = :id'
    );
    $stmt->execute(['id' => (int)$otpRow['id']]);

    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id' => (int)$pending['user_id'],
        'full_name' => (string)$pending['full_name'],
        'email' => (string)$pending['email'],
        'role' => (string)$pending['role'],
    ];

    if (!empty($pending['remember'])) {
        setcookie('remembered_email', (string)$pending['email'], time() + (86400 * 30), '/');
    } else {
        setcookie('remembered_email', '', time() - 3600, '/');
    }

    unset($_SESSION['auth_pending'], $_SESSION['auth_old']);

    authAddMessage('success', 'OTP valid. Selamat datang kembali, ' . (string)$pending['full_name'] . '!');
    authRedirect('../page-login.php');
}

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $mailConfig
 */
function handleOtpResend(array $otpConfig, array $mailConfig): void
{
    $pending = $_SESSION['auth_pending'] ?? null;
    if (!is_array($pending) || empty($pending['user_id']) || empty($pending['email'])) {
        authAddMessage('error', 'Sesi OTP tidak ditemukan. Silakan login ulang.');
        authRedirect('../page-login.php');
    }

    $cooldownSeconds = max(10, (int)($otpConfig['resend_cooldown_seconds'] ?? 60));
    $lastSentAt = (int)($pending['last_otp_sent_at'] ?? 0);
    $elapsed = time() - $lastSentAt;
    if ($elapsed < $cooldownSeconds) {
        $waitSeconds = $cooldownSeconds - $elapsed;
        authAddMessage('error', "Tunggu {$waitSeconds} detik sebelum kirim OTP lagi.");
        authRedirect('../page-login.php?step=otp');
    }

    try {
        $pdo = getDb();
        $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int)$pending['user_id']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        authAddMessage('error', 'Gagal memproses kirim ulang OTP.');
        authRedirect('../page-login.php?step=otp');
    }

    if (!$user) {
        unset($_SESSION['auth_pending']);
        authAddMessage('error', 'Akun tidak ditemukan. Silakan login ulang.');
        authRedirect('../page-login.php');
    }

    $otpResult = createLoginOtp($pdo, $user, $otpConfig);
    if (!$otpResult['ok']) {
        authAddMessage('error', $otpResult['error']);
        authRedirect('../page-login.php?step=otp');
    }

    $sendResult = sendOtpEmail(
        $mailConfig,
        (string)$user['email'],
        (string)$user['name'],
        $otpResult['otp_code'],
        $otpResult['ttl_minutes']
    );

    if (!$sendResult['ok']) {
        $invalidate = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $invalidate->execute(['id' => $otpResult['otp_id']]);

        authAddMessage('error', $sendResult['error']);
        authRedirect('../page-login.php?step=otp');
    }

    $_SESSION['auth_pending']['otp_id'] = (int)$otpResult['otp_id'];
    $_SESSION['auth_pending']['last_otp_sent_at'] = time();

    authAddMessage('success', 'Kode OTP baru sudah dikirim ke email Anda.');
    authRedirect('../page-login.php?step=otp');
}

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $user
 * @return array{ok: bool, error: string, otp_id?: int, otp_code?: string, ttl_minutes?: int}
 */
function createLoginOtp(PDO $pdo, array $user, array $otpConfig): array
{
    $otpLength = max(4, (int)($otpConfig['length'] ?? 6));
    $ttlMinutes = max(1, (int)($otpConfig['ttl_minutes'] ?? 5));
    $maxAttempts = max(1, (int)($otpConfig['max_attempts'] ?? 5));
    $otpSecret = (string)($otpConfig['secret'] ?? '');

    if ($otpSecret === '') {
        return ['ok' => false, 'error' => 'OTP secret belum diset. Isi OTP_SECRET di konfigurasi.'];
    }

    $otpCode = generateNumericCode($otpLength);
    $otpHash = hash_hmac('sha256', $otpCode, $otpSecret);
    $expiresAt = (new DateTimeImmutable('now'))
        ->modify('+' . $ttlMinutes . ' minutes')
        ->format('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $invalidate = $pdo->prepare(
            'UPDATE login_email_otps
             SET used_at = NOW()
             WHERE user_id = :user_id AND purpose = :purpose AND used_at IS NULL'
        );
        $invalidate->execute([
            'user_id' => (int)$user['id'],
            'purpose' => 'login',
        ]);

        $insert = $pdo->prepare(
            'INSERT INTO login_email_otps
            (user_id, email, otp_hash, purpose, attempt_count, max_attempts, expires_at, sent_ip, user_agent)
            VALUES
            (:user_id, :email, :otp_hash, :purpose, 0, :max_attempts, :expires_at, :sent_ip, :user_agent)'
        );
        $insert->execute([
            'user_id' => (int)$user['id'],
            'email' => (string)$user['email'],
            'otp_hash' => $otpHash,
            'purpose' => 'login',
            'max_attempts' => $maxAttempts,
            'expires_at' => $expiresAt,
            'sent_ip' => authClientIp(),
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);

        $otpId = (int)$pdo->lastInsertId();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return ['ok' => false, 'error' => 'Gagal menyimpan OTP. Pastikan tabel login_email_otps sudah dibuat.'];
    }

    return [
        'ok' => true,
        'error' => '',
        'otp_id' => $otpId,
        'otp_code' => $otpCode,
        'ttl_minutes' => $ttlMinutes,
    ];
}

function generateNumericCode(int $length): string
{
    if ($length <= 1) {
        return (string)random_int(0, 9);
    }

    $min = 10 ** ($length - 1);
    $max = (10 ** $length) - 1;

    return (string)random_int($min, $max);
}

function authClientIp(): ?string
{
    $candidates = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
        $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (!is_string($candidate) || trim($candidate) === '') {
            continue;
        }

        $ip = trim(explode(',', $candidate)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return null;
}

function authAddMessage(string $type, string $text): void
{
    $_SESSION['auth_messages'][] = [
        'type' => $type,
        'text' => $text,
    ];
}

function authRedirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * @param array<string, mixed> $user
 */
function completeLoginWithoutOtp(array $user, bool $remember): void
{
    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id' => (int)($user['id'] ?? 0),
        'full_name' => (string)($user['name'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
        'role' => (string)($user['role'] ?? 'customer'),
    ];

    if ($remember) {
        setcookie('remembered_email', (string)($user['email'] ?? ''), time() + (86400 * 30), '/');
    } else {
        setcookie('remembered_email', '', time() - 3600, '/');
    }

    unset($_SESSION['auth_pending'], $_SESSION['auth_old']);
}

/**
 * @param array<string, mixed> $user
 * @param array<string, mixed> $config
 */
function shouldBypassOtpForUser(array $user, array $config): bool
{
    $enabledValue = $config['enabled'] ?? false;
    $enabled = is_bool($enabledValue)
        ? $enabledValue
        : filter_var((string)$enabledValue, FILTER_VALIDATE_BOOLEAN);

    if (!$enabled) {
        return false;
    }

    $email = strtolower(trim((string)($user['email'] ?? '')));
    $role = strtolower(trim((string)($user['role'] ?? '')));

    $allowedEmails = normalizeBypassList($config['emails'] ?? []);
    if ($email !== '' && in_array($email, $allowedEmails, true)) {
        return true;
    }

    $allowedDomains = normalizeBypassList($config['domains'] ?? []);
    if ($email !== '' && !empty($allowedDomains) && str_contains($email, '@')) {
        $domain = strtolower((string)substr((string)strrchr($email, '@'), 1));
        if ($domain !== '' && in_array($domain, $allowedDomains, true)) {
            return true;
        }
    }

    $allowedRoles = normalizeBypassList($config['roles'] ?? []);
    if ($role !== '' && in_array($role, $allowedRoles, true)) {
        return true;
    }

    return false;
}

/**
 * @param mixed $value
 * @return array<int, string>
 */
function normalizeBypassList($value): array
{
    if (is_string($value)) {
        $value = explode(',', $value);
    }

    if (!is_array($value)) {
        return [];
    }

    $normalized = [];
    foreach ($value as $item) {
        if (!is_scalar($item)) {
            continue;
        }

        $key = strtolower(trim((string)$item));
        if ($key === '') {
            continue;
        }

        $normalized[$key] = true;
    }

    return array_keys($normalized);
}
