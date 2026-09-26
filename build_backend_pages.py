import os

BASE_DIR = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM"
OUTPUT_DIR = os.path.join(BASE_DIR, "crm-portfolio-live")
ADMIN_DIR = os.path.join(OUTPUT_DIR, "admin")

common_head = '''
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>{TITLE}</title>
	
	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="../favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="../favicon.png">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="../demo-bar/demo-bar.css">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

	<style>
		:root {
			--primary: #D70006;
			--primary-hover: #B50005;
			--primary-light: rgba(215, 0, 6, 0.1);
			--secondary: #24201F;
			--dark: #24201F;
			--body-font: 'Outfit', sans-serif;
			--title-font: 'Roboto', sans-serif;
		}
		body {
			font-family: 'Outfit', sans-serif !important;
		}
		h1, h2, h3, h4, h5, h6, .page-header h3, .page-header h4 {
			font-family: 'Roboto', sans-serif !important;
		}
		.sidebar {
			background-color: #1a1816 !important;
		}
		.sidebar .sidebar-menu>ul>li a.active, .sidebar .sidebar-menu>ul>li a:hover {
			color: #fff !important;
			background: rgba(215, 0, 6, 0.15) !important;
		}
		.sidebar .sidebar-menu>ul>li a.active i, .sidebar .sidebar-menu>ul>li a:hover i {
			color: #D70006 !important;
		}
		.sidebar-logo {
			background-color: #24201F !important;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
		}
		.header {
			background: #2F3E56 !important;
			border-bottom: 2px solid #233046 !important;
		}
		.header-welcome-message {
			color: #ffffff;
			font-weight: 600;
			font-size: 14px;
		}
		.btn-primary {
			background-color: #D70006 !important;
			border-color: #D70006 !important;
		}
		.btn-primary:hover {
			background-color: #B50005 !important;
			border-color: #B50005 !important;
		}
		.sparkline {
			font-size: 11px;
			color: #64748b;
			font-weight: 600;
		}
	</style>
</head>
<body>
<div class="main-wrapper">
'''

def get_header(user_name, user_role, avatar_num="02"):
    return f'''
	<!-- Header -->
	<div class="header">
		<div class="main-header">
			<div class="header-left">
				<a href="index.html" class="logo">
					<img src="assets/img/logo-antaradark.png" alt="Logo Antara" style="height:36px;">
				</a>
			</div>

			<a id="mobile_btn" class="mobile_btn" href="#sidebar">
				<span class="bar-icon">
					<span></span><span></span><span></span>
				</span>
			</a>

			<div class="header-user">
				<div class="nav user-menu nav-list">
					<div class="me-auto d-flex align-items-center" id="header-search">
						<a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1 text-white">
							<i class="ti ti-arrow-bar-to-left"></i>
						</a>
						<div class="header-welcome-message me-2">
							Selamat Datang di CRM ANTARA <span style="font-style:italic; font-weight:normal; opacity:0.85;">Information System</span>
						</div>
					</div>

					<ul class="nav user-menu">
						<li class="nav-item dropdown me-2">
							<a href="javascript:void(0);" class="btn btn-menubar text-white position-relative">
								<i class="ti ti-bell"></i>
								<span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size:10px;">3</span>
							</a>
						</li>
						<li class="nav-item dropdown has-arrow main-drop">
							<a href="javascript:void(0);" class="dropdown-toggle nav-link userset d-flex align-items-center" data-bs-toggle="dropdown">
								<span class="user-img me-2">
									<img src="assets/img/profiles/avatar-{avatar_num}.jpg" alt="User" class="rounded-circle" style="width:34px; height:34px;">
								</span>
								<div class="user-info text-start d-none d-md-block text-white">
									<h6 class="mb-0 fs-13 text-white fw-bold">{user_name}</h6>
									<span class="fs-11 text-white-50">{user_role}</span>
								</div>
							</a>
							<div class="dropdown-menu menu-drop-user dropdown-menu-end shadow">
								<div class="profilename p-3 border-bottom">
									<h6 class="fs-13 fw-bold mb-0">{user_name}</h6>
									<p class="fs-11 text-muted mb-0">{user_role}</p>
								</div>
								<a class="dropdown-item" href="../login.html"><i class="ti ti-logout me-2"></i>Keluar (Logout)</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
'''

