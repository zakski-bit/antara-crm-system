-- Ubah unique constraint invoice_no menjadi per user

ALTER TABLE `crm_product_invoices`
    DROP INDEX `crm_product_invoices_invoice_no_unique`,
    ADD UNIQUE KEY `crm_product_invoices_user_invoice_unique` (`user_id`, `invoice_no`);
