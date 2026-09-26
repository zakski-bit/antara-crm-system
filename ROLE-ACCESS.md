# Role Access

## Role yang perlu dikenali

Minimal role yang harus dipahami saat handover:

- `customer` atau pelanggan
- `pegawai`
- `admin` atau staff

## Prinsip akses

- Frontend dipakai untuk landing, login, OTP, dan reset password
- Backend dipakai untuk dashboard dan modul CRM utama
- Akses dashboard dan modul harus mengikuti role user

## Akses pelanggan

Role pelanggan dipakai untuk user akhir yang memakai layanan CRM dari sisi client.

Hak akses minimum yang perlu dijelaskan:

- Login dari frontend
- Verifikasi OTP login
- Reset password
- Masuk ke dashboard pelanggan
- Melihat data yang memang disediakan untuk pelanggan

Yang perlu dipastikan saat handover:

- Halaman dashboard pelanggan yang resmi
- Data apa saja yang bisa dilihat pelanggan
- Apakah pelanggan hanya read-only atau boleh mengubah data tertentu

## Akses pegawai

Role pegawai dipakai untuk user internal operasional.

Hak akses minimum yang perlu dijelaskan:

- Login ke backend
- Masuk ke dashboard pegawai
- Mengakses modul operasional yang diperlukan
- Mengelola data sesuai tanggung jawabnya

Yang perlu dipastikan saat handover:

- Modul mana yang boleh diakses pegawai
- Aksi mana yang boleh dibuat, diubah, atau dihapus
- Batas akses dibanding admin

## Akses admin/staff

Role admin atau staff dipakai untuk pengelola penuh sistem CRM.

Hak akses minimum yang perlu dijelaskan:

- Login ke backend
- Masuk ke dashboard admin
- Mengelola modul CRM utama
- Mengelola data master dan operasional

Modul backend yang saat ini terlihat aktif dari route:

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

## Hal yang harus ditulis per role

Untuk setiap role, dokumentasikan:

1. URL dashboard utamanya
2. Modul yang boleh diakses
3. Data yang boleh dilihat
4. Data yang boleh dibuat
5. Data yang boleh diubah
6. Data yang boleh dihapus

## Hal yang masih perlu dikonfirmasi

Beberapa detail role belum sepenuhnya terlihat dari frontend ini, jadi saat serah terima sebaiknya dijelaskan eksplisit:

- Mapping role ke dashboard sebenarnya
- Perbedaan akses `pegawai` dan `admin`
- Modul mana yang hanya untuk staff
- Apakah `customer` bisa mengakses semua fitur dashboard pelanggan atau hanya sebagian

## Format dokumentasi yang disarankan

Tuliskan tabel atau daftar sederhana seperti:

- Role: `customer`
- Dashboard: pelanggan
- Modul: dashboard pelanggan, data akun, data langganan bila ada
- Aksi: lihat, ubah profil, reset password

- Role: `pegawai`
- Dashboard: pegawai
- Modul: sesuai kebutuhan operasional
- Aksi: lihat, tambah, ubah, atau hapus sesuai modul

- Role: `admin`
- Dashboard: admin
- Modul: seluruh modul CRM yang diizinkan sistem
- Aksi: penuh

