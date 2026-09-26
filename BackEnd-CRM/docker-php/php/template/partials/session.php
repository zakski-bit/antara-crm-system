<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$configBasePath = '';
if (class_exists('\App\Core\Config')) {
    $configBasePath = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
$baseUrl = $configBasePath;
$loginPath = $baseUrl === '' ? '/login' : '/' . $baseUrl . '/login';

$publicScripts = [
    'login.php',
    'register.php',
    'register-2.php',
    'register-3.php',
    'forgot-password.php',
    'forgot-password-2.php',
    'forgot-password-3.php',
    'reset-password.php',
    'reset-password-2.php',
    'reset-password-3.php',
];

$currentScript = basename($_SERVER['PHP_SELF'] ?? '');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (empty($_SESSION['user_id'])) {
    $isPublicScript = in_array($currentScript, $publicScripts, true);
    $isLoginPath = rtrim($currentPath, '/') === rtrim($loginPath, '/');

    if (!$isPublicScript && !$isLoginPath) {
        header("Location: {$loginPath}");
        exit;
    }
}
?>
