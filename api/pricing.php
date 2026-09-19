<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$vehicleType = safeString($_GET['vehicle_type'] ?? $_POST['vehicle_type'] ?? 'sedan');
$distanceKm = (float) ($_GET['distance_km'] ?? $_POST['distance_km'] ?? 18.5);
$durationMinutes = (int) ($_GET['duration_minutes'] ?? $_POST['duration_minutes'] ?? 35);
$waitingMinutes = (int) ($_GET['waiting_minutes'] ?? $_POST['waiting_minutes'] ?? 0);
$passengers = max(1, (int) ($_GET['passengers'] ?? $_POST['passengers'] ?? 1));

$fare = calculateTripFare([
    'vehicle_type' => $vehicleType,
    'distance_km' => $distanceKm,
    'duration_minutes' => $durationMinutes,
    'waiting_minutes' => $waitingMinutes,
    'passengers' => $passengers,
]);

$eta = getRouteEtaEstimate([
    'distance_km' => $distanceKm,
    'vehicle_type' => $vehicleType,
]);

echo json_encode([
    'success' => true,
    'vehicle_type' => $vehicleType,
    'fare' => $fare,
    'eta' => $eta,
    'payment_methods' => getPaymentMethods(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
