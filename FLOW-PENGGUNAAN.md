# Flow Penggunaan

## Ringkasan alur

Flow utama sistem:

1. User masuk ke landing page
2. User daftar atau login
3. Sistem mengirim OTP email untuk login
4. User verifikasi OTP
5. User diarahkan ke backend CRM
6. User memakai dashboard sesuai role

## 1. Landing page

- Root frontend ada di `index.php`
- File ini meneruskan ke `index-9.php`
- Fungsinya sebagai halaman publik sebelum user masuk

## 2. Registrasi akun

Lokasi utama:

- Halaman: `page-login.php`
- Handler: `includes/register-handler.php`

Alur:

1. User membuka `page-login.php`
2. User mengisi nama lengkap, email, dan password
3. Form dikirim ke `includes/register-handler.php`
4. Sistem validasi input
5. Jika email belum dipakai, user langsung disimpan ke tabel `users`
6. Role default yang dipakai adalah `customer`
7. User diminta login dengan akun barunya

Aturan:

- Semua kolom wajib diisi
- Email harus valid
- Password minimal 8 karakter
- Email harus unik

Catatan:

- Di frontend ini registrasi belum memakai verifikasi email

## 3. Login akun

Lokasi utama:

- Halaman: `page-login.php`
- Handler: `includes/login-handler.php`

Alur:

1. User memasukkan email dan password
2. Sistem mencocokkan data dengan tabel `users`
3. Jika cocok, sistem membuat OTP login
4. OTP dikirim ke email user
5. Sistem menyimpan sesi `auth_pending`
6. User memasukkan kode OTP
7. Sistem memverifikasi OTP
8. Jika valid, user dianggap login pada frontend

Aturan OTP:

- OTP disimpan di tabel `login_email_otps`
- OTP memiliki masa berlaku
- OTP punya batas percobaan
- OTP punya cooldown untuk kirim ulang

## 4. Reset password

Lokasi utama:

- Halaman: `page-reset-password.php`
- Handler: `includes/password-reset-handler.php`

Alur:

1. User membuka halaman reset password
2. User memasukkan email akun
3. Sistem mengecek apakah email terdaftar
4. Jika terdaftar, sistem mengirim OTP reset
5. User memasukkan OTP, password baru, dan konfirmasi password
6. Sistem memverifikasi OTP
7. Jika valid, password di tabel `users` diperbarui

Aturan reset:

- Password baru minimal 8 karakter
- Password baru harus mengandung huruf dan angka
- OTP hanya bisa dipakai sekali
- OTP reset memiliki masa berlaku

Catatan:

- Untuk alasan keamanan, jika email tidak ditemukan, sistem tetap menampilkan pesan generik
- Akun demo/bypass OTP tidak diproses melalui reset password OTP

## 5. Akses ke member area

Setelah login frontend sukses:

1. Frontend membuat token sekali pakai
2. Token disimpan ke tabel `auth_tokens`
3. User diarahkan ke backend CRM melalui URL token login

Implikasi teknis:

- Frontend dan backend berbagi database yang sama
- Frontend berperan sebagai gerbang masuk
- Dashboard utama tetap berada di backend CRM

## 6. Flow staff/admin

Setelah masuk ke backend, staff/admin mengakses modul CRM seperti:

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
- Email

## 7. SOP singkat pelanggan

1. Buka landing page
2. Daftar akun jika belum punya
3. Login dengan email dan password
4. Cek email untuk OTP
5. Masukkan OTP
6. Masuk ke dashboard pelanggan
7. Jika lupa password, gunakan halaman reset password

## 8. SOP singkat staff

1. Login ke sistem
2. Masuk ke dashboard backend
3. Gunakan modul sesuai hak akses
4. Kelola data CRM
5. Logout setelah selesai

