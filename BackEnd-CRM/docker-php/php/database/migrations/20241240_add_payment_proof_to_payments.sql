-- Tambah kolom bukti pembayaran agar klien bisa mengirim bukti transfer

ALTER TABLE `crm_payments`
    ADD COLUMN IF NOT EXISTS `payment_proof_path` varchar(255) DEFAULT NULL AFTER `notes`;
