-- Tambah kolom user untuk korespondensi internal

ALTER TABLE `email_messages`
    ADD COLUMN IF NOT EXISTS `sender_user_id` bigint unsigned DEFAULT NULL AFTER `id`,
    ADD COLUMN IF NOT EXISTS `recipient_user_id` bigint unsigned DEFAULT NULL AFTER `sender_user_id`,
    ADD INDEX `email_messages_sender_user_index` (`sender_user_id`),
    ADD INDEX `email_messages_recipient_user_index` (`recipient_user_id`);
