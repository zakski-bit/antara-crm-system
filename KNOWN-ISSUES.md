# Known Issues

## 1. Kredensial sensitif masih hardcoded

Saat ini masih ada nilai default sensitif langsung di source code:

- kredensial SMTP pada `includes/auth-config.php`
- kredensial database pada `includes/db.php`

Risiko:

- kredensial mudah bocor
- konfigurasi produksi sulit dipisahkan dari development

Tindakan yang disarankan:

- pindahkan seluruh credential ke environment variable
- buat `.env.example`
- rotasi password yang sudah pernah tertulis di source code

## 2. Tombol login Google belum lengkap

Di `page-login.php` ada tombol menuju `auth/google-login.php`, tetapi file tersebut belum ditemukan pada proyek frontend ini.

Risiko:

- penerima proyek mengira fitur Google login sudah siap
- flow login gagal bila tombol dipakai

Tindakan yang disarankan:

- jelaskan bahwa fitur belum selesai, atau
- sertakan file integrasinya, atau
- sembunyikan tombol sampai implementasi lengkap

## 3. Endpoint token login backend belum terlihat pada route utama

Frontend membentuk URL:

- `http://localhost:8080/auth/token-login`

Namun route tersebut belum terlihat pada `c:\docker-php\php\routes\web.php`.

Risiko:

- redirect dari frontend ke backend bisa gagal
- tim penerima bingung apakah ini fitur aktif atau placeholder

Tindakan yang disarankan:

- konfirmasi apakah endpoint ada di file lain
- jika belum ada, implementasikan atau ubah flow
- dokumentasikan status aktual integrasi frontend ke backend

## 4. Flow auth frontend dan backend belum seragam

Frontend saat ini:

- registrasi langsung insert ke tabel `users`
- login memakai OTP email sendiri

Backend saat ini:

- memiliki logic auth sendiri di `AuthController.php`
- memiliki flow verifikasi registrasi sendiri melalui `user_verifications`

Risiko:

- developer baru bingung flow mana yang resmi
- duplikasi logic auth makin sulit dirawat
- perilaku user bisa berbeda antara frontend dan backend

Tindakan yang disarankan:

- tetapkan satu flow auth resmi
- dokumentasikan flow yang dipakai produksi
- hilangkan flow lama atau beri status deprecated

## 5. Frontend dan backend sangat terikat ke database yang sama

Frontend langsung membuat dan membaca data auth pada database backend.

Risiko:

- coupling tinggi
- perubahan schema backend bisa langsung merusak frontend
- debugging lintas aplikasi jadi lebih sulit

Tindakan yang disarankan:

- dokumentasikan tabel integrasi yang dipakai bersama
- catat dependency lintas aplikasi
- pertimbangkan lapisan API jika nanti arsitektur ingin dirapikan

## 6. Dokumentasi role dan akses belum lengkap

Dari route backend terlihat ada akses berbeda untuk pelanggan, pegawai, dan staff, tetapi batas peran detailnya belum tertulis sebagai dokumen operasional.

Risiko:

- salah pemberian akun
- salah ekspektasi fitur per role
- proses QA dan handover lambat

Tindakan yang disarankan:

- buat dokumen role access
- definisikan dashboard, modul, dan aksi per role

## 7. Belum ada paket handover deployment yang lengkap

Saat ini proyek belum terlihat memiliki paket handover formal seperti:

- `.env.example`
- deployment checklist
- database notes
- daftar akun uji

Risiko:

- serah terima bergantung pada penjelasan lisan
- setup ulang memakan waktu

Tindakan yang disarankan:

- lengkapi dokumen setup
- siapkan dump database
- siapkan akun test
- siapkan checklist pasca deploy

