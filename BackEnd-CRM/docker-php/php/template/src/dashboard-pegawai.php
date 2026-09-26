<?php ob_start();?>

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content dashboard-employee">
            <style>
                .dashboard-employee .card {
                    border: 1px solid #e9edf4;
                    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
                }
                .dashboard-employee .card-header h5 {
                    letter-spacing: 0.2px;
                }
                .dashboard-employee .table > :not(caption) > * > * {
                    vertical-align: middle;
                }
                .dashboard-employee .status-note {
                    font-size: 12px;
                    color: #6c757d;
                }
            </style>

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Dashboard Pegawai</h2>
                    <p class="text-muted mb-1">Ringkasan agenda, progres konten, dan tugas operasional harian.</p>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="dashboard-pegawai.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Pegawai
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Ringkasan Penugasan</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="ti ti-calendar me-1"></i>Hari Ini
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-2">
                        <a href="javascript:void(0);" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#add_project"><i class="ti ti-square-rounded-plus me-1"></i>Laporkan Liputan</a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="card border-0 bg-light mb-3">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                    <div class="mb-2 mb-md-0">
                        <h6 class="mb-1">Operasional Hari Ini</h6>
                        <p class="status-note mb-0">4 agenda lapangan, 5 draft siap edit, dan 1 deadline utama pada 17:30 WIB.</p>
                    </div>
                    <a href="calendar.php" class="btn btn-outline-primary btn-sm">Buka Agenda Lengkap</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-primary mb-2">
                                <i class="ti ti-map-pin fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Penugasan Lapangan</h6>
                            <h3 class="mb-3">3 Lokasi</h3>
                            <p class="fs-12 text-gray-9 mb-0">Jakarta, Bogor, Bandung</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-success mb-2">
                                <i class="ti ti-article fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Draft Siap Edit</h6>
                            <h3 class="mb-3">5</h3>
                            <a href="blogs.php" class="link-default">Buka daftar draft</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-warning mb-2">
                                <i class="ti ti-camera fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Media Diunggah</h6>
                            <h3 class="mb-3">18 File</h3>
                            <p class="fs-12 text-gray-9 mb-0">Perlu verifikasi hak siar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <span class="avatar rounded-circle bg-danger mb-2">
                                <i class="ti ti-clock-hour-4 fs-16"></i>
                            </span>
                            <h6 class="fs-13 fw-medium text-default mb-1">Deadline Terdekat</h6>
                            <h3 class="mb-3">17:30 WIB</h3>
                            <p class="fs-12 text-gray-9 mb-0">Brief "Forum Energi Nasional"</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-8 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Agenda Liputan Hari Ini</h5>
                            <a href="calendar.php" class="btn btn-light btn-md mb-2">Lihat kalender lengkap</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Kegiatan</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>08:00 WIB</td>
                                            <td>Doorstop Menteri Perdagangan</td>
                                            <td>Kemendag, Jakarta</td>
                                            <td><span class="badge badge-success-transparent">Selesai</span></td>
                                        </tr>
                                        <tr>
                                            <td>11:30 WIB</td>
                                            <td>Peliputan Konferensi Pers Energi</td>
                                            <td>Hotel Grandia, Bandung</td>
                                            <td><span class="badge badge-warning-transparent">Berlangsung</span></td>
                                        </tr>
                                        <tr>
                                            <td>15:00 WIB</td>
                                            <td>Wawancara Eksklusif CEO Startup</td>
                                            <td>Via Video Call</td>
                                            <td><span class="badge badge-secondary-transparent">Terjadwal</span></td>
                                        </tr>
                                        <tr>
                                            <td>18:00 WIB</td>
                                            <td>Laporan Langsung Banjir</td>
                                            <td>Bekasi</td>
                                            <td><span class="badge badge-secondary-transparent">Terjadwal</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Perlengkapan yang Dipinjam</h5>
                            <a href="file-manager.php" class="btn btn-light btn-md mb-2">Kelola inventaris</a>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    Kamera Mirrorless A7 IV
                                    <span class="badge bg-primary">Dikembalikan besok 09:00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    Mic Wireless Set
                                    <span class="badge bg-warning text-dark">Sedang dipakai</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    Laptop Editing Mobile
                                    <span class="badge bg-secondary">Booking 17-20 Okt</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Checklist Produksi Pribadi</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Minggu Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a></li>
                                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a></li>
                                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a></li>
                                    </ul>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary btn-icon btn-xs rounded-circle d-flex align-items-center justify-content-center p-0 mb-2" data-bs-toggle="modal" data-bs-target="#add_todo"><i class="ti ti-plus fs-16"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todoEmp1">
                                    <label class="form-check-label fw-medium" for="todoEmp1">Upload footage konferensi pers</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todoEmp2">
                                    <label class="form-check-label fw-medium" for="todoEmp2">Lengkapi metadata foto lapangan</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todoEmp3">
                                    <label class="form-check-label fw-medium" for="todoEmp3">Kirim outline berita malam</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todoEmp4">
                                    <label class="form-check-label fw-medium" for="todoEmp4">Konfirmasi narasumber talkshow</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-0">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todoEmp5">
                                    <label class="form-check-label fw-medium" for="todoEmp5">Update log perjalanan dinas</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Progress Produksi Konten</h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Minggu Ini
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="border p-3 rounded text-center">
                                        <p class="fs-12 text-gray-7 mb-1">Liputan Selesai</p>
                                        <h3 class="text-success mb-0">9</h3>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border p-3 rounded text-center">
                                        <p class="fs-12 text-gray-7 mb-1">Liputan Diproses</p>
                                        <h3 class="text-warning mb-0">4</h3>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border p-3 rounded">
                                        <p class="fs-12 text-gray-7 mb-1">Distribusi Kanal</p>
                                        <div class="d-flex justify-content-between fs-12 text-gray-7">
                                            <span>Portal Berita</span>
                                            <span>52%</span>
                                        </div>
                                        <div class="progress progress-xs mb-2">
                                            <div class="progress-bar bg-primary" style="width:52%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between fs-12 text-gray-7">
                                            <span>Media Sosial</span>
                                            <span>28%</span>
                                        </div>
                                        <div class="progress progress-xs mb-2">
                                            <div class="progress-bar bg-info" style="width:28%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between fs-12 text-gray-7">
                                            <span>TV Digital</span>
                                            <span>20%</span>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-secondary" style="width:20%"></div>
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
