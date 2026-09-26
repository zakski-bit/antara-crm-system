CREATE TABLE IF NOT EXISTS auth_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY auth_tokens_token_unique (token),
    KEY auth_tokens_user_idx (user_id),
    KEY auth_tokens_expires_idx (expires_at),
    CONSTRAINT auth_tokens_user_fk
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_email_otps (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(150) NOT NULL,
    otp_hash CHAR(64) NOT NULL,
    purpose VARCHAR(32) NOT NULL DEFAULT 'login',
    attempt_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 5,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sent_ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    KEY login_email_otps_user_idx (user_id),
    KEY login_email_otps_email_idx (email),
    KEY login_email_otps_expires_idx (expires_at),
    KEY login_email_otps_purpose_idx (purpose),
    CONSTRAINT login_email_otps_user_fk
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
