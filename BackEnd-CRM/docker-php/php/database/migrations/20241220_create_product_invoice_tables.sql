-- -----------------------------------------------------
-- Schema untuk modul Tagihan Produk CRM ANTARA
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `crm_product_invoices` (
  `user_id` bigint unsigned NOT NULL,
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL,
  `invoice_title` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_company` varchar(255) DEFAULT NULL,
  `client_position` varchar(255) DEFAULT NULL,
  `client_email` varchar(255) DEFAULT NULL,
  `client_phone` varchar(50) DEFAULT NULL,
  `client_avatar` varchar(255) DEFAULT NULL,
  `issue_date` datetime NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `currency_code` varchar(10) NOT NULL DEFAULT 'IDR',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text,
  `terms` text,
  `reference_no` varchar(100) DEFAULT NULL,
  `channel` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_product_invoices_invoice_no_unique` (`invoice_no`),
  KEY `crm_product_invoices_status_index` (`status`),
  KEY `crm_product_invoices_issue_date_index` (`issue_date`),
  KEY `crm_product_invoices_user_id_index` (`user_id`),
  CONSTRAINT `crm_product_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `crm_product_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `line_order` int NOT NULL DEFAULT 0,
  `product_name` varchar(255) NOT NULL,
  `description` text,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `crm_product_invoice_items_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `crm_product_invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `crm_product_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Data contoh untuk dashboard Tagihan Produk
-- -----------------------------------------------------

INSERT INTO `crm_product_invoices` (
  `user_id`,
  `invoice_no`, `invoice_title`, `client_name`, `client_company`, `client_position`,
  `client_email`, `client_phone`, `client_avatar`,
  `issue_date`, `due_date`, `status`, `currency_code`,
  `total_amount`, `amount_paid`, `notes`, `terms`, `reference_no`, `channel`
) VALUES
(
  1,
  'INV-2024-0012', 'Distribusi Konten ASEAN Summit', 'Anthony Lewis', 'PT Warta Global Media', 'Kepala Program',
 'anthony@example.com', '+62 812 8899 1234', 'assets/img/users/user-45.jpg',
 '2024-12-01 09:15:00', '2024-12-15 17:00:00', 'paid', 'IDR',
 125000000.00, 125000000.00, 'Tagihan layanan distribusi konten ASEAN Summit dan monitoring newsroom.', 'Pembayaran melalui virtual account dalam 14 hari.', 'PO-AXS-9812', 'Produk Komersial'
),
(
  1,
  'INV-2024-0013', 'Langganan ANTARA Data Premium', 'Brian Villalobos', 'Kementerian Kominfo', 'Koordinator Media',
 'brian@example.com', '+62 821 4455 6677', 'assets/img/users/user-44.jpg',
 '2024-12-05 11:30:00', '2024-12-28 18:00:00', 'pending', 'IDR',
 85000000.00, 0.00, 'Langganan paket data real time untuk kanal kementerian.', 'Termin 30/70, pembayaran awal 30%.', 'PO-KOM-5542', 'Layanan Data'
),
(
  1,
  'INV-2024-0014', 'Liputan Khusus KTT IKN', 'Harvey Smith', 'PT Sinar Nusantara', 'Head of Corporate Affairs',
 'harvey@example.com', '+62 813 2221 4099', 'assets/img/users/user-23.jpg',
 '2024-11-25 15:45:00', '2024-11-28 15:00:00', 'overdue', 'IDR',
 64000000.00, 15000000.00, 'Produksi paket liputan eksklusif dan penyewaan satelit SNG.', 'Pelunasan maksimal 7 hari setelah penayangan.', 'PO-SNS-8831', 'Produksi Broadcast'
),
(
  1,
  'INV-2024-0015', 'Bundel Distribusi Press Release 2025', 'Lori Broaddus', 'PT Digital Pratama', 'VP Marketing',
 'broaddus@example.com', '+62 811 8321 900', 'assets/img/users/user-01.jpg',
 '2024-12-18 10:00:00', '2025-01-10 17:00:00', 'draft', 'IDR',
 32000000.00, 0.00, 'Bundel distribusi press release bulanan Q1 2025.', 'Draft internal, menunggu konfirmasi PO.', 'PO-DIGI-7721', 'Produk Kemitraan'
);

INSERT INTO `crm_product_invoice_items` (
  `invoice_id`, `line_order`, `product_name`, `description`, `quantity`, `unit_price`, `discount_percent`, `line_total`
) VALUES
((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0012'), 1, 'Distribusi konten multikanal', 'Distribusi 12 rilis selama event ASEAN Summit.', 12.00, 6000000.00, 0.00, 72000000.00),
((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0012'), 2, 'Monitoring newsroom realtime', 'Dashboard performa kanal TV & digital.', 1.00, 53000000.00, 0.00, 53000000.00),

((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0013'), 1, 'Langganan data premium', 'Integrasi API ANTARA Data Premium (3 kanal).', 3.00, 15000000.00, 0.00, 45000000.00),
((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0013'), 2, 'Pelatihan redaksi digital', 'Workshop 2 hari untuk tim humas.', 2.00, 20000000.00, 5.00, 38000000.00),

((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0014'), 1, 'Produksi paket TV', 'Liputan eksklusif & editing full HD.', 4.00, 8000000.00, 0.00, 32000000.00),
((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0014'), 2, 'Sewa satelit SNG', 'Sewa 2 hari + kru teknis.', 2.00, 16000000.00, 0.00, 32000000.00),

((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0015'), 1, 'Distribusi press release', 'Distribusi ke 250 kanal nasional/regional.', 3.00, 8000000.00, 0.00, 24000000.00),
((SELECT id FROM `crm_product_invoices` WHERE invoice_no = 'INV-2024-0015'), 2, 'Copywriting newsroom', 'Penulisan konten premium oleh desk ekonomi.', 1.00, 8000000.00, 0.00, 8000000.00);
