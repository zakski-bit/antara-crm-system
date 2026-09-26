CREATE TABLE IF NOT EXISTS `mitra_partners` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(255) NOT NULL,
    `industri` VARCHAR(255) NOT NULL,
    `kontak` VARCHAR(255) NULL,
    `email` VARCHAR(255) NOT NULL,
    `telepon` VARCHAR(50) NOT NULL,
    `status` VARCHAR(100) NOT NULL,
    `alamat` VARCHAR(255) NOT NULL,
    `logo_path` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `mitra_partners_nama_index` (`nama`),
    INDEX `mitra_partners_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mitra_partners` (`nama`, `industri`, `kontak`, `email`, `telepon`, `status`, `alamat`, `logo_path`, `created_at`, `updated_at`) VALUES
('ACN Newswire', 'Distribusi Rilis Pers', 'Naoko Sato', 'support@acnnewswire.com', '+65 6788 8670', 'Media Distribution', '1 Raffles Place, Singapore', 'assets/img/mitra/logo-acnnewswire.png', NOW(), NOW()),
('AFP', 'Kantor Berita Internasional', 'Jean Dupont', 'partnership@afp.com', '+33 1 40 41 46 46', 'Editorial Partner', '11 Place de la Bourse, Paris', 'assets/img/mitra/logo-afp.png', NOW(), NOW()),
('AP News', 'Kantor Berita Internasional', 'Michael Turner', 'partners@ap.org', '+1 212-621-1500', 'Editorial Partner', '200 Liberty Street, New York', 'assets/img/mitra/logo-ap-news.png', NOW(), NOW()),
('AsiaNet', 'Distribusi Berita Asia Pasifik', 'Priya Menon', 'info@asianetnews.net', '+61 2 9322 8659', 'Media Distribution', 'Sydney, Australia', 'assets/img/mitra/logo-asianet.png', NOW(), NOW()),
('Bernama', 'Kantor Berita Malaysia', 'Nur Aisyah', 'info@bernama.com', '+60 3 2693 9933', 'Editorial Partner', 'No. 28 Jalan Yap Kwan Seng, Kuala Lumpur', 'assets/img/mitra/logo-bernama.png', NOW(), NOW()),
('Bloomberg', 'Media & Data Finansial', 'Samantha Lee', 'business@bloomberg.net', '+1 212-318-2000', 'Strategic Alliance', '731 Lexington Avenue, New York', 'assets/img/mitra/logo-bloomberg.png', NOW(), NOW()),
('Agencia EFE', 'Kantor Berita Spanyol', 'Carlos Gutierrez', 'comercial@efe.com', '+34 91 347 82 00', 'Editorial Partner', 'Avenida de Burgos 8B, Madrid', 'assets/img/mitra/logo-efe.png', NOW(), NOW()),
('Finsoft', 'Solusi Teknologi Finansial', 'Ardi Prabowo', 'hello@finsoft.io', '+62 21 555 9911', 'Technology Partner', 'SCBD, Jakarta', 'assets/img/mitra/logo-finsoft.png', NOW(), NOW()),
('HCM Ads Media', 'Jaringan Media Asia Tenggara', 'Tran Thi Hoa', 'partners@hcmedia.vn', '+84 28 3821 9922', 'Commercial Partner', 'Ho Chi Minh City, Vietnam', 'assets/img/mitra/logo-hcm-ads-media.png', NOW(), NOW()),
('Kyodo News', 'Kantor Berita Jepang', 'Kenji Nakamura', 'global@kyodonews.jp', '+81 3-6252-8400', 'Editorial Partner', 'Tokyo, Jepang', 'assets/img/mitra/logo-kyodo-news.png', NOW(), NOW()),
('OANA', 'Aliansi Kantor Berita Asia Pasifik', 'Linh Wirawan', 'secretariat@oananews.org', '+60 3 2693 9933', 'Network Member', 'Kuala Lumpur, Malaysia', 'assets/img/mitra/logo-oana.png', NOW(), NOW()),
('Reuters', 'Media & Informasi Global', 'Emma Johnson', 'partner.sales@reuters.com', '+44 20 7542 8313', 'Strategic Alliance', '5 Canada Square, London', 'assets/img/mitra/logo-reuters.png', NOW(), NOW()),
('SevenCyber', 'Keamanan Siber', 'Bima Arista', 'contact@sevencyber.id', '+62 21 7788 9900', 'Security Partner', 'BSD City, Tangerang', 'assets/img/mitra/logo-sevencyber.png', NOW(), NOW()),
('Sputnik', 'Kantor Berita Rusia', 'Sergey Petrov', 'world@sputniknews.com', '+7 495 139 62 70', 'Editorial Partner', 'Zubovskaya St. 4, Moscow', 'assets/img/mitra/logo-sputnik.png', NOW(), NOW()),
('TTXVN', 'Kantor Berita Vietnam', 'Nguyen Van Minh', 'info@vnanet.vn', '+84 24 3825 4313', 'Editorial Partner', '5 Ly Thuong Kiet, Hanoi', 'assets/img/mitra/logo-ttxvn.png', NOW(), NOW()),
('Xinhua News Agency', 'Kantor Berita China', 'Li Wei', 'service@mail.xinhuanet.com', '+86 10 6307 3666', 'Editorial Partner', '57 Xuanwumen Xidajie, Beijing', 'assets/img/mitra/logo-xinhua-news-agency.png', NOW(), NOW());
