<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$result = processTripPayment([
    'booking_id' => (int) ($_POST['booking_id'] ?? 0),
    'amount' => (float) ($_POST['amount'] ?? 0),
    'payment_method' => safeString($_POST['payment_method'] ?? 'cash'),
]);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
