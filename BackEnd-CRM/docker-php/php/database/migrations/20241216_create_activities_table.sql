CREATE TABLE IF NOT EXISTS `activities` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `activity_type` ENUM('meeting', 'calls', 'email', 'task') NOT NULL DEFAULT 'meeting',
    `due_date` DATE NOT NULL,
    `due_time` TIME NULL DEFAULT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
