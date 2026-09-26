<?php
/**
 * Password reset handler with OTP email verification.
 *
 * Flow:
 * 1) Request OTP via email
 * 2) Verify OTP + set new password
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../page-reset-password.php');
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/otp-mailer.php';

/** @var array<string, mixed> $authConfig */
$authConfig = require __DIR__ . '/auth-config.php';
$otpConfig = is_array($authConfig['otp'] ?? null) ? $authConfig['otp'] : [];
$otpBypassConfig = is_array($authConfig['otp_bypass'] ?? null) ? $authConfig['otp_bypass'] : [];
$mailConfig = is_array($authConfig['mail'] ?? null) ? $authConfig['mail'] : [];

$step = (string)($_POST['reset_step'] ?? 'request_otp');

if ($step === 'verify_reset') {
    handleVerifyAndReset($otpConfig);
}

if ($step === 'resend_otp') {
    handleResendOtp($otpConfig, $otpBypassConfig, $mailConfig);
}

if ($step === 'cancel_reset') {
    unset($_SESSION['reset_pending'], $_SESSION['reset_old']);
    resetAddMessage('success', 'Proses reset kata sandi dibatalkan.');
    resetRedirect('../page-reset-password.php');
}

handleRequestOtp($otpConfig, $otpBypassConfig, $mailConfig);

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $otpBypassConfig
 * @param array<string, mixed> $mailConfig
 */