def get_sidebar(is_admin=True):
    active_admin = 'active' if is_admin else ''
    active_cust = 'active' if not is_admin else ''
    
    return f'''
	<!-- Sidebar -->
	<div class="sidebar" id="sidebar">
		<div class="sidebar-logo p-3 d-flex align-items-center justify-content-between">
			<a href="index.html">
				<img src="assets/img/logo-antaradark.png" alt="Logo" style="height:32px;">
			</a>
		</div>

		<div class="sidebar-inner slimscroll">
			<div id="sidebar-menu" class="sidebar-menu">
				<ul>
					<li class="menu-title"><span>NAVIGASI UTAMA</span></li>
					<li>
						<a href="index.html" class="{active_admin}">
							<i class="ti ti-smart-home"></i><span>Dashboard Admin</span>
						</a>
					</li>
					<li>
						<a href="customer.html" class="{active_cust}">
							<i class="ti ti-users"></i><span>Portal Pelanggan/Mitra</span>
						</a>
					</li>

					<li class="menu-title"><span>APLIKASI CRM</span></li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-mail"></i><span>Korespondensi</span><span class="badge bg-danger ms-auto">2</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-checkup-list"></i><span>Tiket Support & Backlog</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-calendar"></i><span>Jadwal Maintenance</span></a>
					</li>

					<li class="menu-title"><span>MANAJEMEN KONTEN & RELASI</span></li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-address-book"></i><span>Klien & Korporasi</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-world"></i><span>Mitra Internasional</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-packages"></i><span>Katalog Produk ANTARA</span></a>
					</li>

					<li class="menu-title"><span>BILLING & TRANSAKSI</span></li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-file-invoice"></i><span>Tagihan / Invoices</span><span class="badge bg-warning ms-auto text-dark">4</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-credit-card"></i><span>Riwayat Pembayaran</span></a>
					</li>
					<li>
						<a href="javascript:void(0);"><i class="ti ti-receipt"></i><span>Paket Langganan</span></a>
					</li>

					<li class="menu-title"><span>DEMO PORTOFOLIO</span></li>
					<li>
						<a href="../index.html"><i class="ti ti-arrow-back-up"></i><span>Kembali ke Beranda</span></a>
					</li>
					<li>
						<a href="../login.html"><i class="ti ti-lock"></i><span>Halaman Login / OTP</span></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
'''

common_footer_scripts = '''
</div> <!-- /.main-wrapper -->

<!-- Core Scripts -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="../demo-bar/demo-bar.js"></script>
<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            "language": {
                "paginate": { "previous": "‹", "next": "›" },
                "search": "Cari data: ",
                "lengthMenu": "Tampilkan _MENU_ baris"
            },
            "pageLength": 5
        });
    }
});
</script>
</body>
</html>
'''

