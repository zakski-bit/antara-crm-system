<?php require_once __DIR__ . '/session.php'; ?>

<?php
$currentScript = basename($_SERVER['PHP_SELF'] ?? '');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$baseUrlConfig = '';
if (class_exists('\App\Core\Config')) {
    $baseUrlConfig = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
require_once __DIR__ . '/url.php';
if ($baseUrlConfig !== '') {
    $prefix = '/' . $baseUrlConfig;
    if (strpos($requestPath, $prefix) === 0) {
        $requestPath = substr($requestPath, strlen($prefix));
    }
}
$normalizedPath = trim($requestPath, '/');
$authRoutes = [
    'login',
    'login-2',
    'login-3',
    'register',
    'register-2',
    'register-3',
    'forgot-password',
    'forgot-password-2',
    'forgot-password-3',
    'reset-password',
    'reset-password-2',
    'reset-password-3',
    'email-verification',
    'email-verification-2',
    'email-verification-3',
    'two-step-verification',
    'two-step-verification-2',
    'two-step-verification-3',
    'lock-screen',
    'success',
    'success-2',
    'success-3',
    'under-maintenance',
    'under-construction',
    'coming-soon',
    'error-404',
    'error-500',
];
$authScripts = array_map(fn($item) => $item . '.php', $authRoutes);
$authLookup = array_merge($authRoutes, $authScripts);
$isAuthPage = in_array($normalizedPath, $authLookup, true) || in_array($currentScript, $authLookup, true);
?>

<!DOCTYPE html>
<?php require_once __DIR__ . '/theme-settings.php'; ?>

<head>

    <?php require_once __DIR__ . '/title-meta.php'; ?>

    <?php require_once __DIR__ . '/head-css.php'; ?>

</head>

<?php require_once __DIR__ . '/body.php'; ?>

<?php include __DIR__ . '/loader.php'; ?>

<?php if (!$isAuthPage): ?>
    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

        <?php require_once __DIR__ . '/menu.php'; ?>

        <?= template_rewrite_links($content, $baseUrlConfig) ?>

        <?php include_once __DIR__ . '/modal-popup.php'; ?>

    </div>
    <!-- End Main Wrapper -->
<?php else: ?>
    <?= $content ?>
<?php endif; ?>

<?php require_once __DIR__ . '/vendor-scripts.php'; ?>

</body>

</html>