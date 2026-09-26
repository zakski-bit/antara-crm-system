-- Tambah kolom nama lampiran asli

ALTER TABLE `email_messages`
    ADD COLUMN IF NOT EXISTS `attachment_name` varchar(255) DEFAULT NULL AFTER `attachment_path`;
