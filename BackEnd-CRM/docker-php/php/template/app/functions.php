<?php
declare(strict_types=1);

/**
 * Lightweight helper functions for the Antara CRM template.
 */

/**
 * Retrieve configuration values using dot notation.
 */
function app_config(?string $key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

/**
 * Lazily bootstrap a PDO instance using the configuration file.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $database = app_config('database');
    if (!is_array($database)) {
        throw new RuntimeException('Database configuration is missing.');
    }

    $driver = $database['driver'] ?? 'mysql';
    $host = $database['host'] ?? '127.0.0.1';
    $port = $database['port'] ?? 3306;
    $dbname = $database['name'] ?? '';
    $charset = $database['charset'] ?? 'utf8mb4';
    $user = $database['user'] ?? '';
    $password = $database['password'] ?? '';
    $flags = $database['flags'] ?? [];

    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $driver,
        $host,
        $port,
        $dbname,
        $charset
    );

    try {
        $pdo = new PDO($dsn, $user, $password, $flags);
    } catch (PDOException $exception) {
        http_response_code(500);
        exit('Database connection failed: ' . $exception->getMessage());
    }

    return $pdo;
}

/**
 * Resolve a page name to an absolute view file path within /src.
 */
function resolve_view(string $page): ?string
{
    $page = trim($page, '/');
    if ($page === '') {
        $page = app_config('app.default_page', 'index');
    }

    // Prevent directory traversal & allow subdirectories and dashes/underscores.
    if (strpos($page, '..') !== false) {
        return null;
    }

    if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $page)) {
        return null;
    }

    $viewsPath = app_config('app.views_path');
    $viewFile = rtrim((string)$viewsPath, '/') . '/' . $page . '.php';

    if (!file_exists($viewFile)) {
        return null;
    }

    return realpath($viewFile) ?: null;
}

/**
 * Load the requested view inside its own directory context.
 */
function render_view(string $viewFile, array $data = []): void
{
    extract($data, EXTR_SKIP);

    $originalCwd = getcwd();
    $viewDir = dirname($viewFile);

    if ($viewDir !== false && is_dir($viewDir)) {
        chdir($viewDir);
    }

    require $viewFile;

    if ($originalCwd !== false) {
        chdir($originalCwd);
    }
}

/**
 * Ensure the PHP session is started.
 */
function session_ensure_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Store a flash value for the next request.
 */
function flash_set(string $key, $value): void
{
    session_ensure_started();
    $_SESSION['_flash'][$key] = $value;
}

/**
 * Retrieve and remove a flash value from the session.
 */
function flash_get(string $key, $default = null)
{
    session_ensure_started();

    if (!isset($_SESSION['_flash']) || !array_key_exists($key, $_SESSION['_flash'])) {
        return $default;
    }

    $value = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);

    return $value;
}

/**
 * Redirect the current request to a different path.
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Generate or retrieve the current CSRF token.
 */
function csrf_token(): string
{
    session_ensure_started();
    if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Validate the provided CSRF token.
 */
function csrf_validate(?string $token): bool
{
    session_ensure_started();
    if ($token === null || $token === '') {
        return false;
    }
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}
