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
$dashboardUrl = $baseUrl === '' ? '/dashboard/pelanggan' : '/' . $baseUrl . '/dashboard/pelanggan';

$dashboardPages = ['dashboard-pelanggan.php'];
$editorialPages = ['blogs.php', 'knowledgebase-details.php'];
$crmPages = ['clients.php', 'clients-grid.php', 'companies-crm.php', 'companies-grid.php', 'crm/mitra', 'crm/mitra/table'];
$productsPages = ['produk-antara.php'];
$settingsPages = ['config.php', 'language.php', 'theme-settings.php'];

$currentUri = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?');
$isDashboard = in_array($page, $dashboardPages, true) || rtrim($currentUri, '/') === rtrim($dashboardUrl, '/');
$isEditorial = in_array($page, $editorialPages, true);
$isProducts = in_array($page, $productsPages, true);
$isCrm = in_array($page, $crmPages, true);
$isSettings = in_array($page, $settingsPages, true);
?>

<!-- Two Col Sidebar -->
<div class="two-col-sidebar" id="two-col-sidebar">
	<div class="sidebar sidebar-twocol">
		<div class="twocol-mini">
			<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="logo-small">
				<img src="assets/img/logo-antara-light.png" alt="Logo Antara" style="height: 22px; width: auto;">
			</a>
			<div class="sidebar-left slimscroll">
				<div class="nav flex-column align-items-center nav-pills" id="sidebar-tabs" role="tablist"
					aria-orientation="vertical">
					<a href="#" class="nav-link <?php echo $isDashboard ? 'active' : ''; ?>" title="Dashboard"
						data-bs-toggle="tab" data-bs-target="#twocol-dashboard">
						<i class="ti ti-smart-home"></i>
					</a>
					<a href="#" class="nav-link <?php echo $isEditorial ? 'active' : ''; ?>" title="Editorial"
						data-bs-toggle="tab" data-bs-target="#twocol-editorial">
						<i class="ti ti-news"></i>
					</a>
					<a href="#" class="nav-link <?php echo $isProducts ? 'active' : ''; ?>" title="Produk"
						data-bs-toggle="tab" data-bs-target="#twocol-products">
						<i class="ti ti-packages"></i>
					</a>
					<a href="#" class="nav-link <?php echo $isCrm ? 'active' : ''; ?>" title="CRM" data-bs-toggle="tab"
						data-bs-target="#twocol-crm">
						<i class="ti ti-address-book"></i>
					</a>
					<a href="#" class="nav-link <?php echo $isSettings ? 'active' : ''; ?>" title="Settings"
						data-bs-toggle="tab" data-bs-target="#twocol-settings">
						<i class="ti ti-settings"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="twocol-main">
			<div class="twocol-menu">
				<div class="tab-content" id="sidebar-tab-contents">
					<div class="tab-pane fade <?php echo $isDashboard ? 'show active' : ''; ?>" id="twocol-dashboard">
						<ul>
							<li class="menu-title"><span>Dashboard</span></li>
							<li><a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $isDashboard ? 'active' : ''; ?>">Dashboard Pelanggan</a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isEditorial ? 'show active' : ''; ?>" id="twocol-editorial">
						<ul>
							<li class="menu-title"><span>Editorial</span></li>
							<li><a href="<?= htmlspecialchars($pageUrl('blogs.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'blogs.php' ? 'active' : ''; ?>">Berita & Liputan</a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'knowledgebase-details.php' ? 'active' : ''; ?>">Panduan
									CRM</a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isProducts ? 'show active' : ''; ?>" id="twocol-products">
						<ul>
							<li class="menu-title"><span>Produk</span></li>
							<li><a href="<?= htmlspecialchars($pageUrl('produk-antara.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'produk-antara.php' ? 'active' : ''; ?>">Produk
									ANTARA</a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isCrm ? 'show active' : ''; ?>" id="twocol-crm">
						<ul>
							<li class="menu-title"><span>CRM</span></li>
							<li><a href="<?= htmlspecialchars($pageUrl('contacts-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'contacts-grid.php' ? 'active' : ''; ?>">Narasumber</a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('contacts.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'contacts.php' ? 'active' : ''; ?>">Daftar Kontak</a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl($mitraGridUrl), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'companies-grid.php' ? 'active' : ''; ?>">Mitra &
									Korporasi</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('deals-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'deals-grid.php' ? 'active' : ''; ?>">Kerja Sama
									Iklan</a></li>
						</ul>
					</div>
					<div class="tab-pane fade <?php echo $isSettings ? 'show active' : ''; ?>" id="twocol-settings">
						<ul>
							<li class="menu-title"><span>Pengaturan</span></li>
							<li><a href="<?= htmlspecialchars($pageUrl('config.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'config.php' ? 'active' : ''; ?>">Konfigurasi
									Aplikasi</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('language.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'language.php' ? 'active' : ''; ?>">Bahasa</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('theme-settings.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'theme-settings.php' ? 'active' : ''; ?>">Tema</a></li>

						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Two Col Sidebar -->
