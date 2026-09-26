# Handover Proyek CRM

## 1. Tujuan proyek

Proyek ini terdiri dari:

- Frontend landing + autentikasi PHP di `C:\Proyek-CRM\FrontEnd-CRM\autoexpert-php`
- Backend CRM PHP di `C:\docker-php\php`
- Database MariaDB `antara_crm`

Fungsi utama frontend:

- Menampilkan landing page/public page
- Registrasi akun
- Login dengan email dan password
- Verifikasi OTP email saat login
- Reset password dengan OTP email
- Mengarahkan user yang sudah login ke backend CRM

## 2. Struktur yang perlu dipahami staff

### Frontend

- `index.php`: wrapper ke halaman utama `index-9.php`
- `page-login.php`: halaman login, register, OTP login, tombol masuk ke member area
- `page-reset-password.php`: halaman reset password berbasis OTP
- `includes/db.php`: koneksi database frontend ke MariaDB backend
- `includes/auth-config.php`: konfigurasi OTP dan SMTP
- `includes/login-handler.php`: proses login + OTP login
- `includes/password-reset-handler.php`: proses reset password + OTP reset
- `includes/register-handler.php`: registrasi user baru
- `includes/otp-mailer.php`: pengiriman email OTP via SMTP

### Backend

- `c:\docker-php\docker-compose.yml`: stack container
- `c:\docker-php\php\routes\web.php`: route utama backend CRM
- `c:\docker-php\php\app\Controllers\AuthController.php`: login backend
- `c:\docker-php\php\database\antara_crm.sql`: dump database utama

## 3. Cara menjalankan sistem

### Service yang terlibat

- MariaDB: container `app_mariadb`
- PHP app: container `app_php`
- Nginx: container `app_nginx`
- phpMyAdmin: container `app_pma`

### Endpoint lokal

- Backend CRM: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`
- Frontend landing/auth: dijalankan dari folder frontend terpisah

### Konfigurasi database

Sumber runtime Docker:

- `DB_DATABASE=antara_crm`
- `DB_USERNAME=appuser`
- `DB_PASSWORD=apppassword`

Frontend saat ini mengakses database langsung melalui:

- host: `app_mariadb`
- port: `3306`
- database: `antara_crm`
- user: `appuser`

## 4. Alur penggunaan web yang wajib didokumentasikan

### A. Landing page

- User masuk dari root `index.php`
- Root meneruskan ke `index-9.php`
- Halaman ini berfungsi sebagai halaman publik/promosi sebelum login

### B. Registrasi akun

Alur saat ini:

1. User membuka `page-login.php`
2. User mengisi nama, email, dan password
3. Form submit ke `includes/register-handler.php`
4. Data user langsung disimpan ke tabel `users`
5. Role default yang dipakai adalah `customer`

Yang perlu dijelaskan ke staff:

- Tidak ada verifikasi email di alur registrasi frontend ini
- Password minimal 8 karakter
- Email harus unik

### C. Login akun

Alur saat ini:

1. User login di `page-login.php`
2. Form submit ke `includes/login-handler.php`
3. Sistem validasi email + password ke tabel `users`
4. Jika cocok, sistem membuat OTP login
5. OTP dikirim ke email user
6. User input OTP
7. Jika OTP valid, session frontend dibuat
8. Setelah login sukses, halaman menampilkan tombol masuk ke member area

Yang perlu dijelaskan ke staff:

- OTP login disimpan pada tabel `login_email_otps`
- OTP punya masa berlaku, batas percobaan, dan cooldown kirim ulang
- Ada sesi `auth_pending` untuk user yang sedang menunggu verifikasi OTP

### D. Reset password

Alur saat ini:

1. User membuka `page-reset-password.php`
2. User memasukkan email
3. Sistem cek apakah email ada
4. Jika ada, sistem kirim OTP reset
5. User memasukkan OTP, password baru, dan konfirmasi password
6. Jika valid, password di tabel `users` diperbarui

Aturan yang perlu dijelaskan:

- Password baru minimal 8 karakter
- Password baru harus mengandung huruf dan angka
- OTP reset hanya berlaku satu kali
- Akun demo/bypass OTP tidak diproses dengan reset OTP

### E. Akses ke member area / backend CRM

Setelah login frontend berhasil:

1. Frontend membuat token sekali pakai
2. Token disimpan ke tabel `auth_tokens`
3. User diarahkan ke backend CRM melalui URL token login

Hal ini wajib dijelaskan ke staff karena alurnya lintas aplikasi:

- Frontend dan backend memakai database yang sama
- Frontend bukan dashboard utama, tetapi pintu masuk ke backend CRM

## 5. Tabel database yang perlu diketahui

Minimal staff harus diberi penjelasan tabel berikut:

- `users`: data user utama, email, password hash, role
- `login_email_otps`: OTP login dan OTP reset password
- `auth_tokens`: token sekali pakai untuk pindah dari frontend ke backend

Tambahan dari backend:

- Tabel CRM lain mengikuti dump `c:\docker-php\php\database\antara_crm.sql`

## 6. Konfigurasi environment yang wajib diserahkan

Jangan hanya serahkan source code. Sertakan juga daftar variabel dan letak penggunaannya:

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

### OTP bypass untuk akun demo

- `OTP_BYPASS_ENABLED`
- `OTP_BYPASS_EMAILS`
- `OTP_BYPASS_DOMAINS`
- `OTP_BYPASS_ROLES`

### SMTP email OTP

- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_ENCRYPTION`
- `SMTP_FROM_EMAIL`
- `SMTP_FROM_NAME`

