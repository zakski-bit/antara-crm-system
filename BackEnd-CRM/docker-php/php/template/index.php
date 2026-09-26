<?php
/**
 * Simple front controller for the Antara CRM template.
 * Usage example:
 *   http://localhost/php/template/index.php?page=clients
 */

declare(strict_types=1);

require __DIR__ . '/app/functions.php';

$rootPath = dirname(__DIR__);
$composerAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function (string $class) use ($rootPath): void {
        $prefix = 'App\\';
        $baseDir = $rootPath . '/app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

\App\Core\Config::loadFromDirectory($rootPath . '/app/Config');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? null;

    if ($action === 'client_create') {
        require __DIR__ . '/app/actions/clients/create.php';
        action_clients_create($_POST);
    } elseif ($action === 'client_update') {
        require __DIR__ . '/app/actions/clients/update.php';
        action_clients_update($_POST);
    } elseif ($action === 'client_delete') {
        require __DIR__ . '/app/actions/clients/delete.php';
        action_clients_delete($_POST);
    }
}

$requestedPage = $_GET['page'] ?? app_config('app.default_page', 'index');
$viewFile = resolve_view($requestedPage);

if ($viewFile === null) {
    http_response_code(404);
    $requestedPage = 'error-404';
    $viewFile = resolve_view($requestedPage);
    if ($viewFile === null) {
        exit('404 - Halaman tidak ditemukan.');
    }
}

$originalScript = $_SERVER['PHP_SELF'] ?? '';
$viewRoot = app_config('app.views_path');
$realViewRoot = $viewRoot ? realpath($viewRoot) : false;

$virtualSelf = $originalScript;
$pageData = ['page' => $requestedPage];
$relativeView = null;

if ($realViewRoot !== false) {
    $normalizedRoot = str_replace('\\', '/', $realViewRoot);
    $normalizedView = str_replace('\\', '/', $viewFile);
    if (strpos($normalizedView, $normalizedRoot) === 0) {
        $relativeView = ltrim(substr($normalizedView, strlen($normalizedRoot)), '/');
        $baseScriptPath = rtrim(str_replace('\\', '/', dirname($originalScript)), '/');
        $virtualSelf = ($baseScriptPath === '' ? '' : $baseScriptPath) . '/src/' . $relativeView;
    }
}

$_SERVER['PHP_SELF'] = $virtualSelf;

if ($relativeView !== null) {
    $relativeWithoutExtension = preg_replace('/\.php$/', '', $relativeView);
    $pageScript = __DIR__ . '/app/pages/' . $relativeWithoutExtension . '.php';
    if ($pageScript && file_exists($pageScript)) {
        $dataFromScript = require $pageScript;
        if (is_array($dataFromScript)) {
            $pageData = array_merge($pageData, $dataFromScript);
        }
    }
}

render_view($viewFile, $pageData);

$_SERVER['PHP_SELF'] = $originalScript;