# ----------------------------------------------------
# 1. BUILD ADMIN DASHBOARD (admin/index.html)
# ----------------------------------------------------
def build_admin():
    print("Generating admin/index.html...")
    content = f'''
{common_head.replace('{TITLE}', 'ANTARA CRM - Superadmin IT Dashboard')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar(is_admin=True)}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Dashboard Divisi IT & Redaksi</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Superadmin</li>
							<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<span class="badge bg-success py-2 px-3"><i class="ti ti-circle-check me-1"></i>Sistem Berjalan Normal</span>
					<a href="customer.html" class="btn btn-outline-danger btn-sm"><i class="ti ti-switch-horizontal me-1"></i>Beralih ke Portal Pelanggan</a>
				</div>
			</div>

			<!-- Hero Banner -->
			<div class="card border-0 text-white mb-4" style="background: linear-gradient(135deg, #D70006 0%, #800003 100%);">
				<div class="card-body d-flex align-items-center justify-content-between flex-wrap p-4">
					<div class="mb-2">
						<h3 class="text-white mb-1">Pusat Operasional Digital LKBN ANTARA</h3>
						<p class="mb-0 text-white-50">Monitoring realtime kesehatan infrastruktur portal berita, integrasi API mitra, dan tiket keluhan pelanggan.</p>
					</div>
					<div class="d-flex align-items-center flex-wrap gap-2">
						<a href="#tickets" class="btn btn-light text-danger fw-bold"><i class="ti ti-checklist me-1"></i>Lihat Tiket Kritis</a>
						<a href="#maintenance" class="btn btn-outline-light"><i class="ti ti-calendar-time me-1"></i>Jadwal Maintenance</a>
					</div>
				</div>
			</div>

			<!-- Metrics Cards -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm h-100 border-0">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-primary bg-opacity-10 text-primary rounded p-2">
									<i class="ti ti-server fs-20"></i>
								</span>
								<span class="badge bg-success bg-opacity-10 text-success">+2 layanan</span>
							</div>
							<h3 class="mb-1 fw-bold">34</h3>
							<p class="text-muted fs-13 mb-0">Layanan Produksi Aktif</p>
							<div class="sparkline mt-2">● ● ● Uptime 99.98%</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm h-100 border-0">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-danger bg-opacity-10 text-danger rounded p-2">
									<i class="ti ti-alert-triangle fs-20"></i>
								</span>
								<span class="badge bg-danger bg-opacity-10 text-danger">3 prioritas</span>
							</div>
							<h3 class="mb-1 fw-bold">18</h3>
							<p class="text-muted fs-13 mb-0">Tiket Dukungan Terbuka</p>
							<div class="sparkline mt-2 text-danger">● Respon rata-rata: 14 menit</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm h-100 border-0">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-success bg-opacity-10 text-success rounded p-2">
									<i class="ti ti-rocket fs-20"></i>
								</span>
								<span class="badge bg-success bg-opacity-10 text-success">Minggu Ini</span>
							</div>
							<h3 class="mb-1 fw-bold">12</h3>
							<p class="text-muted fs-13 mb-0">Deployment &amp; Rilis Fitur</p>
							<div class="sparkline mt-2 text-success">● 100% lulus QA test</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm h-100 border-0">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-info bg-opacity-10 text-info rounded p-2">
									<i class="ti ti-heart-rate-monitor fs-20"></i>
								</span>
								<span class="badge bg-info bg-opacity-10 text-info">30 Hari</span>
							</div>
							<h3 class="mb-1 fw-bold">99.97%</h3>
							<p class="text-muted fs-13 mb-0">Rata-rata Uptime Sistem</p>
							<div class="sparkline mt-2 text-info">● 0 downtime terencana</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Status Layanan & Insiden -->
			<div class="row g-3 mb-4">
				<div class="col-xl-7">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
							<h5 class="card-title mb-0 fw-bold">Status Layanan Kritis Redaksi</h5>
							<span class="badge bg-primary">Real-time</span>
						</div>
						<div class="card-body">
							<div class="mb-4">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">Portal Newsroom (Antaranews.com)</span>
									<span class="text-success fw-bold">Normal (100%)</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-success" style="width: 100%"></div>
								</div>
							</div>
							<div class="mb-4">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">Sistem Redaksi Terpadu (NRCS)</span>
									<span class="text-success fw-bold">Normal (96%)</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-success" style="width: 96%"></div>
								</div>
							</div>
							<div class="mb-4">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">Media Asset Management (Foto &amp; Arsip)</span>
									<span class="text-warning fw-bold">Degradasi Minor (78%)</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-warning" style="width: 78%"></div>
								</div>
							</div>
							<div class="mb-1">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">API Gateway Distribusi Konten Mitra</span>
									<span class="text-danger fw-bold">Investigasi (62%)</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-danger" style="width: 62%"></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-5">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom">
							<h5 class="card-title mb-0 fw-bold">Catatan Insiden Operasional</h5>
						</div>
						<div class="card-body p-0">
							<ul class="list-group list-group-flush">
								<li class="list-group-item d-flex justify-content-between align-items-start p-3">
									<div>
										<h6 class="mb-1 fw-bold">Gangguan CDN Regional Jatim</h6>
										<p class="mb-0 text-muted fs-12">Durasi 12 menit, mitigasi via failover Jakarta</p>
									</div>
									<span class="badge bg-danger">Kritis</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start p-3">
									<div>
										<h6 class="mb-1 fw-bold">Timeout API Sinkronisasi Foto</h6>
										<p class="mb-0 text-muted fs-12">Normalisasi selesai pukul 09:45 WIB</p>
									</div>
									<span class="badge bg-warning text-dark">Sedang</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start p-3">
									<div>
										<h6 class="mb-1 fw-bold">Integrasi Single Sign-On Mitra</h6>
										<p class="mb-0 text-muted fs-12">Permintaan perpanjangan token OAuth</p>
									</div>
									<span class="badge bg-info text-dark">Info</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<!-- Maintenance & Deployments -->
			<div class="row g-3 mb-4" id="maintenance">
				<div class="col-xl-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom">
							<h5 class="card-title mb-0 fw-bold">Jadwal Maintenance Sistem</h5>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-hover align-middle mb-0">
									<thead class="table-light">
										<tr>
											<th>Tanggal</th>
											<th>Sistem</th>
											<th>Window Waktu</th>
											<th>PIC</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><span class="fw-bold">30 Okt</span></td>
											<td>Database Cluster Newsroom</td>
											<td>00:30 - 02:00 WIB</td>
											<td>Sarah W.</td>
										</tr>
										<tr>
											<td><span class="fw-bold">31 Okt</span></td>
											<td>Portal Antaranews.com Core</td>
											<td>23:00 - 23:45 WIB</td>
											<td>Riko A.</td>
										</tr>
										<tr>
											<td><span class="fw-bold">02 Nov</span></td>
											<td>Streaming OTT Media Server</td>
											<td>01:00 - 03:30 WIB</td>
											<td>Dian K.</td>
										</tr>
										<tr>
											<td><span class="fw-bold">05 Nov</span></td>
											<td>Storage MAM Cloud Backup</td>
											<td>22:00 - 01:00 WIB</td>
											<td>Budi P.</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom">
							<h5 class="card-title mb-0 fw-bold">Deployments &amp; Release Terakhir</h5>
						</div>
						<div class="card-body p-0">
							<ul class="list-group list-group-flush">
								<li class="list-group-item d-flex justify-content-between align-items-center p-3">
									<div>
										<h6 class="mb-1 fw-bold">Release 3.12 - Portal Redaksi Terpadu</h6>
										<p class="mb-0 text-muted fs-12">Penambahan fitur otomatisasi metadata tag dan foto</p>
									</div>
									<span class="badge bg-success">Sukses</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-3">
									<div>
										<h6 class="mb-1 fw-bold">Patch Keamanan API Distribusi Konten</h6>
										<p class="mb-0 text-muted fs-12">Pembaruan token rotasi HMAC &amp; Rate Limiting</p>
									</div>
									<span class="badge bg-success">Sukses</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-center p-3">
									<div>
										<h6 class="mb-1 fw-bold">Update Antara Stories Mobile Feed</h6>
										<p class="mb-0 text-muted fs-12">Penyesuaian bitrate streaming video adaptif HLS</p>
									</div>
									<span class="badge bg-warning text-dark">Validasi QA</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<!-- Tiket Support Table -->
			<div class="card shadow-sm border-0 mb-4" id="tickets">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Tiket Support &amp; Permintaan Layanan CRM</h5>
					<span class="badge bg-danger">5 Tiket Aktif</span>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover align-middle datatable">
							<thead class="table-light">
								<tr>
									<th>Kode Tiket</th>
									<th>Deskripsi Masalah / Kebutuhan</th>
									<th>Prioritas</th>
									<th>Unit Pemohon</th>
									<th>Status</th>
									<th>Petugas (PIC)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><strong class="text-primary">#INC-4578</strong></td>
									<td>Perbaikan feed foto otomatis biro Surabaya ke sistem pusat</td>
									<td><span class="badge bg-danger">Kritis</span></td>
									<td>Biro Jawa Timur</td>
									<td><span class="badge bg-warning text-dark">On Progress</span></td>
									<td>Rani M.</td>
								</tr>
								<tr>
									<td><strong class="text-primary">#INC-4583</strong></td>
									<td>Permintaan akses VPN kontributor foto freelance</td>
									<td><span class="badge bg-secondary">Rendah</span></td>
									<td>HR &amp; Kemitraan</td>
									<td><span class="badge bg-success">Selesai</span></td>
									<td>Ahmad F.</td>
								</tr>
								<tr>
									<td><strong class="text-primary">#INC-4590</strong></td>
									<td>Integrasi metadata video berita 4K ke arsip digital</td>
									<td><span class="badge bg-primary">Sedang</span></td>
									<td>Produksi TV ANTARA</td>
									<td><span class="badge bg-warning text-dark">Review QA</span></td>
									<td>Novi L.</td>
								</tr>
								<tr>
									<td><strong class="text-primary">#INC-4594</strong></td>
									<td>Penyesuaian konfigurasi firewall kantor biro Papua</td>
									<td><span class="badge bg-primary">Sedang</span></td>
									<td>Operasional Regional</td>
									<td><span class="badge bg-info text-dark">Penjadwalan</span></td>
									<td>Yusuf S.</td>
								</tr>
								<tr>
									<td><strong class="text-primary">#INC-4601</strong></td>
									<td>Permintaan dashboard realtime KPI pembaca biro daerah</td>
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
	</div>

{common_footer_scripts}
'''
    target = os.path.join(ADMIN_DIR, "index.html")
    with open(target, "w", encoding="utf-8") as f:
        f.write(content)
    print("Created:", target)

