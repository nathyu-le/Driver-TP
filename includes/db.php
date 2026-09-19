<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getPdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = getDbConfig();
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $pdo = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}

function dbConnected(): bool
{
    try {
        getPdo();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}
