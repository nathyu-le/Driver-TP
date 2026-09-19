<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

function runSqlStatements(PDO $pdo, string $sql): void
{
    $statements = preg_split('/;\s*(?:\r?\n|$)/', $sql, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($statements as $statement) {
        $trimmed = trim($statement);
        if ($trimmed === '' || preg_match('/^--/', $trimmed)) {
            continue;
        }

        $pdo->exec($trimmed . ';');
    }
}

function initializeDatabase(): array
{
    $config = getDbConfig();

    try {
        $rootDsn = sprintf(
            'mysql:host=%s;port=%d;charset=%s',
            $config['host'],
            $config['port'],
            $config['charset']
        );

        $pdo = new PDO(
            $rootDsn,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . $config['dbname'] . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE ' . $config['dbname']);

        $schemaFile = __DIR__ . '/db-schema.sql';
        if (!file_exists($schemaFile)) {
            throw new RuntimeException('Schema file not found: ' . $schemaFile);
        }

        $schemaSql = file_get_contents($schemaFile);
        if ($schemaSql === false) {
            throw new RuntimeException('Unable to read schema file.');
        }

        runSqlStatements($pdo, $schemaSql);

        return [
            'success' => true,
            'message' => 'Database initialized successfully.',
            'database' => $config['dbname'],
        ];
    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'database' => $config['dbname'],
        ];
    }
}

$result = initializeDatabase();
header('Content-Type: application/json; charset=utf-8');

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
