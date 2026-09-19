<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$result = handleBookingSubmission($_POST);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