function handleRequestOtp(array $otpConfig, array $otpBypassConfig, array $mailConfig): void
{
    $email = strtolower(trim((string)($_POST['email'] ?? '')));

    $_SESSION['reset_old'] = ['email' => $email];

    if ($email === '') {
        resetAddMessage('error', 'Alamat email wajib diisi.');
        resetRedirect('../page-reset-password.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        resetAddMessage('error', 'Format email tidak valid.');
        resetRedirect('../page-reset-password.php');
    }

    try {
        $pdo = getDb();
        $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        resetAddMessage('error', 'Koneksi database gagal. Silakan coba lagi nanti.');
        resetRedirect('../page-reset-password.php');
    }

    if (!$user) {
        // Generic message to avoid account enumeration.
        unset($_SESSION['reset_pending'], $_SESSION['reset_old']);
        resetAddMessage('success', 'Jika email terdaftar, kode OTP reset sudah dikirim.');
        resetRedirect('../page-reset-password.php');
    }

    if (shouldBypassOtpForUser($user, $otpBypassConfig)) {
        resetAddMessage('error', 'Akun demo tidak menggunakan fitur reset kata sandi OTP.');
        resetRedirect('../page-reset-password.php');
    }

    $otpResult = createOtpWithPurpose($pdo, $user, $otpConfig, 'password_reset');
    if (!$otpResult['ok']) {
        resetAddMessage('error', $otpResult['error']);
        resetRedirect('../page-reset-password.php');
    }

    $sendResult = sendOtpEmail(
        $mailConfig,
        (string)$user['email'],
        (string)$user['name'],
        $otpResult['otp_code'],
        $otpResult['ttl_minutes'],
        'password_reset'
    );

    if (!$sendResult['ok']) {
        $invalidate = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $invalidate->execute(['id' => (int)$otpResult['otp_id']]);

        resetAddMessage('error', $sendResult['error']);
        resetRedirect('../page-reset-password.php');
    }

    $_SESSION['reset_pending'] = [
        'user_id' => (int)$user['id'],
        'full_name' => (string)$user['name'],
        'email' => (string)$user['email'],
        'role' => (string)($user['role'] ?? 'customer'),
        'otp_id' => (int)$otpResult['otp_id'],
        'last_otp_sent_at' => time(),
    ];

    unset($_SESSION['reset_old']);

    resetAddMessage('success', 'Kode OTP reset sudah dikirim ke email Anda.');
    resetRedirect('../page-reset-password.php?step=otp');
}

/**
 * @param array<string, mixed> $otpConfig
 */
function handleVerifyAndReset(array $otpConfig): void
{
    $pending = $_SESSION['reset_pending'] ?? null;
    if (!is_array($pending) || empty($pending['user_id']) || empty($pending['otp_id'])) {
        resetAddMessage('error', 'Sesi reset tidak ditemukan. Silakan ulangi dari awal.');
        resetRedirect('../page-reset-password.php');
    }

    $otp = preg_replace('/\D+/', '', (string)($_POST['otp_code'] ?? ''));
    $newPassword = (string)($_POST['new_password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    $_SESSION['reset_old'] = ['email' => (string)($pending['email'] ?? '')];

    if ($otp === '') {
        resetAddMessage('error', 'Kode OTP wajib diisi.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $otpLength = max(4, (int)($otpConfig['length'] ?? 6));
    if (strlen($otp) !== $otpLength) {
        resetAddMessage('error', "Kode OTP harus {$otpLength} digit.");
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if ($newPassword === '' || $confirmPassword === '') {
        resetAddMessage('error', 'Kata sandi baru dan konfirmasi wajib diisi.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if (strlen($newPassword) < 8) {
        resetAddMessage('error', 'Kata sandi baru minimal 8 karakter.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/\d/', $newPassword)) {
        resetAddMessage('error', 'Kata sandi baru harus mengandung huruf dan angka.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if ($newPassword !== $confirmPassword) {
        resetAddMessage('error', 'Konfirmasi kata sandi tidak cocok.');
        resetRedirect('../page-reset-password.php?step=otp');
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
            'purpose' => 'password_reset',
        ]);
        $otpRow = $stmt->fetch();
    } catch (PDOException $e) {
        resetAddMessage('error', 'Gagal memverifikasi OTP. Silakan coba lagi.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if (!$otpRow) {
        resetAddMessage('error', 'Kode OTP tidak ditemukan. Silakan kirim ulang.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if (!empty($otpRow['used_at'])) {
        resetAddMessage('error', 'Kode OTP sudah digunakan. Silakan kirim ulang.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $now = new DateTimeImmutable('now');
    $expiresAt = new DateTimeImmutable((string)$otpRow['expires_at']);
    if ($expiresAt <= $now) {
        $stmt = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => (int)$otpRow['id']]);
        resetAddMessage('error', 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $attemptCount = (int)$otpRow['attempt_count'];
    $maxAttempts = (int)$otpRow['max_attempts'];
    if ($attemptCount >= $maxAttempts) {
        $stmt = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => (int)$otpRow['id']]);
        resetAddMessage('error', 'Batas percobaan OTP tercapai. Silakan kirim ulang.');
        resetRedirect('../page-reset-password.php?step=otp');
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
        resetAddMessage('error', "Kode OTP tidak valid. Sisa percobaan: {$remaining}.");
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => (int)$otpRow['id']]);

        $stmt = $pdo->prepare('UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id LIMIT 1');
        $stmt->execute([
            'password' => $newHash,
            'id' => (int)$pending['user_id'],
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        resetAddMessage('error', 'Gagal memperbarui kata sandi. Silakan coba lagi.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    unset($_SESSION['reset_pending'], $_SESSION['reset_old']);
    resetAddMessage('success', 'Kata sandi berhasil diperbarui. Silakan login kembali.');
    resetRedirect('../page-login.php');
}

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $otpBypassConfig
 * @param array<string, mixed> $mailConfig
 */
function handleResendOtp(array $otpConfig, array $otpBypassConfig, array $mailConfig): void
{
    $pending = $_SESSION['reset_pending'] ?? null;
    if (!is_array($pending) || empty($pending['user_id']) || empty($pending['email'])) {
        resetAddMessage('error', 'Sesi reset tidak ditemukan. Silakan ulangi dari awal.');
        resetRedirect('../page-reset-password.php');
    }

    $cooldownSeconds = max(10, (int)($otpConfig['resend_cooldown_seconds'] ?? 60));
    $lastSentAt = (int)($pending['last_otp_sent_at'] ?? 0);
    $elapsed = time() - $lastSentAt;
    if ($elapsed < $cooldownSeconds) {
        $waitSeconds = $cooldownSeconds - $elapsed;
        resetAddMessage('error', "Tunggu {$waitSeconds} detik sebelum kirim OTP lagi.");
        resetRedirect('../page-reset-password.php?step=otp');
    }

    try {
        $pdo = getDb();
        $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int)$pending['user_id']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        resetAddMessage('error', 'Gagal memproses kirim ulang OTP.');
        resetRedirect('../page-reset-password.php?step=otp');
    }

    if (!$user || strtolower((string)($user['email'] ?? '')) !== strtolower((string)$pending['email'])) {
        unset($_SESSION['reset_pending']);
        resetAddMessage('error', 'Akun tidak ditemukan. Silakan mulai ulang reset kata sandi.');
        resetRedirect('../page-reset-password.php');
    }

    if (shouldBypassOtpForUser($user, $otpBypassConfig)) {
        unset($_SESSION['reset_pending']);
        resetAddMessage('error', 'Akun demo tidak menggunakan fitur reset kata sandi OTP.');
        resetRedirect('../page-reset-password.php');
    }

    $otpResult = createOtpWithPurpose($pdo, $user, $otpConfig, 'password_reset');
    if (!$otpResult['ok']) {
        resetAddMessage('error', $otpResult['error']);
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $sendResult = sendOtpEmail(
        $mailConfig,
        (string)$user['email'],
        (string)$user['name'],
        $otpResult['otp_code'],
        $otpResult['ttl_minutes'],
        'password_reset'
    );

    if (!$sendResult['ok']) {
        $invalidate = $pdo->prepare('UPDATE login_email_otps SET used_at = NOW() WHERE id = :id');
        $invalidate->execute(['id' => (int)$otpResult['otp_id']]);

        resetAddMessage('error', $sendResult['error']);
        resetRedirect('../page-reset-password.php?step=otp');
    }

    $_SESSION['reset_pending']['otp_id'] = (int)$otpResult['otp_id'];
    $_SESSION['reset_pending']['last_otp_sent_at'] = time();

    resetAddMessage('success', 'Kode OTP baru sudah dikirim ke email Anda.');
    resetRedirect('../page-reset-password.php?step=otp');
}

/**
 * @param array<string, mixed> $otpConfig
 * @param array<string, mixed> $user
 * @return array{ok: bool, error: string, otp_id?: int, otp_code?: string, ttl_minutes?: int}
 */
function createOtpWithPurpose(PDO $pdo, array $user, array $otpConfig, string $purpose): array
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
            'purpose' => $purpose,
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
            'purpose' => $purpose,
            'max_attempts' => $maxAttempts,
            'expires_at' => $expiresAt,
            'sent_ip' => resetClientIp(),
            'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);

        $otpId = (int)$pdo->lastInsertId();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return ['ok' => false, 'error' => 'Gagal menyimpan OTP reset. Pastikan tabel login_email_otps sudah tersedia.'];
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

function resetClientIp(): ?string
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

function resetAddMessage(string $type, string $text): void
{
    $_SESSION['auth_messages'][] = [
        'type' => $type,
        'text' => $text,
    ];
}

function resetRedirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
