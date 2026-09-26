-- Tambahan kolom verifikasi pembayaran agar bukti transfer pelanggan
-- harus diverifikasi admin/pegawai sebelum dianggap diterima.

ALTER TABLE `crm_payments`
    ADD COLUMN IF NOT EXISTS `verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved' AFTER `status`,
    ADD COLUMN IF NOT EXISTS `verified_by_user_id` bigint unsigned DEFAULT NULL AFTER `verification_status`,
    ADD COLUMN IF NOT EXISTS `verified_at` datetime DEFAULT NULL AFTER `verified_by_user_id`,
    ADD COLUMN IF NOT EXISTS `verification_notes` text DEFAULT NULL AFTER `verified_at`;

ALTER TABLE `crm_payments`
    ADD INDEX `crm_payments_verification_status_index` (`verification_status`),
    ADD INDEX `crm_payments_verified_by_user_id_index` (`verified_by_user_id`);

UPDATE `crm_payments`
SET
    `verification_status` = COALESCE(`verification_status`, 'approved'),
    `verified_at` = COALESCE(`verified_at`, `updated_at`, `created_at`);
