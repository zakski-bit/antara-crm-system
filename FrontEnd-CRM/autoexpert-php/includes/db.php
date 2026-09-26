<?php
/**
 * Database connection helper for the Frontend CRM.
 * Connects to the backend's MariaDB via Docker network.
 */

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = 'app_mariadb';
        $port = 3306;
        $dbname = 'antara_crm';
        $user = 'appuser';
        $pass = 'apppassword';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}
