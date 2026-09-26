-- -----------------------------------------------------
-- Schema untuk modul Pembayaran Klien CRM ANTARA
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `crm_payments` (
  `user_id` bigint unsigned NOT NULL,
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `invoice_no` varchar(30) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_position` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `client_avatar` varchar(255) DEFAULT NULL,
  `payment_method` varchar(100) NOT NULL,
  `channel` varchar(150) DEFAULT NULL,
  `reference_no` varchar(150) DEFAULT NULL,
  `paid_at` datetime NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) NOT NULL DEFAULT 'IDR',
  `status` varchar(20) NOT NULL DEFAULT 'settled',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `crm_payments_user_id_index` (`user_id`),
  KEY `crm_payments_invoice_id_foreign` (`invoice_id`),
  KEY `crm_payments_status_index` (`status`),
  KEY `crm_payments_paid_at_index` (`paid_at`),
  CONSTRAINT `crm_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `crm_product_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Data contoh pembayaran yang terhubung dengan tagihan produk
-- -----------------------------------------------------

INSERT INTO `crm_payments` (
  `user_id`,
  `invoice_id`, `invoice_no`, `client_name`, `client_position`, `company_name`, `client_avatar`,
  `payment_method`, `channel`, `reference_no`, `paid_at`, `amount`, `currency_code`, `status`, `notes`
) VALUES
(
  1,
  (SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0012'),
 'INV-2024-0012', 'Anthony Lewis', 'Kepala Program', 'PT Warta Global Media', 'assets/img/users/user-45.jpg',
 'Virtual Account', 'BCA VA 3910081', 'VA-20241202-AX12', '2024-12-02 10:45:00', 125000000.00, 'IDR', 'settled',
 'Pelunasan paket distribusi konten ASEAN Summit.'
),
(
  1,
  (SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0013'),
 'INV-2024-0013', 'Brian Villalobos', 'Koordinator Media', 'Kementerian Kominfo', 'assets/img/users/user-44.jpg',
 'Transfer Bank', 'Mandiri Kementerian', 'TRF-20241210-KOM', '2024-12-10 09:15:00', 25500000.00, 'IDR', 'partial',
 'Pembayaran termin 30% untuk paket data premium.'
),
(
  1,
  (SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0014'),
 'INV-2024-0014', 'Harvey Smith', 'Head of Corporate Affairs', 'PT Sinar Nusantara', 'assets/img/users/user-23.jpg',
 'Virtual Account', 'Mandiri VA 7755102', 'VA-20241126-SNS', '2024-11-26 16:20:00', 15000000.00, 'IDR', 'partial',
 'Uang muka produksi liputan KTT IKN.'
),
(
  1,
  (SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0015'),
 'INV-2024-0015', 'Lori Broaddus', 'VP Marketing', 'PT Digital Pratama', 'assets/img/users/user-01.jpg',
 'Credit Card', 'Midtrans CC', 'CC-20241220-DIGI', '2024-12-20 11:10:00', 5000000.00, 'IDR', 'pending',
 'Booking bundel distribusi Q1 2025, menunggu PO final.'
);

-- Sinkronkan nominal terbayar di tabel tagihan produk supaya dashboard tagihan & pembayaran konsisten
UPDATE crm_product_invoices i
LEFT JOIN (
    SELECT invoice_id, SUM(amount) AS paid
    FROM crm_payments
    WHERE status IN ('settled', 'partial')
    GROUP BY invoice_id
) p ON p.invoice_id = i.id
SET i.amount_paid = COALESCE(p.paid, 0),
    i.status = CASE
        WHEN COALESCE(p.paid, 0) >= i.total_amount THEN 'paid'
        WHEN COALESCE(p.paid, 0) > 0 AND i.status = 'draft' THEN 'pending'
        WHEN COALESCE(p.paid, 0) > 0 AND i.status = 'paid' THEN 'partial'
        ELSE i.status
    END,
    i.updated_at = NOW()
WHERE i.id IS NOT NULL;
