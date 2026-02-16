<?php

declare(strict_types=1);

namespace App\Data;

use PDO as BasePDO;

class PDO extends BasePDO
{
    private static ?string $dsn = null;

    private static ?string $username = null;

    private static ?string $password = null;

    public function __construct()
    {
        if (self::$dsn === null) {
            $url = parse_url((string) $_ENV['DATABASE_URL']);
            $host = $url['host'] ?? 'localhost';
            $port = $url['port'] ?? 3306;
            $dbname = ltrim($url['path'] ?? '', '/');
            self::$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbname);
            self::$username = $url['user'] ?? 'root';
            self::$password = $url['pass'] ?? '';
        }

        parent::__construct(self::$dsn, self::$username, self::$password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