# ----------------------------------------------------
# 2. BUILD CUSTOMER DASHBOARD (admin/customer.html)
# ----------------------------------------------------
def build_customer():
    print("Generating admin/customer.html...")
    content = f'''
{common_head.replace('{TITLE}', 'ANTARA CRM - Portal Layanan Pelanggan')}
{get_header('PT Media Nusantara Prima', 'Pelanggan Mitra Korporasi', '01')}
{get_sidebar(is_admin=False)}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Portal Kemitraan Pelanggan</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Pelanggan</li>
							<li class="breadcrumb-item active" aria-current="page">Overview Layanan</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<span class="badge bg-primary py-2 px-3"><i class="ti ti-shield-check me-1"></i>Akun Terverifikasi</span>
					<a href="index.html" class="btn btn-outline-danger btn-sm"><i class="ti ti-switch-horizontal me-1"></i>Beralih ke Dashboard Admin</a>
				</div>
			</div>

			<!-- Welcome Card -->
			<div class="card border-0 text-white mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
				<div class="card-body d-flex align-items-center justify-content-between flex-wrap p-4">
					<div class="mb-2">
						<span class="badge bg-danger mb-2">Paket Korporasi Aktif</span>
						<h3 class="text-white mb-1">Selamat Datang, PT Media Nusantara Prima</h3>
						<p class="mb-0 text-white-50">Kelola kuota lisensi berita, tagihan bulanan, dan saluran feed foto/video resmi ANTARA Anda di sini.</p>
					</div>
					<div class="d-flex align-items-center flex-wrap gap-2">
						<a href="#invoices" class="btn btn-danger fw-bold"><i class="ti ti-receipt me-1"></i>Bayar Tagihan</a>
						<a href="javascript:void(0)" class="btn btn-outline-light" onclick="alert('API Token Distribusi: ant_live_9921fa882bc741d')"><i class="ti ti-key me-1"></i>Lihat Kunci API Feed</a>
					</div>
				</div>
			</div>

			<!-- Customer Stat Cards -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-success bg-opacity-10 text-success rounded p-2">
									<i class="ti ti-package fs-20"></i>
								</span>
								<span class="badge bg-success">Aktif</span>
							</div>
							<h3 class="mb-1 fw-bold">3 Layanan</h3>
							<p class="text-muted fs-13 mb-0">Paket Konten Berjalan</p>
							<div class="sparkline text-success mt-2">● Berita Teks, Foto HD, Video News</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-warning bg-opacity-10 text-warning rounded p-2">
									<i class="ti ti-file-invoice fs-20"></i>
								</span>
								<span class="badge bg-warning text-dark">1 Jatuh Tempo</span>
							</div>
							<h3 class="mb-1 fw-bold">Rp 15.000.000</h3>
							<p class="text-muted fs-13 mb-0">Total Tagihan Tertunda</p>
							<div class="sparkline text-warning mt-2">● Jatuh tempo 15 hari lagi</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-info bg-opacity-10 text-info rounded p-2">
									<i class="ti ti-calendar fs-20"></i>
								</span>
								<span class="badge bg-info text-dark">Tahunan</span>
							</div>
							<h3 class="mb-1 fw-bold">31 Des 2026</h3>
							<p class="text-muted fs-13 mb-0">Batas Akhir Kontrak</p>
							<div class="sparkline text-info mt-2">● Perpanjangan otomatis aktif</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<span class="avatar avatar-md bg-primary bg-opacity-10 text-primary rounded p-2">
									<i class="ti ti-download fs-20"></i>
								</span>
								<span class="badge bg-primary">Bulan Ini</span>
							</div>
							<h3 class="mb-1 fw-bold">4.820 Unduhan</h3>
							<p class="text-muted fs-13 mb-0">Penggunaan Kuota Feed</p>
							<div class="sparkline text-primary mt-2">● 62% dari kuota 7.500 artikel</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Paket Langganan Saya -->
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Paket Langganan Konten Anda</h5>
					<span class="badge bg-success">3 Paket Aktif</span>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th>Nama Layanan</th>
									<th>Kode Paket</th>
									<th>Siklus Billing</th>
									<th>Biaya / Periode</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-danger text-white rounded me-2 d-flex align-items-center justify-content-center">
												<i class="ti ti-news"></i>
											</span>
											<div>
												<span class="fw-bold d-block">ANTARA News Wire Feed API</span>
												<small class="text-muted">Distribusi berita teks 24/7 seluruh nusantara</small>
											</div>
										</div>
									</td>
									<td><code>PKG-NEWS-CORP</code></td>
									<td>Bulanan</td>
									<td><strong class="text-dark">Rp 10.000.000</strong></td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><button class="btn btn-sm btn-outline-primary" onclick="alert('Feed URL Endpoint: https://api.antara.id/v1/news?token=ant_live')">Unduh Endpoint</button></td>
								</tr>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-primary text-white rounded me-2 d-flex align-items-center justify-content-center">
												<i class="ti ti-photo"></i>
											</span>
											<div>
												<span class="fw-bold d-block">ANTARA Foto Editorial HD</span>
												<small class="text-muted">Lisensi komersial publikasi 1.000 foto/bulan</small>
											</div>
										</div>
									</td>
									<td><code>PKG-FOTO-PREMIUM</code></td>
									<td>Bulanan</td>
									<td><strong class="text-dark">Rp 5.000.000</strong></td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><button class="btn btn-sm btn-outline-primary" onclick="alert('Akses Galeri HD: https://foto.antara.id/auth/token')">Akses Galeri</button></td>
								</tr>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-success text-white rounded me-2 d-flex align-items-center justify-content-center">
												<i class="ti ti-video"></i>
											</span>
											<div>
												<span class="fw-bold d-block">ANTARA TV Raw Package</span>
												<small class="text-muted">Klip video berita harian format ProRes/MP4 1080p</small>
											</div>
										</div>
									</td>
									<td><code>PKG-TV-BROADCAST</code></td>
									<td>Tahunan</td>
									<td><strong class="text-dark">Rp 45.000.000</strong></td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><button class="btn btn-sm btn-outline-primary" onclick="alert('FTP Server: ftp://media.antara.id:21')">Akses FTP</button></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Tagihan & Invoices -->
			<div class="card shadow-sm border-0 mb-4" id="invoices">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Riwayat &amp; Status Tagihan (Invoices)</h5>
					<span class="badge bg-warning text-dark">1 Menunggu Pembayaran</span>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover align-middle datatable">
							<thead class="table-light">
								<tr>
									<th>Nomor Invoice</th>
									<th>Judul Tagihan</th>
									<th>Jatuh Tempo</th>
									<th>Total Tagihan</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><strong class="text-primary">INV-2026-09-0012</strong></td>
									<td>Langganan News Wire &amp; Foto Bulan September 2026</td>
									<td>15 Okt 2026</td>
									<td><strong>Rp 15.000.000</strong></td>
									<td><span class="badge bg-warning text-dark">Menunggu Pembayaran</span></td>
									<td><button class="btn btn-sm btn-danger" onclick="alert('Simulasi Pembayaran: Tagihan sebesar Rp 15.000.000 berhasil disimulasikan lunas!')"><i class="ti ti-credit-card me-1"></i>Bayar Sekarang</button></td>
								</tr>
								<tr>
									<td><strong class="text-primary">INV-2026-08-0098</strong></td>
									<td>Langganan News Wire &amp; Foto Bulan Agustus 2026</td>
									<td>15 Sep 2026</td>
									<td><strong>Rp 15.000.000</strong></td>
									<td><span class="badge bg-success">Lunas</span></td>
									<td><button class="btn btn-sm btn-outline-secondary" onclick="alert('Kwitansi pelunasan resmi nomor RCP-08-9912 telah diunduh.')"><i class="ti ti-receipt me-1"></i>Kwitansi</button></td>
								</tr>
								<tr>
									<td><strong class="text-primary">INV-2026-07-0045</strong></td>
									<td>Langganan News Wire &amp; Foto Bulan Juli 2026</td>
									<td>15 Agu 2026</td>
									<td><strong>Rp 15.000.000</strong></td>
									<td><span class="badge bg-success">Lunas</span></td>
									<td><button class="btn btn-sm btn-outline-secondary" onclick="alert('Kwitansi pelunasan resmi nomor RCP-07-8831 telah diunduh.')"><i class="ti ti-receipt me-1"></i>Kwitansi</button></td>
								</tr>
								<tr>
									<td><strong class="text-primary">INV-2026-01-0003</strong></td>
									<td>Biaya Lisensi Tahunan ANTARA TV Raw Package 2026</td>
									<td>10 Feb 2026</td>
									<td><strong>Rp 45.000.000</strong></td>
									<td><span class="badge bg-success">Lunas</span></td>
									<td><button class="btn btn-sm btn-outline-secondary" onclick="alert('Kwitansi pelunasan resmi nomor RCP-01-1002 telah diunduh.')"><i class="ti ti-receipt me-1"></i>Kwitansi</button></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

{common_footer_scripts}
'''
    target = os.path.join(ADMIN_DIR, "customer.html")
    with open(target, "w", encoding="utf-8") as f:
        f.write(content)
    print("Created:", target)

build_admin()
build_customer()
