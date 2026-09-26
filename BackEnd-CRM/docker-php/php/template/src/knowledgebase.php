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
                    <h2 class="mb-1">Knowledgebase CRM</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="ti ti-smart-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                Administration
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Knowledgebase</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                        <div>
                            <h5 class="mb-1">Pusat Pengetahuan CRM Antara</h5>
                            <p class="text-muted mb-0">Panduan kerja sama, manajemen narasumber, dan relasi komersial di lingkungan LKBN Antara.</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap row-gap-2">
                            <div class="me-3">
                                <div class="input-icon-end position-relative">
                                    <input type="text" class="form-control" placeholder="Cari artikel, contoh: proposal iklan">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    Filter: Semua Desk
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">CRM Nasional</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Iklan &amp; Sponsorship</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Regional &amp; Biro</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Pengembangan Produk</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Onboarding &amp; Akses CRM <span class="text-primary">( 06 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Permohonan akun CRM untuk tim redaksi &amp; komersial</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Panduan pengaturan role desk dan izin akses</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Checklist onboarding tim biro daerah</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Integrasi SSO Antara-ID dengan modul CRM</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Template pelatihan &amp; evaluasi pengguna baru</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Manajemen Narasumber &amp; Relasi <span class="text-primary">( 09 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Langkah menambahkan narasumber baru ke CRM</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Standard profiling kontak strategis</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Sinkronisasi basis data narasumber biro daerah</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Prosedur review data sensitif dan embargo</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Format notulensi hasil kunjungan mitra</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Pipeline Kemitraan &amp; Iklan <span class="text-primary">( 08 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Membangun pipeline kampanye nasional</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Template proposal kerjasama iklan lintas kanal</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Memantau status negosiasi sponsorship</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Checklist aktivasi kampanye multi platform</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Panduan pencatatan revenue share &amp; SLA</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Aktivasi Brief &amp; Campaign <span class="text-primary">( 07 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Standar brief kampanye branded content</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Checklist approval materi kreatif</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Sinkronisasi jadwal tayang newsroom &amp; komersial</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Formulir evaluasi pasca kampanye</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Repository template kontrak kerja sama</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Pelaporan &amp; Insight <span class="text-primary">( 05 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Membuat laporan kinerja bulanan mitra</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Dashboard insight kolaborasi redaksi-komersial</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Cara membaca skor kesehatan pipeline</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Template laporan efektivitas kampanye</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Distribusi insight ke pimpinan &amp; stakeholder</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-folder text-primary fs-24 me-1"></i>
                                <a href="knowledgebase-view.php" class="text-dark fs-16 fw-medium text-truncate">Integrasi &amp; Automasi <span class="text-primary">( 06 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Sinkronisasi CRM dengan newsroom planning</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Automasi pengingat follow-up narasumber</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Integrasi CRM dengan sistem billing Antara</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Panduan webhook ke platform kampanye digital</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="javascript:void(0);" class="text-gray fs-14 fw-normal text-truncate">Repositori API &amp; kredensial integrasi</a>
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
