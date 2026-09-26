<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

$mitraGridUrl = 'companies-grid.php';
$mitraTableUrl = 'companies-crm.php';
$userRole = $_SESSION['user_role'] ?? 'admin';
$isCustomer = $userRole === 'customer';

$dashboardPages = ['dashboard-pelanggan.php'];
$editorialPages = ['blogs.php', 'knowledgebase-details.php'];
$crmPages = ['clients-grid.php', 'clients.php', 'companies-grid.php', 'companies-crm.php', 'crm/mitra', 'crm/mitra/table', 'company-details.php', 'activity.php'];
$applicationPages = ['email.php', 'todo.php', 'notes.php', 'social-feed.php'];
$productsPages = ['produk-antara.php'];
if ($isCustomer) {
	$financePages = ['payments.php', 'invoices.php'];
} else {
	$financePages = ['payments.php', 'invoices.php', 'subscriptions.php'];
}
$settingsPages = ['my-info.php', 'security-settings.php'];
$currentDirection = strtolower(trim($_GET['direction'] ?? 'outgoing'));

$configBasePath = '';
if (class_exists('\App\Core\Config')) {
	$configBasePath = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
$baseUrl = $configBasePath;
$logoutAction = $baseUrl === '' ? '/logout' : '/' . $baseUrl . '/logout';
$dashboardUrl = $baseUrl === '' ? '/dashboard/pelanggan' : '/' . $baseUrl . '/dashboard/pelanggan';
$emailUrl = $baseUrl === '' ? '/dashboard/email' : '/' . $baseUrl . '/dashboard/email';
require_once __DIR__ . '/url.php';
$pageUrl = function (string $path) use ($baseUrl): string {
	return template_menu_url($path, $baseUrl);
};
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$dashboardActive = rtrim($currentUri, '/') === rtrim($dashboardUrl, '/');
$isDashboard = in_array($page, $dashboardPages, true);
$isEditorial = in_array($page, $editorialPages, true);
$isCrm = in_array($page, $crmPages, true);
$isApplications = in_array($page, $applicationPages, true);
$isProducts = in_array($page, $productsPages, true);
$isFinance = in_array($page, $financePages, true);
$isSettings = in_array($page, $settingsPages, true);
$currentUserId = (int) ($_SESSION['user_id'] ?? ($_SESSION['customer_id'] ?? 1));
$currentUserName = $_SESSION['user_name'] ?? 'Pengguna';
$currentUserEmail = $_SESSION['user_email'] ?? 'user@example.com';
$emailLike = '%' . $currentUserEmail . '%';

// Hitung notifikasi dinamis (tagihan, pembayaran, korespondensi)
$notificationSummary = [
	'overdue' => 0,
	'due_soon' => 0,
	'unread_mail' => 0,
	'pending_payments' => 0,
	'pending_verifications' => 0,
	'items' => [], // detail invoice jatuh tempo/akan jatuh tempo
	'payments' => [], // pembayaran menunggu aksi
	'mail_items' => [], // korespondensi belum dibaca
];

if (!function_exists('db')) {
	@include_once __DIR__ . '/../app/functions.php';
}

try {
	$now = time();

	// TAGIHAN
	if (class_exists('\App\Repositories\ProductInvoiceRepository')) {
		$invRepo = new \App\Repositories\ProductInvoiceRepository();
		$recipientScope = $isCustomer ? $currentUserId : null;
		$invResult = $invRepo->paginate($currentUserId, null, 'due_asc', 12, 1, null, null, $recipientScope);
		foreach ($invResult['data'] ?? [] as $row) {
			$outstanding = (float) ($row['amount_due'] ?? ($row['total_amount'] ?? 0) - ($row['amount_paid'] ?? 0));
			if ($outstanding <= 0) {
				continue;
			}
			$dueTs = !empty($row['due_date'] ?? $row['due_at'] ?? null) ? strtotime((string) ($row['due_date'] ?? $row['due_at'])) : null;
			$status = strtolower((string) ($row['status'] ?? ''));
			$isOverdue = $status === 'overdue' || ($dueTs && $dueTs < $now);
			$isDueSoon = !$isOverdue && $dueTs && ($dueTs - $now) <= 7 * 86400;

			if ($isOverdue) {
				$notificationSummary['overdue']++;
			} elseif ($isDueSoon) {
				$notificationSummary['due_soon']++;
			}

			if ($isOverdue || $isDueSoon) {
				$notificationSummary['items'][] = [
					'title' => $row['invoice_no'] ?? 'Tagihan',
					'subtitle' => $row['invoice_title'] ?? ($row['client_name'] ?? ''),
					'ts' => $row['due_date'] ?? $row['due_at'] ?? null,
					'status' => $row['status'] ?? '',
					'amount_due' => $outstanding,
				];
			}
		}
	}

	// PEMBAYARAN
	if (function_exists('db')) {
		$pdo = db();
		if ($isCustomer) {
			$stmt = $pdo->prepare("
				SELECT
					p.invoice_no,
					COALESCE(i.invoice_title, p.client_name, p.invoice_no) AS subtitle,
					p.amount,
					p.status,
					p.paid_at,
					COALESCE(p.verification_status, 'approved') AS verification_status
				FROM crm_payments p
				LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id
				WHERE i.recipient_user_id = :uid
				  AND p.invoice_id IS NOT NULL
				  AND COALESCE(p.verification_status, 'approved') = 'approved'
				  AND p.status = 'pending'
				ORDER BY COALESCE(p.paid_at, p.created_at) DESC
				LIMIT 5
			");
			$stmt->execute(['uid' => $currentUserId]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
			foreach ($rows as $pay) {
				$notificationSummary['pending_payments']++;
				$notificationSummary['payments'][] = [
					'title' => $pay['invoice_no'] ?? 'Pembayaran',
					'subtitle' => $pay['subtitle'] ?? '',
					'amount' => $pay['amount'] ?? 0,
					'status' => $pay['status'] ?? '',
					'verification_status' => $pay['verification_status'] ?? 'approved',
					'paid_at' => $pay['paid_at'] ?? null,
				];
			}
		} else {
			$stmt = $pdo->prepare("
				SELECT
					p.invoice_no,
					COALESCE(i.invoice_title, p.client_name, p.invoice_no) AS subtitle,
					p.amount,
					p.status,
					COALESCE(p.verification_status, 'approved') AS verification_status,
					p.paid_at
				FROM crm_payments p
				LEFT JOIN crm_product_invoices i ON i.id = p.invoice_id
				WHERE p.user_id = :uid
				  AND COALESCE(p.verification_status, 'approved') = 'pending'
				ORDER BY COALESCE(p.paid_at, p.created_at) DESC
				LIMIT 5
			");
			$stmt->execute(['uid' => $currentUserId]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
			foreach ($rows as $pay) {
				$notificationSummary['pending_verifications']++;
				$notificationSummary['payments'][] = [
					'title' => $pay['invoice_no'] ?? 'Pembayaran',
					'subtitle' => $pay['subtitle'] ?? '',
					'amount' => $pay['amount'] ?? 0,
					'status' => $pay['status'] ?? '',
					'verification_status' => $pay['verification_status'] ?? 'pending',
					'paid_at' => $pay['paid_at'] ?? null,
				];
			}
		}
	}

	// KORESPONDENSI
	if (class_exists('\App\Repositories\EmailRepository')) {
		try {
			$emailRepo = new \App\Repositories\EmailRepository();
			$counts = $emailRepo->counts($currentUserId);
			$notificationSummary['unread_mail'] = (int) ($counts['inbox']['unread'] ?? $counts['unread_total']['unread'] ?? 0);

			$inboxList = $emailRepo->list($currentUserId, 'inbox', null, null, null, 25);
			foreach ($inboxList as $msg) {
				if ((int) ($msg['is_read'] ?? 0) !== 0) {
					continue;
				}
				$notificationSummary['mail_items'][] = [
					'subject' => $msg['subject'] ?? '(Tanpa subjek)',
					'sender' => $msg['sender_name'] ?? $msg['sender_email'] ?? '',
					'received_at' => $msg['received_at'] ?? $msg['created_at'] ?? null,
					'snippet' => $msg['snippet'] ?? '',
				];
				if (count($notificationSummary['mail_items']) >= 5) {
					break;
				}
			}
		} catch (Throwable $e) {
			// fallback DB manual di bawah
		}
	}

	if (($notificationSummary['unread_mail'] === 0 || empty($notificationSummary['mail_items'])) && function_exists('db')) {
		$pdo = db();
		$stmt = $pdo->prepare("
			SELECT COUNT(*) AS c
			FROM email_messages
			WHERE folder = 'inbox'
			  AND COALESCE(is_read, 0) = 0
			  AND (
					recipient_user_id = :uid
					OR sender_email = :mailExact
					OR to_addresses LIKE :mail
			  )
		");
		$stmt->execute([
			'uid' => $currentUserId,
			'mail' => $emailLike,
			'mailExact' => $currentUserEmail,
		]);
		$notificationSummary['unread_mail'] = max($notificationSummary['unread_mail'], (int) $stmt->fetchColumn());

		$stmt = $pdo->prepare("
			SELECT subject, sender_name, sender_email, snippet, received_at, created_at
			FROM email_messages
			WHERE folder = 'inbox'
			  AND COALESCE(is_read, 0) = 0
			  AND (
					recipient_user_id = :uid
					OR sender_email = :mailExact
					OR to_addresses LIKE :mail
			  )
			ORDER BY COALESCE(received_at, created_at) DESC
			LIMIT 5
		");
		$stmt->execute([
			'uid' => $currentUserId,
			'mail' => $emailLike,
			'mailExact' => $currentUserEmail,
		]);
		$notificationSummary['mail_items'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
	}
} catch (Throwable $e) {
	// Abaikan kegagalan koneksi/skema; biarkan badge kosong
}

$invoiceNotificationCount = (int) $notificationSummary['overdue'] + (int) $notificationSummary['due_soon'];
$paymentNotificationCount = $isCustomer
	? (int) $notificationSummary['pending_payments']
	: (int) $notificationSummary['pending_verifications'];
$mailNotificationCount = (int) $notificationSummary['unread_mail'];
$notificationTotal = $invoiceNotificationCount + $paymentNotificationCount + $mailNotificationCount;
$unreadMailCount = (int) $notificationSummary['unread_mail'];
$pendingPaymentCount = $paymentNotificationCount;
$unpaidInvoiceCount = (int) $notificationSummary['overdue'] + (int) $notificationSummary['due_soon'];
$paymentStatusLabels = [
	'pending' => 'Menunggu',
	'settled' => 'Lunas',
	'partial' => 'Sebagian',
	'failed' => 'Gagal',
	'refunded' => 'Refund',
];
$verificationStatusLabels = [
	'pending' => 'Menunggu Verifikasi',
	'approved' => 'Terverifikasi',
	'rejected' => 'Ditolak',
];
$paymentSectionTitle = $isCustomer ? 'Pembayaran Menunggu Konfirmasi' : 'Pembayaran Menunggu Verifikasi';
?>

<!-- Header -->
<div class="header">
	<div class="main-header">

		<div class="header-left">
			<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="logo">
				<img src="assets/img/logo-antara.png" alt="Logo Antara">
			</a>
			<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="dark-logo">
				<img src="assets/img/logo-antaradark.png" alt="Logo Antara Dark">
			</a>
		</div>

		<a id="mobile_btn" class="mobile_btn" href="#sidebar">
			<span class="bar-icon">
				<span></span>
				<span></span>
				<span></span>
			</span>
		</a>

		<div class="header-user">
			<div class="nav user-menu nav-list">

				<div class="me-auto d-flex align-items-center" id="header-search">
					<a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
						<i class="ti ti-arrow-bar-to-left"></i>
					</a>
					<!-- Welcome text -->
					<div class="header-welcome-message me-1">
						Selamat Datang di CRM ANTARA <span class="welcome-english">Information System</span>
					</div>
					<!-- /Welcome text -->
					<a href="<?= htmlspecialchars($pageUrl('my-info.php'), ENT_QUOTES, 'UTF-8'); ?>"
						class="btn btn-menubar">
						<i class="ti ti-settings-cog"></i>
					</a>
				</div>

				<!-- Horizontal Single -->
				<div class="sidebar sidebar-horizontal" id="horizontal-single">
					<div class="sidebar-menu">
						<div class="main-menu">
							<ul class="nav-menu">
								<li class="menu-title"><span>Main</span></li>
								<li>
									<a href="<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
										class="<?php echo ($dashboardActive || $isDashboard) ? 'active' : ''; ?>">
										<i class="ti ti-smart-home"></i><span>Dashboard</span>
									</a>
								</li>

								<li class="submenu">
									<a href="javascript:void(0);"
										class="<?php echo $isApplications ? 'active subdrop' : ''; ?>">
										<i class="ti ti-layout-grid-add"></i><span>Applications</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>"
												class="<?php echo $page === 'email.php' ? 'active' : ''; ?>">Korespondensi<?php if ($unreadMailCount > 0 && $page !== 'email.php') { ?><span
														class="badge bg-danger ms-2"><?php echo (int) $unreadMailCount; ?></span><?php } ?></a>
										</li>
										<li><a href="<?= htmlspecialchars($pageUrl('todo.php'), ENT_QUOTES, 'UTF-8'); ?>"
												class="<?php echo $page === 'todo.php' ? 'active' : ''; ?>">To-Do</a>
										</li>
										<li><a href="<?= htmlspecialchars($pageUrl('notes.php'), ENT_QUOTES, 'UTF-8'); ?>"
												class="<?php echo $page === 'notes.php' ? 'active' : ''; ?>">Notes</a>
										</li>
									</ul>
								</li>

								<li class="menu-title"><span>Editorial</span></li>
								<li class="submenu">
									<a href="javascript:void(0);"
										class="<?php echo $isEditorial ? 'active subdrop' : ''; ?>">
										<i class="ti ti-news"></i><span>Konten</span>
										<span class="menu-arrow"></span>
									</a>
									<ul>
										<li><a href="<?= htmlspecialchars($pageUrl('blogs.php'), ENT_QUOTES, 'UTF-8'); ?>"
												class="<?php echo $page === 'blogs.php' ? 'active' : ''; ?>">Berita &
												Liputan</a></li>
										<li><a href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>"
												class="<?php echo $page === 'knowledgebase-details.php' ? 'active' : ''; ?>">Panduan
												CRM</a></li>
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
													class="<?php echo $page === 'clients-grid.php' ? 'active' : ''; ?>">Klien</a>
											</li>
											<li><a href="<?= htmlspecialchars($pageUrl($mitraGridUrl), ENT_QUOTES, 'UTF-8'); ?>"
													class="<?php echo $page === 'companies-grid.php' ? 'active' : ''; ?>">Mitra
													& Korporasi</a></li>
											<li><a href="<?= htmlspecialchars($pageUrl('activity.php'), ENT_QUOTES, 'UTF-8'); ?>"
													class="<?php echo $page === 'activity.php' ? 'active' : ''; ?>">Aktivitas
													Relasi</a></li>
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
										<a href="javascript:void(0);"
											class="<?php echo $isFinance ? 'active subdrop' : ''; ?>">
											<i class="ti ti-cash"></i><span>Penjualan &amp; Billing</span>
											<span class="menu-arrow"></span>
										</a>
										<ul>
											<li><a href="<?= htmlspecialchars($pageUrl('invoices.php?direction=outgoing'), ENT_QUOTES, 'UTF-8'); ?>"
													class="<?php echo $page === 'invoices.php' && $currentDirection !== 'incoming' ? 'active' : ''; ?>">Tagihan
													Produk (ke Mitra)</a></li>
											<li><a href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>"
													class="<?php echo $page === 'payments.php' ? 'active' : ''; ?>">Pembayaran
													Klien</a></li>
											<li><a href="<?= htmlspecialchars($pageUrl('subscriptions.php'), ENT_QUOTES, 'UTF-8'); ?>"
													class="<?php echo $page === 'subscriptions.php' ? 'active' : ''; ?>">Langganan
													Klien</a></li>
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
				<!-- /Horizontal Single -->

				<div class="d-flex align-items-center">
					<div class="dropdown me-1">
						<a href="#" class="btn btn-menubar" data-bs-toggle="dropdown">
							<i class="ti ti-layout-grid-remove"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<div class="card mb-0 border-0 shadow-none">
								<div class="card-header">
									<h4>Applications</h4>
								</div>
								<div class="card-body">
									<a href="<?= htmlspecialchars($pageUrl('todo.php'), ENT_QUOTES, 'UTF-8'); ?>"
										class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i
												class="ti ti-subtask text-gray-9"></i></span>To Do
									</a>
									<a href="<?= htmlspecialchars($pageUrl('notes.php'), ENT_QUOTES, 'UTF-8'); ?>"
										class="d-block py-2">
										<span class="avatar avatar-md bg-transparent-dark me-2"><i
												class="ti ti-notes text-gray-9"></i></span>Notes
									</a>
								</div>
							</div>
						</div>
					</div>
					<!-- Chat shortcut removed -->
					<div class="me-1">
						<a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-menubar">
							<i class="ti ti-mail"></i>
						</a>
					</div>
					<div class="me-1 notification_item">
						<a href="#" class="btn btn-menubar position-relative me-1" id="notification_popup"
							data-bs-toggle="dropdown">
							<i class="ti ti-bell"></i>
							<?php if ($notificationTotal > 0): ?>
								<span
									class="badge bg-danger rounded-pill d-flex align-items-center justify-content-center header-badge">
									<?= (int) $notificationTotal; ?>
								</span>
							<?php endif; ?>
						</a>
						<style>
							.notification-dropdown {
								min-width: 430px;
								border: 1px solid #e6ebf2;
								border-radius: 12px;
								background: linear-gradient(180deg, #ffffff 0%, #f8fafd 100%);
								box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
							}
							.notification-dropdown .noti-content {
								max-height: 72vh;
								overflow-y: auto;
								padding-right: 2px;
							}
							.notification-dropdown.notification-dropdown-empty .noti-content {
								min-height: 270px;
								display: flex;
								align-items: center;
							}
							.notification-dropdown .noti-empty-state {
								width: 100%;
								border: 1px dashed #cfd8e6;
								border-radius: 12px;
								background: #f7faff;
								padding: 24px 18px;
								text-align: center;
							}
							.notification-dropdown .noti-empty-icon {
								width: 56px;
								height: 56px;
								border-radius: 50%;
								display: inline-flex;
								align-items: center;
								justify-content: center;
								border: 1px solid #d7e3f4;
								background: #ffffff;
								color: #5b6f8f;
								font-size: 24px;
								margin-bottom: 10px;
							}
							.notification-dropdown .noti-empty-actions {
								display: flex;
								justify-content: center;
								gap: 8px;
								flex-wrap: wrap;
								margin-top: 14px;
							}
							.notification-dropdown .noti-empty-actions .btn {
								border-radius: 20px;
							}
							.notification-dropdown .noti-section {
								border: 1px solid #e9edf4;
								border-radius: 10px;
								padding: 10px;
								background-color: #fff;
							}
							.notification-dropdown .noti-section + .noti-section {
								margin-top: 12px;
							}
							.notification-dropdown .noti-card {
								border: 1px solid #dfe6ef;
								border-left: 3px solid #98a2b3;
								border-radius: 8px;
								background: #fff;
								transition: all 0.15s ease-in-out;
							}
							.notification-dropdown .noti-card:hover {
								border-color: #c7d2e2;
								box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
							}
							.notification-dropdown .noti-card.noti-card-overdue {
								border-left-color: #dc3545;
							}
							.notification-dropdown .noti-card.noti-card-due {
								border-left-color: #f59e0b;
							}
							.notification-dropdown .noti-card.noti-card-payment {
								border-left-color: #0d6efd;
							}
							.notification-dropdown .noti-card.noti-card-mail {
								border-left-color: #198754;
							}
						</style>
						<div class="dropdown-menu dropdown-menu-end notification-dropdown <?php echo $notificationTotal === 0 ? 'notification-dropdown-empty' : ''; ?> p-4">
							<div class="d-flex align-items-center justify-content-between border-bottom p-0 pb-3 mb-3">
								<h4 class="notification-title">Notifikasi</h4>
							</div>
							<div class="noti-content">
								<?php if ($notificationTotal === 0): ?>
									<div class="noti-empty-state">
										<div class="noti-empty-icon">
											<i class="ti ti-bell-off"></i>
										</div>
										<p class="mb-1 fw-semibold">Tidak ada notifikasi baru</p>
										<p class="text-muted fs-12 mb-0">Update tagihan, pembayaran, dan korespondensi akan muncul di sini.</p>
										<div class="noti-empty-actions">
											<a href="<?= htmlspecialchars($pageUrl('invoices.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-light border">Tagihan</a>
											<a href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-light border">Pembayaran</a>
											<a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-light border">Korespondensi</a>
										</div>
									</div>
								<?php else: ?>
									<div class="d-flex flex-wrap gap-2 mb-3">
										<span class="badge bg-light text-dark border">Tagihan: <?= (int) $invoiceNotificationCount; ?></span>
										<span class="badge bg-light text-dark border">Pembayaran: <?= (int) $paymentNotificationCount; ?></span>
										<span class="badge bg-light text-dark border">Korespondensi: <?= (int) $mailNotificationCount; ?></span>
									</div>

									<div class="noti-section">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<h6 class="mb-0">Tagihan</h6>
											<a href="<?= htmlspecialchars($pageUrl('invoices.php'), ENT_QUOTES, 'UTF-8'); ?>" class="fs-12">Lihat</a>
										</div>
										<?php if (empty($notificationSummary['items'])): ?>
											<p class="text-muted fs-12 mb-0">Tidak ada tagihan yang butuh perhatian.</p>
										<?php else: ?>
											<?php foreach (($notificationSummary['items'] ?? []) as $item): ?>
												<?php
												$isOverdue = ($item['status'] ?? '') === 'overdue' || (isset($item['ts']) && $item['ts'] < date('Y-m-d'));
												$badgeClass = $isOverdue ? 'text-danger' : 'text-warning';
												$badgeIcon = $isOverdue ? 'ti-alert-octagon' : 'ti-clock';
												$invoiceLink = !empty($item['title'])
													? $pageUrl('invoices.php?search=' . urlencode($item['title']))
													: $pageUrl('invoices.php');
												?>
												<a class="noti-card <?= $isOverdue ? 'noti-card-overdue' : 'noti-card-due'; ?> p-2 mb-2 d-block text-reset text-decoration-none"
													href="<?php echo htmlspecialchars($invoiceLink, ENT_QUOTES, 'UTF-8'); ?>">
													<div class="d-flex">
														<span class="avatar avatar-md me-2 flex-shrink-0 bg-transparent border <?= $badgeClass; ?>">
															<i class="ti <?= $badgeIcon; ?>"></i>
														</span>
														<div class="flex-grow-1">
															<p class="mb-1 fw-semibold text-dark">
																<?= htmlspecialchars($item['title'] ?? 'Tagihan', ENT_QUOTES, 'UTF-8'); ?>
																<?php if (!empty($item['amount_due'])): ?>
																	<span class="text-muted">- Rp<?= number_format((float) $item['amount_due'], 0, ',', '.'); ?></span>
																<?php endif; ?>
															</p>
															<p class="text-muted fs-12 mb-0">
																<?php if (!empty($item['ts'])): ?>
																	Jatuh tempo: <?= htmlspecialchars(date('d M Y', strtotime($item['ts'])), ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
																<?php if (!empty($item['subtitle'])): ?>
																	<br><?= htmlspecialchars($item['subtitle'], ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
															</p>
														</div>
													</div>
												</a>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>

									<div class="noti-section">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<h6 class="mb-0"><?= htmlspecialchars($paymentSectionTitle, ENT_QUOTES, 'UTF-8'); ?></h6>
											<a href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>" class="fs-12">Lihat</a>
										</div>
										<?php if (empty($notificationSummary['payments'])): ?>
											<p class="text-muted fs-12 mb-0">Tidak ada pembayaran yang menunggu aksi.</p>
										<?php else: ?>
											<?php foreach ($notificationSummary['payments'] as $pay): ?>
												<?php
												$verifyKey = strtolower((string) ($pay['verification_status'] ?? 'approved'));
												$statusKey = strtolower((string) ($pay['status'] ?? 'pending'));
												$statusText = $isCustomer
													? ($paymentStatusLabels[$statusKey] ?? ucfirst($statusKey))
													: ($verificationStatusLabels[$verifyKey] ?? ucfirst($verifyKey));
												?>
												<a class="noti-card noti-card-payment p-2 mb-2 d-block text-reset text-decoration-none"
													href="<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>">
													<div class="d-flex">
														<span class="avatar avatar-md me-2 flex-shrink-0 bg-transparent border text-info">
															<i class="ti ti-cash"></i>
														</span>
														<div class="flex-grow-1">
															<p class="mb-1 fw-semibold text-dark">
																<?= htmlspecialchars($pay['title'] ?? 'Pembayaran', ENT_QUOTES, 'UTF-8'); ?>
																<?php if (isset($pay['amount'])): ?>
																	<span class="text-muted">- Rp<?= number_format((float) $pay['amount'], 0, ',', '.'); ?></span>
																<?php endif; ?>
															</p>
															<p class="text-muted fs-12 mb-0">
																<?php if (!empty($pay['paid_at'])): ?>
																	Diajukan: <?= htmlspecialchars(date('d M Y', strtotime($pay['paid_at'])), ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
																<?php if (!empty($pay['subtitle'])): ?>
																	<br><?= htmlspecialchars($pay['subtitle'], ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
																<br>Status: <?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8'); ?>
															</p>
														</div>
													</div>
												</a>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>

									<div class="noti-section">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<h6 class="mb-0">Korespondensi</h6>
											<a href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" class="fs-12">Lihat</a>
										</div>
										<?php if (empty($notificationSummary['mail_items'])): ?>
											<p class="text-muted fs-12 mb-0">Tidak ada korespondensi baru.</p>
										<?php else: ?>
											<?php foreach ($notificationSummary['mail_items'] as $mail): ?>
												<a class="noti-card noti-card-mail p-2 mb-2 d-block text-reset text-decoration-none"
													href="<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>">
													<div class="d-flex">
														<span class="avatar avatar-md me-2 flex-shrink-0 bg-transparent border text-primary">
															<i class="ti ti-mail"></i>
														</span>
														<div class="flex-grow-1">
															<p class="mb-1 fw-semibold text-dark"><?= htmlspecialchars($mail['subject'] ?? 'Korespondensi', ENT_QUOTES, 'UTF-8'); ?></p>
															<p class="text-muted fs-12 mb-0">
																<?php if (!empty($mail['sender'] ?? $mail['sender_name'] ?? $mail['sender_email'])): ?>
																	Dari: <?= htmlspecialchars($mail['sender'] ?? ($mail['sender_name'] ?? $mail['sender_email']), ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
																<?php if (!empty($mail['received_at'] ?? $mail['created_at'])): ?>
																	<br><?= htmlspecialchars(date('d M Y H:i', strtotime($mail['received_at'] ?? $mail['created_at'])), ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
																<?php if (!empty($mail['snippet'])): ?>
																	<br><?= htmlspecialchars($mail['snippet'], ENT_QUOTES, 'UTF-8'); ?>
																<?php endif; ?>
															</p>
														</div>
													</div>
												</a>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="dropdown profile-dropdown">
						<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
							data-bs-toggle="dropdown">
							<span class="avatar avatar-sm online">
								<img src="assets/img/profiles/avatar-12.jpg" alt="Img" class="img-fluid rounded-circle">
							</span>
						</a>
						<div class="dropdown-menu shadow-none">
							<div class="card mb-0">
								<div class="card-header">
									<div class="d-flex align-items-center">
										<span class="avatar avatar-lg me-2 avatar-rounded">
											<img src="assets/img/profiles/avatar-12.jpg" alt="img">
										</span>
										<div>
											<h5 class="mb-0">
												<?= htmlspecialchars($currentUserName, ENT_QUOTES, 'UTF-8'); ?>
											</h5>
											<p class="fs-12 fw-medium mb-0">
												<?= htmlspecialchars($currentUserEmail, ENT_QUOTES, 'UTF-8'); ?>
											</p>
										</div>
									</div>
								</div>
								<div class="card-body">
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
										href="<?= htmlspecialchars($pageUrl('profile.php'), ENT_QUOTES, 'UTF-8'); ?>">
										<i class="ti ti-user-circle me-1"></i>My Profile
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
										href="<?= htmlspecialchars($pageUrl('security-settings.php'), ENT_QUOTES, 'UTF-8'); ?>">
										<i class="ti ti-status-change me-1"></i>My Status
									</a>
									<a class="dropdown-item d-inline-flex align-items-center p-0 py-2"
										href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>">
										<i class="ti ti-question-mark me-1"></i>Panduan CRM
									</a>
								</div>
								<div class="card-footer">
									<form action="<?= htmlspecialchars($logoutAction, ENT_QUOTES, 'UTF-8'); ?>"
										method="post" class="d-inline-flex w-100">
										<button type="submit"
											class="dropdown-item d-inline-flex align-items-center p-0 py-2 border-0 bg-transparent w-100 text-start">
											<i class="ti ti-login me-2"></i>Logout
										</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			document.addEventListener('DOMContentLoaded', function () {

				// module search redirect
				const moduleSearch = document.getElementById('module-search');
				if (moduleSearch) {
					moduleSearch.addEventListener('keydown', function (e) {
						if (e.key === 'Enter') {
							e.preventDefault();
							const q = (moduleSearch.value || '').trim().toLowerCase();
							if (!q) return;
							const baseDashboard = "<?= htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>";
							const routes = [
								{ keys: ['dashboard', 'home'], url: baseDashboard },
								{ keys: ['korespondensi', 'email', 'mail'], url: "<?= htmlspecialchars($emailUrl, ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['pembayaran', 'payment'], url: "<?= htmlspecialchars($pageUrl('payments.php'), ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['tagihan', 'invoice'], url: "<?= htmlspecialchars($pageUrl('invoices.php'), ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['langganan', 'subscription'], url: "<?= htmlspecialchars($pageUrl('subscriptions.php'), ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['produk', 'product'], url: "<?= htmlspecialchars($pageUrl('produk-antara.php'), ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['keamanan', 'security'], url: "<?= htmlspecialchars($pageUrl('security-settings.php'), ENT_QUOTES, 'UTF-8'); ?>" },
								{ keys: ['info', 'profile', 'profil'], url: "<?= htmlspecialchars($pageUrl('my-info.php'), ENT_QUOTES, 'UTF-8'); ?>" },
							];
							let target = null;
							for (const r of routes) {
								if (r.keys.some(k => q.includes(k))) {
									target = r.url;
									break;
								}
							}
							if (target) {
								window.location.href = target;
							} else {
								window.location.href = "<?= htmlspecialchars($pageUrl('invoices.php'), ENT_QUOTES, 'UTF-8'); ?>" + '?search=' + encodeURIComponent(q);
							}
						}
					});
				}
			});
		</script>

		<!-- Mobile Menu -->
		<div class="dropdown mobile-user-menu">
			<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
				aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
			<div class="dropdown-menu dropdown-menu-end">
				<a class="dropdown-item"
					href="<?= htmlspecialchars($pageUrl('profile.php'), ENT_QUOTES, 'UTF-8'); ?>">My Profile</a>
				<a class="dropdown-item"
					href="<?= htmlspecialchars($pageUrl('security-settings.php'), ENT_QUOTES, 'UTF-8'); ?>">My Status</a>
				<a class="dropdown-item"
					href="<?= htmlspecialchars($pageUrl('knowledgebase-details.php'), ENT_QUOTES, 'UTF-8'); ?>">Panduan CRM</a>
				<form action="<?= htmlspecialchars($logoutAction, ENT_QUOTES, 'UTF-8'); ?>" method="post">
					<button type="submit" class="dropdown-item border-0 bg-transparent text-start w-100">Logout</button>
				</form>
			</div>
		</div>
		<!-- /Mobile Menu -->
	</div>
</div>
<!-- /Header -->
