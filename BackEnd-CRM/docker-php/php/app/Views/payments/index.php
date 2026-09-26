<?php
/** @var array $payments */
/** @var array $stats */
/** @var array $flash */
/** @var array $errors */
/** @var array $old */
/** @var array $filters */
/** @var array $statusOptions */
/** @var array $statusLabels */
/** @var array $verificationOptions */
/** @var array $verificationLabels */
/** @var array $sortOptions */
/** @var array $perPageOptions */
/** @var array $invoiceOptions */
/** @var string $userRole */
/** @var bool $isCustomer */
/** @var bool $isStaff */
/** @var string $baseUrl */
/** @var string $listUrl */
/** @var string $storeUrl */
/** @var string $verifyUrlBase */

$payments = $payments ?? [];
$stats = $stats ?? [];
$flash = $flash ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$statusLabels = $statusLabels ?? [];
$verificationOptions = $verificationOptions ?? [];
$verificationLabels = $verificationLabels ?? [];
$sortOptions = $sortOptions ?? [];
$perPageOptions = $perPageOptions ?? [10, 25, 50];
$invoiceOptions = $invoiceOptions ?? [];
$userRole = $userRole ?? (string) ($_SESSION['user_role'] ?? 'admin');
$isCustomer = $isCustomer ?? ($userRole === 'customer');
$isStaff = $isStaff ?? in_array($userRole, ['admin', 'employee'], true);
$baseUrl = $baseUrl ?? '';
$listUrl = $listUrl ?? ($baseUrl === '' ? '/payments.php' : $baseUrl . '/payments.php');
$storeUrl = $storeUrl ?? (rtrim($baseUrl, '/') . '/crm/payments');
$verifyUrlBase = $verifyUrlBase ?? (rtrim($baseUrl, '/') . '/crm/payments');

$_SERVER['APP_BASE_URL'] = $baseUrl;
$_SERVER['PHP_SELF'] = '/payments.php';

require BASE_PATH . '/template/src/payments.php';
