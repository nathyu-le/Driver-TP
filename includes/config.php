<?php

declare(strict_types=1);

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

function getDbConfig(): array
{
    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');
    $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
    $dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'driver_tp');
    $username = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
    $password = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');
    $charset = getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? 'utf8mb4');

    return [
        'host' => $host,
        'port' => (int) $port,
        'dbname' => $dbname,
        'username' => $username,
        'password' => $password,
        'charset' => $charset,
    ];
}

function siteName(): string
{
    return 'Airport Transfer';
}
