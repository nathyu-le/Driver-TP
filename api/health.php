<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'status' => 'ok',
    'app' => siteName(),
    'database' => dbConnected() ? 'connected' : 'offline',
    'timestamp' => date('c'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
