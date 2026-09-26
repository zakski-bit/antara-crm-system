CREATE DATABASE IF NOT EXISTS `antara_crm`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `antara_crm`;

CREATE TABLE IF NOT EXISTS `clients` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `clients_username_unique` (`username`),
  UNIQUE KEY `clients_email_unique` (`email`),
  UNIQUE KEY `clients_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients`
  (`code`, `first_name`, `last_name`, `name`, `username`, `email`, `company`, `phone`, `status`, `job_title`, `position`, `password_hash`, `avatar_path`, `project_name`, `project_progress`, `notes`)
VALUES
  ('CLI-001', 'Michael', 'Walker', 'Michael Walker', 'michael.walker', 'michael@example.com', 'BrightWave Innovations', '(163) 2459 315', 'active', 'CEO', 'Executive', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-39.jpg', 'Office Management App', 60, '{\"seed\":true}'),
  ('CLI-002', 'Olivia', 'Coleman', 'Olivia Coleman', 'olivia.coleman', 'olivia@example.com', 'InFusionSoft', '(132) 4568 951', 'active', 'Professor', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-32.jpg', 'Campus Portal Revamp', 80, '{\"seed\":true}'),
  ('CLI-003', 'Eleanor', 'Baker', 'Eleanor Baker', 'eleanor.baker', 'eleanor@example.com', 'Stone Clinic', '(123) 4745 325', 'prospect', 'Behavioral Specialist', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-26.jpg', 'Mental Health Tracker', 45, '{\"seed\":true}'),
  ('CLI-004', 'Elijah', 'Brown', 'Elijah Brown', 'elijah.brown', 'elijah@example.com', 'Spotless Services', '(135) 4258 895', 'inactive', 'Web Developer', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-02.jpg', NULL, NULL, '{\"seed\":true}'),
  ('CLI-005', 'Amelia', 'Lawson', 'Amelia Lawson', 'amelia.lawson', 'amelia@example.com', 'The Bright Agency', '(132) 3254 395', 'active', 'Graphic Designer', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-03.jpg', 'Brand Refresh', 95, '{\"seed\":true}'),
  ('CLI-006', 'Lucas', 'Turner', 'Lucas Turner', 'lucas.turner', 'lucas@example.com', 'Vista Systems', '(123) 3456 951', 'active', 'Database Administrator', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-30.jpg', 'Data Warehouse', 30, '{\"seed\":true}'),
  ('CLI-007', 'Jackie', 'Myers', 'Jackie Myers', 'jackie.myers', 'jackie@example.com', 'GearUp Fitness', '(132) 4659 853', 'prospect', 'Developer', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-03.jpg', 'Member Mobile App', 15, '{\"seed\":true}'),
  ('CLI-008', 'Scarlett', 'Johnson', 'Scarlett Johnson', 'scarlett.johnson', 'scarlett@example.com', 'FreshMart Supermarket', '(153) 4568 951', 'active', 'Chemical Engineer', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-35.jpg', 'Supply Chain AI', 52, '{\"seed\":true}'),
  ('CLI-009', 'Stephen', 'Dias', 'Stephen Dias', 'stephen.dias', 'stephen@example.com', 'Nimbus Networks', '(175) 2496 125', 'active', 'CEO', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-48.jpg', 'IoT Rollout', 70, '{\"seed\":true}'),
  ('CLI-010', 'Angela', 'Thomas', 'Angela Thomas', 'angela.thomas', 'angela@example.com', 'Epicurean Delights', '(132) 3145 977', 'active', 'Consultant', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-43.jpg', 'Culinary Marketplace', 40, '{\"seed\":true}');
