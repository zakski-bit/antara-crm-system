# 🏢 ANTARA CRM & Digital Media Portal

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Vercel-success?style=for-the-badge&logo=vercel)](https://crm-portfolio-live.vercel.app)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)](https://mariadb.org/)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

Sistem Manajemen Hubungan Pelanggan (CRM) dan Portal Distribusi Konten Digital untuk **Lembaga Kantor Berita Nasional (LKBN) ANTARA**. Proyek ini dikembangkan untuk memfasilitasi monitoring infrastruktur media berita, manajemen lisensi kemitraan korporasi, serta billing dan langganan produk multimedia (teks, foto HD, video broadcast).

---

## 🌐 Demo Portofolio Live

Website versi showcase interaktif dapat diakses langsung tanpa perlu menjalankan database lokal:

👉 **[https://crm-portfolio-live.vercel.app](https://crm-portfolio-live.vercel.app)**

- **Landing Page Portal ANTARA**: [https://crm-portfolio-live.vercel.app](https://crm-portfolio-live.vercel.app)
- **Simulasi Login & OTP 6-Digit**: [https://crm-portfolio-live.vercel.app/login.html](https://crm-portfolio-live.vercel.app/login.html)
- **Dashboard Admin CRM**: [https://crm-portfolio-live.vercel.app/admin/index.html](https://crm-portfolio-live.vercel.app/admin/index.html)
- **Portal Kemitraan Pelanggan**: [https://crm-portfolio-live.vercel.app/admin/customer.html](https://crm-portfolio-live.vercel.app/admin/customer.html)

---

## 🏗️ Arsitektur Sistem

Proyek ini dibangun dengan arsitektur modular yang terbagi menjadi:

```
├── BackEnd-CRM/                # Core Backend CRM (PHP MVC)
│   └── docker-php/             # Konfigurasi Docker & Source Code
│       ├── docker-compose.yml  # Multi-container orchestration (PHP, Nginx, MariaDB, PMA)
│       ├── nginx/              # Konfigurasi reverse proxy & virtual host
│       └── php/                # Aplikasi backend (Controllers, Models, Routes, Views)
│           ├── app/            # Application logic & MVC architecture
│           ├── database/       # Skema database awal (antara_crm.sql)
│           ├── routes/         # Routing web & API
│           └── template/       # Template UI dashboard Superadmin & Pelanggan
├── FrontEnd-CRM/               # Landing Page & Autentikasi Pengguna
│   └── autoexpert-php/         # Portal landing newsroom & form login OTP
├── crm-portfolio-live/         # Versi static live preview siap hosting (Vercel / GitHub Pages)
└── docs/                       # Dokumentasi serah terima & technical guides
```

---

## ✨ Fitur Utama

1. **Multi-Role Access Control (RBAC):**
   - **Superadmin (Divisi IT / Redaksi)**: Monitoring kesehatan sistem real-time (Antaranews, NRCS, MAM, API Gateway), penanganan insiden, kalender maintenance, dan tiket support.
   - **Pelanggan / Mitra Korporasi**: Manajemen kuota unduhan berita, lisensi komersial foto HD, klip TV broadcast, dan riwayat invoice.
   - **Pegawai Redaksi**: Akses editorial, agenda liputan, dan korespondensi.

2. **Sistem Autentikasi Aman Berbasis OTP:**
   - Login ganda (Password + One-Time Password 6-digit ke email).
   - Fitur reset password terverifikasi OTP.
   - Mekanisme bypass domain uji (`@antara.local`) untuk staging dan evaluasi cepat.

3. **Manajemen Transaksi & Billing:**
   - Pembuatan dan tracking invoice tagihan kemitraan.
   - Verifikasi bukti pembayaran otomatis / manual.
   - Monitoring siklus langganan bulanan dan tahunan.

4. **Containerized Deployment:**
   - Lingkungan terisolasi menggunakan Docker Compose:
     - `app_php`: PHP-FPM runtime
     - `app_nginx`: Web server & reverse proxy
     - `app_mariadb`: Database relasional MySQL/MariaDB
     - `app_pma`: phpMyAdmin untuk manajemen database lokal

---

## 🚀 Panduan Menjalankan Secara Lokal (Docker)

### Prasyarat:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) telah terpasang dan berjalan di komputer Anda.

### Langkah-langkah:

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/zakski-bit/antara-crm-system.git
   cd antara-crm-system
   ```

2. **Jalankan Container Docker:**
   ```bash
   cd BackEnd-CRM/docker-php
   docker compose up -d
   ```

3. **Akses Layanan:**
   - **Backend CRM Dashboard**: `http://localhost:8080`
   - **phpMyAdmin**: `http://localhost:8081`

4. **Kredensial Database Default:**
   - **Host**: `mariadb` (port `3306`)
   - **Database**: `antara_crm`
   - **User**: `appuser`
   - **Password**: `apppassword`

---

## 🔑 Akun Uji Coba (Staging / Demo)

| Role | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Superadmin (IT Lead)** | `admin@antara.local` | `admin123` | Akses penuh dashboard operasional & sistem |
| **Pegawai Redaksi** | `pegawai@antara.local` | `pegawai123` | Akses modul konten & to-do |
| **Pelanggan Mitra** | `pelanggan@antara.local` | `pelanggan123` | Akses modul langganan & tagihan |

> *Catatan: Akun dengan domain `@antara.local` secara default mengaktifkan fitur OTP bypass untuk kemudahan demonstrasi.*

---

## 📄 Lisensi & Kontributor

- **Pengembang**: Dikembangkan sebagai proyek magang Sistem Informasi CRM di **LKBN ANTARA**.
- **Repositori**: [https://github.com/zakski-bit/antara-crm-system](https://github.com/zakski-bit/antara-crm-system)
- **Live Demo**: [https://crm-portfolio-live.vercel.app](https://crm-portfolio-live.vercel.app)
