<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = $_POST;
$booking = [
    'vehicle_type' => safeString($payload['vehicle_type'] ?? 'sedan'),
    'passengers' => max(1, (int) ($payload['passengers'] ?? 1)),
    'distance_km' => (float) ($payload['distance_km'] ?? 18.5),
    'duration_minutes' => (int) ($payload['duration_minutes'] ?? 35),
    'waiting_minutes' => (int) ($payload['waiting_minutes'] ?? 0),
];

$result = buildTripDispatchSummary($booking);

echo json_encode([
    'success' => true,
    'data' => $result,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
