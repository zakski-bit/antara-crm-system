import os
import json

BASE_DIR = r"C:\Users\Dell 3490\.gemini\antigravity\scratch\Proyek-CRM"
OUTPUT_DIR = os.path.join(BASE_DIR, "crm-portfolio-live")
ADMIN_DIR = os.path.join(OUTPUT_DIR, "admin")

def get_head(title, active_page="dashboard"):
    return f'''<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>{title}</title>
	
	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="../favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="../favicon.png">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="../demo-bar/demo-bar.css">

	<!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

	<style>
		:root {{
			--primary: #D70006;
			--primary-hover: #B50005;
			--primary-light: rgba(215, 0, 6, 0.1);
			--secondary: #24201F;
			--dark: #24201F;
			--body-font: 'Outfit', sans-serif;
			--title-font: 'Roboto', sans-serif;
		}}
		body {{
			font-family: 'Outfit', sans-serif !important;
		}}
		h1, h2, h3, h4, h5, h6, .page-header h3, .page-header h4 {{
			font-family: 'Roboto', sans-serif !important;
		}}
		.sidebar {{
			background-color: #1a1816 !important;
		}}
		.sidebar .sidebar-menu>ul>li a.active, .sidebar .sidebar-menu>ul>li a:hover {{
			color: #fff !important;
			background: rgba(215, 0, 6, 0.15) !important;
		}}
		.sidebar .sidebar-menu>ul>li a.active i, .sidebar .sidebar-menu>ul>li a:hover i {{
			color: #D70006 !important;
		}}
		.sidebar-logo {{
			background-color: #24201F !important;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
		}}
		.header {{
			background: #2F3E56 !important;
			border-bottom: 2px solid #233046 !important;
		}}
		.header-welcome-message {{
			color: #ffffff;
			font-weight: 600;
			font-size: 14px;
		}}
		.btn-primary {{
			background-color: #D70006 !important;
			border-color: #D70006 !important;
		}}
		.btn-primary:hover {{
			background-color: #B50005 !important;
			border-color: #B50005 !important;
		}}
		.badge-antara {{
			background-color: rgba(215, 0, 6, 0.12);
			color: #D70006;
			font-weight: 600;
		}}
		.card {{
			border-radius: 8px;
		}}
		.sparkline {{
			font-size: 11px;
			color: #64748b;
			font-weight: 600;
		}}
		.nav-pills .nav-link.active {{
			background-color: #D70006 !important;
		}}
	</style>
</head>
<body>
<div class="main-wrapper">
'''

def get_header(user_name="Redaksi Antara", user_role="Superadministrator / IT Lead", avatar_num="02"):
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
							<a href="javascript:void(0);" class="btn btn-menubar text-white position-relative" data-bs-toggle="dropdown">
								<i class="ti ti-bell"></i>
								<span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size:10px;">3</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width: 280px;">
								<h6 class="fw-bold mb-2">Notifikasi Sistem</h6>
								<div class="list-group list-group-flush fs-12">
									<a href="todo.html" class="list-group-item list-group-item-action px-0 py-2">
										<i class="ti ti-alert-circle text-danger me-1"></i> 1 Tiket Kritis Memerlukan Tindakan
									</a>
									<a href="invoices.html" class="list-group-item list-group-item-action px-0 py-2">
										<i class="ti ti-file-invoice text-warning me-1"></i> 1 Tagihan Mendekati Jatuh Tempo
									</a>
									<a href="email.html" class="list-group-item list-group-item-action px-0 py-2">
										<i class="ti ti-mail text-primary me-1"></i> 2 Pesan Korespondensi Baru
									</a>
								</div>
							</div>
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
								<a class="dropdown-item" href="index.html"><i class="ti ti-layout-dashboard me-2"></i>Dashboard Admin</a>
								<a class="dropdown-item" href="customer.html"><i class="ti ti-users me-2"></i>Portal Pelanggan</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item text-danger" href="../login.html"><i class="ti ti-logout me-2"></i>Keluar (Logout)</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
'''

def get_sidebar(active_code="dashboard"):
    act_dash = 'active' if active_code == 'dashboard' else ''
    act_cust = 'active' if active_code == 'customer' else ''
    act_email = 'active' if active_code == 'email' else ''
    act_todo = 'active' if active_code == 'todo' else ''
    act_mitra = 'active' if active_code == 'mitra' else ''
    act_prod = 'active' if active_code == 'produk' else ''
    act_inv = 'active' if active_code == 'invoices' else ''
    act_pay = 'active' if active_code == 'payments' else ''

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
						<a href="index.html" class="{act_dash}">
							<i class="ti ti-smart-home"></i><span>Dashboard Admin</span>
						</a>
					</li>
					<li>
						<a href="customer.html" class="{act_cust}">
							<i class="ti ti-users"></i><span>Portal Pelanggan/Mitra</span>
						</a>
					</li>

					<li class="menu-title"><span>APLIKASI CRM</span></li>
					<li>
						<a href="email.html" class="{act_email}">
							<i class="ti ti-mail"></i><span>Korespondensi</span><span class="badge bg-danger ms-auto">2</span>
						</a>
					</li>
					<li>
						<a href="todo.html" class="{act_todo}">
							<i class="ti ti-checkup-list"></i><span>Tiket Support & To-Do</span>
						</a>
					</li>

					<li class="menu-title"><span>KONTEN & RELASI</span></li>
					<li>
						<a href="mitra.html" class="{act_mitra}">
							<i class="ti ti-world"></i><span>Mitra & Korporasi</span>
						</a>
					</li>
					<li>
						<a href="produk-antara.html" class="{act_prod}">
							<i class="ti ti-packages"></i><span>Katalog Produk ANTARA</span>
						</a>
					</li>

					<li class="menu-title"><span>BILLING & TRANSAKSI</span></li>
					<li>
						<a href="invoices.html" class="{act_inv}">
							<i class="ti ti-file-invoice"></i><span>Tagihan / Invoices</span><span class="badge bg-warning ms-auto text-dark">4</span>
						</a>
					</li>
					<li>
						<a href="payments.html" class="{act_pay}">
							<i class="ti ti-credit-card"></i><span>Riwayat Pembayaran</span>
						</a>
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

def get_footer():
    return '''
</div> <!-- /.main-wrapper -->

