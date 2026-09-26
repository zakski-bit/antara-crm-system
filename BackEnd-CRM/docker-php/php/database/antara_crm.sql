-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Des 2025 pada 03.53
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `antara_crm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_type` enum('meeting','calls','email','task') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'meeting',
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `activities`
--

INSERT INTO `activities` (`id`, `title`, `activity_type`, `due_date`, `due_time`, `owner`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Briefing liputan bersama Dishub DKI', 'meeting', '2025-11-13', '09:00:00', 'Farah Laksmi', 'Koordinasikan rundown liputan pagi dan daftar narasumber Dishub.', '2025-11-12 02:41:56', '2025-11-12 02:41:56'),
(3, 'Follow up sponsorship Telkomsel', 'calls', '2025-11-15', '15:30:00', 'Rudi Hartono', 'Tanyakan kepastian jadwal negosiasi minggu depan.', '2025-11-12 02:41:56', '2025-11-12 02:41:56'),
(5, 'CLI ddmmy', 'task', '2025-11-12', '09:45:00', 'CLI', 'dd/mm and dot time', '2025-11-12 02:54:45', '2025-11-12 02:54:45'),
(6, 'adaadada', 'calls', '2025-06-05', '07:06:00', 'saya zaki', 'letsgoa', '2025-11-12 03:05:05', '2025-11-12 03:06:44'),
(7, 'ada', 'meeting', '2025-10-27', '14:36:00', 'adadad', 'adadadada', '2025-11-12 03:36:16', '2025-11-12 03:36:16'),
(8, 'zczxz', 'email', '2025-11-11', '12:40:00', 'zzxcz', 'zxczxcz', '2025-11-12 03:36:39', '2025-11-12 03:36:39'),
(9, 'qweqw', 'email', '2025-10-28', '13:40:00', 'qwewqeq', 'qweqewq', '2025-11-12 03:37:09', '2025-11-12 03:37:09'),
(10, 'asada', 'task', '2025-11-02', '14:40:00', 'asadsa', 'adsadadsa', '2025-11-12 03:37:26', '2025-11-12 03:37:26'),
(12, 'asdada', 'calls', '2025-11-05', '10:41:00', 'asdada', 'asdasdas', '2025-11-12 03:38:12', '2025-11-12 03:38:12'),
(13, 'asdasdas', 'calls', '2025-11-11', '12:38:00', 'adadad', 'aavsvfdvdfv', '2025-11-12 03:38:27', '2025-11-12 03:38:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `clients`
--

CREATE TABLE `clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(201) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `job_title` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_progress` tinyint(3) UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `clients`
--

INSERT INTO `clients` (`id`, `user_id`, `code`, `first_name`, `last_name`, `name`, `username`, `email`, `company`, `phone`, `status`, `job_title`, `position`, `password_hash`, `avatar_path`, `project_name`, `project_progress`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'CLI-ADM', 'Administrator', '', 'Administrator', 'admin', 'admin@antara.local', 'PT ANTARA', '+62 812 0000 0001', 'active', 'Administrator', 'Executive', '$2y$10$98zUbY8l8LLR5irCfP/BGevT2JwoG.t6YnEEqxtO2E0S19iXdwHnq', 'assets/img/users/user-01.jpg', 'Admin Account', 100, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(2, 1, 'CLI-RED', 'Pegawai', 'Redaksi', 'Pegawai Redaksi', 'pegawai.redaksi', 'pegawai@antara.local', 'PT ANTARA', '+62 812 0000 0002', 'active', 'Editor', 'Staff', '$2y$10$9EZZbugleC1IbewMdVzYPe6mbpQnflJ2jh3OT0y2hKuxn5AWgTg3K', 'assets/img/users/user-02.jpg', 'Redaksi Account', 90, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(3, 1, 'CLI-PEL', 'Pelanggan', 'Mitra', 'Pelanggan Mitra', 'pelanggan.mitra', 'pelanggan@antara.local', 'PT ANTARA', '+62 812 0000 0003', 'active', 'Client', 'Client', '$2y$10$wbB2pYMOPH1kW.YQF3CkVOuX2wvvnHsOHSbPMcOmZDDr.vp6EyUAO', 'assets/img/users/user-03.jpg', 'Pelanggan Mitra', 80, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(4, 1, 'CLI-ALPHA', 'Klien', 'Alpha', 'Klien Alpha', 'alpha.client', 'alpha.client@antara.local', 'PT ANTARA', '+62 812 0000 0004', 'active', 'Client', 'Client', '$2y$10$98zIXUNpj0r00GfOcUJmEuo.TnyMZrTBTsGLIO5l4HGFQ/OQUtmEy', 'assets/img/users/user-04.jpg', 'Project Alpha', 70, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(5, 1, 'CLI-BETA', 'Klien', 'Beta', 'Klien Beta', 'beta.client', 'beta.client@antara.local', 'PT ANTARA', '+62 812 0000 0005', 'active', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-05.jpg', 'Project Beta', 60, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(6, 1, 'CLI-GAMMA', 'Klien', 'Gamma', 'Klien Gamma', 'gamma.client', 'gamma.client@antara.local', 'PT ANTARA', '+62 812 0000 0006', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-06.jpg', 'Project Gamma', 50, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(7, 1, 'CLI-DELTA', 'Klien', 'Delta', 'Klien Delta', 'delta.client', 'delta.client@antara.local', 'PT ANTARA', '+62 812 0000 0007', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-07.jpg', 'Project Delta', 40, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46'),
(8, 1, 'CLI-EPS', 'Klien', 'Epsilon', 'Klien Epsilon', 'epsilon.client', 'epsilon.client@antara.local', 'PT ANTARA', '+62 812 0000 0008', 'prospect', 'Client', 'Client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assets/img/users/user-08.jpg', 'Project Epsilon', 30, NULL, '2025-12-02 12:38:46', '2025-12-02 12:38:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_pic`
--

CREATE TABLE `company_pic` (
  `user_id` int(11) NOT NULL,
  `name` varchar(190) DEFAULT '',
  `role` varchar(190) DEFAULT '',
  `email` varchar(190) DEFAULT '',
  `phone` varchar(50) DEFAULT '',
  `location` varchar(190) DEFAULT '',
  `note` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_pic`
--

INSERT INTO `company_pic` (`user_id`, `name`, `role`, `email`, `phone`, `location`, `note`, `avatar_url`, `updated_at`) VALUES
(1, 'Rizka Putri', 'Procurement Lead', 'rizka.putri@antara.co.id', '0812 7788 9900', 'Jakarta Pusat, Indonesia', 'Hubungi via email untuk verifikasi dokumen pada hari kerja 09.00 - 17.00 WIB.', 'assets/uploads/my-info/user-1/pic/pic-20251125-164134-c41cbc85.jpg', '2025-11-25 16:41:34'),
(3, '', '', '', '', '', '', 'assets/uploads/my-info/user-3/pic/pic-20251202-110406-9283d50b.png', '2025-12-02 11:04:06'),
(4, 'Rizka Putri', 'Procurement Lead', 'rizka.putri@antara.co.id', '0812 7788 9900', 'Jakarta Pusat, Indonesia', 'Hubungi via email untuk verifikasi dokumen pada hari kerja 09.00 - 17.00 WIB.', 'assets/img/profiles/avatar-05.jpg', '2025-12-02 10:35:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_profiles`
--

CREATE TABLE `company_profiles` (
  `user_id` int(11) NOT NULL,
  `name` varchar(190) DEFAULT '',
  `type` varchar(120) DEFAULT '',
  `email` varchar(190) DEFAULT '',
  `phone` varchar(50) DEFAULT '',
  `mobile` varchar(50) DEFAULT '',
  `address` text DEFAULT NULL,
  `province` varchar(120) DEFAULT '',
  `city` varchar(120) DEFAULT '',
  `district` varchar(120) DEFAULT '',
  `subdistrict` varchar(120) DEFAULT '',
  `categories_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`categories_json`)),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `company_profiles`
--

INSERT INTO `company_profiles` (`user_id`, `name`, `type`, `email`, `phone`, `mobile`, `address`, `province`, `city`, `district`, `subdistrict`, `categories_json`, `updated_at`) VALUES
(1, 'PT Media Antara', 'BUMN', 'corp@antara.co.id', '021 - 380 1234', '0812 9900 1122', 'Gedung Graha Antara, Jl. Medan Merdeka Selatan No.17', 'DKI Jakarta', 'Jakarta Pusat', 'Gambir', 'Gambir', '{\"jasa_perhotelan\":false,\"kjpp\":true,\"fnb\":false,\"jasa_sewa\":false,\"kontraktor\":false,\"peralatan_komputer\":true,\"jasa_transportasi\":false,\"logistik_atk\":false,\"foto_video\":false,\"kap\":false,\"konsultan\":false,\"provider_internet\":false}', '2025-11-25 16:19:45'),
(3, '', '', '', '', '', '', '', '', '', '', '{\"jasa_perhotelan\":false,\"kjpp\":false,\"fnb\":false,\"jasa_sewa\":false,\"kontraktor\":false,\"peralatan_komputer\":false,\"jasa_transportasi\":false,\"logistik_atk\":false,\"foto_video\":false,\"kap\":false,\"konsultan\":false,\"provider_internet\":false}', '2025-12-02 10:39:04'),
(4, 'PT Media Antara', 'BUMN', 'corp@antara.co.id', '021 - 380 1234', '0812 9900 1122', 'Gedung Graha Antara, Jl. Medan Merdeka Selatan No.17', 'DKI Jakarta', 'Jakarta Pusat', 'Gambir', 'Gambir', '{\"jasa_perhotelan\":false,\"kjpp\":false,\"fnb\":false,\"jasa_sewa\":false,\"kontraktor\":false,\"peralatan_komputer\":false,\"jasa_transportasi\":false,\"logistik_atk\":false,\"foto_video\":false,\"kap\":false,\"konsultan\":false,\"provider_internet\":false}', '2025-12-02 10:32:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_client_subscriptions`
--

CREATE TABLE `crm_client_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `plan_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `started_at` datetime DEFAULT NULL,
  `renewal_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_client_subscriptions`
--

INSERT INTO `crm_client_subscriptions` (`id`, `user_id`, `customer_user_id`, `client_id`, `plan_name`, `plan_code`, `cycle`, `amount`, `currency_code`, `status`, `started_at`, `renewal_at`, `ends_at`, `metadata`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, 'Paket Distribusi Konten', 'ANTARA-DISTRO', 'monthly', '15000000.00', 'IDR', 'active', '2025-11-18 10:30:03', '2025-12-18 10:30:03', NULL, '{\"channels\": \"TV + Digital\"}', 'Sample untuk demo', '2025-12-03 10:30:03', '2025-12-03 10:30:03'),
(3, 1, 4, NULL, 'Paket Normal', 'ANTARA-FOTO', 'monthly', '200000.00', 'IDR', 'active', '2025-12-03 10:40:00', '2026-01-01 10:40:00', '2025-12-31 10:40:00', NULL, 'Sudah aktif', '2025-12-03 10:40:41', '2025-12-03 10:40:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_deals`
--

CREATE TABLE `crm_deals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deal_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_deals`
--

INSERT INTO `crm_deals` (`id`, `user_id`, `title`, `partner_name`, `stage`, `deal_value`, `start_date`, `end_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Iklan Ramadan Pertamina 2024', 'PT Pertamina (Persero)', 'Negosiasi Final', '1200000000.00', '2024-02-12', '2024-04-15', 'Kampanye lintas kanal menjelang Ramadan.', '2025-11-14 07:29:46', '2025-11-14 07:29:46'),
(2, 1, 'Roadshow Ekonomi Bank Indonesia', 'Bank Indonesia', 'Berjalan', '850000000.00', '2024-01-05', '2024-06-30', 'Rangkaian acara tatap muka di beberapa kota.', '2025-11-14 07:29:46', '2025-11-14 07:29:46'),
(3, 1, 'Kampanye Digital Telkomsel 5G', 'PT Telkomsel', 'Briefing Redaksi', '975000000.00', '2024-03-10', '2024-05-30', 'Liputan edukasi publik jaringan 5G.', '2025-11-14 07:29:46', '2025-11-14 07:29:46'),
(4, 1, 'Liputan Khusus PLN Transisi Energi', 'PLN (Persero)', 'Produksi Konten', '650000000.00', '2024-02-20', '2024-07-20', 'Seri liputan khusus terkait energi bersih.', '2025-11-14 07:29:46', '2025-11-14 07:29:46'),
(5, 1, 'Program CSR Antara X Kemenkeu', 'Kementerian Keuangan RI', 'Proposal', '450000000.00', '2024-04-01', '2024-09-30', 'Program CSR di beberapa daerah.', '2025-11-14 07:29:46', '2025-11-14 07:29:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_estimates`
--

CREATE TABLE `crm_estimates` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `ref_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimate_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','declined','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_estimates`
--

INSERT INTO `crm_estimates` (`id`, `user_id`, `ref_no`, `client_name`, `client_title`, `company_name`, `project_title`, `contact_email`, `estimate_date`, `expiry_date`, `amount`, `status`, `notes`, `avatar_path`, `created_at`, `updated_at`) VALUES
(4, 1, 'EST-ANT-24004a', 'Rio Saputra', 'Head of PR', 'Telkom Indonesia', 'Peluncuran satelit Merah Putih 3', 'rio.saputra@telkom.co.id', '2024-10-28', '2024-11-05', '150000000.00', 'expired', 'Penawaran kedaluwarsa, diminta revisi jadwal peluncuran.', '', '2025-11-19 10:47:42', '2025-11-19 11:12:14'),
(5, 1, 'EST-ANT-24005', 'Sarah Dewi', 'Communication Lead', 'PT Freeport Indonesia', 'Publikasi program CSR Papua 2025', 'sarah.dewi@ptfi.co.id', '2024-12-12', '2024-12-22', '86000000.00', 'sent', 'Memerlukan penyesuaian visual untuk kanal bahasa Inggris.', 'assets/img/users/user-46.jpg', '2025-11-19 10:47:42', '2025-11-19 10:47:42'),
(6, 1, 'EST-ANT-24006', 'Yusuf Akbar', 'Marketing Director', 'Traveloka', 'Promo Akhir Tahun Nusantara', 'yusuf.akbar@traveloka.com', '2024-12-01', '2024-12-08', '64000000.00', 'accepted', 'Termasuk liputan video pendek + penempatan banner news portal.', 'assets/img/users/user-48.jpg', '2025-11-19 10:47:42', '2025-11-19 10:47:42'),
(9, 1, '01312312', 'zaki', 'adaka', 'menfod', 'Peluncuran satelit Merah Putih 3', 'zkai@gmail.com', '2025-11-28', '2025-11-29', '111111111119.00', 'accepted', 'kocak', '', '2025-11-19 11:23:21', '2025-11-19 11:23:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_leads`
--

CREATE TABLE `crm_leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stage` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_leads`
--

INSERT INTO `crm_leads` (`id`, `user_id`, `title`, `organization`, `contact_name`, `contact_email`, `contact_phone`, `stage`, `target_value`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kampanye Pariwisata Nusantara', 'Kemenparekraf', 'Mira Prameswari', 'mira.prameswari@kemenparekraf.go.id', '021-555-019298', 'Presentasi', '400000000.00', 'Kolaborasi konten untuk mendukung pariwisata.', '2025-11-14 07:30:01', '2025-11-17 07:46:52'),
(2, 1, 'Program Literasi Keuangan', 'Otoritas Jasa Keuangan', 'Andre Suwito', 'andre.suwito@ojk.go.id', '021-888-7711', 'Follow Up', '275000000.00', 'Kerja sama edukasi melalui multiplatform.', '2025-11-14 07:30:01', '2025-11-14 07:30:01'),
(3, 1, 'Festival Kuliner Nusantara', 'SariRoti Group', 'Rani Winata', 'rani.winata@sariroti.co.id', '0812-8832-1100', 'Penawaran', '320000000.00', 'Liputan tematik untuk festival kuliner.', '2025-11-14 07:30:01', '2025-11-14 07:30:01'),
(4, 1, 'Sponsorship Liga Basket Nasional', 'Perbasi & BUMN', 'Arman Sabbah', 'arman.sabbah@perbasi.or.id', '0811-8877-663', 'Briefing', '500000000.00', 'Kerja sama sponsorship event olahraga.', '2025-11-14 07:30:01', '2025-11-14 07:30:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_payments`
--

CREATE TABLE `crm_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'settled',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_payments`
--

INSERT INTO `crm_payments` (`id`, `user_id`, `invoice_id`, `invoice_no`, `client_name`, `client_position`, `company_name`, `client_avatar`, `payment_method`, `channel`, `reference_no`, `paid_at`, `amount`, `currency_code`, `status`, `notes`, `payment_proof_path`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'INV-2024-0012', 'Anthony Lewis', 'Kepala Program', 'PT Warta Global Media', 'assets/img/users/user-45.jpg', 'Virtual Account', 'BCA VA 3910081', 'VA-20241202-AX12', '2024-12-02 10:45:00', '125000000.00', 'IDR', 'settled', 'Pelunasan paket distribusi konten ASEAN Summit.', NULL, '2025-11-24 04:46:32', '2025-11-24 04:46:32'),
(2, 1, NULL, 'INV-2024-0013', 'Brian Villalobos', 'Koordinator Media', 'Kementerian Kominfo', 'assets/img/users/user-44.jpg', 'Transfer Bank', 'Mandiri Kementerian', 'TRF-20241210-KOM', '2024-12-10 09:15:00', '25500000.00', 'IDR', 'partial', 'Pembayaran termin 30% untuk paket data premium.', NULL, '2025-11-24 04:46:32', '2025-11-24 04:46:32'),
(3, 1, NULL, 'INV-2024-0014', 'Harvey Smith', 'Head of Corporate Affairs', 'PT Sinar Nusantara', 'assets/img/users/user-23.jpg', 'Virtual Account', 'Mandiri VA 7755102', 'VA-20241126-SNS', '2024-11-26 16:20:00', '15000000.00', 'IDR', 'partial', 'Uang muka produksi liputan KTT IKN.', NULL, '2025-11-24 04:46:32', '2025-11-24 04:46:32'),
(4, 1, 4, 'INV-2024-0015', 'Lori Broaddus', 'VP Marketing', 'PT Digital Pratama', 'assets/img/users/user-01.jpg', 'Credit Card', 'Midtrans CC', 'CC-20241220-DIGI', '2024-12-20 11:10:00', '5000000.00', 'IDR', 'pending', 'Booking bundel distribusi Q1 2025, menunggu PO final.', NULL, '2025-11-24 04:46:32', '2025-11-24 04:46:32'),
(5, 1, NULL, 'INV-2024-0012', 'Anthony Lewis', 'Kepala Program', 'PT Warta Global Media', 'assets/img/users/user-45.jpg', 'Virtual Account', 'BCA VA 3910081', 'VA-20241202-AX12', '2024-12-02 10:45:00', '125000000.00', 'IDR', 'settled', 'Pelunasan paket distribusi konten ASEAN Summit.', NULL, '2025-11-26 03:07:00', '2025-11-26 03:07:00'),
(6, 1, NULL, 'INV-2024-0013', 'Brian Villalobos', 'Koordinator Media', 'Kementerian Kominfo', 'assets/img/users/user-44.jpg', 'Transfer Bank', 'Mandiri Kementerian', 'TRF-20241210-KOM', '2024-12-10 09:15:00', '25500000.00', 'IDR', 'partial', 'Pembayaran termin 30% untuk paket data premium.', NULL, '2025-11-26 03:07:00', '2025-11-26 03:07:00'),
(7, 1, NULL, 'INV-2024-0014', 'Harvey Smith', 'Head of Corporate Affairs', 'PT Sinar Nusantara', 'assets/img/users/user-23.jpg', 'Virtual Account', 'Mandiri VA 7755102', 'VA-20241126-SNS', '2024-11-26 16:20:00', '15000000.00', 'IDR', 'partial', 'Uang muka produksi liputan KTT IKN.', NULL, '2025-11-26 03:07:00', '2025-11-26 03:07:00'),
(8, 1, 4, 'INV-2024-0015', 'Lori Broaddus', 'VP Marketing', 'PT Digital Pratama', 'assets/img/users/user-01.jpg', 'Credit Card', 'Midtrans CC', 'CC-20241220-DIGI', '2024-12-20 11:10:00', '5000000.00', 'IDR', 'pending', 'Booking bundel distribusi Q1 2025, menunggu PO final.', NULL, '2025-11-26 03:07:00', '2025-11-26 03:07:00'),
(9, 3, NULL, 'INV-2024-0015', 'zaki', 'PO LDK', 'Telkom Indonesia', 'assets/img/users/user-01.jpg', 'Transfer bank', 'BCA', 'PO-DIGI-7721', '2025-11-26 15:45:00', '5000000.00', 'IDR', 'settled', 'aadaadada', NULL, '2025-11-26 08:46:43', '2025-11-26 08:46:43'),
(10, 3, NULL, 'inca', 'nicko', 'oakaosa', 'jasjas', '', 'Transfer bank', 'Mandiri', 'PO_Zaki', '2025-11-27 09:45:00', '99999991.00', 'IDR', 'settled', 'iseng', NULL, '2025-11-27 03:04:51', '2025-11-27 03:04:51'),
(11, 3, NULL, 'INV-2025-1212', 'zaki', 'PO LDK', 'Media Tama', '', 'Transfer bank', 'Mandiri bank', '', '2025-11-28 16:47:00', '3000001.00', 'IDR', 'settled', 'Bayar Q4 Fitur Berita', 'assets/uploads/payments/payment-proof-69281e909c2c66.20529295.jpg', '2025-11-27 09:49:04', '2025-11-27 09:49:04'),
(12, 1, 12, 'INV-2025-11-2', 'pelangganzaki', 'PO LDK', 'PT Cuan', '', 'Transfer bank', 'Mandiri', 'PO-zakisebagian', '2025-12-01 10:23:00', '20000000.00', 'IDR', 'partial', 'Ini coba coba', 'assets/uploads/payments/payment-proof-692d0a8fa8dc99.49418202.jpg', '2025-12-01 03:25:03', '2025-12-01 03:25:03'),
(13, 1, 11, 'INV-2025-1212', 'pelanggan', 'PO LDK', 'PT utama', '', 'Transfer bank', 'BCA', 'PO-132323', '2025-12-01 10:51:00', '2850000.00', 'IDR', 'settled', 'Ini coba lagi', 'assets/uploads/payments/payment-proof-692d10f567d8a7.38401414.jpg', '2025-12-01 03:52:21', '2025-12-01 03:52:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_pipeline_entries`
--

CREATE TABLE `crm_pipeline_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimate_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_pipeline_entries`
--

INSERT INTO `crm_pipeline_entries` (`id`, `user_id`, `title`, `partner_name`, `stage`, `estimate_value`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Forum UMKM Kemenkop', 'Kemenkop UKM', 'Prospecting', '180000000.00', 'Forum dan pameran UMKM.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(2, 1, 'Seri Webinar Literasi Pajak', 'Ditjen Pajak', 'Prospecting', '150000000.00', 'Seri webinar edukasi perpajakan.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(3, 1, 'Program CSR Astra Hijau', 'PT Astra International', 'Penawaran', '420000000.00', 'Inisiatif CSR lingkungan.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(4, 1, 'Partnership Event Musik', 'Bank Rakyat Indonesia', 'Penawaran', '260000000.00', 'Sponsorship event musik nasional.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(5, 1, 'Roadshow Infrastruktur Nusantara', 'Kementerian PUPR', 'Presentasi', '390000000.00', 'Roadshow infrastruktur ke beberapa kota.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(6, 1, 'Kampanye Digital Telkomsel 5G', 'PT Telkomsel', 'Negosiasi', '975000000.00', 'Kampanye edukasi 5G.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(7, 1, 'Iklan Ramadan Pertamina 2024', 'PT Pertamina (Persero)', 'Negosiasi', '1200000000.00', 'Kampanye Ramadan lintas kanal.', '2025-11-17 02:48:52', '2025-11-17 02:48:52'),
(8, 1, 'Liputan Khusus PLN Transisi Energi', 'PLN (Persero)', 'Selesai', '650000000.00', 'Liputan khusus energi bersih.', '2025-11-17 02:48:52', '2025-11-17 02:48:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_product_invoices`
--

CREATE TABLE `crm_product_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `recipient_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `direction` enum('outgoing','incoming') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'outgoing',
  `invoice_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` datetime NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_status` enum('pending','processing','settled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supporting_docs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`supporting_docs`)),
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal_amount` decimal(15,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT NULL,
  `grand_total` decimal(15,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_product_invoices`
--

INSERT INTO `crm_product_invoices` (`id`, `user_id`, `recipient_user_id`, `direction`, `invoice_no`, `invoice_title`, `client_name`, `client_company`, `vendor_name`, `period_label`, `client_position`, `client_email`, `client_phone`, `client_avatar`, `issue_date`, `due_date`, `received_at`, `verification_status`, `payment_status`, `attachment_path`, `payment_proof_path`, `supporting_docs`, `status`, `currency_code`, `total_amount`, `amount_paid`, `subtotal_amount`, `tax_amount`, `grand_total`, `notes`, `terms`, `reference_no`, `channel`, `created_at`, `updated_at`) VALUES
(4, 1, NULL, 'outgoing', 'INV-2024-0015', 'Bundel Distribusi Press Release 2025', 'Lori Broaddus  Zaki', 'PT Digital Pratama', NULL, NULL, 'VP Marketing', 'broaddus@example.com', '+62 811 8321 900', 'assets/img/users/user-01.jpg', '2025-11-20 14:02:00', '2025-11-21 17:01:00', NULL, 'pending', 'pending', NULL, NULL, NULL, 'draft', 'IDR', '32000000.00', '0.00', NULL, NULL, NULL, 'Bundel distribusi press release bulanan Q1 2025.', 'Draft internal, menunggu konfirmasi PO.', 'PO-DIGI-7721', 'Produk Kemitraana', '2025-11-20 04:30:05', '2025-11-26 03:07:01'),
(5, 1, NULL, 'outgoing', 'INV-adad', 'Bundel distribusi barang Buku press release bulanan Q1 2025.', 'Zaki Abdussalam', 'PT Literasi Zaki Utana', NULL, NULL, 'PO LDK', 'azki@gmail.com', '08812913893', 'assets/img/users/user-01.jpg', '2025-11-28 14:51:00', '2025-11-29 06:54:00', NULL, 'pending', 'pending', NULL, NULL, NULL, 'partial', 'IDR', '64000000.80', '0.00', NULL, NULL, NULL, 'Bundel distribusi barang Buku press release bulanan Q1 2025.', 'Pembayaran 3 bulan', '', 'Produk TV', '2025-11-20 04:49:54', '2025-11-26 03:07:01'),
(11, 1, 3, 'outgoing', 'INV-2025-1212', 'Distribusi Konten ASEAN Summit', 'pelanggan', 'PT utama', '', '', 'PO LDK', 'broaddus@example.com', '08812913893', '', '2025-11-27 10:15:00', '2025-11-29 10:15:00', NULL, 'pending', 'pending', '', '', NULL, 'paid', 'IDR', '2850000.00', '2850000.00', '2850000.00', '0.00', '2850000.00', '', 'Termin 1', 'PO_Zaki', 'Produk Fotoa', '2025-11-27 03:16:56', '2025-12-01 03:52:21'),
(12, 1, 3, 'outgoing', 'INV-2025-11-2', 'Keres', 'pelangganzaki', 'PT Cuan', '', '', 'PO LDK', 'azki@gmail.com', '0881291389399', '', '2025-11-27 14:40:00', '2025-11-28 14:44:00', NULL, 'pending', 'pending', '', '', NULL, 'overdue', 'IDR', '30000001.00', '20000000.00', '30000001.00', '0.00', '30000001.00', 'Bundel distribusi press release bulanan Q1 2025.', 'aaaa', '', 'Produk TV', '2025-11-27 07:45:29', '2025-12-01 03:25:03'),
(13, 1, 4, 'outgoing', 'INV-20251203-C775', 'Pembayaran langganan Antara FOTO', 'Alpha', 'PT Alpha', '', '', 'Marketing', 'alpha@antara.local', '08812913832', '', '2025-12-03 10:08:00', '2025-12-03 10:09:00', NULL, 'pending', 'pending', '', '', NULL, 'pending', 'IDR', '20000000.00', '0.00', '20000000.00', '0.00', '20000000.00', 'Tagihan layanan Antara FOTO', 'Termin Bulan Desember', '', 'Produk Foto', '2025-12-03 03:10:39', '2025-12-03 03:10:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `crm_product_invoice_items`
--

CREATE TABLE `crm_product_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `line_order` int(11) NOT NULL DEFAULT 0,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `crm_product_invoice_items`
--

INSERT INTO `crm_product_invoice_items` (`id`, `invoice_id`, `line_order`, `product_name`, `description`, `quantity`, `unit_price`, `discount_percent`, `line_total`, `created_at`, `updated_at`) VALUES
(21, 4, 0, 'Distribusi ke 250 kanal nasional/regional.', 'Distribusi ke 250 kanal nasional/regional.', '3.00', '8000000.00', '0.00', '24000000.00', '2025-11-20 07:33:29', NULL),
(22, 4, 1, 'Penulisan konten premium oleh desk ekonomi.', 'Penulisan konten premium oleh desk ekonomi.', '1.00', '8000000.00', '0.00', '8000000.00', '2025-11-20 07:33:29', NULL),
(23, 5, 0, 'Antara TV', 'Antara TV', '1.00', '80000001.00', '20.00', '64000000.80', '2025-11-24 09:12:49', NULL),
(24, 11, 0, 'Produk TV', 'Produk TV', '1.00', '3000000.00', '5.00', '2850000.00', '2025-11-27 03:16:56', NULL),
(25, 12, 0, 'Produk TV', 'Produk TV', '1.00', '30000001.00', '0.00', '30000001.00', '2025-11-27 07:45:29', NULL),
(26, 13, 0, 'Antara Foto', 'Antara Foto', '1.00', '20000000.00', '0.00', '20000000.00', '2025-12-03 03:10:39', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_messages`
--

CREATE TABLE `email_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `folder` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inbox',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `snippet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_addresses` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_addresses` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_starred` tinyint(1) NOT NULL DEFAULT 0,
  `has_attachments` tinyint(1) NOT NULL DEFAULT 0,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `labels` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `scheduled_for` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `email_messages`
--

INSERT INTO `email_messages` (`id`, `sender_user_id`, `recipient_user_id`, `folder`, `subject`, `snippet`, `body_html`, `body_text`, `sender_name`, `sender_email`, `to_addresses`, `cc_addresses`, `is_read`, `is_starred`, `has_attachments`, `attachment_path`, `attachment_name`, `category`, `labels`, `scheduled_for`, `received_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'inbox', 'Pembaruan Jadwal Rapat Redaksi', 'Rapat editorial dipindah ke pukul 10.00 WIB besok.', '<p>Halo tim,</p><p>Rapat editorial harian kita dipindah ke pukul <strong>10.00 WIB</strong> besok untuk menyesuaikan dengan konfirmasi narasumber utama.</p><p>Agenda singkat:</p><ul><li>Briefing liputan utama</li><li>Pemetaan distribusi konten</li><li>Status kolaborasi mitra</li></ul><p>Mohon konfirmasi kehadiran melalui kanal biasa.</p><p>Terima kasih,<br>Raisa</p>', 'Halo tim,\n\nRapat editorial harian kita dipindah ke pukul 10.00 WIB besok untuk menyesuaikan dengan konfirmasi narasumber utama.\n\nAgenda singkat:\n- Briefing liputan utama\n- Pemetaan distribusi konten\n- Status kolaborasi mitra\n\nMohon konfirmasi kehadiran melalui kanal biasa.\n\nTerima kasih,\nRaisa', 'Raisa Pramesti', 'raisa.pramesti@antara.id', 'redaksi@antara.id', NULL, 1, 1, 0, NULL, NULL, 'events', 'Team Events,Internal', '2025-11-06 10:31:16', '2025-11-04 14:19:16', '2025-11-04 16:19:16', '2025-11-06 13:28:25'),
(2, NULL, NULL, 'inbox', 'Brief Kampanye Konten Mitra Telkom', 'Lampiran berisi kebutuhan konten video dan artikel untuk minggu depan.', '<p>Selamat siang tim Antara,</p><p>Kami lampirkan brief kampanye konten terbaru untuk Telkom Indonesia.</p><p>Poin penting:</p><ol><li>Kebutuhan artikel: 4 topik transformasi digital daerah.</li><li>Kebutuhan video: 2 highlight berdurasi 45 detik (format vertikal).</li><li>Deadline draft awal: Jumat, 13 Desember.</li></ol><p>Silakan hubungi saya jika diperlukan diskusi lanjutan.</p><p>Salam,<br>Michael</p>', 'Selamat siang tim Antara,\n\nKami lampirkan brief kampanye konten terbaru untuk Telkom Indonesia.\n\nPoin penting:\n1. Kebutuhan artikel: 4 topik transformasi digital daerah.\n2. Kebutuhan video: 2 highlight berdurasi 45 detik (format vertikal).\n3. Deadline draft awal: Jumat, 13 Desember.\n\nSilakan hubungi saya jika diperlukan diskusi lanjutan.\n\nSalam,\nMichael', 'Michael Turner', 'michael.turner@telkom.co.id', 'redaksi@antara.id', 'partnership@antara.id', 1, 0, 1, NULL, NULL, 'campaign', 'Work,External', NULL, '2025-11-04 11:19:16', '2025-11-04 16:19:16', '2025-11-06 14:26:16'),
(3, NULL, NULL, 'inbox', 'Reminder Akreditasi Liputan Istana', 'Mohon lengkapi data reporter sebelum 15 Desember.', '<p>Yth. Redaksi Antara,</p><p>Ini adalah pengingat untuk melengkapi form akreditasi liputan Istana Negara menjelang perayaan Natal.</p><p>Data yang dibutuhkan:</p><ul><li>Nama reporter</li><li>Nomor KTP</li><li>Nomor telepon</li><li>Nomor plat kendaraan (jika membawa kendaraan)</li></ul><p>Mohon kirimkan kembali sebelum <strong>15 Desember 2024</strong>.</p><p>Hormat kami,<br>Protokol Sekretariat Presiden</p>', 'Yth. Redaksi Antara,\n\nIni adalah pengingat untuk melengkapi form akreditasi liputan Istana Negara menjelang perayaan Natal.\n\nData yang dibutuhkan:\n- Nama reporter\n- Nomor KTP\n- Nomor telepon\n- Nomor plat kendaraan (jika membawa kendaraan)\n\nMohon kirimkan kembali sebelum 15 Desember 2024.\n\nHormat kami,\nProtokol Sekretariat Presiden', 'Sekretariat Presiden', 'protokol@setpres.go.id', 'redaksi@antara.id', NULL, 1, 0, 0, NULL, NULL, 'compliance', 'External', NULL, '2025-11-03 16:19:16', '2025-11-04 16:19:16', '2025-11-06 14:01:14'),
(4, NULL, NULL, 'sent', 'Distribusi Rilis Pers Kolaborasi Unesco', 'Rilis pers telah dikirim ke 120 jaringan media internasional.', '<p>Halo tim,</p><p>Rilis pers mengenai kolaborasi Antara &amp; UNESCO sudah didistribusikan ke 120 jaringan media internasional.</p><p>Detail singkat:</p><ul><li>Target: Asia, Timur Tengah, Eropa</li><li>Headline: \"Transformasi Digital Arsip Berita Nasional\"</li><li>Lead: Highlight kemitraan untuk digitalisasi arsip.</li></ul><p>Mohon pantau respons rekan media di dashboard monitoring.</p><p>Terima kasih,<br>Divisi PR</p>', 'Halo tim,\n\nRilis pers mengenai kolaborasi Antara & UNESCO sudah didistribusikan ke 120 jaringan media internasional.\n\nDetail singkat:\n- Target: Asia, Timur Tengah, Eropa\n- Headline: \"Transformasi Digital Arsip Berita Nasional\"\n- Lead: Highlight kemitraan untuk digitalisasi arsip.\n\nMohon pantau respons rekan media di dashboard monitoring.\n\nTerima kasih,\nDivisi PR', 'Divisi PR Antara', 'pr@antara.id', 'kolaborasi@antara.id', NULL, 1, 0, 0, NULL, NULL, 'campaign', 'Projects', NULL, '2025-11-01 16:19:16', '2025-11-04 16:19:16', '2025-11-05 13:54:28'),
(5, NULL, NULL, 'drafts', 'Konsep Newsletter Investor Desember', 'Draft awal newsletter investor menunggu review final.', '<p>Hi Tim,</p><p>Saya siapkan draft awal newsletter investor untuk edisi Desember.</p><p>Hal yang perlu dipastikan:</p><ul><li>Update grafik performa anak usaha</li><li>Highlight kolaborasi Antara X Bank BUMN</li><li>Pernyataan resmi Direktur Utama</li></ul><p>Mohon review sebelum Jumat.</p><p>Regards,<br>Andini</p>', 'Hi Tim,\n\nSaya siapkan draft awal newsletter investor untuk edisi Desember.\n\nHal yang perlu dipastikan:\n- Update grafik performa anak usaha\n- Highlight kolaborasi Antara X Bank BUMN\n- Pernyataan resmi Direktur Utama\n\nMohon review sebelum Jumat.\n\nRegards,\nAndini', 'Andini Wira', 'andini.wira@antara.id', 'investor@antara.id', NULL, 1, 0, 0, NULL, NULL, 'work', 'Work', NULL, '2025-11-04 10:19:16', '2025-11-04 16:19:16', '2025-11-05 09:15:40'),
(6, NULL, NULL, 'spam', 'Undian Berhadiah Tidak Resmi', 'Selamat Anda mendapatkan hadiah besar, klik tautan berikut untuk klaim.', '<p>Selamat! Anda terpilih mendapatkan hadiah senilai Rp250 juta. Segera klaim dengan mengisi formulir berikut.</p>', 'Selamat! Anda terpilih mendapatkan hadiah senilai Rp250 juta. Segera klaim dengan mengisi formulir berikut.', 'Undian Palsu', 'hadiah@lotterypalsu.com', 'redaksi@antara.id', NULL, 1, 0, 0, NULL, NULL, 'spam', 'External', NULL, '2025-11-05 06:31:16', '2025-11-05 10:31:16', '2025-11-05 10:32:33'),
(7, NULL, NULL, 'inbox', 'Draft Press Release Lama', 'Draft lama yang perlu ditinjau ulang sebelum dipublikasi.', '<p>Halo tim,</p><p>Draft press release versi lama saya pindahkan ke Deleted supaya tidak salah kirim. Jika masih diperlukan, silakan restore.</p>', 'Halo tim,\n\nDraft press release versi lama saya pindahkan ke Deleted supaya tidak salah kirim. Jika masih diperlukan, silakan restore.', 'Divisi PR Antara', 'pr@antara.id', 'kolaborasi@antara.id', NULL, 1, 0, 0, NULL, NULL, 'archive', 'Projects,Internal', NULL, '2025-11-03 10:31:16', '2025-11-05 10:31:16', '2025-11-06 14:26:49'),
(8, NULL, NULL, 'sent', 'Re: Brief Kampanye Konten Mitra Telkom', '--- Pesan sebelumnya --- Selamat siang tim Antara, Kami lampirkan brief kampanye konten terbaru untuk Telkom Indonesi...', '<p>--- Pesan sebelumnya ---<br>Selamat siang tim Antara,</p>\n<p>Kami lampirkan brief kampanye konten terbaru untuk Telkom Indonesia.</p>\n<p>Poin penting:<br>1. Kebutuhan artikel: 4 topik transformasi digital daerah.<br>2. Kebutuhan video: 2 highlight berdurasi 45 detik (format vertikal).<br>3. Deadline draft awal: Jumat, 13 Desember.</p>\n<p>Silakan hubungi saya jika diperlukan diskusi lanjutan.</p>\n<p>Salam,<br>Michael</p>', '--- Pesan sebelumnya ---\r\nSelamat siang tim Antara,\r\n\r\nKami lampirkan brief kampanye konten terbaru untuk Telkom Indonesia.\r\n\r\nPoin penting:\r\n1. Kebutuhan artikel: 4 topik transformasi digital daerah.\r\n2. Kebutuhan video: 2 highlight berdurasi 45 detik (format vertikal).\r\n3. Deadline draft awal: Jumat, 13 Desember.\r\n\r\nSilakan hubungi saya jika diperlukan diskusi lanjutan.\r\n\r\nSalam,\r\nMichael', 'Administrator', 'admin@antara.local', 'michael.turner@telkom.co.id', NULL, 1, 0, 0, NULL, NULL, 'outbound', '', NULL, '2025-11-05 14:45:11', '2025-11-05 14:45:11', '2025-11-05 14:45:11'),
(9, NULL, NULL, 'inbox', 'Follow-up Jadwal Produksi Video', 'Tim produksi menunggu konfirmasi jadwal syuting liputan akhir pekan.', '<p>Halo Redaksi,</p><p>Kami butuh konfirmasi jadwal syuting untuk liputan akhir pekan ini.</p><p>Mohon pilih salah satu slot berikut:</p><ul><li>Sabtu, 09.00 WIB - Lokasi: Studio 3</li><li>Minggu, 13.00 WIB - Lokasi: Lapangan Banteng</li></ul><p>Terima kasih,<br>Divisi Produksi</p>', 'Halo Redaksi,\n\nKami butuh konfirmasi jadwal syuting untuk liputan akhir pekan ini.\n\nMohon pilih salah satu slot berikut:\n- Sabtu, 09.00 WIB - Lokasi: Studio 3\n- Minggu, 13.00 WIB - Lokasi: Lapangan Banteng\n\nTerima kasih,\nDivisi Produksi', 'Divisi Produksi', 'produksi@antara.id', 'redaksi@antara.id', NULL, 1, 0, 0, NULL, NULL, 'ops', 'Internal', NULL, '2025-11-06 07:14:57', '2025-11-06 15:14:57', '2025-11-06 16:49:56'),
(10, NULL, NULL, 'inbox', 'Permintaan Revisi Rilis Media', 'Harap revisi paragraf ketiga sesuai masukan mitra.', '<p>Selamat sore,</p><p>Mitra meminta revisi paragraf ketiga pada rilis media tentang kolaborasi digital.</p><p>Highlight perubahan:</p><ol><li>Tambahkan penjelasan singkat tentang dampak sosial.</li><li>Perbaiki penulisan nama produk menjadi huruf kapital.</li></ol><p>Draft revisi terlampir.</p><p>Salam,<br>Divisi PR</p>', 'Selamat sore,\n\nMitra meminta revisi paragraf ketiga pada rilis media tentang kolaborasi digital.\n\nHighlight perubahan:\n1. Tambahkan penjelasan singkat tentang dampak sosial.\n2. Perbaiki penulisan nama produk menjadi huruf kapital.\n\nDraft revisi terlampir.\n\nSalam,\nDivisi PR', 'Divisi PR Antara', 'pr@antara.id', 'redaksi@antara.id', NULL, 1, 0, 1, NULL, NULL, 'communication', 'External', NULL, '2025-11-06 03:14:57', '2025-11-06 15:14:57', '2025-11-12 14:44:50'),
(11, NULL, NULL, 'inbox', 'Update Komitmen Sponsor Event', 'Sponsor utama mengirimkan revisi materi promosi.', '<p>Halo tim marketing,</p><p>Sponsor utama mengirimkan revisi materi promosi. Mohon segera disesuaikan di landing page dan materi sosial media.</p><p>Perubahan utama:</p><ul><li>Logo terbaru</li><li>Slogan baru</li><li>CTA khusus tiket early bird</li></ul><p>Deadline implementasi: <strong>Jumat, 8 November pukul 17.00 WIB</strong>.</p><p>Terima kasih,<br>Account Management</p>', 'Halo tim marketing,\n\nSponsor utama mengirimkan revisi materi promosi. Mohon segera disesuaikan di landing page dan materi sosial media.\n\nPerubahan utama:\n- Logo terbaru\n- Slogan baru\n- CTA khusus tiket early bird\n\nDeadline implementasi: Jumat, 8 November pukul 17.00 WIB.\n\nTerima kasih,\nAccount Management', 'Account Management', 'account@antara.id', 'marketing@antara.id', 'redaksi@antara.id', 1, 0, 0, NULL, NULL, 'partnership', 'External,Work', NULL, '2025-11-05 23:14:57', '2025-11-06 15:14:57', '2025-11-10 13:46:20'),
(12, NULL, NULL, 'inbox', 'Pengingat Review Konten Podcast', 'Jangan lupa review konten podcast episode terbaru sebelum tayang.', '<p>Halo semuanya,</p><p>Episode podcast terbaru sudah siap dan butuh review editorial.</p><p>Checklist:</p><ul><li>Durasi final 28 menit</li><li>Topik: Transformasi digital media</li><li>Narasumber: CEO Media Insight</li></ul><p>Mohon review sebelum <strong>Rabu, 6 November pukul 15.00 WIB</strong>.</p><p>Terima kasih,<br>Tim Podcast</p>', 'Halo semuanya,\n\nEpisode podcast terbaru sudah siap dan butuh review editorial.\n\nChecklist:\n- Durasi final 28 menit\n- Topik: Transformasi digital media\n- Narasumber: CEO Media Insight\n\nMohon review sebelum Rabu, 6 November pukul 15.00 WIB.\n\nTerima kasih,\nTim Podcast', 'Tim Podcast', 'podcast@antara.id', 'redaksi@antara.id', NULL, 0, 0, 0, NULL, NULL, 'content', 'Internal,Team Events', NULL, '2025-11-05 19:14:57', '2025-11-06 15:14:57', '2025-11-06 15:14:57'),
(13, 1, 3, 'sent', 'Bayar WOI', 'Ini ada lah tese', 'Ini ada lah tese', 'Ini ada lah tese', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d2cfba38585.75849593.pdf', NULL, 'correspondence', '', NULL, '2025-12-01 12:51:55', '2025-12-01 12:51:55', '2025-12-01 13:07:55'),
(14, 1, 3, 'inbox', 'Bayar WOI', 'Ini ada lah tese', 'Ini ada lah tese', 'Ini ada lah tese', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d2cfba38585.75849593.pdf', NULL, 'correspondence', '', NULL, '2025-12-01 12:51:55', '2025-12-01 12:51:55', '2025-12-01 12:52:39'),
(15, 3, 1, 'sent', 'Woi udah bayar', 'iyakah', 'iyakah', 'iyakah', 'Pelanggan Mitra', 'pelanggan@antara.local', 'Administrator', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d30ff35c848.87812739.png', NULL, 'correspondence', '', NULL, '2025-12-01 13:09:03', '2025-12-01 13:09:03', '2025-12-01 14:16:29'),
(16, 3, 1, 'spam', 'Woi udah bayar', 'iyakah', 'iyakah', 'iyakah', 'Pelanggan Mitra', 'pelanggan@antara.local', 'Administrator', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d30ff35c848.87812739.png', NULL, 'correspondence', '', NULL, '2025-12-01 13:09:03', '2025-12-01 13:09:03', '2025-12-01 16:42:16'),
(17, 1, 3, 'inbox', 'Bayar WOI', 'DASAS', 'DASAS', 'DASAS', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d64689f7a87.19006706.pdf', 'Log Book MAGENTA November_Zaki Abdussalam.docx.pdf', 'correspondence', '', NULL, '2025-12-01 16:48:24', '2025-12-01 16:48:24', '2025-12-02 08:49:00'),
(18, 1, 3, 'inbox', 'Bayar WOI', 'DASAS', 'DASAS', 'DASAS', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692d64689f7a87.19006706.pdf', 'Log Book MAGENTA November_Zaki Abdussalam.docx.pdf', 'correspondence', '', NULL, '2025-12-01 16:48:24', '2025-12-01 16:48:24', '2025-12-02 09:28:51'),
(19, 1, 3, 'sent', 'test aja', 'ni test', '<b>ni test</b>', 'ni test', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-02 16:09:07', '2025-12-02 16:09:07', '2025-12-02 16:09:07'),
(20, 1, 3, 'inbox', 'test aja', 'ni test', '<b>ni test</b>', 'ni test', 'Administrator', 'admin@antara.local', 'Pelanggan Mitra', NULL, 1, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-02 16:09:07', '2025-12-02 16:09:07', '2025-12-02 16:09:16'),
(21, 4, 1, 'sent', 'Halo admin', 'Mau nanya', 'Mau nanya', 'Mau nanya', 'Klien Alpha', 'alpha.client@antara.local', 'Administrator', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692fa94f1dd8d2.22257708.png', 'ngirim korespondensi.png', 'correspondence', '', NULL, '2025-12-03 10:06:55', '2025-12-03 10:06:55', '2025-12-03 10:06:55'),
(22, 4, 1, 'inbox', 'Halo admin', 'Mau nanya', 'Mau nanya', 'Mau nanya', 'Klien Alpha', 'alpha.client@antara.local', 'Administrator', NULL, 1, 0, 1, 'assets/uploads/correspondence/correspondence-692fa94f1dd8d2.22257708.png', 'ngirim korespondensi.png', 'correspondence', '', NULL, '2025-12-03 10:06:55', '2025-12-03 10:06:55', '2025-12-03 11:43:06'),
(23, 1, 4, 'sent', 'Jatuh Tempo - Produk FOTO', 'Menunggu pembayaran', 'Menunggu pembayaran', 'Menunggu pembayaran', 'Administrator', 'admin@antara.local', 'Klien Alpha', NULL, 1, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-03 11:33:56', '2025-12-03 11:33:56', '2025-12-03 11:33:56'),
(24, 1, 4, 'inbox', 'Jatuh Tempo - Produk FOTO', 'Menunggu pembayaran', 'Menunggu pembayaran', 'Menunggu pembayaran', 'Administrator', 'admin@antara.local', 'Klien Alpha', NULL, 0, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-03 11:33:56', '2025-12-03 11:33:56', '2025-12-03 11:33:56'),
(25, 1, 4, 'sent', 'Jatuh Tempo - Produk FOTOa', 'iya ya', 'iya ya', 'iya ya', 'Administrator', 'admin@antara.local', 'Klien Alpha', NULL, 1, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-03 14:17:23', '2025-12-03 14:17:23', '2025-12-03 14:17:23'),
(26, 1, 4, 'inbox', 'Jatuh Tempo - Produk FOTOa', 'iya ya', 'iya ya', 'iya ya', 'Administrator', 'admin@antara.local', 'Klien Alpha', NULL, 1, 0, 0, NULL, NULL, 'correspondence', '', NULL, '2025-12-03 14:17:23', '2025-12-03 14:17:23', '2025-12-03 14:18:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mitra_partners`
--

CREATE TABLE `mitra_partners` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `industri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mitra_partners`
--

INSERT INTO `mitra_partners` (`id`, `nama`, `industri`, `kontak`, `email`, `telepon`, `status`, `alamat`, `logo_path`, `created_at`, `updated_at`) VALUES
(1, 'ACN Newswire ya', 'Distribusi Rilis Pers', 'Naoko Sato', 'support@acnnewswire.com', '+65 6788 8670', 'Media Distribution', '1 Raffles Place, Singapore', 'assets/img/mitra/logo-acnnewswire.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(3, 'AP News', 'Kantor Berita Internasional', 'Michael Turner', 'partners@ap.org', '+1 212-621-1500', 'Editorial Partner', '200 Liberty Street, New York', 'assets/img/mitra/logo-ap-news.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(4, 'AsiaNet', 'Distribusi Berita Asia Pasifik', 'Priya Menon', 'info@asianetnews.net', '+61 2 9322 8659', 'Media Distribution', 'Sydney, Australia', 'assets/img/mitra/logo-asianet.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(5, 'Bernama', 'Kantor Berita Malaysia', 'Nur Aisyah', 'info@bernama.com', '+60 3 2693 9933', 'Editorial Partner', 'No. 28 Jalan Yap Kwan Seng, Kuala Lumpur', 'assets/img/mitra/logo-bernama.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(6, 'Bloomberg', 'Media & Data Finansial', 'Samantha Lee', 'business@bloomberg.net', '+1 212-318-2000', 'Strategic Alliance', '731 Lexington Avenue, New York', 'assets/img/mitra/logo-bloomberg.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(7, 'Agencia EFE', 'Kantor Berita Spanyol', 'Carlos Gutierrez', 'comercial@efe.com', '+34 91 347 82 00', 'Editorial Partner', 'Avenida de Burgos 8B, Madrid', 'assets/img/mitra/logo-efe.png', '2025-10-27 09:38:43', '2025-10-27 06:36:21'),
(8, 'Finsoft', 'Solusi Teknologi Finansial', 'Ardi Prabowo', 'hello@finsoft.io', '+62 21 555 9911', 'Technology Partner', 'SCBD, Jakarta', 'assets/img/mitra/logo-finsoft.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(9, 'HCM Ads Media', 'Jaringan Media Asia Tenggara', 'Tran Thi Hoa', 'partners@hcmedia.vn', '+84 28 3821 9922', 'Commercial Partner', 'Ho Chi Minh City, Vietnam', 'assets/img/mitra/logo-hcm-ads-media.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(10, 'Kyodo News', 'Kantor Berita Jepang', 'Kenji Nakamura', 'global@kyodonews.jp', '+81 3-6252-8400', 'Editorial Partner', 'Tokyo, Jepang', 'assets/img/mitra/logo-kyodo-news.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(11, 'OANA', 'Aliansi Kantor Berita Asia Pasifik', 'Linh Wirawan', 'secretariat@oananews.org', '+60 3 2693 9933', 'Network Member', 'Kuala Lumpur, Malaysia', 'assets/img/mitra/logo-oana.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(12, 'Reuters', 'Media & Informasi Global', 'Emma Johnson', 'partner.sales@reuters.com', '+44 20 7542 8313', 'Strategic Alliance', '5 Canada Square, London', 'assets/img/mitra/logo-reuters.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(13, 'SevenCyber', 'Keamanan Siber', 'Bima Arista', 'contact@sevencyber.id', '+62 21 7788 9900', 'Security Partner', 'BSD City, Tangerang', 'assets/img/mitra/logo-sevencyber.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(14, 'Sputnik', 'Kantor Berita Rusia', 'Sergey Petrov', 'world@sputniknews.com', '+7 495 139 62 70', 'Editorial Partner', 'Zubovskaya St. 4, Moscow', 'assets/img/mitra/logo-sputnik.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(15, 'TTXVN', 'Kantor Berita Vietnam', 'Nguyen Van Minh', 'info@vnanet.vn', '+84 24 3825 4313', 'Editorial Partner', '5 Ly Thuong Kiet, Hanoi', 'assets/img/mitra/logo-ttxvn.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(16, 'Xinhua News Agency', 'Kantor Berita China', 'Li Wei', 'service@mail.xinhuanet.com', '+86 10 6307 3666', 'Editorial Partner', '57 Xuanwumen Xidajie, Beijing', 'assets/img/mitra/logo-xinhua-news-agency.png', '2025-10-27 09:38:43', '2025-10-27 09:38:43'),
(18, 'AFP', 'Kantor Berita Internasional', 'Jean Dupont', 'partnership@afp.com', '+33 1 40 41 46 46', 'Editorial Partner', '11 Place de la Bourse, Paris', 'assets/img/mitra/logo-afp.png', '2025-10-27 12:53:18', '2025-10-27 12:53:18'),
(22, 'zaa1', 'kantor gweh', 'Zaki Halo', 'zas@gmail.com', '013131', 'Strategic Alliance', 'ada', 'assets/img/mitra/logo-6909844f5aeb24.68403928.jpg', '2025-11-04 11:42:55', '2025-11-04 11:42:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','employee','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@antara.local', '$2y$10$AgBV18LL.rR5irCrFx2d9u.mcbaWDmEEHm6C4T.1XDDwstXGXEa3G', 'admin', '2025-10-29 08:38:03', '2025-11-27 03:14:27'),
(2, 'Pegawai Redaksi', 'pegawai@antara.local', '$2y$10$E7Zbu9leC1lbemW3208VUOGtCkd/oFUB0cySj/sEMSaEQDZvqGDr.', 'employee', '2025-10-29 08:38:03', '2025-11-27 03:14:27'),
(3, 'Pelanggan Mitra', 'pelanggan@antara.local', '$2y$10$wbB2pYMOPHf1kVwmMe1zG.m.3qPxITFS.4LMrAja/pq5bEcrRQ6GG', 'customer', '2025-10-29 08:38:03', '2025-11-27 03:14:27'),
(4, 'Klien Alpha', 'alpha.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-11-27 03:14:27', '2025-11-27 03:14:27'),
(5, 'Klien Beta', 'beta.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-11-27 03:14:27', '2025-11-27 03:14:27'),
(6, 'Klien Gamma', 'gamma.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-11-27 03:14:27', '2025-11-27 03:14:27'),
(7, 'Klien Delta', 'delta.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-11-27 03:14:27', '2025-11-27 03:14:27'),
(8, 'Klien Epsilon', 'epsilon.client@antara.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '2025-11-27 03:14:27', '2025-11-27 03:14:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_key` varchar(40) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_documents`
--

INSERT INTO `user_documents` (`id`, `user_id`, `doc_key`, `file_path`, `original_name`, `uploaded_at`) VALUES
(1, 1, 'ktp', 'assets/uploads/my-info/user-1/ktp-20251125-161655-159c8353.jpg', 'KTM Zaki Abdussalam.jpg', '2025-11-25 16:16:55'),
(12, 3, 'ktp', 'assets/uploads/my-info/user-3/ktp-20251206-092343-209efeba.png', 'ttd abi bener.png', '2025-12-06 09:23:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `name` varchar(190) DEFAULT '',
  `type` varchar(120) DEFAULT '',
  `email` varchar(190) DEFAULT '',
  `phone` varchar(50) DEFAULT '',
  `mobile` varchar(50) DEFAULT '',
  `address` text DEFAULT NULL,
  `province` varchar(120) DEFAULT '',
  `city` varchar(120) DEFAULT '',
  `district` varchar(120) DEFAULT '',
  `subdistrict` varchar(120) DEFAULT '',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `name`, `type`, `email`, `phone`, `mobile`, `address`, `province`, `city`, `district`, `subdistrict`, `updated_at`) VALUES
(1, '', '', '', '', '', '', '', '', '', '', '2025-12-02 10:38:28'),
(3, '', '', '', '', '', '', '', '', '', '', '2025-12-02 10:39:04'),
(4, 'Alpha', 'Perusahaan Media', 'alhpa@gmail.com', '021 - 380 1234', '0812 9000 1234', 'Jalan Medan Merdeka Selatan No.17', 'DKI Jakarta', 'Jakarta Pusat', 'Gambir', 'Gambir', '2025-12-02 10:38:04');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_username_unique` (`username`),
  ADD UNIQUE KEY `clients_email_unique` (`email`),
  ADD UNIQUE KEY `clients_code_unique` (`code`),
  ADD KEY `clients_user_id_idx` (`user_id`);

--
-- Indeks untuk tabel `company_pic`
--
ALTER TABLE `company_pic`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks untuk tabel `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks untuk tabel `crm_client_subscriptions`
--
ALTER TABLE `crm_client_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_client_subscriptions_user_idx` (`user_id`),
  ADD KEY `crm_client_subscriptions_customer_idx` (`customer_user_id`),
  ADD KEY `crm_client_subscriptions_client_idx` (`client_id`),
  ADD KEY `crm_client_subscriptions_status_idx` (`status`),
  ADD KEY `crm_client_subscriptions_cycle_idx` (`cycle`);

--
-- Indeks untuk tabel `crm_deals`
--
ALTER TABLE `crm_deals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_deals_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `crm_estimates`
--
ALTER TABLE `crm_estimates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_estimates_ref_no_unique` (`ref_no`),
  ADD KEY `crm_estimates_status_index` (`status`),
  ADD KEY `crm_estimates_estimate_date_index` (`estimate_date`),
  ADD KEY `crm_estimates_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `crm_leads`
--
ALTER TABLE `crm_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_leads_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `crm_payments`
--
ALTER TABLE `crm_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `crm_payments_status_index` (`status`),
  ADD KEY `crm_payments_paid_at_index` (`paid_at`),
  ADD KEY `crm_payments_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `crm_pipeline_entries`
--
ALTER TABLE `crm_pipeline_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_pipeline_entries_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `crm_product_invoices`
--
ALTER TABLE `crm_product_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_product_invoices_user_invoice_unique` (`user_id`,`invoice_no`),
  ADD KEY `crm_product_invoices_status_index` (`status`),
  ADD KEY `crm_product_invoices_issue_date_index` (`issue_date`),
  ADD KEY `crm_product_invoices_user_id_index` (`user_id`),
  ADD KEY `crm_product_invoices_direction_index` (`direction`),
  ADD KEY `crm_product_invoices_recipient_user_index` (`recipient_user_id`);

--
-- Indeks untuk tabel `crm_product_invoice_items`
--
ALTER TABLE `crm_product_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_product_invoice_items_invoice_id_foreign` (`invoice_id`);

--
-- Indeks untuk tabel `email_messages`
--
ALTER TABLE `email_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_messages_folder_index` (`folder`),
  ADD KEY `email_messages_received_at_index` (`received_at`),
  ADD KEY `email_messages_starred_index` (`is_starred`),
  ADD KEY `email_messages_read_index` (`is_read`),
  ADD KEY `email_messages_category_index` (`category`),
  ADD KEY `email_messages_scheduled_index` (`scheduled_for`),
  ADD KEY `email_messages_sender_user_index` (`sender_user_id`),
  ADD KEY `email_messages_recipient_user_index` (`recipient_user_id`);

--
-- Indeks untuk tabel `mitra_partners`
--
ALTER TABLE `mitra_partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mitra_partners_nama_index` (`nama`),
  ADD KEY `mitra_partners_status_index` (`status`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_doc_unique` (`user_id`,`doc_key`);

--
-- Indeks untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `crm_client_subscriptions`
--
ALTER TABLE `crm_client_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `crm_deals`
--
ALTER TABLE `crm_deals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `crm_estimates`
--
ALTER TABLE `crm_estimates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `crm_leads`
--
ALTER TABLE `crm_leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `crm_payments`
--
ALTER TABLE `crm_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `crm_pipeline_entries`
--
ALTER TABLE `crm_pipeline_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `crm_product_invoices`
--
ALTER TABLE `crm_product_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `crm_product_invoice_items`
--
ALTER TABLE `crm_product_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `email_messages`
--
ALTER TABLE `email_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `mitra_partners`
--
ALTER TABLE `mitra_partners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `crm_client_subscriptions`
--
ALTER TABLE `crm_client_subscriptions`
  ADD CONSTRAINT `crm_client_subscriptions_client_fk` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_client_subscriptions_customer_fk` FOREIGN KEY (`customer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_client_subscriptions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `crm_payments`
--
ALTER TABLE `crm_payments`
  ADD CONSTRAINT `crm_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `crm_product_invoices` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `crm_product_invoice_items`
--
ALTER TABLE `crm_product_invoice_items`
  ADD CONSTRAINT `crm_product_invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `crm_product_invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
