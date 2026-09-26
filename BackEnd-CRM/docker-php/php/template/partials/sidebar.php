<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

$mitraGridUrl = 'companies-grid.php';
$mitraTableUrl = 'companies-crm.php';
$currentDirection = strtolower(trim($_GET['direction'] ?? 'outgoing'));

$dashboardPages = ['dashboard-pelanggan.php'];
$applicationPages = ['email.php', 'todo.php', 'notes.php', 'social-feed.php'];
$editorialPages = ['blogs.php', 'knowledgebase-details.php'];
$crmPages = ['clients-grid.php', 'clients.php', 'companies-grid.php', 'companies-crm.php', 'crm/mitra', 'crm/mitra/table', 'company-details.php', 'activity.php'];
$projectPages = [];
$productsPages = ['produk-antara.php'];

$userRole = $_SESSION['user_role'] ?? 'admin';
$isCustomer = $userRole === 'customer';

if ($isCustomer) {
	$financePages = ['payments.php', 'invoices.php'];
} else {
	$financePages = ['estimates.php', 'invoices.php', 'payments.php', 'subscriptions.php', 'expenses.php', 'provident-fund.php', 'taxes.php', 'categories.php', 'budgets.php', 'budget-expenses.php', 'budget-revenues.php', 'employee-salary.php', 'payslip.php', 'payroll.php'];
}

$reportPages = ['expenses-report.php', 'invoice-report.php', 'payment-report.php', 'project-report.php', 'task-report.php', 'user-report.php', 'attendance-report.php', 'leave-report.php', 'daily-report.php'];
$settingsPages = ['my-info.php', 'security-settings.php'];

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
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

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

// Badge counts (korespondensi, tagihan, pembayaran) per user login
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
	// Ignore badge errors
}

// Fallback jika repo tidak tersedia: hitung langsung via DB
if (($unreadMailCount === 0 || $pendingPaymentCount === 0 || $unpaidInvoiceCount === 0) && !function_exists('db') && file_exists(__DIR__ . '/../app/functions.php')) {
	include_once __DIR__ . '/../app/functions.php';
}
if (function_exists('db')) {
	try {
		$pdo = db();
		$currentUserId = (int) ($_SESSION['user_id'] ?? 0);
		// Unread mail
		if ($unreadMailCount === 0) {
			$stmt = $pdo->prepare("
				SELECT COUNT(*) AS c
				FROM email_messages
				WHERE COALESCE(is_read, 0) = 0
				  AND (
						(recipient_user_id = :uid)
						OR (sender_user_id = :uid)
						OR sender_email = :mailExact
						OR to_addresses LIKE :mail
						OR COALESCE(cc_addresses, '') LIKE :mail
				  )
				  AND folder NOT IN ('deleted', 'spam')
			");
			$stmt->execute([
				'uid' => $currentUserId,
				'mail' => '%' . ($_SESSION['user_email'] ?? '') . '%',
				'mailExact' => $_SESSION['user_email'] ?? '',
			]);
			$unreadMailCount = max($unreadMailCount, (int) ($stmt->fetchColumn() ?: 0));
		}
		// Unpaid invoices
		if ($unpaidInvoiceCount === 0) {
			$outstandingExpr = "GREATEST(COALESCE(total_amount, 0) - COALESCE(amount_paid, 0), 0)";
			$stmt = $pdo->prepare("
				SELECT COUNT(*) FROM crm_product_invoices
				WHERE (user_id = :uid OR recipient_user_id = :uid)
				  AND {$outstandingExpr} > 0
				  AND status <> 'paid'
			");
			$stmt->execute(['uid' => $currentUserId]);
			$unpaidInvoiceCount = (int) ($stmt->fetchColumn() ?: 0);
		}
		// Pending payments
		if ($pendingPaymentCount === 0) {
			$stmt = $pdo->prepare("
				SELECT COUNT(*) FROM crm_payments p
				LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id
				WHERE p.status = 'pending'
				  AND (p.user_id = :uid OR i.recipient_user_id = :uid)
			");
			$stmt->execute(['uid' => $currentUserId]);
			$pendingPaymentCount = (int) ($stmt->fetchColumn() ?: 0);
		}
	} catch (\Throwable $ignored) {
		// Jika gagal, biarkan badge kosong
	}
}

$dashboardMenuActive = rtrim($currentUri, '/') === rtrim($dashboardUrl, '/') || $isDashboard;

?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
	<!-- Logo -->
	<div class="sidebar-logo">
		<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="logo logo-normal">
			<img src="assets/img/logo-antaradark.png" alt="Logo Antara" style="height: 30px; width: auto;">
		</a>
		<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="logo-small">
			<img src="assets/img/logo-antara-light.png" alt="Logo Antara" style="height: 20px; width: auto;">
		</a>
	</div>
	<!-- /Logo -->

	<div class="modern-profile p-3 pb-0">
		<div class="text-center rounded bg-light p-3 mb-4 user-profile">
			<div class="avatar avatar-lg online mb-3">
				<img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<h6 class="fs-12 fw-normal mb-1">Redaksi Antara</h6>
			<p class="fs-10">Administrator</p>
		</div>
		<div class="sidebar-nav mb-3">
			<ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
				<li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
				<li class="nav-item"><a class="nav-link border-0"
						href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>">Inbox</a></li>
			</ul>
		</div>
	</div>
	<div class="sidebar-header p-3 pb-0 pt-2">
		<div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
			<div class="avatar avatar-md onlin">
				<img src="assets/img/profiles/avatar-02.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<div class="text-start sidebar-profile-info ms-2">
				<h6 class="fs-12 fw-normal mb-1">Redaksi Antara</h6>
				<p class="fs-10">Administrator</p>
			</div>
		</div>
		<div class="input-group input-group-flat d-inline-flex mb-4">
			<span class="input-icon-addon">
				<i class="ti ti-search"></i>
			</span>
			<input type="text" class="form-control" placeholder="Search modules">
			<span class="input-group-text">
				<kbd>CTRL + / </kbd>
			</span>
		</div>
		<div class="d-flex align-items-center justify-content-between menu-item mb-3">
			<div class="me-3 notification-item">
				<a href="<?= htmlspecialchars($pageUrl('activity.php'), ENT_QUOTES, 'UTF-8'); ?>"
					class="btn btn-menubar position-relative me-1">
					<i class="ti ti-bell"></i>
					<span class="notification-status-dot"></span>
				</a>
			</div>
			<div class="me-0">
				<a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-menubar">
					<i class="ti ti-message"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title"><span>MAIN MENU</span></li>
				<li>
					<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $dashboardMenuActive ? 'active' : ''; ?>">
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
							<i class="ti ti-file-invoice"></i><span>Tagihan
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
							<li><a href="<?= htmlspecialchars($pageUrl('invoices.php?direction=outgoing'), ENT_QUOTES, 'UTF-8'); ?>"
									class="<?php echo $page === 'invoices.php' && $currentDirection !== 'incoming' ? 'active' : ''; ?>">Tagihan
									Produk (ke Mitra)</a></li>
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
					<a href="<?= htmlspecialchars($pageUrl('my-info.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $page === 'my-info.php' ? 'active' : ''; ?>">
						<i class="ti ti-user"></i><span>My Info</span>
					</a>
				</li>
				<li>
					<a href="<?= htmlspecialchars($pageUrl('security-settings.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="<?php echo $page === 'security-settings.php' ? 'active' : ''; ?>">
						<i class="ti ti-shield-check"></i><span>Keamanan</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->
