<?php

if (!function_exists('authBoolEnv')) {
    function authBoolEnv(string $key, bool $default): bool
    {
        $raw = getenv($key);
        if ($raw === false || trim((string)$raw) === '') {
            return $default;
        }

        $normalized = strtolower(trim((string)$raw));
        if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
            return false;
        }

        return $default;
    }
}

if (!function_exists('authCsvEnv')) {
    /**
     * @return array<int, string>
     */
    function authCsvEnv(string $key, string $default = ''): array
    {
        $raw = getenv($key);
        $value = ($raw === false || trim((string)$raw) === '') ? $default : (string)$raw;

        $parts = array_filter(
            array_map('trim', explode(',', $value)),
            static fn(string $item): bool => $item !== ''
        );

        return array_values(array_unique(array_map(
            static fn(string $item): string => strtolower($item),
            $parts
        )));
    }
}

return [
    'app_name' => getenv('APP_NAME') ?: 'ANTARA CRM',
    'otp' => [
        'length' => (int)(getenv('OTP_LENGTH') ?: 6),
        'ttl_minutes' => (int)(getenv('OTP_TTL_MINUTES') ?: 5),
        'max_attempts' => (int)(getenv('OTP_MAX_ATTEMPTS') ?: 5),
        'resend_cooldown_seconds' => (int)(getenv('OTP_RESEND_COOLDOWN_SECONDS') ?: 60),
        'secret' => getenv('OTP_SECRET') ?: 'ganti-random-secret-min-32-char',
    ],
    'otp_bypass' => [
        // Dipakai untuk akun demo agar bisa login tanpa OTP email.
        'enabled' => authBoolEnv('OTP_BYPASS_ENABLED', true),
        'emails' => authCsvEnv('OTP_BYPASS_EMAILS', ''),
        'domains' => authCsvEnv('OTP_BYPASS_DOMAINS', 'antara.local'),
        'roles' => authCsvEnv('OTP_BYPASS_ROLES', ''),
    ],
    'mail' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => (int)(getenv('SMTP_PORT') ?: 587),
        'username' => getenv('SMTP_USERNAME') ?: 'zakiabdussalamal@gmail.com',
        'password' => getenv('SMTP_PASSWORD') ?: 'ecthzurxygqvijdw', // app password tanpa spasi
        'encryption' => strtolower((string)(getenv('SMTP_ENCRYPTION') ?: 'tls')),
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'zakiabdussalamal@gmail.com',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'ANTARA CRM OTP',
    ],
];