<!-- Core Scripts -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="assets/js/script.js"></script>
<script src="../demo-bar/demo-bar.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi daterangepicker jika ada elemen bookingrange
    if ($.fn.daterangepicker && $('.bookingrange').length > 0) {
        $('.bookingrange').daterangepicker({
            opens: 'left',
            locale: {
                format: 'DD/MM/YYYY'
            }
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
{get_head('ANTARA CRM - Superadmin IT Dashboard', 'dashboard')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('dashboard')}

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
					<div class="input-icon mb-2 position-relative">
						<span class="input-icon-addon">
							<i class="ti ti-calendar text-gray-9"></i>
						</span>
						<input type="text" class="form-control date-range bookingrange" placeholder="Filter Rentang Tanggal" style="min-width: 200px;">
					</div>
					<span class="badge bg-success py-2 px-3 mb-2"><i class="ti ti-circle-check me-1"></i>Sistem Berjalan Normal</span>
					<a href="customer.html" class="btn btn-outline-danger btn-sm mb-2"><i class="ti ti-switch-horizontal me-1"></i>Portal Pelanggan</a>
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
						<a href="todo.html" class="btn btn-light text-danger fw-bold"><i class="ti ti-checklist me-1"></i>Lihat Backlog & Tiket</a>
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
							<p class="text-muted fs-13 mb-0">Rata-rata Uptime Global</p>
							<div class="sparkline mt-2 text-info">● 0 Unscheduled Downtime</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Status Layanan Kritis & Insiden Operasional -->
			<div class="row g-3 mb-4">
				<div class="col-xl-7">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom">
							<h5 class="card-title mb-0 fw-bold">Status Infrastruktur &amp; Layanan Kritis</h5>
						</div>
						<div class="card-body">
							<div class="mb-4">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">Portal Newsroom Terpadu (Antaranews Core)</span>
									<span class="text-success fw-bold">Normal (100%)</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-success" style="width: 100%"></div>
								</div>
							</div>
							<div class="mb-4">
								<div class="d-flex justify-content-between mb-1">
									<span class="fw-semibold">Sistem Otomasi Redaksi (NRCS News Flow)</span>
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

{get_footer()}
'''
    with open(os.path.join(ADMIN_DIR, "index.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/index.html")

# ----------------------------------------------------
# 2. BUILD CUSTOMER PORTAL (admin/customer.html)
# ----------------------------------------------------
def build_customer():
    print("Generating admin/customer.html...")
    content = f'''
{get_head('ANTARA CRM - Portal Layanan Pelanggan', 'customer')}
{get_header('PT Media Nusantara Prima', 'Pelanggan Korporasi / Mitra', '01')}
{get_sidebar('customer')}

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
						<a href="invoices.html" class="btn btn-danger fw-bold"><i class="ti ti-receipt me-1"></i>Bayar Tagihan</a>
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
									<i class="ti ti-headphones fs-20"></i>
								</span>
								<span class="badge bg-primary">Dedicated</span>
							</div>
							<h3 class="mb-1 fw-bold">Budi Santoso</h3>
							<p class="text-muted fs-13 mb-0">Account Manager ANTARA</p>
							<div class="sparkline text-primary mt-2">● budi.s@antara.co.id</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Paket Langganan Aktif -->
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Paket Langganan &amp; Akses Layanan Konten</h5>
					<a href="produk-antara.html" class="btn btn-sm btn-outline-danger"><i class="ti ti-plus me-1"></i>Upgrade / Tambah Layanan</a>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-hover align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th>Layanan / Produk</th>
									<th>Siklus Tagihan</th>
									<th>Batas Kuota / Bulan</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-danger bg-opacity-10 text-danger rounded p-2 me-2"><i class="ti ti-news fs-16"></i></span>
											<div>
												<h6 class="mb-0 fw-bold">ANTARA News Wire Fullfeed</h6>
												<span class="text-muted fs-12">Berita Teks Politik, Ekonomi &amp; Internasional</span>
											</div>
										</div>
									</td>
									<td>Tahunan (Rp 35.000.000/th)</td>
									<td>Unlimited API Requests</td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><a href="javascript:void(0)" class="btn btn-light btn-sm" onclick="alert('Dokumentasi Endpoint API Feed: https://api.antara.id/v1/news')"><i class="ti ti-code me-1"></i>Endpoint API</a></td>
								</tr>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-warning bg-opacity-10 text-warning rounded p-2 me-2"><i class="ti ti-photo fs-16"></i></span>
											<div>
												<h6 class="mb-0 fw-bold">ANTARA Foto Jurnalistik Editorial</h6>
												<span class="text-muted fs-12">Resolusi Tinggi Editorial Lisensi Korporasi</span>
											</div>
										</div>
									</td>
									<td>Bulanan (Rp 5.000.000/bln)</td>
									<td>500 Foto / Bulan (Sisa: 312)</td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><a href="javascript:void(0)" class="btn btn-light btn-sm" onclick="alert('Membuka Galeri Foto Lisensi')"><i class="ti ti-download me-1"></i>Download Feed</a></td>
								</tr>
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="avatar avatar-sm bg-info bg-opacity-10 text-info rounded p-2 me-2"><i class="ti ti-video fs-16"></i></span>
											<div>
												<h6 class="mb-0 fw-bold">ANTARA TV Broadcast Video Package</h6>
												<span class="text-muted fs-12">Siaran Berita Video 1080p Siap Tayang</span>
											</div>
										</div>
									</td>
									<td>Per Proyek / Kuartal</td>
									<td>20 Klip Siar / Minggu</td>
									<td><span class="badge bg-success">Aktif</span></td>
									<td><a href="javascript:void(0)" class="btn btn-light btn-sm" onclick="alert('Kredensial Server SFTP Video: ftp.video.antara.id')"><i class="ti ti-server me-1"></i>Akses SFTP</a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Tagihan & Invoices -->
			<div class="card shadow-sm border-0 mb-4" id="invoices">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Riwayat &amp; Status Tagihan Anda</h5>
					<span class="badge bg-warning text-dark">1 Perlu Pembayaran Segera</span>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover align-middle datatable">
							<thead class="table-light">
								<tr>
									<th>Nomor Invoice</th>
									<th>Rincian Layanan</th>
									<th>Periode</th>
									<th>Nominal</th>
									<th>Jatuh Tempo</th>
									<th>Status</th>
									<th>Tindakan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><strong>#INV-ANT-2026-081</strong></td>
									<td>Perpanjangan Sindikasi Berita Teks Kuartal IV</td>
									<td>Okt - Des 2026</td>
									<td><strong>Rp 15.000.000</strong></td>
									<td>25 Okt 2026</td>
									<td><span class="badge bg-warning text-dark">Belum Dibayar</span></td>
									<td><a href="invoices.html" class="btn btn-sm btn-danger"><i class="ti ti-credit-card me-1"></i>Bayar</a></td>
								</tr>
								<tr>
									<td><strong>#INV-ANT-2026-054</strong></td>
									<td>Langganan Foto Jurnalistik Lisensi Komersil</td>
									<td>Jul - Sep 2026</td>
									<td>Rp 15.000.000</td>
									<td>25 Jul 2026</td>
									<td><span class="badge bg-success">Lunas</span></td>
									<td><a href="invoices.html" class="btn btn-sm btn-light"><i class="ti ti-receipt me-1"></i>Bukti</a></td>
								</tr>
								<tr>
									<td><strong>#INV-ANT-2026-022</strong></td>
									<td>Paket Siar TV &amp; Dokumentasi Liputan Khusus</td>
									<td>Apr - Jun 2026</td>
									<td>Rp 22.500.000</td>
									<td>25 Apr 2026</td>
									<td><span class="badge bg-success">Lunas</span></td>
									<td><a href="invoices.html" class="btn btn-sm btn-light"><i class="ti ti-receipt me-1"></i>Bukti</a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

{get_footer()}
'''
    with open(os.path.join(ADMIN_DIR, "customer.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/customer.html")

# ----------------------------------------------------
# 3. BUILD PRODUK ANTARA (admin/produk-antara.html)
# ----------------------------------------------------
def build_produk():
    print("Generating admin/produk-antara.html...")
    products = [
        {
            "id": "afiliasi-antara",
            "name": "Program Afiliasi ANTARA",
            "category": "Kemitraan Konten",
            "cat_code": "kemitraan",
            "summary": "Sindikasi berita resmi ANTARA lengkap dengan dukungan editorial untuk portal dan platform digital mitra.",
            "logo": "assets/img/product/logo-afiliasi-antara.png",
            "highlights": [
                "Kurasi berita prioritas nasional, daerah, dan tematik real-time.",
                "Widget siap tanam untuk portal web, aplikasi, dan digital signage.",
                "Dukungan editor khusus penyesuaian tone-of-voice mitra media."
            ],
            "details": "Program Afiliasi ANTARA memberikan akses langsung ke jaringan jurnalis nasional kami sehingga mitra dapat memperkaya kanal berita mereka dengan konten kredibel dan terkini. Paket kemitraan mencakup integrasi API, dukungan onboarding, hingga monetisasi bersama."
        },
        {
            "id": "ahc",
            "name": "ANTARA Health Communications (AHC)",
            "category": "Komunikasi Kesehatan",
            "cat_code": "layanan",
            "summary": "Repositori konten kesehatan berbasis jurnalisme data untuk mendukung kampanye edukasi institusi kesehatan dan CSR.",
            "logo": "assets/img/product/logo-ahc.png",
            "highlights": [
                "Paket artikel, infografik, dan video literasi kesehatan terverifikasi.",
                "Editorial plan bulanan yang disusun bersama tim komunikasi klien.",
                "Distribusi multi-kanal ke jaringan portal ANTARA nasional."
            ],
            "details": "AHC memadukan riset kesehatan publik dengan gaya bercerita khas ANTARA sehingga pesan edukasi lebih mudah diterima masyarakat. Ideal untuk kementerian/lembaga, rumah sakit, dan korporasi."
        },
        {
            "id": "antara-etp",
            "name": "ANTARA Event Tracking Platform (ETP)",
            "category": "Peliputan & Event",
            "cat_code": "kemitraan",
            "summary": "Platform pemantauan event dan konferensi pers yang mengintegrasikan agenda, liputan, dan distribusi materi publikasi.",
            "logo": "assets/img/product/logo-antara-etp.png",
            "highlights": [
                "Dashboard agenda nasional dan korporasi untuk newsroom dan mitra.",
                "Manajemen kredensial media dengan notifikasi otomatis.",
                "Distribusi sertifikat kehadiran dan materi liputan secara digital."
            ],
            "details": "ANTARA ETP membantu tim PR dan penyelenggara event mengelola akreditasi media, publikasi jadwal, hingga distribusi rilis dan materi visual pasca acara."
        },
        {
            "id": "antara-foto",
            "name": "ANTARA Foto",
            "category": "Konten Visual",
            "cat_code": "foto-video",
            "summary": "Layanan foto jurnalistik dan arsip visual Indonesia dengan lisensi fleksibel untuk media dan korporasi.",
            "logo": "assets/img/product/logo-antara-foto.png",
            "highlights": [
                "Lebih dari satu juta foto dengan metadata lengkap dan kontinu diperbarui.",
                "Pilihan lisensi editorial, komersial, dan korporasi.",
                "Tim fotografer nasional siap membantu permintaan pemotretan khusus."
            ],
            "details": "ANTARA Foto menyajikan dokumentasi bersejarah dan liputan terkini dalam resolusi tinggi. Sistem penelusuran memudahkan kurator konten menemukan materi visual."
        },
        {
            "id": "antara-news",
            "name": "ANTARA News Wire",
            "category": "Portal Berita & Sindikasi",
            "cat_code": "berita",
            "summary": "Portal berita resmi Republik Indonesia dengan update 24/7 dalam bahasa Indonesia dan Inggris.",
            "logo": "assets/img/product/logo-antara-news.png",
            "highlights": [
                "Jaringan koresponden di 34 provinsi dan 13 biro luar negeri.",
                "Ragam kanal tematik ekonomi, politik, olahraga, dan industri kreatif.",
                "Integrasi API untuk kebutuhan embed headline dan RSS khusus."
            ],
            "details": "Sebagai kantor berita nasional, ANTARA News memastikan akurasi, kecepatan, dan kedalaman setiap publikasi berita."
        },
        {
            "id": "antara-tv",
            "name": "ANTARA TV & Video News",
            "category": "Broadcasting & Video",
            "cat_code": "foto-video",
            "summary": "Pasokan paket video berita broadcast-ready 1080p/4K untuk stasiun televisi, OTT, dan saluran media sosial.",
            "logo": "assets/img/product/logo-antara-tv.png",
            "highlights": [
                "Video berita harian dengan voice-over atau clean audio (natural sound).",
                "Format siap tayang untuk format horizontal TV dan format vertikal media sosial.",
                "Dukungan live streaming konferensi pers dan agenda nasional."
            ],
            "details": "ANTARA TV melayani kebutuhan stasiun televisi nasional, regional, maupun portal web yang membutuhkan pasokan video berita terkini dengan izin siar resmi."
        },
        {
            "id": "bloomberg",
            "name": "Bloomberg ANTARA Feed",
            "category": "Media & Data Finansial",
            "cat_code": "layanan",
            "summary": "Integrasi berita pasar modal, bursa komoditas, dan pergerakan makroekonomi internasional berstandar Bloomberg.",
            "logo": "assets/img/product/logo-bloomberg.png",
            "highlights": [
                "Data indeks bursa saham dan valuta asing terhubung langsung.",
                "Analisis mendalam dari analis ekonomi global.",
                "Terminal berita pasar uang untuk korporasi perbankan."
            ],
            "details": "Kolaborasi strategis LKBN ANTARA dengan Bloomberg memberikan keunggulan informasi bisnis terpercaya untuk para pelaku industri pasar modal di Indonesia."
        },
        {
            "id": "branda",
            "name": "Branda (Brand Activation & PR)",
            "category": "Publisitas & PR Wire",
            "cat_code": "kemitraan",
            "summary": "Layanan diseminasi siaran pers ke ratusan media massa terverifikasi Dewan Pers di seluruh Indonesia.",
            "logo": "assets/img/product/logo-branda.png",
            "highlights": [
                "Jaminan publikasi di jaringan media cetak, online, dan televisi rekanan.",
                "Laporan clipping berita komprehensif dengan metrik PR Value.",
                "Bantuan copywriting dan kurasi sudut pandang berita pers."
            ],
            "details": "Branda adalah solusi tepat bagi instansi pemerintah dan perusahaan yang ingin menyuarakan pencapaian mereka kepada publik luas secara efektif dan terukur."
        },
        {
            "id": "hcm",
            "name": "HCM Ads Media",
            "category": "Jaringan Iklan Digital",
            "cat_code": "layanan",
            "summary": "Jaringan iklan programmatic dan programmatic display di portal-portal berita terkemuka Asia Tenggara.",
            "logo": "assets/img/product/logo-hcm-ads-media.png",
            "highlights": [
                "Jangkauan audiens puluhan juta pembaca aktif setiap hari.",
                "Penargetan demografis dan geospasial presisi tinggi.",
                "Format banner rich media dan video in-stream interaktif."
            ],
            "details": "HCM Ads Media mengoptimalkan performa kampanye pemasaran digital klien dengan penempatan iklan yang aman bagi reputasi brand (brand safe)."
        },
        {
            "id": "imcs",
            "name": "IMCS (Integrated Media Content)",
            "category": "Platform Redaksi & CMS",
            "cat_code": "layanan",
            "summary": "Sistem manajemen konten terpadu untuk newsroom, kurasi multi-format teks, foto, infografik, dan video.",
            "logo": "assets/img/product/logo-imcs.png",
            "highlights": [
                "Workflow editorial berjenjang (Reporter, Redaktur, Redpel, Pemred).",
                "Pencarian cerdas aset arsip dengan tagging otomatis AI.",
                "Penerbitan multi-kanal otomatis ke website dan media sosial."
            ],
            "details": "IMCS digunakan oleh kantor berita dan media partner untuk meningkatkan efisiensi proses peliputan hingga penerbitan secara terintegrasi."
        },
        {
            "id": "it-hardware",
            "name": "IT Hardware Rental & Cloud",
            "category": "Infrastruktur IT",
            "cat_code": "layanan",
            "summary": "Penyediaan server, perangkat broadcast lapangan, workstation redaksi, dan cloud backup bersertifikasi.",
            "logo": "assets/img/product/LOGO-IT-HARDWARE-RENTAL.png",
            "highlights": [
                "Dukungan teknisi onsite 24/7 dan penggantian unit cepat (SLA 4 Jam).",
                "Spesifikasi komputasi tinggi untuk rendering video 4K dan pemrosesan foto.",
                "Konektivitas satelit dan microwave link cadangan untuk event luar ruang."
            ],
            "details": "Layanan sewa infrastruktur teknologi informasi LKBN ANTARA mendukung kelancaran operasional media centre pada event internasional seperti KTT G20 dan ASEAN Summit."
        },
        {
            "id": "reuters",
            "name": "Reuters News Agency Feed",
            "category": "Kemitraan Global",
            "cat_code": "berita",
            "summary": "Kanal distribusi sindikasi informasi global melalui aliansi resmi LKBN ANTARA dengan Reuters News Agency.",
            "logo": "assets/img/product/logo-reuters.png",
            "highlights": [
                "Liputan investigasi dan peristiwa dunia dari wartawan Reuters di 200 lokasi.",
                "Feed teks dwibahasa (Inggris & terjemahan Bahasa Indonesia).",
                "Foto arsip internasional dan infografis interaktif terkini."
            ],
            "details": "Menghubungkan ekosistem media Indonesia dengan dinamika internasional terhangat secara real-time melalui jaringan tepercaya Reuters."
        }
    ]

    cards_html = ""
    for p in products:
        hl_list = "".join([f'<li class="mb-1"><i class="ti ti-check text-success me-1"></i>{hl}</li>' for hl in p["highlights"]])
        cards_html += f'''
		<div class="col-xl-4 col-md-6 product-card-col" data-cat="{p['cat_code']}" data-name="{p['name'].lower()}">
			<div class="card shadow-sm border-0 h-100 product-card">
				<div class="card-body d-flex flex-column p-4">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="p-2 bg-light rounded d-flex align-items-center justify-content-center" style="width: 72px; height: 50px;">
							<img src="{p['logo']}" alt="{p['name']}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='assets/img/logo.png'">
						</div>
						<span class="badge badge-antara">{p['category']}</span>
					</div>
					<h5 class="fw-bold mb-2">{p['name']}</h5>
					<p class="text-muted fs-13 mb-3 flex-grow-0">{p['summary']}</p>
					
					<div class="bg-light p-3 rounded mb-3 flex-grow-1">
						<h6 class="fs-12 fw-bold text-uppercase text-secondary mb-2">Fitur Unggulan:</h6>
						<ul class="list-unstyled fs-12 mb-0">
							{hl_list}
						</ul>
					</div>

					<div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
						<button class="btn btn-outline-danger btn-sm" onclick="showProductModal('{p['name']}', '{p['category']}', '{p['summary']}', '{p['details']}')">
							<i class="ti ti-info-circle me-1"></i>Detail Spesifikasi
						</button>
						<a href="invoices.html" class="btn btn-primary btn-sm">
							<i class="ti ti-shopping-cart me-1"></i>Order / Berlangganan
						</a>
					</div>
				</div>
			</div>
		</div>
        '''

    content = f'''
{get_head('ANTARA CRM - Katalog Produk & Solusi Digital', 'produk')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('produk')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Katalog Produk &amp; Solusi Digital ANTARA</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Layanan</li>
							<li class="breadcrumb-item active" aria-current="page">Produk ANTARA</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<span class="badge bg-danger py-2 px-3"><i class="ti ti-packages me-1"></i>12 Solusi Digital Aktif</span>
					<a href="mitra.html" class="btn btn-outline-secondary btn-sm"><i class="ti ti-world me-1"></i>Lihat Jaringan Mitra</a>
				</div>
			</div>

			<!-- Filter Bar -->
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-body p-3">
					<div class="row g-2 align-items-center">
						<div class="col-md-5">
							<div class="input-group">
								<span class="input-group-text bg-transparent border-end-0"><i class="ti ti-search"></i></span>
								<input type="text" id="searchProduct" class="form-control border-start-0" placeholder="Cari nama produk, feed, atau kata kunci...">
							</div>
						</div>
						<div class="col-md-7">
							<div class="d-flex flex-wrap gap-1 justify-content-md-end" id="categoryFilterButtons">
								<button class="btn btn-sm btn-danger filter-btn active" data-filter="all">Semua Produk</button>
								<button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="berita">Berita &amp; Teks</button>
								<button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="foto-video">Foto &amp; Video</button>
								<button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="kemitraan">Kemitraan &amp; PR</button>
								<button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="layanan">Teknologi &amp; Data</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Product Grid -->
			<div class="row g-4" id="productGridContainer">
				{cards_html}
			</div>

		</div>
	</div>

	<!-- Product Detail Modal -->
	<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content border-0 shadow">
				<div class="modal-header bg-light">
					<h5 class="modal-title fw-bold" id="modalProdTitle">Detail Produk</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<div class="mb-3">
						<span class="badge badge-antara" id="modalProdCat">Kategori</span>
					</div>
					<h4 class="fw-bold mb-2 text-dark" id="modalProdNameHeading">Nama Produk</h4>
					<p class="text-muted fs-14 mb-3" id="modalProdSummary">Ringkasan</p>
					
					<hr>
					
					<h6 class="fw-bold mb-2">Penjelasan Teknis &amp; Lingkup Layanan:</h6>
					<p class="fs-13 text-secondary mb-3" id="modalProdDetails">Deskripsi</p>

					<div class="alert alert-info border-0 d-flex align-items-center mb-0">
						<i class="ti ti-info-circle fs-20 me-2"></i>
						<span class="fs-12">Layanan ini dilengkapi dengan Service Level Agreement (SLA) uptime 99.9% dan dukungan integrasi RESTful API / RSS feed untuk newsroom mitra.</span>
					</div>
				</div>
				<div class="modal-footer bg-light">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
					<a href="invoices.html" class="btn btn-primary"><i class="ti ti-receipt me-1"></i>Buat Penawaran / Invoice</a>
				</div>
			</div>
		</div>
	</div>

{get_footer()}

<script>
function showProductModal(name, category, summary, details) {{
	$('#modalProdTitle').text(name);
	$('#modalProdNameHeading').text(name);
	$('#modalProdCat').text(category);
	$('#modalProdSummary').text(summary);
	$('#modalProdDetails').text(details);
	var myModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
	myModal.show();
}}

$(document).ready(function() {{
	// Search filter
	$('#searchProduct').on('keyup', function() {{
		var val = $(this).val().toLowerCase();
		$('.product-card-col').each(function() {{
			var text = $(this).data('name');
			if (text.indexOf(val) > -1) {{
				$(this).show();
			}} else {{
				$(this).hide();
			}}
		}});
	}});

	// Category filter
	$('.filter-btn').on('click', function() {{
		$('.filter-btn').removeClass('btn-danger active').addClass('btn-outline-secondary');
		$(this).removeClass('btn-outline-secondary').addClass('btn-danger active');
		var cat = $(this).data('filter');
		if (cat === 'all') {{
			$('.product-card-col').show();
		}} else {{
			$('.product-card-col').each(function() {{
				if ($(this).data('cat') === cat) {{
					$(this).show();
				}} else {{
					$(this).hide();
				}}
			}});
		}}
	}});
}});
</script>
'''
    with open(os.path.join(ADMIN_DIR, "produk-antara.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/produk-antara.html")

# ----------------------------------------------------
# 4. BUILD MITRA & KORPORASI (admin/mitra.html)
# ----------------------------------------------------
def build_mitra():
    print("Generating admin/mitra.html...")
    partners = [
        {"id": 1, "nama": "Reuters News Agency", "industri": "Media & Informasi Global", "kontak": "Emma Johnson", "email": "partner.sales@reuters.com", "telepon": "+44 20 7542 8313", "status": "Strategic Alliance", "alamat": "5 Canada Square, London", "logo": "assets/img/mitra/logo-reuters.png"},
        {"id": 2, "nama": "Bloomberg L.P.", "industri": "Media & Data Finansial", "kontak": "Samantha Lee", "email": "business@bloomberg.net", "telepon": "+1 212-318-2000", "status": "Strategic Alliance", "alamat": "731 Lexington Avenue, New York", "logo": "assets/img/mitra/logo-bloomberg.png"},
        {"id": 3, "nama": "Associated Press (AP)", "industri": "Kantor Berita Internasional", "kontak": "Michael Turner", "email": "partners@ap.org", "telepon": "+1 212-621-1500", "status": "Editorial Partner", "alamat": "200 Liberty Street, New York", "logo": "assets/img/mitra/logo-ap-news.png"},
        {"id": 4, "nama": "Agence France-Presse (AFP)", "industri": "Kantor Berita Internasional", "kontak": "Jean Dupont", "email": "partnership@afp.com", "telepon": "+33 1 40 41 46 46", "status": "Editorial Partner", "alamat": "11 Place de la Bourse, Paris", "logo": "assets/img/mitra/logo-afp.png"},
        {"id": 5, "nama": "Xinhua News Agency", "industri": "Kantor Berita Asia", "kontak": "Li Wei", "email": "service@mail.xinhuanet.com", "telepon": "+86 10 6307 3666", "status": "Editorial Partner", "alamat": "57 Xuanwumen Xidajie, Beijing", "logo": "assets/img/mitra/logo-xinhua-news-agency.png"},
        {"id": 6, "nama": "Kyodo News", "industri": "Kantor Berita Jepang", "kontak": "Kenji Nakamura", "email": "global@kyodonews.jp", "telepon": "+81 3-6252-8400", "status": "Editorial Partner", "alamat": "Shiodome Media Tower, Tokyo", "logo": "assets/img/mitra/logo-kyodo-news.png"},
        {"id": 7, "nama": "Bernama", "industri": "Kantor Berita Malaysia", "kontak": "Nur Aisyah", "email": "info@bernama.com", "telepon": "+60 3 2693 9933", "status": "Editorial Partner", "alamat": "Jalan Yap Kwan Seng, Kuala Lumpur", "logo": "assets/img/mitra/logo-bernama.png"},
        {"id": 8, "nama": "Agencia EFE", "industri": "Kantor Berita Spanyol", "kontak": "Carlos Gutierrez", "email": "comercial@efe.com", "telepon": "+34 91 347 82 00", "status": "Editorial Partner", "alamat": "Avenida de Burgos 8B, Madrid", "logo": "assets/img/mitra/logo-efe.png"},
        {"id": 9, "nama": "OANA (Asia-Pacific News)", "industri": "Aliansi Kantor Berita", "kontak": "Linh Wirawan", "email": "secretariat@oananews.org", "telepon": "+60 3 2693 9933", "status": "Network Member", "alamat": "Sekretariat OANA, Kuala Lumpur", "logo": "assets/img/mitra/logo-oana.png"},
        {"id": 10, "nama": "Sputnik News", "industri": "Kantor Berita Internasional", "kontak": "Sergey Petrov", "email": "world@sputniknews.com", "telepon": "+7 495 139 62 70", "status": "Editorial Partner", "alamat": "Zubovskaya St. 4, Moscow", "logo": "assets/img/mitra/logo-sputnik.png"},
        {"id": 11, "nama": "AsiaNet Australia", "industri": "Distribusi Berita Asia Pasifik", "kontak": "Priya Menon", "email": "info@asianetnews.net", "telepon": "+61 2 9322 8659", "status": "Media Distribution", "alamat": "Sydney, Australia", "logo": "assets/img/mitra/logo-asianet.png"},
        {"id": 12, "nama": "ACN Newswire", "industri": "Distribusi Rilis Pers", "kontak": "Naoko Sato", "email": "support@acnnewswire.com", "telepon": "+65 6788 8670", "status": "Media Distribution", "alamat": "1 Raffles Place, Singapore", "logo": "assets/img/mitra/logo-acnnewswire.png"},
        {"id": 13, "nama": "HCM Ads Media", "industri": "Jaringan Media Asia Tenggara", "kontak": "Tran Thi Hoa", "email": "partners@hcmedia.vn", "telepon": "+84 28 3821 9922", "status": "Commercial Partner", "alamat": "District 1, Ho Chi Minh City", "logo": "assets/img/mitra/logo-hcm-ads-media.png"},
        {"id": 14, "nama": "SevenCyber Security", "industri": "Keamanan Siber & Enkripsi", "kontak": "Bima Arista", "email": "contact@sevencyber.id", "telepon": "+62 21 7788 9900", "status": "Security Partner", "alamat": "BSD City, Tangerang", "logo": "assets/img/mitra/logo-sevencyber.png"}
    ]

    cards_html = ""
    table_rows = ""
    for pt in partners:
        # Badge color based on status
        st_badge = "bg-primary"
        if "Strategic" in pt["status"]:
            st_badge = "bg-danger"
        elif "Editorial" in pt["status"]:
            st_badge = "bg-info text-dark"
        elif "Security" in pt["status"]:
            st_badge = "bg-dark"
        elif "Distribution" in pt["status"]:
            st_badge = "bg-success"

        cards_html += f'''
		<div class="col-xl-4 col-md-6">
			<div class="card shadow-sm border-0 h-100">
				<div class="card-body p-4">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="p-2 bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 50px;">
							<img src="{pt['logo']}" alt="{pt['nama']}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='assets/img/logo.png'">
						</div>
						<span class="badge {st_badge}">{pt['status']}</span>
					</div>
					<h5 class="fw-bold mb-1">{pt['nama']}</h5>
					<p class="text-muted fs-12 mb-3">{pt['industri']}</p>
					
					<div class="fs-12 text-secondary mb-3">
						<div class="mb-1"><i class="ti ti-user me-2 text-danger"></i><strong>PIC:</strong> {pt['kontak']}</div>
						<div class="mb-1"><i class="ti ti-mail me-2 text-danger"></i>{pt['email']}</div>
						<div class="mb-1"><i class="ti ti-phone me-2 text-danger"></i>{pt['telepon']}</div>
						<div><i class="ti ti-map-pin me-2 text-danger"></i>{pt['alamat']}</div>
					</div>

					<div class="d-flex align-items-center justify-content-between pt-2 border-top">
						<a href="email.html" class="btn btn-outline-danger btn-sm"><i class="ti ti-mail me-1"></i>Kirim Surat</a>
						<a href="invoices.html" class="btn btn-light btn-sm"><i class="ti ti-file-invoice me-1"></i>Tagihan</a>
					</div>
				</div>
			</div>
		</div>
        '''

        table_rows += f'''
		<tr>
			<td>
				<div class="d-flex align-items-center">
					<div class="p-1 bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 30px;">
						<img src="{pt['logo']}" alt="{pt['nama']}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='assets/img/logo.png'">
					</div>
					<div>
						<h6 class="mb-0 fw-bold">{pt['nama']}</h6>
						<span class="text-muted fs-11">{pt['alamat']}</span>
					</div>
				</div>
			</td>
			<td>{pt['industri']}</td>
			<td>{pt['kontak']}</td>
			<td><span class="badge {st_badge}">{pt['status']}</span></td>
			<td>{pt['email']}</td>
			<td>
				<a href="email.html" class="btn btn-sm btn-light"><i class="ti ti-mail"></i></a>
				<a href="invoices.html" class="btn btn-sm btn-light"><i class="ti ti-receipt"></i></a>
			</td>
		</tr>
        '''

    content = f'''
{get_head('ANTARA CRM - Mitra & Korporasi', 'mitra')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('mitra')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Jaringan Mitra Media &amp; Korporasi</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Relasi &amp; Kemitraan</li>
							<li class="breadcrumb-item active" aria-current="page">Mitra &amp; Korporasi</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahMitraModal"><i class="ti ti-plus me-1"></i>Tambah Mitra Baru</button>
				</div>
			</div>

			<!-- Stats Row -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-muted fs-12">Total Rekanan Mitra</span>
								<span class="avatar avatar-sm bg-danger bg-opacity-10 text-danger rounded"><i class="ti ti-world"></i></span>
							</div>
							<h3 class="fw-bold mb-0">14</h3>
							<span class="text-success fs-11">● 100% Terverifikasi Aktif</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-muted fs-12">Aliansi Strategis Global</span>
								<span class="avatar avatar-sm bg-primary bg-opacity-10 text-primary rounded"><i class="ti ti-flag"></i></span>
							</div>
							<h3 class="fw-bold mb-0">2</h3>
							<span class="text-primary fs-11">● Reuters &amp; Bloomberg</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-muted fs-12">Editorial Partner</span>
								<span class="avatar avatar-sm bg-info bg-opacity-10 text-info rounded"><i class="ti ti-news"></i></span>
							</div>
							<h3 class="fw-bold mb-0">8</h3>
							<span class="text-info fs-11">● Pertukaran Berita Internasional</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="text-muted fs-12">Jalur Distribusi Media</span>
								<span class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded"><i class="ti ti-broadcast"></i></span>
							</div>
							<h3 class="fw-bold mb-0">4</h3>
							<span class="text-success fs-11">● AsiaNet, ACN, HCM Ads, Finsoft</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Tabs Tampilan -->
			<ul class="nav nav-pills mb-3" id="mitraTab" role="tablist">
				<li class="nav-item">
					<button class="nav-link active" id="grid-tab" data-bs-toggle="pill" data-bs-target="#grid-view"><i class="ti ti-layout-grid me-1"></i>Tampilan Kartu (Grid)</button>
				</li>
				<li class="nav-item">
					<button class="nav-link" id="table-tab" data-bs-toggle="pill" data-bs-target="#table-view"><i class="ti ti-table me-1"></i>Tampilan Tabel Data</button>
				</li>
			</ul>

			<!-- Tab Content -->
			<div class="tab-content" id="mitraTabContent">
				<div class="tab-pane fade show active" id="grid-view">
					<div class="row g-4">
						{cards_html}
					</div>
				</div>

				<div class="tab-pane fade" id="table-view">
					<div class="card shadow-sm border-0">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover align-middle datatable">
									<thead class="table-light">
										<tr>
											<th>Mitra Korporasi</th>
											<th>Bidang Industri</th>
											<th>PIC Kontak</th>
											<th>Status Kemitraan</th>
											<th>Email Korespondensi</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>
										{table_rows}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- Modal Tambah Mitra -->
	<div class="modal fade" id="tambahMitraModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow">
				<div class="modal-header bg-light">
					<h5 class="modal-title fw-bold">Tambah Rekanan Mitra Baru</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<form id="formTambahMitra" onsubmit="event.preventDefault(); alert('Simulasi: Mitra berhasil didaftarkan ke sistem CRM ANTARA.'); $('#tambahMitraModal').modal('hide');">
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Nama Korporasi / Instansi</label>
							<input type="text" class="form-control" required placeholder="Contoh: PT Kompas Media Nusantara">
						</div>
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Kategori Industri</label>
							<input type="text" class="form-control" required placeholder="Contoh: Portal Berita &amp; Televisi">
						</div>
						<div class="row g-2 mb-3">
							<div class="col-md-6">
								<label class="form-label fs-13 fw-semibold">Nama PIC Kontak</label>
								<input type="text" class="form-control" required placeholder="Nama lengkap">
							</div>
							<div class="col-md-6">
								<label class="form-label fs-13 fw-semibold">Status Kemitraan</label>
								<select class="form-select">
									<option>Editorial Partner</option>
									<option>Strategic Alliance</option>
									<option>Media Distribution</option>
									<option>Technology Partner</option>
								</select>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Email Korespondensi</label>
							<input type="email" class="form-control" required placeholder="partner@domain.com">
						</div>
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Nomor Telepon</label>
							<input type="text" class="form-control" required placeholder="+62 21...">
						</div>
						<div class="d-flex justify-content-end gap-2">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
							<button type="submit" class="btn btn-primary">Simpan Mitra</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{get_footer()}
'''
    with open(os.path.join(ADMIN_DIR, "mitra.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/mitra.html")

# ----------------------------------------------------
# 5. BUILD INVOICES (admin/invoices.html)
# ----------------------------------------------------
def build_invoices():
    print("Generating admin/invoices.html...")
    invoices = [
        {"no": "INV-2024-0015", "title": "Bundel Distribusi Press Release 2025", "client": "Lori Broaddus", "company": "PT Digital Pratama", "amount": 32000000, "paid": 0, "status": "draft", "issue": "20 Nov 2025", "due": "21 Nov 2025"},
        {"no": "INV-2025-1212", "title": "Distribusi Konten ASEAN Summit", "client": "PT Media Nusantara", "company": "PT Media Nusantara Prima", "amount": 28500000, "paid": 28500000, "status": "paid", "issue": "27 Nov 2025", "due": "29 Nov 2025"},
        {"no": "INV-2025-11-2", "title": "Paket Siaran Video Wire LKBN", "client": "Zaki Abdussalam", "company": "PT Cuan Media Kreatif", "amount": 30000000, "paid": 20000000, "status": "overdue", "issue": "27 Nov 2025", "due": "28 Nov 2025"},
        {"no": "INV-20251203-C775", "title": "Langganan Lisensi ANTARA Foto", "client": "Klien Alpha Media", "company": "PT Alpha Televisi Indonesia", "amount": 20000000, "paid": 0, "status": "pending", "issue": "03 Des 2025", "due": "15 Des 2025"},
        {"no": "INV-2026-0041", "title": "Koneksi Terminal Bloomberg Feed Q1", "client": "Samantha Lee", "company": "Bloomberg L.P. Partner", "amount": 45000000, "paid": 45000000, "status": "paid", "issue": "05 Jan 2026", "due": "20 Jan 2026"},
        {"no": "INV-2026-0089", "title": "Sindikasi Berita Nasional Antaranews", "client": "Ardi Prabowo", "company": "Finsoft Solusi Digital", "amount": 16500000, "paid": 0, "status": "pending", "issue": "12 Jan 2026", "due": "25 Jan 2026"},
    ]

    table_rows = ""
    for inv in invoices:
        due_amount = inv["amount"] - inv["paid"]
        
        st_badge = "bg-warning text-dark"
        st_text = "Pending"
        if inv["status"] == "paid":
            st_badge = "bg-success"
            st_text = "Lunas"
        elif inv["status"] == "overdue":
            st_badge = "bg-danger"
            st_text = "Jatuh Tempo"
        elif inv["status"] == "draft":
            st_badge = "bg-secondary"
            st_text = "Draft"

        table_rows += f'''
		<tr>
			<td><strong class="text-primary">{inv['no']}</strong></td>
			<td>
				<h6 class="mb-0 fs-13 fw-bold">{inv['title']}</h6>
				<span class="text-muted fs-11">{inv['company']} ({inv['client']})</span>
			</td>
			<td>{inv['issue']}</td>
			<td><span class="text-danger fw-semibold">{inv['due']}</span></td>
			<td><strong>Rp {inv['amount']:,}</strong></td>
			<td><span class="text-success">Rp {inv['paid']:,}</span></td>
			<td><span class="text-danger fw-bold">Rp {due_amount:,}</span></td>
			<td><span class="badge {st_badge}">{st_text}</span></td>
			<td>
				<button class="btn btn-sm btn-light" onclick="alert('Detail Invoice {inv['no']}\\nPelanggan: {inv['company']}\\nTotal: Rp {inv['amount']:,}\\nStatus: {st_text}')"><i class="ti ti-eye"></i></button>
				<button class="btn btn-sm btn-light" onclick="window.print()"><i class="ti ti-printer"></i></button>
			</td>
		</tr>
        '''

    content = f'''
{get_head('ANTARA CRM - Manajemen Tagihan & Invoices', 'invoices')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('invoices')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Tagihan Produk &amp; Invoicing</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Billing &amp; Transaksi</li>
							<li class="breadcrumb-item active" aria-current="page">Tagihan Produk</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<button class="btn btn-primary btn-sm" onclick="alert('Simulasi: Form pembuatan tagihan produk baru.')"><i class="ti ti-plus me-1"></i>Buat Invoice Baru</button>
				</div>
			</div>

			<!-- Stats Row -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Total Tagihan Periode Ini</span>
							<h3 class="fw-bold mb-1">Rp 172.350.000</h3>
							<span class="badge bg-primary bg-opacity-10 text-primary">6 Dokumen Invoice</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Belum Terbayar (Outstanding)</span>
							<h3 class="fw-bold mb-1 text-warning">Rp 98.500.000</h3>
							<span class="badge bg-warning bg-opacity-10 text-warning">3 Perlu Tindak Lanjut</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Sudah Lunas (Settled)</span>
							<h3 class="fw-bold mb-1 text-success">Rp 73.850.000</h3>
							<span class="badge bg-success bg-opacity-10 text-success">Terverifikasi Masuk</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Jatuh Tempo (Overdue)</span>
							<h3 class="fw-bold mb-1 text-danger">Rp 10.000.000</h3>
							<span class="badge bg-danger bg-opacity-10 text-danger">1 Melewati Batas Waktu</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Table Card -->
			<div class="card shadow-sm border-0">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Daftar Tagihan Produk ke Mitra</h5>
					<a href="payments.html" class="btn btn-outline-danger btn-sm"><i class="ti ti-credit-card me-1"></i>Lihat Riwayat Pembayaran</a>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover align-middle datatable">
							<thead class="table-light">
								<tr>
									<th>No Invoice</th>
									<th>Rincian Produk / Layanan</th>
									<th>Tgl Terbit</th>
									<th>Jatuh Tempo</th>
									<th>Total Nilai</th>
									<th>Terbayar</th>
									<th>Sisa Tagihan</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								{table_rows}
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

{get_footer()}
'''
    with open(os.path.join(ADMIN_DIR, "invoices.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/invoices.html")

# ----------------------------------------------------
# 6. BUILD PAYMENTS (admin/payments.html)
# ----------------------------------------------------
def build_payments():
    print("Generating admin/payments.html...")
    payments = [
        {"ref": "PAY-20251127-01", "inv": "INV-2025-1212", "client": "PT Media Nusantara Prima", "method": "Bank BCA Virtual Account", "channel": "Transfer Bank", "amount": 28500000, "date": "28 Nov 2025", "status": "approved"},
        {"ref": "PAY-20251128-04", "inv": "INV-2025-11-2", "client": "PT Cuan Media Kreatif", "method": "Bank Mandiri Corporate", "channel": "Direct Transfer", "amount": 20000000, "date": "28 Nov 2025", "status": "approved"},
        {"ref": "PAY-20260106-09", "inv": "INV-2026-0041", "client": "Bloomberg L.P. Partner", "method": "SWIFT Wire Transfer", "channel": "International Wire", "amount": 45000000, "date": "06 Jan 2026", "status": "approved"},
        {"ref": "PAY-20260114-12", "inv": "INV-20251203-C775", "client": "PT Alpha Televisi Indonesia", "method": "Bank BNI Virtual Account", "channel": "VA Payment", "amount": 20000000, "date": "14 Jan 2026", "status": "pending"},
        {"ref": "PAY-20260118-15", "inv": "INV-2026-0089", "client": "Finsoft Solusi Digital", "method": "QRIS Corporate Dinamis", "channel": "QRIS B2B", "amount": 16500000, "date": "18 Jan 2026", "status": "pending"},
    ]

    table_rows = ""
    for pay in payments:
        st_badge = "bg-success" if pay["status"] == "approved" else "bg-warning text-dark"
        st_text = "Terverifikasi" if pay["status"] == "approved" else "Menunggu Verifikasi"

        table_rows += f'''
		<tr>
			<td><strong class="text-primary">{pay['ref']}</strong></td>
			<td><a href="invoices.html" class="fw-semibold">{pay['inv']}</a></td>
			<td>
				<h6 class="mb-0 fs-13 fw-bold">{pay['client']}</h6>
				<span class="text-muted fs-11">{pay['channel']}</span>
			</td>
			<td><i class="ti ti-building-bank me-1 text-secondary"></i>{pay['method']}</td>
			<td><strong>Rp {pay['amount']:,}</strong></td>
			<td>{pay['date']}</td>
			<td><span class="badge {st_badge}">{st_text}</span></td>
			<td>
				<button class="btn btn-sm btn-light" onclick="alert('Bukti Pembayaran {pay['ref']}\\nJumlah: Rp {pay['amount']:,}\\nMetode: {pay['method']}\\nStatus: {st_text}')"><i class="ti ti-receipt"></i> Bukti</button>
			</td>
		</tr>
        '''

    content = f'''
{get_head('ANTARA CRM - Riwayat Transaksi & Pembayaran', 'payments')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('payments')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Riwayat Transaksi &amp; Pembayaran Klien</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Billing &amp; Transaksi</li>
							<li class="breadcrumb-item active" aria-current="page">Riwayat Pembayaran</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<a href="invoices.html" class="btn btn-outline-danger btn-sm"><i class="ti ti-file-invoice me-1"></i>Ke Menu Tagihan</a>
				</div>
			</div>

			<!-- Stats Row -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Total Pembayaran Masuk</span>
							<h3 class="fw-bold mb-1 text-success">Rp 130.000.000</h3>
							<span class="badge bg-success bg-opacity-10 text-success">5 Transaksi Terdata</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Menunggu Verifikasi Bank</span>
							<h3 class="fw-bold mb-1 text-warning">Rp 36.500.000</h3>
							<span class="badge bg-warning bg-opacity-10 text-warning">2 Perlu Review</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Metode Terpopuler</span>
							<h3 class="fw-bold mb-1">BCA &amp; Mandiri</h3>
							<span class="text-muted fs-11">● 80% Saluran Domestik</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Waktu Rekonsiliasi Rata-rata</span>
							<h3 class="fw-bold mb-1">12 Menit</h3>
							<span class="text-success fs-11">● Otomatisasi Host-to-Host</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Table Card -->
			<div class="card shadow-sm border-0">
				<div class="card-header bg-transparent border-bottom">
					<h5 class="card-title mb-0 fw-bold">Daftar Log Transaksi Pembayaran</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-hover align-middle datatable">
							<thead class="table-light">
								<tr>
									<th>Kode Transaksi</th>
									<th>Ref Invoice</th>
									<th>Mitra Pembayar</th>
									<th>Kanal / Metode</th>
									<th>Nominal</th>
									<th>Tanggal Bayar</th>
									<th>Status Verifikasi</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								{table_rows}
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

{get_footer()}
'''
    with open(os.path.join(ADMIN_DIR, "payments.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/payments.html")

# ----------------------------------------------------
# 7. BUILD EMAIL (admin/email.html)
# ----------------------------------------------------
def build_email():
    print("Generating admin/email.html...")
    emails = [
        {"id": 1, "from": "Raisa Pramesti", "email": "raisa.pramesti@antara.id", "subject": "Pembaruan Jadwal Rapat Redaksi Harian", "date": "04 Nov 2025", "badge": "Internal", "body": "Halo tim Redaksi,<br><br>Rapat editorial harian kita dipindah ke pukul <strong>10.00 WIB</strong> besok untuk menyesuaikan dengan konfirmasi narasumber utama dari Kementerian Luar Negeri.<br><br>Agenda:<br>1. Briefing liputan utama KTT ASEAN<br>2. Pemetaan distribusi konten ke mitra internasional<br>3. Status kolaborasi feed foto<br><br>Mohon konfirmasi kehadiran.<br><br>Salam,<br>Raisa"},
        {"id": 2, "from": "Michael Turner (AP)", "email": "partners@ap.org", "subject": "Brief Kampanye Konten Mitra & Sindikasi Berita", "date": "04 Nov 2025", "badge": "Mitra Global", "body": "Selamat siang rekan-rekan Redaksi Antara,<br><br>Kami lampirkan brief kebutuhan pertukaran konten foto dan video untuk liputan ekonomi Asia Tenggara minggu depan.<br><br>Highlight:<br>- 4 topik transformasi digital perbankan daerah.<br>- 2 video highlight durasi 45 detik 1080p.<br><br>Terima kasih,<br>Michael Turner - AP Partner Desk"},
        {"id": 3, "from": "Protokol Sekretariat Presiden", "email": "protokol@setpres.go.id", "subject": "Reminder Akreditasi Liputan Istana Negara", "date": "03 Nov 2025", "badge": "Pemerintah", "body": "Yth. Pimpinan Redaksi LKBN ANTARA,<br><br>Ini adalah pengingat untuk melengkapi form akreditasi liputan di lingkungan Istana Kepresidenan menjelang agenda kenegaraan akhir tahun.<br><br>Harap mengirimkan nama wartawan dan juru kamera sebelum tanggal 15 November 2025.<br><br>Hormat kami,<br>Biro Pers dan Media Sekretariat Presiden"},
        {"id": 4, "from": "Divisi PR ANTARA", "email": "pr@antara.id", "subject": "Distribusi Rilis Pers Kolaborasi UNESCO", "date": "01 Nov 2025", "badge": "Rilis Pers", "body": "Halo rekan media,<br><br>Rilis pers resmi mengenai kerja sama perlindungan warisan arsip sejarah visual bersama UNESCO telah siap didistribusikan ke jaringan media nasional.<br><br>Materi teks dan foto resolusi tinggi telah diunggah ke portal media center.<br><br>Salam hangat,<br>Humas LKBN ANTARA"},
        {"id": 5, "from": "Klien Alpha Media", "email": "alpha.client@antara.local", "subject": "Konfirmasi Pembayaran Lisensi Paket ANTARA Foto", "date": "14 Jan 2026", "badge": "Billing", "body": "Yth. Tim Billing ANTARA,<br><br>Kami telah mengunggah bukti pembayaran untuk invoice #INV-20251203-C775 senilai Rp 20.000.000 melalui Bank BNI Virtual Account.<br><br>Mohon dilakukan verifikasi akun agar akses feed foto resolusi tinggi dapat diperpanjang.<br><br>Terima kasih,<br>Finance Alpha Media"}
    ]

    list_items = ""
    for idx, em in enumerate(emails):
        active_cls = "bg-light border-danger border-start border-3" if idx == 0 else ""
        list_items += f'''
		<a href="javascript:void(0)" class="list-group-item list-group-item-action email-item-link p-3 {active_cls}" onclick="loadEmail({em['id']})">
			<div class="d-flex justify-content-between align-items-center mb-1">
				<h6 class="mb-0 fw-bold fs-13 text-dark">{em['from']}</h6>
				<span class="text-muted fs-11">{em['date']}</span>
			</div>
			<div class="fw-semibold text-truncate fs-13 text-secondary mb-1">{em['subject']}</div>
			<div class="d-flex align-items-center justify-content-between">
				<span class="badge bg-secondary bg-opacity-10 text-secondary fs-10">{em['badge']}</span>
				<i class="ti ti-star text-warning fs-13"></i>
			</div>
		</a>
        '''

    content = f'''
{get_head('ANTARA CRM - Korespondensi Surat & Email Redaksi', 'email')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('email')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Korespondensi &amp; Pesan Masuk Redaksi</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Aplikasi CRM</li>
							<li class="breadcrumb-item active" aria-current="page">Korespondensi</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<button class="btn btn-primary btn-sm" onclick="alert('Simulasi: Membuka jendela tulis surat / email baru.')"><i class="ti ti-pencil me-1"></i>Tulis Surat Baru</button>
				</div>
			</div>

			<!-- Email App Layout -->
			<div class="row g-3">
				<!-- Folder Nav -->
				<div class="col-xl-3 col-lg-4">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body p-3">
							<div class="d-grid mb-3">
								<button class="btn btn-danger fw-bold"><i class="ti ti-plus me-1"></i>Kirim Pesan</button>
							</div>
							<div class="list-group list-group-flush fs-13">
								<a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active bg-danger border-0 rounded text-white mb-1">
									<span><i class="ti ti-inbox me-2"></i>Kotak Masuk (Inbox)</span>
									<span class="badge bg-white text-danger">5</span>
								</a>
								<a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded mb-1">
									<span><i class="ti ti-star me-2"></i>Berbintang</span>
									<span class="badge bg-light text-secondary">2</span>
								</a>
								<a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded mb-1">
									<span><i class="ti ti-send me-2"></i>Terkirim (Sent)</span>
									<span class="badge bg-light text-secondary">12</span>
								</a>
								<a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded mb-1">
									<span><i class="ti ti-file me-2"></i>Draf (Drafts)</span>
									<span class="badge bg-light text-secondary">1</span>
								</a>
								<a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded mb-1">
									<span><i class="ti ti-trash me-2"></i>Sampah (Trash)</span>
									<span class="badge bg-light text-secondary">0</span>
								</a>
							</div>
						</div>
					</div>
				</div>

				<!-- Email List -->
				<div class="col-xl-4 col-lg-8">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom p-3">
							<input type="text" class="form-control form-control-sm" placeholder="Cari subjek atau pengirim...">
						</div>
						<div class="card-body p-0">
							<div class="list-group list-group-flush" id="emailList">
								{list_items}
							</div>
						</div>
					</div>
				</div>

				<!-- Reading Pane -->
				<div class="col-xl-5">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center p-3">
							<h6 class="mb-0 fw-bold" id="readSubject">{emails[0]['subject']}</h6>
							<div class="btn-group btn-group-sm">
								<button class="btn btn-light" onclick="alert('Pesan diarsipkan.')"><i class="ti ti-archive"></i></button>
								<button class="btn btn-light" onclick="alert('Pesan dihapus.')"><i class="ti ti-trash"></i></button>
							</div>
						</div>
						<div class="card-body p-4">
							<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
								<div class="d-flex align-items-center">
									<div class="avatar avatar-md bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold">
										<span id="readAvatar">R</span>
									</div>
									<div>
										<h6 class="mb-0 fw-bold" id="readFrom">{emails[0]['from']}</h6>
										<span class="text-muted fs-12" id="readEmail">&lt;{emails[0]['email']}&gt;</span>
									</div>
								</div>
								<span class="text-muted fs-12" id="readDate">{emails[0]['date']}</span>
							</div>

							<div class="fs-13 text-secondary lh-lg mb-4" id="readBody">
								{emails[0]['body']}
							</div>

							<div class="border-top pt-3 mt-4">
								<button class="btn btn-outline-danger btn-sm" onclick="alert('Membuka kotak balas pesan.')"><i class="ti ti-corner-up-left me-1"></i>Balas Pesan</button>
								<button class="btn btn-light btn-sm ms-2" onclick="alert('Membuka opsi teruskan pesan.')"><i class="ti ti-corner-up-right me-1"></i>Teruskan</button>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

{get_footer()}

<script>
var emailData = {json.dumps(emails)};

function loadEmail(id) {{
	var item = emailData.find(e => e.id === id);
	if (!item) return;

	$('#readSubject').text(item.subject);
	$('#readFrom').text(item.from);
	$('#readEmail').text('<' + item.email + '>');
	$('#readDate').text(item.date);
	$('#readBody').html(item.body);
	$('#readAvatar').text(item.from.charAt(0).toUpperCase());

	$('.email-item-link').removeClass('bg-light border-danger border-start border-3');
	$(event.currentTarget).addClass('bg-light border-danger border-start border-3');
}}
</script>
'''
    with open(os.path.join(ADMIN_DIR, "email.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/email.html")

# ----------------------------------------------------
# 8. BUILD TODO / TICKETS (admin/todo.html)
# ----------------------------------------------------
def build_todo():
    print("Generating admin/todo.html...")
    todos = [
        {"id": 1, "title": "Perbaikan feed foto otomatis biro Surabaya ke sistem pusat", "category": "Operasional", "priority": "high", "due": "Hari Ini", "completed": False},
        {"id": 2, "title": "Audit token rotasi HMAC API Gateway distribusi konten mitra", "category": "Keamanan", "priority": "high", "due": "Besok", "completed": False},
        {"id": 3, "title": "Verifikasi pembayaran paket siaran video PT Cuan Media", "category": "Keuangan", "priority": "medium", "due": "18 Jan 2026", "completed": False},
        {"id": 4, "title": "Konfigurasi firewall port streaming OTT Media Server Jayapura", "category": "Infrastruktur", "priority": "medium", "due": "20 Jan 2026", "completed": False},
        {"id": 5, "title": "Update lisensi arsip digital foto KTT G20 ke format WebP", "category": "Produk", "priority": "low", "due": "25 Jan 2026", "completed": True},
        {"id": 6, "title": "Penyelarasan backup database cluster newsroom tengah malam", "category": "Operasional", "priority": "low", "due": "30 Jan 2026", "completed": True},
    ]

    todo_rows = ""
    for t in todos:
        chk = "checked" if t["completed"] else ""
        strike = "text-decoration-line-through text-muted" if t["completed"] else "fw-semibold"
        
        pr_badge = "bg-danger"
        pr_label = "Tinggi"
        if t["priority"] == "medium":
            pr_badge = "bg-warning text-dark"
            pr_label = "Menengah"
        elif t["priority"] == "low":
            pr_badge = "bg-success"
            pr_label = "Rendah"

        todo_rows += f'''
		<li class="list-group-item d-flex align-items-center justify-content-between p-3 todo-row" id="todo-item-{t['id']}">
			<div class="d-flex align-items-center">
				<input class="form-check-input me-3 todo-checkbox" type="checkbox" {chk} onchange="toggleTodo({t['id']}, this)">
				<div>
					<span class="fs-14 todo-text {strike}">{t['title']}</span>
					<div class="mt-1 fs-11 text-muted">
						<span class="badge bg-light text-secondary me-2"><i class="ti ti-tag me-1"></i>{t['category']}</span>
						<i class="ti ti-clock me-1"></i>Batas Waktu: {t['due']}
					</div>
				</div>
			</div>
			<div class="d-flex align-items-center gap-2">
				<span class="badge {pr_badge}">{pr_label}</span>
				<button class="btn btn-sm btn-light text-danger" onclick="$('#todo-item-{t['id']}').remove(); updateCounts();"><i class="ti ti-trash"></i></button>
			</div>
		</li>
        '''

    content = f'''
{get_head('ANTARA CRM - Tiket Support & To-Do Backlog', 'todo')}
{get_header('Redaksi Antara', 'Superadministrator / IT Lead', '02')}
{get_sidebar('todo')}

	<div class="page-wrapper">
		<div class="content container-fluid">

			<!-- Page Header -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">Tiket Support &amp; Backlog Tugas IT</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item"><a href="index.html"><i class="ti ti-smart-home"></i></a></li>
							<li class="breadcrumb-item">Aplikasi CRM</li>
							<li class="breadcrumb-item active" aria-current="page">Tiket Support &amp; To-Do</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
					<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahTugasModal"><i class="ti ti-plus me-1"></i>Buat Tugas Baru</button>
				</div>
			</div>

			<!-- Stats Row -->
			<div class="row g-3 mb-4">
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Total Item Backlog</span>
							<h3 class="fw-bold mb-1" id="totalCount">6</h3>
							<span class="badge bg-secondary bg-opacity-10 text-secondary">Semua Tugas</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Belum Selesai (Pending)</span>
							<h3 class="fw-bold mb-1 text-danger" id="pendingCount">4</h3>
							<span class="badge bg-danger bg-opacity-10 text-danger">Memerlukan Aksi Segera</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Selesai (Completed)</span>
							<h3 class="fw-bold mb-1 text-success" id="completedCount">2</h3>
							<span class="badge bg-success bg-opacity-10 text-success">Lulus Verifikasi QA</span>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-sm-6">
					<div class="card shadow-sm border-0 h-100">
						<div class="card-body">
							<span class="text-muted fs-12">Tingkat Penyelesaian</span>
							<h3 class="fw-bold mb-1 text-primary">33.3%</h3>
							<div class="progress" style="height: 6px;">
								<div class="progress-bar bg-primary" style="width: 33.3%"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Todo Card -->
			<div class="card shadow-sm border-0">
				<div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0 fw-bold">Daftar Checklist Operasional Redaksi &amp; IT</h5>
					<div class="btn-group btn-group-sm" id="todoFilterGroup">
						<button class="btn btn-outline-secondary active" onclick="filterTodo('all', this)">Semua</button>
						<button class="btn btn-outline-secondary" onclick="filterTodo('pending', this)">Pending</button>
						<button class="btn btn-outline-secondary" onclick="filterTodo('done', this)">Selesai</button>
					</div>
				</div>
				<div class="card-body p-0">
					<ul class="list-group list-group-flush" id="todoList">
						{todo_rows}
					</ul>
				</div>
			</div>

		</div>
	</div>

	<!-- Modal Tambah Tugas -->
	<div class="modal fade" id="tambahTugasModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow">
				<div class="modal-header bg-light">
					<h5 class="modal-title fw-bold">Tambah Tiket / Tugas Baru</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<form id="formTambahTugas" onsubmit="event.preventDefault(); addNewTodo();">
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Judul Tugas / Tiket</label>
							<input type="text" id="newTaskTitle" class="form-control" required placeholder="Contoh: Perbaikan modul RSS Feed">
						</div>
						<div class="row g-2 mb-3">
							<div class="col-md-6">
								<label class="form-label fs-13 fw-semibold">Kategori</label>
								<select id="newTaskCategory" class="form-select">
									<option>Operasional</option>
									<option>Keamanan</option>
									<option>Produk</option>
									<option>Keuangan</option>
									<option>Infrastruktur</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="form-label fs-13 fw-semibold">Prioritas</label>
								<select id="newTaskPriority" class="form-select">
									<option value="high">Tinggi (Kritis)</option>
									<option value="medium" selected>Menengah</option>
									<option value="low">Rendah</option>
								</select>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label fs-13 fw-semibold">Batas Waktu</label>
							<input type="text" id="newTaskDue" class="form-control" placeholder="Contoh: 3 Hari Lagi">
						</div>
						<div class="d-flex justify-content-end gap-2">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
							<button type="submit" class="btn btn-primary">Simpan Tugas</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

{get_footer()}

<script>
function toggleTodo(id, checkbox) {{
	var row = $(checkbox).closest('.todo-row');
	var text = row.find('.todo-text');
	if (checkbox.checked) {{
		text.addClass('text-decoration-line-through text-muted').removeClass('fw-semibold');
	}} else {{
		text.removeClass('text-decoration-line-through text-muted').addClass('fw-semibold');
	}}
	updateCounts();
}}

function updateCounts() {{
	var total = $('.todo-row').length;
	var completed = $('.todo-checkbox:checked').length;
	var pending = total - completed;

	$('#totalCount').text(total);
	$('#completedCount').text(completed);
	$('#pendingCount').text(pending);
}}

function filterTodo(type, btn) {{
	$('#todoFilterGroup button').removeClass('active btn-danger').addClass('btn-outline-secondary');
	$(btn).addClass('active');

	if (type === 'all') {{
		$('.todo-row').show();
	}} else if (type === 'pending') {{
		$('.todo-row').each(function() {{
			var chk = $(this).find('.todo-checkbox').is(':checked');
			if (!chk) $(this).show(); else $(this).hide();
		}});
	}} else if (type === 'done') {{
		$('.todo-row').each(function() {{
			var chk = $(this).find('.todo-checkbox').is(':checked');
			if (chk) $(this).show(); else $(this).hide();
		}});
	}}
}}

var newId = 100;
function addNewTodo() {{
	var title = $('#newTaskTitle').val();
	var cat = $('#newTaskCategory').val();
	var pr = $('#newTaskPriority').val();
	var due = $('#newTaskDue').val() || 'Segera';

	var prBadge = 'bg-warning text-dark';
	var prLabel = 'Menengah';
	if (pr === 'high') {{ prBadge = 'bg-danger'; prLabel = 'Tinggi'; }}
	else if (pr === 'low') {{ prBadge = 'bg-success'; prLabel = 'Rendah'; }}

	newId++;
	var html = `
	<li class="list-group-item d-flex align-items-center justify-content-between p-3 todo-row" id="todo-item-${{newId}}">
		<div class="d-flex align-items-center">
			<input class="form-check-input me-3 todo-checkbox" type="checkbox" onchange="toggleTodo(${{newId}}, this)">
			<div>
				<span class="fs-14 todo-text fw-semibold">${{title}}</span>
				<div class="mt-1 fs-11 text-muted">
					<span class="badge bg-light text-secondary me-2"><i class="ti ti-tag me-1"></i>${{cat}}</span>
					<i class="ti ti-clock me-1"></i>Batas Waktu: ${{due}}
				</div>
			</div>
		</div>
		<div class="d-flex align-items-center gap-2">
			<span class="badge ${{prBadge}}">${{prLabel}}</span>
			<button class="btn btn-sm btn-light text-danger" onclick="$('#todo-item-${{newId}}').remove(); updateCounts();"><i class="ti ti-trash"></i></button>
		</div>
	</li>`;

	$('#todoList').prepend(html);
	updateCounts();
	$('#tambahTugasModal').modal('hide');
	$('#formTambahTugas')[0].reset();
}}
</script>
'''
    with open(os.path.join(ADMIN_DIR, "todo.html"), "w", encoding="utf-8") as f:
        f.write(content.strip())
    print("Done admin/todo.html")

def main():
    build_admin()
    build_customer()
    build_produk()
    build_mitra()
    build_invoices()
    build_payments()
    build_email()
    build_todo()
    print("All admin suite pages successfully generated!")

if __name__ == "__main__":
    main()
