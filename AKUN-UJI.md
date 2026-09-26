# Akun Uji

## Catatan penggunaan

Dokumen ini untuk kebutuhan internal handover dan testing. Jangan dipublikasikan ke repo publik atau dibagikan ke pihak yang tidak perlu.

## Aturan bypass OTP saat ini

Berdasarkan konfigurasi frontend saat ini:

- `OTP_BYPASS_ENABLED` default `true`
- `OTP_BYPASS_DOMAINS` default `antara.local`
- `OTP_BYPASS_EMAILS` default kosong
- `OTP_BYPASS_ROLES` default kosong

Implikasinya:

- selama bypass aktif
- dan domain default tidak diubah
- akun dengan email `@antara.local` akan lolos login tanpa OTP email

Catatan:

- nilai final tetap mengikuti environment saat runtime
- jika env override di server berbeda, perilaku bypass bisa berubah

## Akun uji yang ditemukan

Sumber utama daftar ini:

- `c:\docker-php\php\List akun CRM.txt`
- seed user pada database/dump yang memakai domain `@antara.local`

## Daftar akun

| Nama | Role | Email | Password | Catatan bypass |
| --- | --- | --- | --- | --- |
| Administrator | admin | `admin@antara.local` | `admin123` | Bypass OTP jika domain bypass aktif |
| Pegawai Redaksi | employee / pegawai | `pegawai@antara.local` | `pegawai123` | Bypass OTP jika domain bypass aktif |
| Pelanggan Mitra | customer / pelanggan | `pelanggan@antara.local` | `pelanggan123` | Bypass OTP jika domain bypass aktif |
| Klien Alpha | customer | `alpha.client@antara.local` | `password` | Bypass OTP jika domain bypass aktif |
| Klien Beta | customer | `beta.client@antara.local` | `password` | Bypass OTP jika domain bypass aktif |
| Klien Gamma | customer | `gamma.client@antara.local` | `password` | Bypass OTP jika domain bypass aktif |
| Klien Delta | customer | `delta.client@antara.local` | `password` | Bypass OTP jika domain bypass aktif |
| Klien Epsilon | customer | `epsilon.client@antara.local` | `password` | Bypass OTP jika domain bypass aktif |

## Cara verifikasi cepat

Untuk memastikan bypass benar-benar aktif di environment yang sedang dipakai, cek:

1. nilai env `OTP_BYPASS_ENABLED`
2. nilai env `OTP_BYPASS_DOMAINS`
3. apakah email akun memakai domain `antara.local`

## Saran sebelum serah terima

- pisahkan akun demo dari akun produksi
- ganti password akun uji bila proyek akan dipakai di environment nyata
- dokumentasikan akun mana yang hanya untuk demo
- nonaktifkan bypass OTP di production jika tidak benar-benar diperlukan

