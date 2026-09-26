<?php
/**
 * Global application configuration.
 * Adjust the database section to match your local credentials.
 */

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Antara CRM Template',
        'default_page' => 'index',
        'views_path' => dirname(__DIR__) . '/src',
        'base_url' => '',
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_DATABASE') ?: 'antara_crm',
        'user' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
        'flags' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];
