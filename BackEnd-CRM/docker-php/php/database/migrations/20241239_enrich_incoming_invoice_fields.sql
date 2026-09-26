-- Field tambahan untuk Tagihan dari Mitra (incoming)

ALTER TABLE `crm_product_invoices`
    ADD COLUMN IF NOT EXISTS `vendor_name` varchar(255) DEFAULT NULL AFTER `client_company`,
    ADD COLUMN IF NOT EXISTS `period_label` varchar(120) DEFAULT NULL AFTER `vendor_name`,
    ADD COLUMN IF NOT EXISTS `subtotal_amount` decimal(15,2) DEFAULT NULL AFTER `amount_paid`,
    ADD COLUMN IF NOT EXISTS `tax_amount` decimal(15,2) DEFAULT NULL AFTER `subtotal_amount`,
    ADD COLUMN IF NOT EXISTS `grand_total` decimal(15,2) DEFAULT NULL AFTER `tax_amount`,
    ADD COLUMN IF NOT EXISTS `received_at` datetime DEFAULT NULL AFTER `due_date`,
    ADD COLUMN IF NOT EXISTS `verification_status` enum('pending','approved','rejected') DEFAULT 'pending' AFTER `received_at`,
    ADD COLUMN IF NOT EXISTS `payment_status` enum('pending','processing','settled') DEFAULT 'pending' AFTER `verification_status`,
    ADD COLUMN IF NOT EXISTS `attachment_path` varchar(255) DEFAULT NULL AFTER `payment_status`,
    ADD COLUMN IF NOT EXISTS `payment_proof_path` varchar(255) DEFAULT NULL AFTER `attachment_path`,
    ADD COLUMN IF NOT EXISTS `supporting_docs` json DEFAULT NULL AFTER `payment_proof_path`;