## 7. Hak akses / role yang perlu dijelaskan

Minimal dokumentasikan:

- `customer` / pelanggan
- pegawai
- admin / staff

Hal yang harus dijelaskan:

- Role mana yang boleh masuk dashboard tertentu
- Menu backend mana yang hanya untuk staff
- Data mana yang bisa dilihat pelanggan dan mana yang hanya bisa diubah staff

## 8. Modul backend yang perlu dijelaskan ke penerima

Berdasarkan route backend, modul yang aktif mencakup:

- Dashboard
- Mitra
- Clients
- Deals
- Estimates
- Invoices
- Leads
- Pipeline
- Subscriptions
- Payments
- Calendar API
- Email

Untuk setiap modul, dokumentasikan:

1. Tujuan modul
2. Siapa yang boleh mengakses
3. Data input utama
4. Output/tampilan utama
5. Relasi tabel jika ada
6. Proses create, update, delete

## 9. Yang harus dijelaskan sebagai SOP penggunaan

Sebaiknya staff menerima SOP singkat per peran:

### SOP pelanggan

- Cara daftar
- Cara login
- Cara verifikasi OTP
- Cara reset password
- Cara masuk ke dashboard pelanggan

### SOP staff/admin

- Cara login backend
- Cara kelola mitra
- Cara kelola client
- Cara kelola deal/leads/pipeline
- Cara cek payment/invoice/subscription
- Cara logout

## 10. Known issues / catatan penting sebelum handover

Bagian ini sangat penting agar penerima tidak salah asumsi.

### 1. Kredensial sensitif masih hardcoded

Saat ini ada nilai default sensitif langsung di source code frontend:

- kredensial SMTP di `includes/auth-config.php`
- kredensial database di `includes/db.php`

Sebelum handover produksi, pindahkan semua credential ke environment variable dan rotasi password yang sudah terlanjur tertulis di source code.

### 2. Tombol login Google belum lengkap

Di `page-login.php` ada tombol menuju `auth/google-login.php`, tetapi file tersebut belum ditemukan pada proyek frontend ini. Dokumentasikan bahwa fitur Google login belum siap atau file integrasinya belum ikut terserah.

### 3. Endpoint token-login backend belum terlihat pada route utama

Frontend membentuk URL `http://localhost:8080/auth/token-login`, tetapi route itu belum terlihat di `c:\docker-php\php\routes\web.php`. Ini harus dijelaskan:

- apakah endpoint ada di file lain
- apakah belum diimplementasikan
- atau apakah alur redirect frontend masih placeholder

### 4. Alur auth frontend dan backend belum sepenuhnya seragam

Frontend register langsung ke tabel `users`, sedangkan backend memiliki logika register/verifikasi OTP sendiri di `AuthController`. Dokumentasikan alur mana yang resmi dipakai agar developer penerima tidak bingung.

## 11. Paket dokumen yang sebaiknya Anda serahkan

Minimal serahkan 6 dokumen ini:

1. `README-SETUP.md`
   Berisi cara install, dependency, Docker, env, dan URL akses.

2. `FLOW-PENGGUNAAN.md`
   Berisi alur user: daftar, login, OTP, reset password, masuk dashboard.

3. `ROLE-ACCESS.md`
   Berisi hak akses tiap role.

4. `DATABASE-NOTES.md`
   Berisi tabel penting, relasi utama, dan tabel yang dipakai auth/OTP.

5. `KNOWN-ISSUES.md`
   Berisi fitur belum selesai, gap integrasi, dan technical debt.

6. `DEPLOYMENT-CHECKLIST.md`
   Berisi checklist env, SMTP, database, migration/import SQL, dan testing setelah deploy.

## 12. Checklist serah terima

Sebelum proyek diberikan ke staff ahli coding, pastikan:

- source code frontend dan backend lengkap
- file `.env.example` tersedia
- kredensial asli tidak tertulis di repo
- dump database terbaru tersedia
- daftar akun contoh/test tersedia
- flow login OTP sudah dites
- flow reset password sudah dites
- role customer/staff/admin sudah diuji
- URL frontend dan backend sudah dicatat
- fitur yang belum selesai sudah ditulis jelas

## 13. Rekomendasi praktis

Kalau ingin staff cepat paham, jangan hanya jelaskan file. Jelaskan alur bisnis per layar:

1. User masuk dari landing page
2. User daftar atau login
3. OTP dikirim ke email
4. User diverifikasi
5. User diarahkan ke CRM
6. Staff mengelola modul CRM sesuai role

Dokumen handover yang bagus untuk proyek ini harus menggabungkan:

- penjelasan fungsi halaman
- alur user
- setup teknis
- struktur database
- role akses
- known issues

