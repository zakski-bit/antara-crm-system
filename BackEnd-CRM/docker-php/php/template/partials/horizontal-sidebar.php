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
$emailUrl = $baseUrl === '' ? '/dashboard/email' : '/' . $baseUrl . '/dashboard/email';

$dashboardPages = ['dashboard-pelanggan.php'];
$applicationPages = ['email.php', 'todo.php', 'notes.php'];
$editorialPages = ['blogs.php', 'knowledgebase-details.php'];
$crmPages = ['clients-grid.php', 'companies-grid.php', 'activity.php'];
$projectPages = [];
$productsPages = ['produk-antara.php'];
$currentDirection = strtolower(trim($_GET['direction'] ?? 'outgoing'));

$userRole = $_SESSION['user_role'] ?? 'admin';
$isCustomer = $userRole === 'customer';

if ($isCustomer) {
	$financePages = ['payments.php', 'invoices.php'];
} else {
	$financePages = ['estimates.php', 'invoices.php', 'payments.php', 'subscriptions.php', 'expenses.php', 'provident-fund.php', 'taxes.php', 'categories.php', 'budgets.php', 'budget-expenses.php', 'budget-revenues.php', 'employee-salary.php', 'payslip.php', 'payroll.php'];
}

$reportPages = [];
$settingsPages = ['security-settings.php', 'my-info.php'];

$isDashboard = in_array($page, $dashboardPages, true);
$isApplications = in_array($page, $applicationPages, true);
$isEditorial = in_array($page, $editorialPages, true);
$isCrm = in_array($page, $crmPages, true);
$isProjects = in_array($page, $projectPages, true);
$isProducts = in_array($page, $productsPages, true);
$isFinance = in_array($page, $financePages, true);
$isReports = in_array($page, $reportPages, true);
$isSettings = in_array($page, $settingsPages, true);
$unreadMailCount = 0;
$pendingPaymentCount = 0;
$unpaidInvoiceCount = 0;

// Hitung badge notifikasi menu (korespondensi, tagihan, pembayaran)
try {
	if (class_exists('\App\Repositories\EmailRepository')) {
		$emailRepo = new \App\Repositories\EmailRepository();
		$mailCounts = $emailRepo->counts((int) ($_SESSION['user_id'] ?? 0));
		$unreadMailCount = (int) ($mailCounts['unread_total']['unread'] ?? $mailCounts['unread_total'] ?? 0);
	}
	if (class_exists('\App\Repositories\ProductInvoiceRepository')) {
		$invRepo = new \App\Repositories\ProductInvoiceRepository();
		$invResult = $invRepo->paginate((int) ($_SESSION['user_id'] ?? 0), null, 'due_asc', 50, 1, null, null, (int) ($_SESSION['user_id'] ?? 0));
		foreach ($invResult['data'] ?? [] as $invRow) {
			$outstanding = (float) ($invRow['amount_due'] ?? ($invRow['total_amount'] ?? 0) - ($invRow['amount_paid'] ?? 0));
			$status = strtolower((string) ($invRow['status'] ?? ''));
			if ($outstanding > 0 && $status !== 'paid') {
				$unpaidInvoiceCount++;
			}
		}
	}
	if (class_exists('\App\Repositories\PaymentRepository')) {
		$payRepo = new \App\Repositories\PaymentRepository();
		$payResult = $payRepo->paginate((int) ($_SESSION['user_id'] ?? 0), 'pending', 'recent', 50, 1, null, null, null, (int) ($_SESSION['user_id'] ?? 0));
		$pendingPaymentCount = (int) ($payResult['total'] ?? count($payResult['data'] ?? []));
	}
} catch (\Throwable $ignored) {
	// Abaikan error perhitungan badge
}
?>

