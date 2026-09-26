# README Setup

## Gambaran sistem

Proyek CRM ini terdiri dari dua bagian:

- Frontend landing + autentikasi PHP: `C:\Proyek-CRM\FrontEnd-CRM\autoexpert-php`
- Backend CRM PHP: `C:\docker-php\php`

Keduanya terhubung ke database MariaDB yang sama: `antara_crm`.

## Struktur penting

### Frontend

- `index.php`: entry root frontend
- `page-login.php`: login, register, OTP login, akses ke member area
- `page-reset-password.php`: reset password berbasis OTP
- `includes/db.php`: koneksi DB frontend
- `includes/auth-config.php`: konfigurasi OTP dan SMTP
- `includes/login-handler.php`: proses login + OTP
- `includes/password-reset-handler.php`: reset password + OTP
- `includes/register-handler.php`: registrasi user

### Backend

- `c:\docker-php\docker-compose.yml`: definisi service Docker
- `c:\docker-php\php\routes\web.php`: route backend CRM
- `c:\docker-php\php\app\Controllers\AuthController.php`: auth backend
- `c:\docker-php\php\database\antara_crm.sql`: dump database

## Service Docker

Stack backend menggunakan:

- `app_mariadb`
- `app_php`
- `app_nginx`
- `app_pma`

## Endpoint lokal

- Backend CRM: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

Frontend landing/auth dijalankan dari folder frontend terpisah.

## Konfigurasi database

Nilai yang dipakai Docker saat ini:

- `DB_HOST=mariadb`
- `DB_PORT=3306`
- `DB_DATABASE=antara_crm`
- `DB_USERNAME=appuser`
- `DB_PASSWORD=apppassword`
- `DB_ROOT_PASSWORD=rootpassword`

Frontend saat ini mengakses DB langsung dengan:

- host `app_mariadb`
- port `3306`
- database `antara_crm`
- user `appuser`

## Konfigurasi environment yang harus disiapkan

### Database

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD`

### OTP

- `APP_NAME`
- `OTP_LENGTH`
- `OTP_TTL_MINUTES`
- `OTP_MAX_ATTEMPTS`
- `OTP_RESEND_COOLDOWN_SECONDS`
- `OTP_SECRET`

### OTP bypass

- `OTP_BYPASS_ENABLED`
- `OTP_BYPASS_EMAILS`
- `OTP_BYPASS_DOMAINS`
- `OTP_BYPASS_ROLES`

### SMTP

- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_ENCRYPTION`
- `SMTP_FROM_EMAIL`
- `SMTP_FROM_NAME`

## Tabel minimum yang harus ada

- `users`
- `login_email_otps`
- `auth_tokens`

Tabel backend lainnya mengikuti dump `c:\docker-php\php\database\antara_crm.sql`.

## Dokumen internal terkait akun uji

- `AKUN-UJI.md`: daftar akun contoh, role, dan catatan bypass OTP

## Checklist setup

- Pastikan Docker berjalan.
- Pastikan container MariaDB, PHP, Nginx, dan phpMyAdmin aktif.
- Pastikan database `antara_crm` tersedia.
- Pastikan frontend bisa terkoneksi ke `app_mariadb`.
- Pastikan konfigurasi SMTP valid untuk kirim OTP.
- Pastikan tabel `users`, `login_email_otps`, dan `auth_tokens` tersedia.
- Pastikan backend bisa dibuka di `http://localhost:8080`.

## Checklist serah terima teknis

- Source code frontend dan backend lengkap
- Dump database terbaru tersedia
- File env contoh tersedia
- URL akses dicatat
- Akun uji dicatat
- Flow login OTP dan reset password sudah dites
- Role pelanggan, pegawai, dan admin sudah diuji
