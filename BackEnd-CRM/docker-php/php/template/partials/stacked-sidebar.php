<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

$mitraGridUrl = 'companies-grid.php';
$mitraTableUrl = 'companies-crm.php';

$configBasePath = '';
if (class_exists('\App\Core\Config')) {
	$configBasePath = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
$baseUrl = $configBasePath;
require_once __DIR__ . '/url.php';
$pageUrl = function (string $path) use ($baseUrl): string {
	return template_menu_url($path, $baseUrl);
};
$emailUrl = $baseUrl === '' ? '/dashboard/email' : '/' . $baseUrl . '/dashboard/email';
$dashboardUrl = $baseUrl === '' ? '/dashboard/pelanggan' : '/' . $baseUrl . '/dashboard/pelanggan';

$dashboardPages = ['dashboard-pelanggan.php'];
$editorialPages = ['blogs.php', 'knowledgebase-details.php'];
$projectsPages = ['clients-grid.php', 'projects-grid.php', 'tasks.php', 'task-board.php'];
$productsPages = ['produk-antara.php'];
$crmPages = ['contacts-grid.php', 'contacts.php', 'contact-details.php', 'companies-grid.php', 'companies-crm.php', 'crm/mitra', 'crm/mitra/table', 'company-details.php', 'deals-grid.php', 'deals-details.php', 'leads-grid.php', 'leads-details.php', 'pipeline.php', 'analytics.php', 'activity.php'];
$settingsPages = ['config.php', 'language.php', 'theme-settings.php'];

$currentUri = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?');
$isDashboard = in_array($page, $dashboardPages, true) || rtrim($currentUri, '/') === rtrim($dashboardUrl, '/');
$isEditorial = in_array($page, $editorialPages, true);
$isProjects = in_array($page, $projectsPages, true);
$isProducts = in_array($page, $productsPages, true);
$isCrm = in_array($page, $crmPages, true);
$isSettings = in_array($page, $settingsPages, true);
?>

<!-- Stacked Sidebar -->
<div class="stacked-sidebar" id="stacked-sidebar">
	<div class="sidebar sidebar-stacked">
		<div class="stacked-mini">
			<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="logo-small">
				<img src="assets/img/logo-antara-light.png" alt="Logo Antara" style="height: 22px; width: auto;">
			</a>
			<div class="sidebar-left slimscroll">
				<div class="d-flex align-items-center flex-column">
					<div class="mb-1 notification-item">
						<a href="<?= htmlspecialchars($pageUrl('activity.php'), ENT_QUOTES, 'UTF-8'); ?>"
							class="btn btn-menubar position-relative">
							<i class="ti ti-bell"></i>
							<span class="notification-status-dot"></span>
						</a>
					</div>
					<div class="mb-1">
						<a href="javascript:void(0);" class="btn btn-menubar btnFullscreen">
							<i class="ti ti-maximize"></i>
						</a>
					</div>
					<div class="mb-1">
						<a href="<?= htmlspecialchars($pageUrl('chat.php'), ENT_QUOTES, 'UTF-8'); ?>"
							class="btn btn-menubar position-relative">
							<i class="ti ti-brand-hipchat"></i>
							<span
								class="badge bg-info rounded-pill d-flex align-items-center justify-content-center header-badge">5</span>
						</a>
					</div>
					<div class="mb-1">
						<a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-menubar">
							<i class="ti ti-mail"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="sidebar-right d-flex justify-content-between flex-column">
			<div class="sidebar-scroll">
				<h6 class="mb-3">Welcome to Antara Newsroom</h6>
				<div class="sidebar-profile text-center rounded bg-light p-3 mb-4">
					<div class="avatar avatar-lg online mb-3">
						<img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
					</div>
					<h6 class="fs-12 fw-normal mb-1">Redaksi Antara</h6>
					<p class="fs-10">Administrator</p>
				</div>
				<div class="stack-menu">
					<div class="nav flex-column align-items-center nav-pills" role="tablist"
						aria-orientation="vertical">
						<div class="row g-2">
							<div class="col-6">
								<a href="#menu-dashboard" role="tab"
									class="nav-link <?php echo $isDashboard ? 'active' : ''; ?>" title="Dashboard"
									data-bs-toggle="tab" data-bs-target="#menu-dashboard">
									<span><i class="ti ti-smart-home"></i></span>
									<p>Dashboard</p>
								</a>
							</div>
							<div class="col-6">
								<a href="#menu-editorial" role="tab"
									class="nav-link <?php echo $isEditorial ? 'active' : ''; ?>" title="Editorial"
									data-bs-toggle="tab" data-bs-target="#menu-editorial">
									<span><i class="ti ti-news"></i></span>
									<p>Editorial</p>
								</a>
							</div>
							<div class="col-6">
								<a href="#menu-projects" role="tab"
									class="nav-link <?php echo $isProjects ? 'active' : ''; ?>" title="Projects"
									data-bs-toggle="tab" data-bs-target="#menu-projects">
									<span><i class="ti ti-layout-board-split"></i></span>
									<p>Projects</p>
								</a>
							</div>
							<div class="col-6">
								<a href="#menu-products" role="tab"
									class="nav-link <?php echo $isProducts ? 'active' : ''; ?>" title="Produk"
									data-bs-toggle="tab" data-bs-target="#menu-products">
									<span><i class="ti ti-packages"></i></span>
									<p>Produk</p>
								</a>
							</div>
							<div class="col-6">
								<a href="#menu-crm" role="tab" class="nav-link <?php echo $isCrm ? 'active' : ''; ?>"
									title="CRM" data-bs-toggle="tab" data-bs-target="#menu-crm">
									<span><i class="ti ti-user-shield"></i></span>
									<p>CRM</p>
								</a>
							</div>
							<div class="col-6">
								<a href="#menu-settings" role="tab"
									class="nav-link <?php echo $isSettings ? 'active' : ''; ?>" title="Settings"
									data-bs-toggle="tab" data-bs-target="#menu-settings">
									<span><i class="ti ti-settings"></i></span>
									<p>Settings</p>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-content">
					<div class="tab-pane fade <?php echo $isDashboard ? 'show active' : ''; ?>" id="menu-dashboard">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $isDashboard ? 'active' : ''; ?>"><span>Dashboard
										Pelanggan</span></a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isEditorial ? 'show active' : ''; ?>" id="menu-editorial">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($pageUrl('blogs.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'blogs.php' ? 'active' : ''; ?>"><span>Berita &
										Liputan</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'knowledgebase-details.php' ? 'active' : ''; ?>"><span>Panduan
										CRM</span></a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isProjects ? 'show active' : ''; ?>" id="menu-projects">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($pageUrl('clients-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'clients-grid.php' ? 'active' : ''; ?>"><span>Clients</span></a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('projects-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'projects-grid.php' ? 'active' : ''; ?>"><span>Projects</span></a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('tasks.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'tasks.php' ? 'active' : ''; ?>"><span>Tasks</span></a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('task-board.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'task-board.php' ? 'active' : ''; ?>"><span>Task
										Board</span></a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isProducts ? 'show active' : ''; ?>" id="menu-products">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($pageUrl('produk-antara.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'produk-antara.php' ? 'active' : ''; ?>"><span>Produk
										ANTARA</span></a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isCrm ? 'show active' : ''; ?>" id="menu-crm">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($pageUrl('contacts-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'contacts-grid.php' ? 'active' : ''; ?>"><span>Narasumber</span></a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl($mitraGridUrl), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'companies-grid.php' ? 'active' : ''; ?>"><span>Mitra &
										Korporasi</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('deals-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'deals-grid.php' ? 'active' : ''; ?>"><span>Kerja Sama
										Iklan</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('leads-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'leads-grid.php' ? 'active' : ''; ?>"><span>Prospek
										Kemitraan</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('pipeline.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'pipeline.php' ? 'active' : ''; ?>"><span>Pipeline Kerja
										Sama</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('analytics.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'analytics.php' ? 'active' : ''; ?>"><span>Analitik
										Relasi</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('activity.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'activity.php' ? 'active' : ''; ?>"><span>Aktivitas
										Relasi</span></a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isSettings ? 'show active' : ''; ?>" id="menu-settings">
						<ul class="stack-submenu">
							<li><a href="<?= htmlspecialchars($pageUrl('config.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'config.php' ? 'active' : ''; ?>"><span>Konfigurasi
										Aplikasi</span></a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('language.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'language.php' ? 'active' : ''; ?>"><span>Bahasa</span></a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('theme-settings.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'theme-settings.php' ? 'active' : ''; ?>"><span>Pengaturan
										Tema</span></a></li>

						</ul>
					</div>
				</div>
			</div>
			<div class="sidebar-footer text-center p-3">
				<p class="fs-11 text-muted mb-0">© <?= date('Y'); ?> Antara Newsroom</p>
			</div>
		</div>
	</div>
</div>
<!-- /Stacked Sidebar -->