<!-- Horizontal Menu -->
<div class="sidebar sidebar-horizontal" id="horizontal-menu">
	<div class="sidebar-menu">
		<div class="main-menu">
			<ul class="nav-menu">
				<li class="menu-title"><span>MAIN MENU</span></li>
				<li>
					<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $isDashboard ? 'active' : ''; ?>">
						<i class="ti ti-smart-home"></i><span>Dashboard</span>
					</a>
				</li>

				<li class="submenu">
					<a href="javascript:void(0);" class="<?php echo $isApplications ? 'active subdrop' : ''; ?>">
						<i class="ti ti-layout-grid-add"></i><span>Applications</span>
						<span class="menu-arrow"></span>
					</a>
					<ul>
						<li><a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>"
								class="<?php echo $page === 'email.php' ? 'active' : ''; ?>">Korespondensi<?php if ($unreadMailCount > 0 && $page !== 'email.php') { ?><span
										class="badge bg-danger ms-2"><?php echo (int) $unreadMailCount; ?></span><?php } ?></a>
						</li>
						<li><a href="<?= htmlspecialchars($pageUrl('todo.php'), ENT_QUOTES, 'UTF-8'); ?>"
								class="<?php echo $page === 'todo.php' ? 'active' : ''; ?>">To-Do</a></li>
						<li><a href="<?= htmlspecialchars($pageUrl('notes.php'), ENT_QUOTES, 'UTF-8'); ?>"
								class="<?php echo $page === 'notes.php' ? 'active' : ''; ?>">Notes</a></li>
					</ul>
				</li>

				<li class="menu-title"><span>Editorial</span></li>
				<li class="submenu">
					<a href="javascript:void(0);" class="<?php echo $isEditorial ? 'active subdrop' : ''; ?>">
						<i class="ti ti-news"></i><span>Konten</span>
						<span class="menu-arrow"></span>
					</a>
					<ul>
						<li><a href="<?= htmlspecialchars($pageUrl('blogs.php'), ENT_QUOTES, 'UTF-8'); ?>"
								class="<?php echo $page === 'blogs.php' ? 'active' : ''; ?>">Berita & Liputan</a></li>
						<li><a href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>"
								class="<?php echo $page === 'knowledgebase-details.php' ? 'active' : ''; ?>">Panduan CRM</a></li>
					</ul>
				</li>

				<?php if (!$isCustomer): ?>
					<li class="menu-title"><span>CRM</span></li>
					<li class="submenu">
						<a href="javascript:void(0);" class="<?php echo $isCrm ? 'active subdrop' : ''; ?>">
							<i class="ti ti-address-book"></i><span>Relasi</span>
							<span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="<?= htmlspecialchars($pageUrl('clients-grid.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'clients-grid.php' ? 'active' : ''; ?>">Klien</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl($mitraGridUrl), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'companies-grid.php' ? 'active' : ''; ?>">Mitra &
									Korporasi</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('activity.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'activity.php' ? 'active' : ''; ?>">Aktivitas Relasi</a>
							</li>
						</ul>
					</li>
				<?php endif; ?>

				<li class="menu-title"><span>Produk</span></li>
				<li>
					<a href="<?= htmlspecialchars($pageUrl('produk-antara.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $isProducts ? 'active' : ''; ?>">
						<i class="ti ti-packages"></i><span>Produk ANTARA</span>
					</a>
				</li>

				<?php if ($isCustomer): ?>
					<li class="menu-title"><span>Transaksi</span></li>
					<li>
						<a href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>"
							class="<?php echo $page === 'payments.php' ? 'active' : ''; ?>">
							<i class="ti ti-wallet"></i><span>Pembayaran
								Saya</span><?php if ($pendingPaymentCount > 0 && $page !== 'payments.php') { ?><span
									class="badge bg-danger ms-2"><?php echo (int) $pendingPaymentCount; ?></span><?php } ?>
						</a>
					</li>
					<li>
						<a href="<?= htmlspecialchars($pageUrl('invoices.php'), ENT_QUOTES, 'UTF-8'); ?>"
							class="<?php echo $page === 'invoices.php' ? 'active' : ''; ?>">
							<i class="ti ti-file-invoice"></i><span>Invoice
								Saya</span><?php if ($unpaidInvoiceCount > 0 && $page !== 'invoices.php') { ?><span
									class="badge bg-danger ms-2"><?php echo (int) $unpaidInvoiceCount; ?></span><?php } ?>
						</a>
					</li>
				<?php else: ?>
					<li class="menu-title"><span>Keuangan Produk</span></li>
					<li class="submenu">
						<a href="javascript:void(0);" class="<?php echo $isFinance ? 'active subdrop' : ''; ?>">
							<i class="ti ti-cash"></i><span>Penjualan &amp; Billing</span>
							<span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="<?= htmlspecialchars($pageUrl('estimates.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'estimates.php' ? 'active' : ''; ?>">Draft Penawaran</a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('invoices.php?direction=outgoing'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'invoices.php' && $currentDirection !== 'incoming' ? 'active' : ''; ?>">Tagihan
									Produk (ke Mitra)</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('invoices.php?direction=incoming'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'invoices.php' && $currentDirection === 'incoming' ? 'active' : ''; ?>">Tagihan
									Produk (dari Mitra)</a></li>
							<li><a href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'payments.php' ? 'active' : ''; ?>">Pembayaran Klien</a>
							</li>
							<li><a href="<?= htmlspecialchars($pageUrl('subscriptions.php'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'subscriptions.php' ? 'active' : ''; ?>">Langganan Klien</a>
							</li>
						</ul>
					</li>
				<?php endif; ?>

				<li class="menu-title"><span>Settings</span></li>
				<li>
					<a href="<?= htmlspecialchars($pageUrl('security-settings.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $page === 'security-settings.php' ? 'active' : ''; ?>">
						<i class="ti ti-shield-check"></i><span>Keamanan</span>
					</a>
				</li>
				<li>
					<a href="<?= htmlspecialchars($pageUrl('my-info.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $page === 'my-info.php' ? 'active' : ''; ?>">
						<i class="ti ti-user"></i><span>My Info</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
