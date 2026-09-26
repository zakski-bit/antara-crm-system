# 🚀 Antara CRM - Live Portfolio Showcase

Showcase versi web statis (*Interactive Mockup & UI Live*) dari proyek magang **Sistem CRM & Portal Digital Media LKBN ANTARA**.

Direktori ini sudah 100% siap di-hosting ke **GitHub Pages** atau **Vercel** secara gratis tanpa memerlukan server PHP atau database MySQL aktif.

---

## 🌟 Halaman yang Tersedia

1. **`index.html` (Landing Page)**: Tampilan utama portal media ANTARA, carousel video headline, produk, mitra, klien, artikel berita, dan kontak redaksi.
2. **`login.html` (Simulasi Login & OTP)**: Halaman autentikasi berlatar belakang video interaktif dengan tombol *Quick Access Demo* dan modal simulasi input 6-digit OTP.
3. **`admin/index.html` (Dashboard Admin CRM)**: Dashboard monitoring kesehatan server/layanan digital, log insiden, jadwal maintenance, dan tiket keluhan relasi.
4. **`admin/customer.html` (Portal Layanan Pelanggan)**: Dashboard khusus mitra korporasi/pelanggan untuk memantau paket langganan berita, kuota unduhan, dan invoice pembayaran.
5. **Floating Demo Switcher Bar**: Bar navigasi mengambang di bawah layar untuk memudahkan recruiter/penguji berpindah antar-halaman hanya dengan 1 klik.

---

## ⚡ Cara Deploy 1: Vercel (Paling Cepat & Modern)

### Opsi A: Menggunakan Vercel CLI (1 Menit)
1. Buka terminal di folder ini:
   ```bash
   cd "C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM\crm-portfolio-live"
   ```
2. Jalankan perintah:
   ```bash
   npx vercel
   ```
3. Ikuti petunjuk di layar (tekan Enter untuk semua pilihan default).
4. Dalam 30 detik, Anda akan mendapatkan URL live seperti: `https://antara-crm-portfolio.vercel.app`!

### Opsi B: Menggunakan Dashboard Vercel (via GitHub)
1. Buat repository baru di GitHub (misal: `antara-crm-portfolio`).
2. Push seluruh isi folder `crm-portfolio-live` ini ke repo tersebut.
3. Buka [vercel.com](https://vercel.com) -> Login -> Klik **"Add New Project"**.
4. Pilih repo GitHub tadi -> Klik **"Deploy"**. Selesai!

---

## ⚡ Cara Deploy 2: GitHub Pages (100% Gratis di Akun GitHub)

1. Buat repository baru di akun GitHub Anda, misalnya bernama `crm-portfolio`.
2. Buka terminal di folder ini dan inisialisasi Git:
   ```bash
   cd "C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM\crm-portfolio-live"
   git init
   git add .
   git commit -m "feat: initial release of live crm portfolio showcase"
   git branch -M main
   git remote add origin https://github.com/<username-github-anda>/crm-portfolio.git
   git push -u origin main
   ```
3. Di halaman repositori GitHub Anda:
   - Masuk ke tab **Settings** -> pilih menu **Pages** (di sidebar kiri).
   - Pada bagian **Build and deployment** -> Source: pilih **Deploy from a branch**.
   - Branch: pilih **main** dan folder **/(root)** -> Klik **Save**.
4. Tunggu sekitar 1-2 menit, website portofolio Anda langsung aktif di:
   `https://<username-github-anda>.github.io/crm-portfolio/`

---

## 💻 Cara Menguji di Komputer Lokal (Offline)

Cukup jalankan web server lokal bawaan Python:
```bash
python -m http.server 8000
```
Lalu buka browser di: `http://localhost:8000`

---

## 🛠️ Stack Teknologi Asli Proyek
- **Frontend**: Bootstrap 5, jQuery, OwlCarousel, FontAwesome, Tabler Icons
- **Backend Asli**: Native PHP MVC, PSR-4 Autoloading, Custom Session/Router
- **Database**: MariaDB / MySQL
- **Infrastruktur**: Multi-container Docker Compose (PHP-FPM + Nginx + MariaDB + phpMyAdmin)
