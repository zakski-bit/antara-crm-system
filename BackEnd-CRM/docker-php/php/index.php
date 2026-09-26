<?php
/**
 * Application front controller
 */

declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');

$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        $baseDir = APP_PATH . '/';

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

use App\Core\Application;

$app = new Application();

$debug = $app->config('app.debug', false);
error_reporting($debug ? E_ALL : 0);
ini_set('display_errors', $debug ? '1' : '0');

$routesPath = BASE_PATH . '/routes/web.php';
if (file_exists($routesPath)) {
    require $routesPath;
} else {
    http_response_code(503);
    echo 'Routes file is missing.';
    exit;
}

$app->run();
