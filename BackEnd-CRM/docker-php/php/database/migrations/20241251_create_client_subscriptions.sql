-- Langganan klien / pelanggan

CREATE TABLE IF NOT EXISTS `crm_client_subscriptions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL, -- owner (admin/pegawai) pencatat
    `customer_user_id` BIGINT UNSIGNED DEFAULT NULL, -- akun login pelanggan (users.id)
    `client_id` INT UNSIGNED DEFAULT NULL, -- relasi ke tabel clients (id = INT UNSIGNED)
    `plan_name` VARCHAR(190) NOT NULL,
    `plan_code` VARCHAR(100) DEFAULT NULL,
    `cycle` VARCHAR(50) NOT NULL DEFAULT 'monthly',
    `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `currency_code` VARCHAR(10) NOT NULL DEFAULT 'IDR',
    `status` VARCHAR(50) NOT NULL DEFAULT 'active',
    `started_at` DATETIME DEFAULT NULL,
    `renewal_at` DATETIME DEFAULT NULL,
    `ends_at` DATETIME DEFAULT NULL,
    `metadata` JSON NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `crm_client_subscriptions_user_idx` (`user_id`),
    KEY `crm_client_subscriptions_customer_idx` (`customer_user_id`),
    KEY `crm_client_subscriptions_client_idx` (`client_id`),
    KEY `crm_client_subscriptions_status_idx` (`status`),
    KEY `crm_client_subscriptions_cycle_idx` (`cycle`),
    CONSTRAINT `crm_client_subscriptions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `crm_client_subscriptions_customer_fk` FOREIGN KEY (`customer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `crm_client_subscriptions_client_fk` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contoh data awal (opsional)
INSERT INTO `crm_client_subscriptions`
(`user_id`, `customer_user_id`, `client_id`, `plan_name`, `plan_code`, `cycle`, `amount`, `currency_code`, `status`, `started_at`, `renewal_at`, `metadata`, `notes`)
VALUES
    (1, NULL, 3, 'Paket Distribusi Konten', 'ANTARA-DISTRO', 'monthly', 15000000.00, 'IDR', 'active', NOW() - INTERVAL 15 DAY, NOW() + INTERVAL 15 DAY, JSON_OBJECT('channels', 'TV + Digital'), 'Sample untuk demo'),
    (1, NULL, 4, 'Paket Data Premium', 'ANTARA-DATA', 'yearly', 120000000.00, 'IDR', 'trial', NOW() - INTERVAL 10 DAY, NOW() + INTERVAL 20 DAY, JSON_OBJECT('api_quota', '250k'), 'Trial 30 hari');
