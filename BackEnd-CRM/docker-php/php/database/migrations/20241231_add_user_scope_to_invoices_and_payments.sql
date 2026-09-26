-- -----------------------------------------------------
-- Tambah kolom user_id agar data tagihan & pembayaran terikat ke akun pemilik
-- Jalankan ini jika database sudah terlanjur dibuat tanpa kolom user_id.
-- -----------------------------------------------------

ALTER TABLE `crm_product_invoices`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_product_invoices_user_id_index` (`user_id`);

UPDATE `crm_product_invoices`
SET `user_id` = 1
WHERE `user_id` IS NULL OR `user_id` = 0;

ALTER TABLE `crm_payments`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_payments_user_id_index` (`user_id`);

UPDATE `crm_payments` p
LEFT JOIN `crm_product_invoices` i ON p.invoice_id = i.id
SET p.user_id = COALESCE(i.user_id, 1)
WHERE p.user_id IS NULL OR p.user_id = 0;
