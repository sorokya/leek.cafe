<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

return
    [
        'paths' => [
            'migrations' => __DIR__ . '/db/migrations',
            'seeds' => __DIR__ . '/db/seeds',
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => $_ENV['APP_ENV'] ?: 'development',
            'production' => [
                'dsn' => $_ENV['DATABASE_URL'],
            ],
            'development' => [
                'dsn' => $_ENV['DATABASE_URL'],
            ],
        ],
        'version_order' => 'creation',
    ];
