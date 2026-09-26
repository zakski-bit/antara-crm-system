<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = Config::get('database');

        if (!$config) {
            throw new PDOException('Database configuration is missing. Please create app/Config/database.php.');
        }

        $driver = $config['default'] ?? null;
        if (!$driver || !isset($config['connections'][$driver])) {
            throw new PDOException('Database connection [default] is not defined.');
        }

        $connectionConfig = $config['connections'][$driver];
        if (($connectionConfig['driver'] ?? '') !== 'mysql') {
            throw new PDOException(sprintf('Database driver [%s] is not supported.', $connectionConfig['driver'] ?? 'unknown'));
        }

        $host = $connectionConfig['host'] ?? '127.0.0.1';
        $port = (int)($connectionConfig['port'] ?? 3306);
        $database = $connectionConfig['database'] ?? '';
        $username = $connectionConfig['username'] ?? '';
        $password = $connectionConfig['password'] ?? '';
        $charset = $connectionConfig['charset'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $database, $charset);

        $options = $connectionConfig['options'] ?? [];
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $options = $options + $defaultOptions;

        self::$connection = new PDO($dsn, $username, $password, $options);

        return self::$connection;
    }
}
