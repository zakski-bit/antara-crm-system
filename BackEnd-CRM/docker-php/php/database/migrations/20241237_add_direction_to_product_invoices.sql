-- Tambah kolom direction untuk membedakan tagihan keluar (ke mitra) dan masuk (dari mitra)

ALTER TABLE `crm_product_invoices`
    ADD COLUMN IF NOT EXISTS `direction` ENUM('outgoing','incoming') NOT NULL DEFAULT 'outgoing' AFTER `user_id`,
    ADD INDEX `crm_product_invoices_direction_index` (`direction`);

UPDATE `crm_product_invoices`
SET `direction` = 'outgoing'
WHERE `direction` IS NULL OR `direction` = '';
