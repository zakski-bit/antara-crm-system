-- Reset table to allow re-import without manual cleanup
DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `code` VARCHAR(32) DEFAULT NULL,
    `first_name` VARCHAR(80) NOT NULL,
    `last_name` VARCHAR(120) DEFAULT NULL,
    `name` VARCHAR(201) NOT NULL,
    `username` VARCHAR(120) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `company` VARCHAR(190) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `status` VARCHAR(30) NOT NULL DEFAULT 'active',
    `job_title` VARCHAR(120) DEFAULT NULL,
    `position` VARCHAR(120) DEFAULT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `avatar_path` VARCHAR(255) DEFAULT NULL,
    `project_name` VARCHAR(190) DEFAULT NULL,
    `project_progress` TINYINT UNSIGNED DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `clients_user_id_idx` (`user_id`),
    UNIQUE KEY `clients_username_unique` (`username`),
    UNIQUE KEY `clients_email_unique` (`email`),
    UNIQUE KEY `clients_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients`
    (`id`, `user_id`, `code`, `first_name`, `last_name`, `name`, `username`, `email`, `company`, `phone`, `status`, `job_title`, `position`, `password_hash`, `avatar_path`, `project_name`, `project_progress`, `notes`)
VALUES
    (1, 1, 'CLI-ADM', 'Administrator', '', 'Administrator', 'admin', 'admin@antara.local', 'PT ANTARA', '+62 812 0000 0001', 'active', 'Administrator', 'Executive', '$2y$10$98zUbY8l8LLR5irCfP/BGevT2JwoG.t6YnEEqxtO2E0S19iXdwHnq', 'assets/img/users/user-01.jpg', 'Admin Account', 100, NULL),
    (2, 1, 'CLI-RED', 'Pegawai', 'Redaksi', 'Pegawai Redaksi', 'pegawai.redaksi', 'pegawai@antara.local', 'PT ANTARA', '+62 812 0000 0002', 'active', 'Editor', 'Staff', '$2y$10$9EZZbugleC1IbewMdVzYPe6mbpQnflJ2jh3OT0y2hKuxn5AWgTg3K', 'assets/img/users/user-02.jpg', 'Redaksi Account', 90, NULL),
    (3, 1, 'CLI-PEL', 'Pelanggan', 'Mitra', 'Pelanggan Mitra', 'pelanggan.mitra', 'pelanggan@antara.local', 'PT ANTARA', '+62 812 0000 0003', 'active', 'Client', 'Client', '$2y$10$wbB2pYMOPH1kW.YQF3CkVOuX2wvvnHsOHSbPMcOmZDDr.vp6EyUAO', 'assets/img/users/user-03.jpg', 'Pelanggan Mitra', 80, NULL),
    (4, 1, 'CLI-ALPHA', 'Klien', 'Alpha', 'Klien Alpha', 'alpha.client', 'alpha.client@antara.local', 'PT ANTARA', '+62 812 0000 0004', 'active', 'Client', 'Client', '$2y$10$98zIXUNpj0r00GfOcUJmEuo.TnyMZrTBTsGLIO5l4HGFQ/OQUtmEy', 'assets/img/users/user-04.jpg', 'Project Alpha', 70, NULL),
    (5, 1, 'CLI-BETA', 'Klien', 'Beta', 'Klien Beta', 'beta.client', 'beta.client@antara.local', 'PT ANTARA', '+62 812 0000 0005', 'active', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-05.jpg', 'Project Beta', 60, NULL),
    (6, 1, 'CLI-GAMMA', 'Klien', 'Gamma', 'Klien Gamma', 'gamma.client', 'gamma.client@antara.local', 'PT ANTARA', '+62 812 0000 0006', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-06.jpg', 'Project Gamma', 50, NULL),
    (7, 1, 'CLI-DELTA', 'Klien', 'Delta', 'Klien Delta', 'delta.client', 'delta.client@antara.local', 'PT ANTARA', '+62 812 0000 0007', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-07.jpg', 'Project Delta', 40, NULL),
    (8, 1, 'CLI-EPS', 'Klien', 'Epsilon', 'Klien Epsilon', 'epsilon.client', 'epsilon.client@antara.local', 'PT ANTARA', '+62 812 0000 0008', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-08.jpg', 'Project Epsilon', 30, NULL)
ON DUPLICATE KEY UPDATE
    user_id = VALUES(user_id),
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    name = VALUES(name),
    username = VALUES(username),
    email = VALUES(email),
    company = VALUES(company),
    phone = VALUES(phone),
    status = VALUES(status),
    job_title = VALUES(job_title),
    position = VALUES(position),
    password_hash = VALUES(password_hash),
    avatar_path = VALUES(avatar_path),
    project_name = VALUES(project_name),
    project_progress = VALUES(project_progress),
    notes = VALUES(notes),
    updated_at = CURRENT_TIMESTAMP;
