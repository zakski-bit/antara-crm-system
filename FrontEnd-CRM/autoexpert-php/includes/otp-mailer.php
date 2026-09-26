<?php

require_once __DIR__ . '/phpmailer/class.phpmailer.php';
require_once __DIR__ . '/phpmailer/class.smtp.php';

/**
 * @param array<string, mixed> $mailConfig
 * @return array{ok: bool, error: string}
 */
function sendOtpEmail(
    array $mailConfig,
    string $toEmail,
    string $toName,
    string $otpCode,
    int $ttlMinutes,
    string $context = 'login'
): array
{
    $host = trim((string)($mailConfig['host'] ?? ''));
    $username = trim((string)($mailConfig['username'] ?? ''));
    $password = (string)($mailConfig['password'] ?? '');
    $fromEmail = trim((string)($mailConfig['from_email'] ?? ''));
    $fromName = trim((string)($mailConfig['from_name'] ?? 'ANTARA CRM'));
    $port = (int)($mailConfig['port'] ?? 587);
    $encryption = strtolower(trim((string)($mailConfig['encryption'] ?? 'tls')));

    if ($host === '' || $username === '' || $password === '' || $fromEmail === '') {
        return [
            'ok' => false,
            'error' => 'SMTP belum dikonfigurasi. Isi SMTP_HOST, SMTP_USERNAME, SMTP_PASSWORD, dan SMTP_FROM_EMAIL.',
        ];
    }

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Port = $port;
    $mail->CharSet = 'UTF-8';

    if ($encryption === 'ssl') {
        $mail->SMTPSecure = 'ssl';
    } else {
        $mail->SMTPSecure = 'tls';
    }

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, $toName !== '' ? $toName : $toEmail);
    $mail->isHTML(true);

    $recipientName = $toName !== '' ? $toName : $toEmail;
    $safeRecipientName = htmlspecialchars($recipientName, ENT_QUOTES, 'UTF-8');
    $safeOtp = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');

    if ($context === 'password_reset') {
        $mail->Subject = 'Kode OTP Reset Kata Sandi';
        $mail->Body = sprintf(
            '<p>Halo %s,</p><p>Kode OTP untuk reset kata sandi akun Anda:</p><h2 style="letter-spacing:4px;">%s</h2><p>Kode berlaku %d menit.</p><p>Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>',
            $safeRecipientName,
            $safeOtp,
            $ttlMinutes
        );
        $mail->AltBody = sprintf(
            "Halo %s,\n\nKode OTP reset kata sandi Anda: %s\nBerlaku %d menit.\nJika Anda tidak meminta reset kata sandi, abaikan email ini.",
            $recipientName,
            $otpCode,
            $ttlMinutes
        );
    } else {
        $mail->Subject = 'Kode OTP Login';
        $mail->Body = sprintf(
            '<p>Halo %s,</p><p>Kode OTP login Anda adalah:</p><h2 style="letter-spacing:4px;">%s</h2><p>Kode berlaku %d menit.</p><p>Jika ini bukan Anda, abaikan email ini.</p>',
            $safeRecipientName,
            $safeOtp,
            $ttlMinutes
        );
        $mail->AltBody = sprintf(
            "Halo %s,\n\nKode OTP login Anda: %s\nBerlaku %d menit.\nJika ini bukan Anda, abaikan email ini.",
            $recipientName,
            $otpCode,
            $ttlMinutes
        );
    }

    if (!$mail->send()) {
        return [
            'ok' => false,
            'error' => 'Gagal mengirim email OTP: ' . $mail->ErrorInfo,
        ];
    }

    return ['ok' => true, 'error' => ''];
}
