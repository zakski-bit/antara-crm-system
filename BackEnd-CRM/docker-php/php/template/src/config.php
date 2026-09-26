<?php ob_start(); ?>

<!-- ========================
        Start Page Content
    ========================= -->

<div class="page-wrapper">

    <!-- Start Content -->
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Konfigurasi Aplikasi</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.php"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Administration
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </nav>
            </div>
            <div class="head-icons ms-2">
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-original-title="Collapse" id="collapse-header">
                    <i class="ti ti-chevrons-up"></i>
                </a>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <ul class="nav nav-tabs nav-tabs-solid bg-transparent border-bottom mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="config.php"><i class="ti ti-settings me-2"></i>General Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="bussiness-settings.php"><i class="ti ti-world-cog me-2"></i>Website
                    Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="salary-settings.php"><i class="ti ti-device-ipad-horizontal-cog me-2"></i>App
                    Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="payment-gateways.php"><i class="ti ti-settings-dollar me-2"></i>Financial
                    Settings</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-xl-3 theiaStickySidebar">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column list-group settings-list">
                            <a href="config.php" class="d-inline-flex align-items-center rounded active py-2 px-3"><i
                                    class="ti ti-arrow-badge-right me-2"></i>Konfigurasi Aplikasi</a>
                            <a href="language.php" class="d-inline-flex align-items-center rounded py-2 px-3">Bahasa
                                &amp; Regional</a>
                            <a href="theme-settings.php" class="d-inline-flex align-items-center rounded py-2 px-3">Tema
                                &amp; Tampilan</a>

                            <a href="my-info.php"
                                class="d-inline-flex align-items-center rounded py-2 px-3">Profil Pengguna</a>
                            <a href="security-settings.php"
                                class="d-inline-flex align-items-center rounded py-2 px-3">Keamanan</a>
                            <a href="notification-settings.php"
                                class="d-inline-flex align-items-center rounded py-2 px-3">Notifikasi</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div>
                                <h4 class="mb-1">Informasi Aplikasi</h4>
                                <p class="text-muted mb-0">Atur identitas dasar dan perilaku global dashboard redaksi.
                                </p>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <button class="btn btn-light border me-2"><i class="ti ti-refresh me-1"></i>Muat
                                    Ulang</button>
                                <button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan
                                    Perubahan</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="ANTARA Newsroom Console"
                                        placeholder="Masukkan nama aplikasi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Domain Utama</label>
                                    <input type="url" class="form-control" value="https://dashboard.antaranews.com"
                                        placeholder="https://">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Alamat Email Support</label>
                                    <input type="email" class="form-control" value="support@antaranews.com"
                                        placeholder="nama@domain.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nomor Hotline</label>
                                    <input type="text" class="form-control" value="+62 21 3812 234"
                                        placeholder="Masukkan nomor hotline">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="form-label">Catatan Internal</label>
                                    <textarea rows="3" class="form-control"
                                        placeholder="Pesan singkat untuk tim redaksi terkait penggunaan aplikasi.">Gunakan akun terotentikasi internal dan aktifkan MFA untuk akses administratif.</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Zona Waktu &amp; Regional</h4>
                        <p class="text-muted mb-0">Menentukan format tanggal, jam, dan mata uang default.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Zona Waktu</label>
                                    <select class="select">
                                        <option>Asia/Jakarta (GMT+7)</option>
                                        <option>Asia/Makassar (GMT+8)</option>
                                        <option>Asia/Jayapura (GMT+9)</option>
                                        <option>Asia/Singapore (GMT+8)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Format Tanggal</label>
                                    <select class="select">
                                        <option>d M Y</option>
                                        <option>d/m/Y</option>
                                        <option>m/d/Y</option>
                                        <option>Y-m-d</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Mata Uang</label>
                                    <select class="select">
                                        <option>IDR - Indonesian Rupiah</option>
                                        <option>USD - US Dollar</option>
                                        <option>EUR - Euro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Hari Kerja Pertama</label>
                                    <select class="select">
                                        <option>Senin</option>
                                        <option>Minggu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Format Jam</label>
                                    <select class="select">
                                        <option>24 Jam</option>
                                        <option>12 Jam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="form-label">Tampilkan Zona Waktu di Header</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showTimezone" checked>
                                        <label class="form-check-label" for="showTimezone">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-header">
                        <h4 class="mb-1">Integrasi &amp; Failover</h4>
                        <p class="text-muted mb-0">Kelola kunci integrasi inti dan opsi pemulihan saat layanan utama
                            bermasalah.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">API Base URL</label>
                                    <input type="url" class="form-control" value="https://api.antaranews.com/v1"
                                        placeholder="https://">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">API Key</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="ANTARA-API-KEY-xxxxx" readonly>
                                        <button class="btn btn-light border" type="button"><i
                                                class="ti ti-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">RSS Feed Default</label>
                                    <input type="url" class="form-control"
                                        value="https://www.antaranews.com/rss/terkini.xml">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fallback Mode</label>
                                    <select class="select">
                                        <option>Gunakan cache lokal bila API gagal</option>
                                        <option>Matikan modul dinamis</option>
                                        <option>Tampilkan mode pemeliharaan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enableAuditTrail" checked>
                                        <label class="form-check-label" for="enableAuditTrail">Aktifkan pencatatan audit
                                            konfigurasi</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-outline-danger me-2"><i
                                                class="ti ti-trash me-1"></i>Reset ke Default</button>
                                        <button class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan
                                            Konfigurasi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Content -->

    <?php require_once __DIR__ . '/../partials/footer.php'; ?>

</div>

<!-- ========================
        End Page Content
    ========================= -->

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../partials/main.php'; ?>