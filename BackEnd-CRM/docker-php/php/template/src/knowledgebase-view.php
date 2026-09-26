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

            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Direktori pengetahuan untuk tim CRM LKBN Antara</h5>
                            <p class="text-muted mb-0">Pelajari praktik terbaik mengelola narasumber, mitra iklan, dan pipeline kerja sama lintas desk.</p>
                        </div>
                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-sm-7">
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control" placeholder="Cari artikel, contoh: laporan kampanye">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <select class="select">
                                        <option>Filter Desk</option>
                                        <option>CRM Nasional</option>
                                        <option>Wilayah &amp; Biro</option>
                                        <option>Iklan &amp; Sponsorship</option>
                                        <option>Pengembangan Produk</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-primary-transparent text-primary mb-2">Panduan Utama</span>
                                    <h4 class="mb-2">Memulai di CRM Antara untuk Tim Hubungan Mitra</h4>
                                    <p class="text-muted mb-0">Langkah ringkas untuk onboarding, struktur data, dan etika pengelolaan relasi di platform CRM newsroom.</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark d-inline-flex align-items-center mb-1"><i class="ti ti-clock me-1"></i>10 menit baca</span>
                                    <p class="mb-0 text-muted fs-12">Terakhir diperbarui: 15 Okt 2025</p>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 d-flex">
                                    <i class="ti ti-circle-check text-success me-2"></i>
                                    <div>
                                        <a href="knowledgebase-details.php" class="link-default fw-medium">1. Permohonan akses &amp; struktur role</a>
                                        <p class="mb-0 text-muted fs-12">Cara meminta akun, menetapkan role desk, dan mengaktifkan autentikasi dua faktor.</p>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex">
                                    <i class="ti ti-circle-check text-success me-2"></i>
                                    <div>
                                        <a href="knowledgebase-details.php" class="link-default fw-medium">2. Standar input data narasumber</a>
                                        <p class="mb-0 text-muted fs-12">Format metadata yang wajib diisi sebelum narasumber dipakai oleh desk redaksi.</p>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex">
                                    <i class="ti ti-circle-check text-success me-2"></i>
                                    <div>
                                        <a href="knowledgebase-details.php" class="link-default fw-medium">3. Menyusun pipeline kemitraan</a>
                                        <p class="mb-0 text-muted fs-12">Membagi tahapan prospect â†’ negosiasi â†’ aktivasi dan cara mengukur progres.</p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <i class="ti ti-circle-check text-success me-2"></i>
                                    <div>
                                        <a href="knowledgebase-details.php" class="link-default fw-medium">4. Dashboard monitoring &amp; laporan bulanan</a>
                                        <p class="mb-0 text-muted fs-12">Menggunakan template insight untuk pimpinan redaksi dan divisi komersial.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                <h5 class="mb-0">Artikel per Alur Kerja</h5>
                                <a href="knowledgebase.php" class="btn btn-sm btn-white border">Lihat semua kategori</a>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="text-dark fw-semibold mb-2">Narasumber &amp; Relasi</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Menilai sensitivitas narasumber strategis</a>
                                        </li>
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Format notulensi &amp; follow-up kunjungan mitra</a>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Sinkronisasi kontak biro dan desk nasional</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-dark fw-semibold mb-2">Pipeline Kemitraan</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Checklist aktivasi kampanye lintas platform</a>
                                        </li>
                                        <li class="mb-2 d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Mengelola SLA dan target KPI sponsor</a>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <i class="ti ti-file-description me-2 mt-1 text-primary"></i>
                                            <a href="knowledgebase-details.php" class="text-gray">Panduan negosiasi paket iklan multi-produk</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                <h5 class="mb-0">Artikel Terbaru</h5>
                                <span class="badge bg-success-transparent text-success">Pembaruan 15 Okt 2025</span>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="knowledgebase-details.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Workflow persetujuan materi branded content</h6>
                                        <p class="mb-0 text-muted fs-12">Alur koordinasi antara desk komersial, redaksi, dan legal.</p>
                                    </div>
                                    <span class="badge bg-light text-dark">6 langkah</span>
                                </a>
                                <a href="knowledgebase-details.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Template laporan efektivitas kampanye digital</h6>
                                        <p class="mb-0 text-muted fs-12">Contoh metrik reach, engagement, dan kontribusi revenue.</p>
                                    </div>
                                    <span class="badge bg-light text-dark">Spreadsheet</span>
                                </a>
                                <a href="knowledgebase-details.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Integrasi CRM dengan sistem billing Antara</h6>
                                        <p class="mb-0 text-muted fs-12">Langkah membuat koneksi API dan pengaturan kredensial.</p>
                                    </div>
                                    <span class="badge bg-light text-dark">Teknis</span>
                                </a>
                                <a href="knowledgebase-details.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Panduan audit tahunan data narasumber</h6>
                                        <p class="mb-0 text-muted fs-12">Proses validasi, penandaan risiko, dan pemutakhiran kontak.</p>
                                    </div>
                                    <span class="badge bg-light text-dark">Checklist</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card mb-4">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center border-bottom mb-3 pb-3">
                                <a href="javascript:void(0);" class="text-dark fs-16 fw-semibold text-truncate">Kategori Populer</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-folder text-primary fs-16 me-1"></i>
                                <a href="knowledgebase.php" class="text-gray fs-14 fw-normal text-truncate">Manajemen Narasumber <span class="text-primary">( 24 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-folder text-primary fs-16 me-1"></i>
                                <a href="knowledgebase.php" class="text-gray fs-14 fw-normal text-truncate">Pipeline Kemitraan <span class="text-primary">( 18 )</span></a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-folder text-primary fs-16 me-1"></i>
                                <a href="knowledgebase.php" class="text-gray fs-14 fw-normal text-truncate">Aktivasi Kampanye <span class="text-primary">( 12 )</span></a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-folder text-primary fs-16 me-1"></i>
                                <a href="knowledgebase.php" class="text-gray fs-14 fw-normal text-truncate">Pelaporan &amp; Insight <span class="text-primary">( 10 )</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center border-bottom mb-3 pb-3">
                                <a href="javascript:void(0);" class="text-dark fs-16 fw-semibold text-truncate">Artikel Teratas</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="knowledgebase-details.php" class="text-gray fs-14 fw-normal text-truncate">Checklist aktivasi kampanye lintas platform</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="knowledgebase-details.php" class="text-gray fs-14 fw-normal text-truncate">Membuat laporan kinerja bulanan mitra</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="knowledgebase-details.php" class="text-gray fs-14 fw-normal text-truncate">Integrasi CRM dengan newsroom planning</a>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <i class="ti ti-file me-1"></i>
                                <a href="knowledgebase-details.php" class="text-gray fs-14 fw-normal text-truncate">Kontrol mutu data narasumber strategis</a>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-1"></i>
                                <a href="knowledgebase-details.php" class="text-gray fs-14 fw-normal text-truncate">Mengelola SLA sponsor dan follow-up otomatis</a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center border-bottom mb-3 pb-3">
                                <a href="javascript:void(0);" class="text-dark fs-16 fw-semibold text-truncate">Sumber Pendukung</a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                <div>
                                    <h6 class="fw-medium mb-0">Template Proposal Sponsorship</h6>
                                    <p class="text-muted fs-12 mb-0">DOCX â€¢ Update 10 Okt 2025</p>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-sm btn-white border"><i class="ti ti-download me-1"></i>Unduh</a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                <div>
                                    <h6 class="fw-medium mb-0">Spreadsheet Pelaporan KPI Kampanye</h6>
                                    <p class="text-muted fs-12 mb-0">XLSX â€¢ Update 4 Okt 2025</p>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-sm btn-white border"><i class="ti ti-download me-1"></i>Unduh</a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-medium mb-0">Dokumen SOP Pengelolaan Narasumber</h6>
                                    <p class="text-muted fs-12 mb-0">PDF â€¢ Update 1 Okt 2025</p>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-sm btn-white border"><i class="ti ti-download me-1"></i>Unduh</a>
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
