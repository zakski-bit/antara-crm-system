-- Tambah kolom user_id untuk memisahkan data per pengguna pada modul CRM lainnya

ALTER TABLE `crm_leads`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_leads_user_id_index` (`user_id`);

UPDATE `crm_leads` SET `user_id` = 1 WHERE `user_id` IS NULL OR `user_id` = 0;

ALTER TABLE `crm_deals`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_deals_user_id_index` (`user_id`);

UPDATE `crm_deals` SET `user_id` = 1 WHERE `user_id` IS NULL OR `user_id` = 0;

ALTER TABLE `crm_pipeline_entries`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_pipeline_entries_user_id_index` (`user_id`);

UPDATE `crm_pipeline_entries` SET `user_id` = 1 WHERE `user_id` IS NULL OR `user_id` = 0;

ALTER TABLE `crm_estimates`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `crm_estimates_user_id_index` (`user_id`);

UPDATE `crm_estimates` SET `user_id` = 1 WHERE `user_id` IS NULL OR `user_id` = 0;

ALTER TABLE `clients`
  ADD COLUMN IF NOT EXISTS `user_id` bigint unsigned NOT NULL DEFAULT 1 AFTER `id`,
  ADD INDEX `clients_user_id_index` (`user_id`);

UPDATE `clients` SET `user_id` = 1 WHERE `user_id` IS NULL OR `user_id` = 0;
