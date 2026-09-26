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
                    <h2 class="mb-1">Dashboard Redaksi</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Redaksi
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard Redaksi</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="me-2 mb-2">
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="ti ti-file-export me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-1"></i>Export as Excel </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="input-icon w-120 position-relative">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar text-gray-9"></i>
                            </span>
                            <input type="text" class="form-control yearpicker" value="2025">
                        </div>
                    </div>
                    <div class="ms-2 head-icons">
                        <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                            <i class="ti ti-chevrons-up"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Welcome Wrap -->
            <div class="card border-0">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap pb-1">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-xl flex-shrink-0">
                            <img src="assets/img/profiles/avatar-31.jpg" class="rounded-circle" alt="img">
                        </span>
                        <div class="ms-3">
                            <h3 class="mb-2">Selamat datang kembali, Redaktur! <a href="javascript:void(0);" class="edit-icon"><i class="ti ti-edit fs-14"></i></a></h3>
                            <p>Hari ini ada <span class="text-primary text-decoration-underline">18</span> liputan menunggu validasi & <span class="text-primary text-decoration-underline">9</span> permintaan wawancara baru.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <a href="#" class="btn btn-secondary btn-md me-2 mb-2" data-bs-toggle="modal" data-bs-target="#add_project"><i class="ti ti-square-rounded-plus me-1"></i>Buat Penugasan</a>
                        <a href="#" class="btn btn-primary btn-md mb-2" data-bs-toggle="modal" data-bs-target="#add_leaves"><i class="ti ti-square-rounded-plus me-1"></i>Tambah Agenda</a>
                    </div>
                </div>
            </div>
            <!-- /Welcome Wrap -->

            <div class="row">

                <!-- Widget Info -->
                <div class="col-xxl-8 d-flex">
                    <div class="row flex-fill">
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-primary mb-2">
                                        <i class="ti ti-calendar-share fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Artikel Terbit Hari Ini</h6>
                                    <h3 class="mb-3">68 <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+4.5%</span></h3>
                                    <a href="blogs.php" class="link-default">Lihat Semua Artikel</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-secondary mb-2">
                                        <i class="ti ti-browser fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Liputan Lapangan Aktif</h6>
                                    <h3 class="mb-3">42 <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-1.2%</span></h3>
                                    <a href="knowledgebase.php" class="link-default">Lihat Penugasan</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-info mb-2">
                                        <i class="ti ti-users-group fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Reporter On Duty</h6>
                                    <h3 class="mb-3">112 <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+3.8%</span></h3>
                                    <a href="contacts-grid.php" class="link-default">Lihat Daftar Reporter</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-pink mb-2">
                                        <i class="ti ti-checklist fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Draft Menunggu Review</h6>
                                    <h3 class="mb-3">27 <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-0.8%</span></h3>
                                    <a href="blogs.php" class="link-default">Telusuri Draft</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-purple mb-2">
                                        <i class="ti ti-moneybag fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Pendapatan Iklan</h6>
                                    <h3 class="mb-3">Rp512Jt <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+7.4%</span></h3>
                                    <a href="expenses.php" class="link-default">Rincian Monetisasi</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-danger mb-2">
                                        <i class="ti ti-browser fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Jangkauan Digital Pekan Ini</h6>
                                    <h3 class="mb-3">8.2M <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+2.1%</span></h3>
                                    <a href="purchase-transaction.php" class="link-default">Lihat Insight</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-success mb-2">
                                        <i class="ti ti-users-group fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Pitch Liputan Baru</h6>
                                    <h3 class="mb-3">18 <span class="fs-12 fw-medium text-success"><i class="fa-solid fa-caret-up me-1"></i>+5.0%</span></h3>
                                    <a href="job-list.php" class="link-default">Review Pitch</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <span class="avatar rounded-circle bg-dark mb-2">
                                        <i class="ti ti-user-star fs-16"></i>
                                    </span>
                                    <h6 class="fs-13 fw-medium text-default mb-1">Reporter Baru Bergabung</h6>
                                    <h3 class="mb-3">12 <span class="fs-12 fw-medium text-danger"><i class="fa-solid fa-caret-down me-1"></i>-1.4%</span></h3>
                                    <a href="candidates.php" class="link-default">Lihat Orientasi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Widget Info -->

                <!-- Reporter By Desk -->
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Reporter per Desk Redaksi</h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Minggu Ini
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Lalu</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="emp-department"></div>
                            <p class="fs-13"><i class="ti ti-circle-filled me-2 fs-8 text-primary"></i>Porsi reporter desk nasional meningkat <span class="text-success fw-bold">+20%</span> dibanding minggu lalu.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- /Reporter By Desk -->

            </div>

            <div class="row">

                <!-- Status Tim Redaksi -->
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Status Kru Redaksi</h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Minggu Ini
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <p class="fs-13 mb-3">Total Reporter & Editor</p>
                                <h3 class="mb-3">154</h3>
                            </div>
                            <div class="progress-stacked emp-stack mb-3">
                                <div class="progress" role="progressbar" aria-label="Segment one" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                    <div class="progress-bar bg-warning"></div>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                    <div class="progress-bar bg-secondary"></div>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Segment three" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
                                    <div class="progress-bar bg-danger"></div>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Segment four" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
                                    <div class="progress-bar bg-pink"></div>
                                </div>
                            </div>
                            <div class="border mb-3">
                                <div class="row gx-0">
                                    <div class="col-6">
                                        <div class="p-2 flex-fill border-end border-bottom">
                                            <p class="fs-13 mb-2"><i class="ti ti-square-filled text-primary fs-12 me-2"></i>Desk Nasional <span class="text-gray-9">(48%)</span></p>
                                            <h2 class="display-1">112</h2>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 flex-fill border-bottom text-end">
                                            <p class="fs-13 mb-2"><i class="ti ti-square-filled me-2 text-secondary fs-12"></i>Desk Ekonomi <span class="text-gray-9">(20%)</span></p>
                                            <h2 class="display-1">42</h2>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 flex-fill border-end">
                                            <p class="fs-13 mb-2"><i class="ti ti-square-filled me-2 text-danger fs-12"></i>Desk Daerah <span class="text-gray-9">(22%)</span></p>
                                            <h2 class="display-1">34</h2>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 flex-fill text-end">
                                            <p class="fs-13 mb-2"><i class="ti ti-square-filled text-pink me-2 fs-12"></i>Tim Investigasi <span class="text-gray-9">(10%)</span></p>
                                            <h2 class="display-1">15</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-2">Reporter Terproduktif</h6>
                            <div class="p-2 d-flex align-items-center justify-content-between border border-primary bg-primary-100 br-5 mb-4">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <span class="me-2">
                                        <i class="ti ti-award-filled text-primary fs-24"></i>
                                    </span>
                                    <a href="employee-details.php" class="avatar avatar-md me-2">
                                        <img src="assets/img/profiles/avatar-24.jpg" class="rounded-circle border border-white" alt="img">
                                    </a>
                                    <div>
                                        <h6 class="text-truncate mb-1 fs-14 fw-medium"><a href="employee-details.php">Daniel Esbella</a></h6>
                                        <p class="fs-13">Koresponden Istana</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <p class="fs-13 mb-1">Jumlah Artikel</p>
                                    <h5 class="text-primary">31 Berita</h5>
                                </div>
                            </div>
                            <a href="contacts-grid.php" class="btn btn-light btn-md w-100">Lihat Semua Reporter</a>
                        </div>
                    </div>
                </div>
                <!-- /Status Tim Redaksi -->

                <!-- Monitoring Kehadiran Reporter -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Monitoring Kehadiran Reporter</h5>
                            <div class="dropdown mb-2">
                                <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    <i class="ti ti-calendar me-1"></i>Hari Ini
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="attendance" height="200"></canvas>
                                <div class="position-absolute text-center attendance-canvas">
                                    <p class="fs-13 mb-1">Total Reporter Aktif</p>
                                    <h3>120</h3>
                                </div>
                            </div>
                            <h6 class="mb-3">Status Penugasan</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-success me-1"></i>On Assignment</p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">59%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-secondary me-1"></i>Dalam Perjalanan</p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">21%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-warning me-1"></i>Izin Produksi</p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">3%</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="f-13 mb-2"><i class="ti ti-circle-filled text-danger me-1"></i>Tidak Bertugas</p>
                                <p class="f-13 fw-medium text-gray-9 mb-2">15%</p>
                            </div>
                            <div class="bg-light br-5 box-shadow-xs p-2 pb-0 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <p class="mb-2 me-2">Reporter tidak bertugas</p>
                                    <div class="avatar-list-stacked avatar-group-sm mb-2">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/profiles/avatar-27.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/profiles/avatar-30.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img src="assets/img/profiles/avatar-14.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img src="assets/img/profiles/avatar-29.jpg" alt="img">
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10" href="javascript:void(0);">
                                            +1
                                        </a>
                                    </div>
                                </div>
                                <a href="leaves.php" class="fs-13 link-primary text-decoration-underline mb-2">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Monitoring Kehadiran Reporter -->

                <!-- Shift Studio -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Shift Studio</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-sm d-inline-flex align-items-center border-0 fs-13 me-2" data-bs-toggle="dropdown">
                                        Semua Desk
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Desk Nasional</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Desk Ekonomi</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Desk Daerah</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Hari Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <div
                                    class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                            <img src="assets/img/profiles/avatar-24.jpg" class="rounded-circle border border-2" alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium text-truncate">Daniel Esbella</h6>
                                            <p class="fs-13">UI/UX Designer</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="link-default me-2"><i class="ti ti-clock-share"></i></a>
                                        <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i class="ti ti-circle-filled fs-5 me-1"></i>09:15</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 border br-5">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                            <img src="assets/img/profiles/avatar-23.jpg" class="rounded-circle border border-2" alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 fw-medium">Doglas Martini</h6>
                                            <p class="fs-13">Project Manager</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="link-default me-2"><i class="ti ti-clock-share"></i></a>
                                        <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i class="ti ti-circle-filled fs-5 me-1"></i>09:36</span>
                                    </div>
                                </div>
                                <div class="mb-3 p-2 border br-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                                <img src="assets/img/profiles/avatar-27.jpg" class="rounded-circle border border-2" alt="img">
                                            </a>
                                            <div class="ms-2">
                                                <h6 class="fs-14 fw-medium text-truncate">Brian Villalobos</h6>
                                                <p class="fs-13">PHP Developer</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="link-default me-2"><i class="ti ti-clock-share"></i></a>
                                            <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i class="ti ti-circle-filled fs-5 me-1"></i>09:15</span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap mt-2 border br-5 p-2 pb-0">
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i class="ti ti-circle-filled text-success fs-5 me-1"></i>Clock In</p>
                                            <h6 class="fs-13 fw-normal mb-2">10:30 AM</h6>
                                        </div>
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i class="ti ti-circle-filled text-danger fs-5 me-1"></i>Clock Out</p>
                                            <h6 class="fs-13 fw-normal mb-2">09:45 AM</h6>
                                        </div>
                                        <div>
                                            <p class="mb-1 d-inline-flex align-items-center"><i class="ti ti-circle-filled text-warning fs-5 me-1"></i>Production</p>
                                            <h6 class="fs-13 fw-normal mb-2">09:21 Hrs</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-2">Late</h6>
                            <div class="d-flex align-items-center justify-content-between mb-3 p-2 border border-dashed br-5">
                                <div class="d-flex align-items-center">
                                    <span class="avatar flex-shrink-0">
                                        <img src="assets/img/profiles/avatar-29.jpg" class="rounded-circle border border-2" alt="img">
                                    </span>
                                    <div class="ms-2">
                                        <h6 class="fs-14 fw-medium text-truncate">Anthony Lewis <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-success"><i class="ti ti-clock-hour-11 me-1"></i>30 Min</span></h6>
                                        <p class="fs-13">Marketing Head</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="link-default me-2"><i class="ti ti-clock-share"></i></a>
                                    <span class="fs-10 fw-medium d-inline-flex align-items-center badge badge-danger"><i class="ti ti-circle-filled fs-5 me-1"></i>08:35</span>
                                </div>
                            </div>
                            <a href="attendance-report.php" class="btn btn-light btn-md w-100">View All Attendance</a>
                        </div>
                    </div>
                </div>
                <!-- /Shift Studio -->

            </div>

            <div class="row">

                <!-- Jobs Applicants -->
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Jobs Applicants</h5>
                            <a href="job-list.php" class="btn btn-light btn-md mb-2">View All</a>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs tab-style-1 nav-justified d-sm-flex d-block p-0 mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium" data-bs-toggle="tab" data-bs-target="#openings" aria-current="page" href="#openings" aria-selected="true" role="tab">Openings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link fw-medium active" data-bs-toggle="tab" data-bs-target="#applicants" href="#applicants" aria-selected="false" tabindex="-1" role="tab">Applicants</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="openings">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="assets/img/icons/apple.svg" class="img-fluid rounded-circle w-auto h-auto" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="javascript:void(0);">Senior IOS Developer</a></p>
                                                <span class="fs-12">No of Openings : 25 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="assets/img/icons/php.svg" class="img-fluid w-auto h-auto" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="javascript:void(0);">Junior PHP Developer</a></p>
                                                <span class="fs-12">No of Openings : 20 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="assets/img/icons/react.svg" class="img-fluid w-auto h-auto" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="javascript:void(0);">Junior React Developer </a></p>
                                                <span class="fs-12">No of Openings : 30 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i class="ti ti-edit"></i></a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-0">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="assets/img/icons/laravel-icon.svg" class="img-fluid w-auto h-auto" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="javascript:void(0);">Senior Laravel Developer</a></p>
                                                <span class="fs-12">No of Openings : 40 </span>
                                            </div>
                                        </div>
                                        <a href="javascript:void(0);" class="btn btn-light btn-sm p-0 btn-icon d-flex align-items-center justify-content-center"><i class="ti ti-edit"></i></a>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="applicants">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="assets/img/users/user-09.jpg" class="img-fluid rounded-circle" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Brian Villalobos</a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 5+ Years<i class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA</span>
                                            </div>
                                        </div>
                                        <span class="badge badge-secondary badge-xs">UI/UX Designer</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="assets/img/users/user-32.jpg" class="img-fluid rounded-circle" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Anthony Lewis</a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 4+ Years<i class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA</span>
                                            </div>
                                        </div>
                                        <span class="badge badge-info badge-xs">Python Developer</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="assets/img/users/user-32.jpg" class="img-fluid rounded-circle" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="#">Stephan Peralt</a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 6+ Years<i class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA</span>
                                            </div>
                                        </div>
                                        <span class="badge badge-pink badge-xs">Android Developer</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-0">
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar overflow-hidden flex-shrink-0">
                                                <img src="assets/img/users/user-34.jpg" class="img-fluid rounded-circle" alt="img">
                                            </a>
                                            <div class="ms-2 overflow-hidden">
                                                <p class="text-dark fw-medium text-truncate mb-0"><a href="javascript:void(0);">Doglas Martini</a></p>
                                                <span class="fs-13 d-inline-flex align-items-center">Exp : 2+ Years<i class="ti ti-circle-filled fs-4 mx-2 text-primary"></i>USA</span>
                                            </div>
                                        </div>
                                        <span class="badge badge-purple badge-xs">React Developer</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Jobs Applicants -->
                
                <!-- Tim Redaksi -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Tim Redaksi</h5>
                            <a href="contacts-grid.php" class="btn btn-light btn-md mb-2">Lihat semua kru</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">	
                                <table class="table table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Desk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="javascript:void(0);" class="avatar">
                                                        <img src="assets/img/users/user-32.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="javascript:void(0);">Rina Pratama</a></h6>
                                                        <span class="fs-12">Redaktur Nasional</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary-transparent badge-xs">
                                                    Nasional
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="avatar">
                                                        <img src="assets/img/users/user-09.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="#">Bima Nugraha</a></h6>
                                                        <span class="fs-12">Reporter Ekonomi</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-danger-transparent badge-xs">Ekonomi</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="avatar">
                                                        <img src="assets/img/users/user-01.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="#">Siti Marlina</a></h6>
                                                        <span class="fs-12">Produser Siaran</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info-transparent badge-xs">Broadcast</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="javascript:void(0);" class="avatar">
                                                        <img src="assets/img/users/user-34.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="javascript:void(0);">Damar Putra</a></h6>
                                                        <span class="fs-12">Reporter Olahraga</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-purple-transparent badge-xs">Olahraga</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="javascript:void(0);" class="avatar">
                                                        <img src="assets/img/users/user-37.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="javascript:void(0);">Anita Kurnia</a></h6>
                                                        <span class="fs-12">Visual Editor</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0">
                                                <span class="badge badge-pink-transparent badge-xs">Visual</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Tim Redaksi -->
                
                <!-- Checklist Produksi -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Checklist Produksi</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2 me-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Hari Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="#" class="btn btn-primary btn-icon btn-xs rounded-circle d-flex align-items-center justify-content-center p-0 mb-2"  data-bs-toggle="modal" data-bs-target="#add_todo"><i class="ti ti-plus fs-16"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo1">
                                    <label class="form-check-label fw-medium" for="todo1">Siapkan rundown siaran pagi</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo2">
                                    <label class="form-check-label fw-medium" for="todo2">Follow up narasumber ekonomi</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo3">
                                    <label class="form-check-label fw-medium" for="todo3">Sinkronisasi headline portal</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo4">
                                    <label class="form-check-label fw-medium" for="todo4">Koordinasi liputan foto</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-2">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo5">
                                    <label class="form-check-label fw-medium" for="todo5">Perbarui kalender konferensi pers</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center todo-item border p-2 br-5 mb-0">
                                <i class="ti ti-grid-dots me-2"></i>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="todo6">
                                    <label class="form-check-label fw-medium" for="todo6">Review pedoman gaya terbaru</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Checklist Produksi -->

            </div>

            <div class="row">
                
                <!-- Distribusi Kanal Digital -->
                <div class="col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Distribusi Kanal Digital</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white border-0 btn-sm d-inline-flex align-items-center fs-13 me-2" data-bs-toggle="dropdown">
                                        Semua Kanal
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Portal Berita</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Media Sosial</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">TV Digital</a>
                                        </li>
                                    </ul>
                                </div>	
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-1">
                                    <p class="fs-13 text-gray-9 me-3 mb-0"><i class="ti ti-square-filled me-2 text-primary"></i>Kunjungan</p>
                                    <p class="fs-13 text-gray-9 mb-0"><i class="ti ti-square-filled me-2 text-gray-2"></i>Interaksi</p>
                                </div>
                                <p class="fs-13 mb-1">Pembaruan 11:30 WIB</p>
                            </div>
                            <div id="sales-income"></div>
                        </div>
                    </div>
                </div>
                <!-- /Distribusi Kanal Digital -->
                
                <!-- Kerja Sama Komersial -->
                <div class="col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Kerja Sama Komersial</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-sm d-inline-flex align-items-center fs-13 me-2 border-0" data-bs-toggle="dropdown">
                                        Kerja Sama Komersial
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Kerja Sama Komersial</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Paid</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Unpaid</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center"  data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Minggu Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <div class="table-responsive pt-1">	
                                <table class="table table-nowrap table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php" class="avatar">
                                                        <img src="assets/img/users/user-39.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.php">Redesign Website</a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO2<i class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Logistics</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment</p>
                                                <h6 class="fw-medium">$3560</h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Unpaid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php" class="avatar">
                                                        <img src="assets/img/users/user-40.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.php">Module Completion</a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO5<i class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Yip Corp</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment</p>
                                                <h6 class="fw-medium">$4175</h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Unpaid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php" class="avatar">
                                                        <img src="assets/img/users/user-55.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.php">Change on Emp Module</a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO3<i class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Ignis LLP</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment</p>
                                                <h6 class="fw-medium">$6985</h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Unpaid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php" class="avatar">
                                                        <img src="assets/img/users/user-42.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.php">Changes on the Board</a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO2<i class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>Ignis LLP</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment</p>
                                                <h6 class="fw-medium">$1457</h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span class="badge badge-danger-transparent badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Unpaid</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center">
                                                    <a href="invoice-details.php" class="avatar">
                                                        <img src="assets/img/users/user-44.jpg" class="img-fluid rounded-circle" alt="img">
                                                    </a>
                                                    <div class="ms-2">
                                                        <h6 class="fw-medium"><a href="invoice-details.php">Hospital Management</a></h6>
                                                        <span class="fs-13 d-inline-flex align-items-center">#INVOO6<i class="ti ti-circle-filled fs-4 mx-1 text-primary"></i>HCL Corp</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="fs-13 mb-1">Payment</p>
                                                <h6 class="fw-medium">$6458</h6>
                                            </td>
                                            <td class="px-0 text-end">
                                                <span class="badge badge-success-transparent badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Paid</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="invoice.php" class="btn btn-light btn-md w-100 mt-2">View All</a>
                        </div>
                    </div>
                </div>
                <!-- /Kerja Sama Komersial -->

            </div>

            <div class="row">
                
                <!-- Agenda Liputan -->
                <div class="col-xxl-8 col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Agenda Liputan</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center"  data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Minggu Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">	
                                <table class="table table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Team</th>
                                            <th>Hours</th>
                                            <th>Deadline</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-001</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Office Management App</a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-02.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-03.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-05.jpg" alt="img">
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">15/255 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 40%"></div>
                                                </div>
                                            </td>
                                            <td>12 Sep 2024</td>
                                            <td>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-002</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Clinic Management </a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-06.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-07.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-08.jpg" alt="img">
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium" href="javascript:void(0);">
                                                        +1
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">15/255 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 40%"></div>
                                                </div>
                                            </td>
                                            <td>24 Oct 2024</td>
                                            <td>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Low
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-003</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Educational Platform</a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-06.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-08.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-09.jpg" alt="img">
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">40/255 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>18 Feb 2024</td>
                                            <td>
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-004</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Chat & Call Mobile App</a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-11.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-12.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-13.jpg" alt="img">
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">35/155 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>19 Feb 2024</td>
                                            <td>
                                                <span class="badge badge-danger d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-005</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Travel Planning Website</a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-17.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-18.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-19.jpg" alt="img">
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">50/235 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>18 Feb 2024</td>
                                            <td>
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="project-details.php" class="link-default">PRO-006</a></td>
                                            <td><h6 class="fw-medium"><a href="project-details.php">Service Booking Software</a></h6></td>
                                            <td>
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-06.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-08.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-09.jpg" alt="img">
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">40/255 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 50%"></div>
                                                </div>
                                            </td>
                                            <td>20 Feb 2024</td>
                                            <td>
                                                <span class="badge badge-success d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Low
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-0"><a href="project-details.php" class="link-default">PRO-008</a></td>
                                            <td class="border-0"><h6 class="fw-medium"><a href="project-details.php">Travel Planning Website</a></h6></td>
                                            <td class="border-0">
                                                <div class="avatar-list-stacked avatar-group-sm">
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-15.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-16.jpg" alt="img">
                                                    </span>
                                                    <span class="avatar avatar-rounded">
                                                        <img class="border border-white" src="assets/img/profiles/avatar-17.jpg" alt="img">
                                                    </span>
                                                    <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium" href="javascript:void(0);">
                                                        +2
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="border-0">
                                                <p class="mb-1">15/255 Hrs</p>
                                                <div class="progress progress-xs w-100" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-primary" style="width: 45%"></div>
                                                </div>
                                            </td>
                                            <td class="border-0">17 Oct 2024</td>
                                            <td class="border-0">
                                                <span class="badge badge-pink d-inline-flex align-items-center badge-xs">
                                                    <i class="ti ti-point-filled me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Agenda Liputan -->

                <!-- Status Produksi Konten -->
                <div class="col-xxl-4 col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Status Produksi Konten</h5>
                            <div class="d-flex align-items-center">
                                <div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center"  data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i>Minggu Ini
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Bulan Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minggu Ini</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Hari Ini</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chartjs-wrapper-demo position-relative mb-4">
                                <canvas id="mySemiDonutChart" height="190"></canvas>
                                <div class="position-absolute text-center attendance-canvas">
                                    <p class="fs-13 mb-1">Total Tasks</p>
                                    <h3>124/165</h3>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="border-end text-center me-2 pe-2 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-10 me-1 text-warning"></i>Ongoing</p>
                                    <h5>24%</h5>
                                </div>
                                <div class="border-end text-center me-2 pe-2 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-10 me-1 text-info"></i>On Hold </p>
                                    <h5>10%</h5>
                                </div>
                                <div class="border-end text-center me-2 pe-2 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-10 me-1 text-danger"></i>Overdue</p>
                                    <h5>16%</h5>
                                </div>
                                <div class="text-center me-2 pe-2 mb-3">
                                    <p class="fs-13 d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-10 me-1 text-success"></i>Ongoing</p>
                                    <h5>40%</h5>
                                </div>
                            </div>
                            <div class="bg-dark br-5 p-3 pb-0 d-flex align-items-center justify-content-between">
                                <div class="mb-2">
                                    <h4 class="text-success">389/689 hrs</h4>
                                    <p class="fs-13 mb-0">Spent on Overall Tasks Minggu Ini</p>
                                </div>
                                <a href="tasks.php" class="btn btn-sm btn-light mb-2 text-nowrap">View All</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Status Produksi Konten -->

            </div>

            <div class="row">

                <!-- Agenda Lapangan -->
                <div class="col-xxl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Agenda Lapangan</h5>
                            <a href="candidates.php" class="btn btn-light btn-md mb-2">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="bg-light p-3 br-5 mb-4">
                                <span class="badge badge-secondary badge-xs mb-1">UI/ UX Designer</span>
                                <h6 class="mb-2 text-truncate">Interview Candidates - UI/UX Designer</h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs-13 mb-1 me-2"><i class="ti ti-calendar-event me-2"></i>Thu, 15 Feb 2025</p>
                                    <p class="fs-13 mb-1"><i class="ti ti-clock-hour-11 me-2"></i>01:00 PM - 02:20 PM</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-top mt-2 pt-3">
                                    <div class="avatar-list-stacked avatar-group-sm">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-49.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-13.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-11.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-22.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-58.jpg" alt="img">
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium" href="javascript:void(0);">
                                            +3
                                        </a>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-xs">Join Meeting</a>
                                </div>
                            </div>
                            <div class="bg-light p-3 br-5 mb-0">
                                <span class="badge badge-dark badge-xs mb-1">IOS Developer</span>
                                <h6 class="mb-2 text-truncate">Interview Candidates - IOS Developer</h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs-13 mb-1 me-2"><i class="ti ti-calendar-event me-2"></i>Thu, 15 Feb 2025</p>
                                    <p class="fs-13 mb-1"><i class="ti ti-clock-hour-11 me-2"></i>02:00 PM - 04:20 PM</p>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-top mt-2 pt-3">
                                    <div class="avatar-list-stacked avatar-group-sm">
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-49.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-13.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-11.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-22.jpg" alt="img">
                                        </span>
                                        <span class="avatar avatar-rounded">
                                            <img class="border border-white" src="assets/img/users/user-58.jpg" alt="img">
                                        </span>
                                        <a class="avatar bg-primary avatar-rounded text-fixed-white fs-10 fw-medium" href="javascript:void(0);">
                                            +3
                                        </a>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-xs">Join Meeting</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Agenda Lapangan -->

                <!-- Aktivitas Redaksi -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Aktivitas Redaksi</h5>
                            <a href="activity.php" class="btn btn-light btn-md mb-2">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-38.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">Matt Morgan</a></h6>
                                                <p class="fs-13">05:30 PM</p>
                                            </div>
                                            <p class="fs-13">Added New Project <span class="text-primary">HRMS Dashboard</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-01.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">Jay Ze</a></h6>
                                                <p class="fs-13">05:00 PM</p>
                                            </div>
                                            <p class="fs-13">Commented on Uploaded Document</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-19.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">Mary Donald</a></h6>
                                                <p class="fs-13">05:30 PM</p>
                                            </div>
                                            <p class="fs-13">Approved Task Agenda Liputan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-11.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">George David</a></h6>
                                                <p class="fs-13">06:00 PM</p>
                                            </div>
                                            <p class="fs-13">Requesting Access to Module Tickets</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-20.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">Aaron Zeen</a></h6>
                                                <p class="fs-13">06:30 PM</p>
                                            </div>
                                            <p class="fs-13">Downloaded App Reportss</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center w-100">
                                        <a href="javscript:void(0);" class="avatar  flex-shrink-0">
                                            <img src="assets/img/users/user-08.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 flex-fill">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="fs-medium text-truncate"><a href="javscript:void(0);">Hendry Daniel</a></h6>
                                                <p class="fs-13">05:30 PM</p>
                                            </div>
                                            <p class="fs-13">Completed New Project <span>HMS</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Aktivitas Redaksi -->

                <!-- Sorotan Reporter -->
                <div class="col-xxl-4 col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                            <h5 class="mb-2">Sorotan Reporter</h5>
                            <a href="javascript:void(0);" class="btn btn-light btn-md mb-2">View All</a>
                        </div>
                        <div class="card-body pb-1">
                            <h6 class="mb-2">Hari Ini</h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="assets/img/users/user-38.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium ">Andrew Jermia</h6>
                                            <p class="fs-13">IOS Developer</p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i class="ti ti-cake me-1"></i>Send</a>
                                </div>
                            </div>
                            <h6 class="mb-2">Tomorow</h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="assets/img/users/user-10.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium"><a href="javascript:void(0);">Mary Zeen</a></h6>
                                            <p class="fs-13">UI/UX Designer</p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i class="ti ti-cake me-1"></i>Send</a>
                                </div>
                            </div>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" class="avatar">
                                            <img src="assets/img/users/user-09.jpg" class="rounded-circle" alt="img">
                                        </a>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium "><a href="javascript:void(0);">Antony Lewis</a></h6>
                                            <p class="fs-13">Android Developer</p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i class="ti ti-cake me-1"></i>Send</a>
                                </div>
                            </div>
                            <h6 class="mb-2">25 Jan 2025</h6>
                            <div class="bg-light p-2 border border-dashed rounded-top mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar">
                                            <img src="assets/img/users/user-12.jpg" class="rounded-circle" alt="img">
                                        </span>
                                        <div class="ms-2 overflow-hidden">
                                            <h6 class="fs-medium ">Doglas Martini</h6>
                                            <p class="fs-13">.Net Developer</p>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs"><i class="ti ti-cake me-1"></i>Send</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Sorotan Reporter -->

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
