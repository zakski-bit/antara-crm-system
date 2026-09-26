<?php ob_start();?>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Dashboard</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Superadmin
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="input-icon mb-2 position-relative">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar text-gray-9"></i>
                        </span>
                        <input type="text" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="card border-0 bg-primary text-white mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                    <div class="mb-3">
                        <h2 class="text-white mb-2">Dashboard Divisi IT Antara</h2>
                        <p class="mb-0 text-white-50">Monitor kesehatan infrastruktur digital, dukungan internal, dan inisiatif transformasi kantor berita Antara.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap">
                        <a href="todo.php" class="btn btn-light text-primary me-2 mb-2"><i class="ti ti-checklist me-1"></i>Lihat Backlog</a>
                        <a href="calendar.php" class="btn btn-outline-light mb-2"><i class="ti ti-calendar-time me-1"></i>Agenda Maintenance</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="avatar avatar-md bg-primary mb-3">
                                    <i class="ti ti-server fs-16"></i>
                                </span>
                                <span class="badge bg-success fw-normal mb-3">+2 layanan</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="mb-1">34</h2>
                                    <p class="fs-13 mb-0">Layanan Produksi Aktif</p>
                                </div>
                                <div class="sparkline" data-type="line" data-width="90" data-height="40" data-line-color="#0d6efd" data-fill-color="rgba(13,110,253,0.1)" data-line-width="2">4,6,5,7,6,9,8</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="avatar avatar-md bg-danger mb-3">
                                    <i class="ti ti-alert-triangle fs-16"></i>
                                </span>
                                <span class="badge bg-danger fw-normal mb-3">18 aktif</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="mb-1">18</h2>
                                    <p class="fs-13 mb-0">Tiket Dukungan Terbuka</p>
                                </div>
                                <div class="sparkline" data-type="line" data-width="90" data-height="40" data-line-color="#dc3545" data-fill-color="rgba(220,53,69,0.1)" data-line-width="2">5,3,4,6,5,4,3</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="avatar avatar-md bg-success mb-3">
                                    <i class="ti ti-rocket fs-16"></i>
                                </span>
                                <span class="badge bg-success fw-normal mb-3">+25%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="mb-1">12</h2>
                                    <p class="fs-13 mb-0">Deployment Minggu Ini</p>
                                </div>
                                <div class="sparkline" data-type="line" data-width="90" data-height="40" data-line-color="#198754" data-fill-color="rgba(25,135,84,0.1)" data-line-width="2">2,3,4,3,5,4,6</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="avatar avatar-md bg-info mb-3">
                                    <i class="ti ti-heart-rate-monitor fs-16"></i>
                                </span>
                                <span class="badge bg-info fw-normal mb-3">30 Hari</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="mb-1">99.97%</h2>
                                    <p class="fs-13 mb-0">Rata-rata Uptime</p>
                                </div>
                                <div class="sparkline" data-type="line" data-width="90" data-height="40" data-line-color="#0dcaf0" data-fill-color="rgba(13,202,240,0.1)" data-line-width="2">9,9,9,9,9,9,10</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Status Layanan Kritis</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between"><span>Portal Newsroom</span><span class="text-success">Normal</span></div>
                                <div class="progress progress-xs mt-2">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between"><span>Sistem Redaksi (NRCS)</span><span class="text-success">Normal</span></div>
                                <div class="progress progress-xs mt-2">
                                    <div class="progress-bar bg-success" style="width: 96%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between"><span>Media Asset Management</span><span class="text-warning">Degradasi minor</span></div>
                                <div class="progress progress-xs mt-2">
                                    <div class="progress-bar bg-warning" style="width: 78%"></div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="d-flex justify-content-between"><span>API Distribusi Konten</span><span class="text-danger">Investigasi</span></div>
                                <div class="progress progress-xs mt-2">
                                    <div class="progress-bar bg-danger" style="width: 62%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ringkasan Insiden Minggu Ini</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Gangguan CDN Regional</h6>
                                        <p class="mb-0 text-muted">Durasi 12 menit, mitigasi melalui failover Jakarta</p>
                                    </div>
                                    <span class="badge bg-danger">Kritis</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Timeout API Foto Arsip</h6>
                                        <p class="mb-0 text-muted">Normalisasi selesai 09:45 WIB</p>
                                    </div>
                                    <span class="badge bg-warning text-dark">Sedang</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Integrasi Single Sign-On</h6>
                                        <p class="mb-0 text-muted">Permintaan perubahan konfigurasi</p>
                                    </div>
                                    <span class="badge bg-info text-dark">Perubahan</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Monitoring Kamera Lapangan</h6>
                                        <p class="mb-0 text-muted">Sensor lokasi Jayapura belum merespon</p>
                                    </div>
                                    <span class="badge bg-secondary">Dalam proses</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Jadwal Maintenance</h5>
                            <a href="calendar.php" class="text-primary">Lihat kalender</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Sistem</th>
                                            <th>Window</th>
                                            <th>PIC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>30 Jan</td>
                                            <td>Database Newsroom</td>
                                            <td>00:30 - 02:00</td>
                                            <td>Sarah W.</td>
                                        </tr>
                                        <tr>
                                            <td>31 Jan</td>
                                            <td>Portal Antara.co.id</td>
                                            <td>23:00 - 23:45</td>
                                            <td>Riko A.</td>
                                        </tr>
                                        <tr>
                                            <td>02 Feb</td>
                                            <td>Streaming OTT</td>
                                            <td>01:00 - 03:30</td>
                                            <td>Dian K.</td>
                                        </tr>
                                        <tr>
                                            <td>05 Feb</td>
                                            <td>Storage MAM</td>
                                            <td>22:00 - 01:00</td>
                                            <td>Budi P.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Deployments Terakhir</h5>
                            <a href="kanban-view.php" class="text-primary">Lihat riwayat</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Release 3.12 - Portal Redaksi</h6>
                                        <p class="mb-0 text-muted">23 Jan 2025 • Penambahan fitur editor otomatis</p>
                                    </div>
                                    <span class="badge bg-success">Sukses</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Patch keamanan API Distribusi</h6>
                                        <p class="mb-0 text-muted">22 Jan 2025 • Perbaikan token OAuth</p>
                                    </div>
                                    <span class="badge bg-success">Sukses</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Update mobile Antara Stories</h6>
                                        <p class="mb-0 text-muted">20 Jan 2025 • Penyesuaian streaming adaptif</p>
                                    </div>
                                    <span class="badge bg-warning text-dark">Perlu validasi</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Automasi backup kantor biro</h6>
                                        <p class="mb-0 text-muted">18 Jan 2025 • Rollout tahap 1 untuk wilayah timur</p>
                                    </div>
                                    <span class="badge bg-secondary">On-going</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tiket Support Aktif</h5>
                    <a href="tickets.php" class="text-primary">Kelola tiket</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Permintaan</th>
                                    <th>Prioritas</th>
                                    <th>Unit Pemohon</th>
                                    <th>Status</th>
                                    <th>Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#INC-4578</td>
                                    <td>Perbaikan feed foto otomatis biro Surabaya</td>
                                    <td><span class="badge bg-danger">Kritis</span></td>
                                    <td>Biro Surabaya</td>
                                    <td><span class="badge bg-warning text-dark">On Progress</span></td>
                                    <td>Rani M.</td>
                                </tr>
                                <tr>
                                    <td>#INC-4583</td>
                                    <td>Permintaan akses VPN kontributor freelance</td>
                                    <td><span class="badge bg-secondary">Rendah</span></td>
                                    <td>HR &amp; Kemitraan</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td>Ahmad F.</td>
                                </tr>
                                <tr>
                                    <td>#INC-4590</td>
                                    <td>Integrasi metadata video ke sistem arsip</td>
                                    <td><span class="badge bg-primary">Sedang</span></td>
                                    <td>Produksi TV</td>
                                    <td><span class="badge bg-warning text-dark">Review QA</span></td>
                                    <td>Novi L.</td>
                                </tr>
                                <tr>
                                    <td>#INC-4594</td>
                                    <td>Penyesuaian firewall kantor biro Papua</td>
                                    <td><span class="badge bg-primary">Sedang</span></td>
                                    <td>Operasional Regional</td>
                                    <td><span class="badge bg-info text-dark">Penjadwalan</span></td>
                                    <td>Yusuf S.</td>
                                </tr>
                                <tr>
                                    <td>#INC-4601</td>
                                    <td>Permintaan dashboard realtime KPI biro</td>
                                    <td><span class="badge bg-success">Normal</span></td>
                                    <td>Manajemen Redaksi</td>
                                    <td><span class="badge bg-secondary">Backlog</span></td>
                                    <td>Tim Data</td>
                                </tr>
                            </tbody>
                        </table>
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

