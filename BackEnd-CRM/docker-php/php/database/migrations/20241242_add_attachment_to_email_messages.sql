-- Tambah kolom lampiran untuk korespondensi

ALTER TABLE `email_messages`
    ADD COLUMN IF NOT EXISTS `attachment_path` varchar(255) DEFAULT NULL AFTER `has_attachments`;
