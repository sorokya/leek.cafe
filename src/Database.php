<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (!self::$pdo instanceof \PDO) {
            $url = parse_url((string) $_ENV['DATABASE_URL']);
            $host = $url['host'] ?? 'localhost';
            $port = $url['port'] ?? 3306;
            $dbname = ltrim($url['path'] ?? '', '/');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbname);
            $username = $url['user'] ?? 'root';
            $password = $url['pass'] ?? '';

            self::$pdo = new PDO($dsn, $username, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$pdo;
    }
}
