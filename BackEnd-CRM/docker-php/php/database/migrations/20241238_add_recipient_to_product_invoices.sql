-- Tambah kolom penerima agar admin/pegawai bisa mengarahkan tagihan ke klien tertentu

ALTER TABLE `crm_product_invoices`
    ADD COLUMN IF NOT EXISTS `recipient_user_id` bigint unsigned DEFAULT NULL AFTER `user_id`,
    ADD INDEX `crm_product_invoices_recipient_user_index` (`recipient_user_id`);
