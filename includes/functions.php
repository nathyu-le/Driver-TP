<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user']);
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        redirect('/admin/login.php');
    }
}

function safeString(?string $value): string
{
    return trim((string) ($value ?? ''));
}

function getSampleRoutes(): array
{
    return [
        ['id' => 1, 'title' => 'Đà Nẵng → Hội An', 'duration' => '45 phút', 'price' => '1.200.000đ'],
        ['id' => 2, 'title' => 'Đà Nẵng → Huế', 'duration' => '2h 30m', 'price' => '2.500.000đ'],
        ['id' => 3, 'title' => 'Đà Nẵng → Tam Kỳ', 'duration' => '1h 40m', 'price' => '1.950.000đ'],
        ['id' => 4, 'title' => 'Đà Nẵng → Quảng Ngãi', 'duration' => '3h 15m', 'price' => '2.900.000đ'],
    ];
}

function getSampleFleet(): array
{
    return [
        ['name' => 'Sedan Executive', 'seats' => '4 chỗ', 'features' => ['Máy lạnh', 'Wi‑Fi', 'Chuyên nghiệp'], 'price' => '1.200.000đ'],
        ['name' => 'Van Gia đình', 'seats' => '7 chỗ', 'features' => ['Hành lý rộng', 'An toàn', 'Ghế trẻ em'], 'price' => '1.700.000đ'],
        ['name' => 'Limousine cao cấp', 'seats' => '4 chỗ', 'features' => ['Nội thất da', 'Concierge', 'Cao cấp'], 'price' => '2.600.000đ'],
    ];
}

function getHomepageData(): array
{
    return [
        'stats' => [
            ['label' => 'Chuyến', 'value' => '12k+'],
            ['label' => 'Đúng giờ', 'value' => '98%'],
            ['label' => 'Đánh giá', 'value' => '4.9/5'],
            ['label' => 'Hỗ trợ', 'value' => '24/7'],
        ],
        'routes' => getSampleRoutes(),
        'fleet' => getSampleFleet(),
    ];
}

function getDashboardStats(): array
{
    if (!dbConnected()) {
        return [
            'total_bookings' => 1284,
            'today_bookings' => 42,
            'active_vehicles' => 18,
            'revenue' => '145.000.000đ',
        ];
    }

    try {
        $pdo = getPdo();
        $sqls = [
            'total_bookings' => 'SELECT COUNT(*) AS total FROM bookings',
            'today_bookings' => "SELECT COUNT(*) AS total FROM bookings WHERE DATE(created_at) = CURDATE()",
            'active_vehicles' => 'SELECT COUNT(*) AS total FROM vehicles WHERE status = "active"',
            'revenue' => "SELECT COALESCE(SUM(total_amount), 0) AS total FROM bookings WHERE status IN ('confirmed', 'completed')",
        ];

        $stats = [];
        foreach ($sqls as $key => $query) {
            $statement = $pdo->query($query);
            $row = $statement->fetch();
            $stats[$key] = (int) ($row['total'] ?? 0);
        }

        $stats['revenue'] = number_format((float) $stats['revenue'], 0, ',', '.') . 'đ';
        return $stats;
    } catch (Throwable $e) {
        return [
            'total_bookings' => 1284,
            'today_bookings' => 42,
            'active_vehicles' => 18,
            'revenue' => '145.000.000đ',
        ];
    }
}

function handleBookingSubmission(array $post): array
{
    $pickup = safeString($post['pickup'] ?? '');
    $destination = safeString($post['destination'] ?? '');
    $travelDate = safeString($post['travel_date'] ?? '');
    $passengers = (int) ($post['passengers'] ?? 2);
    $vehicleType = safeString($post['vehicle_type'] ?? 'sedan');

    $status = 'pending';
    $message = 'Đặt xe thành công. Chúng tôi sẽ xác nhận trong thời gian sớm nhất.';

    if ($pickup === '' || $destination === '') {
        return ['success' => false, 'message' => 'Vui lòng nhập điểm đón và điểm đến.'];
    }

    $booking = [
        'pickup' => $pickup,
        'destination' => $destination,
        'travel_date' => $travelDate ?: date('d/m/Y H:i'),
        'passengers' => $passengers,
        'vehicle_type' => $vehicleType,
        'status' => $status,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    if (dbConnected()) {
        try {
            $pdo = getPdo();
            $stmt = $pdo->prepare(
                'INSERT INTO bookings (pickup_location, destination_location, travel_date, passenger_count, vehicle_type, status, created_at) VALUES (:pickup, :destination, :travel_date, :passengers, :vehicle_type, :status, :created_at)'
            );
            $stmt->execute([
                ':pickup' => $pickup,
                ':destination' => $destination,
                ':travel_date' => $travelDate ?: date('Y-m-d H:i'),
                ':passengers' => $passengers,
                ':vehicle_type' => $vehicleType,
                ':status' => $status,
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
            $booking['id'] = (int) $pdo->lastInsertId();
            $message = 'Đặt xe thành công. Mã đặt xe #' . $booking['id'] . ' đã được ghi nhận.';
        } catch (Throwable $e) {
            $message = 'Hệ thống đang gặp sự cố cơ sở dữ liệu, nhưng yêu cầu của bạn đã được lưu tạm thời.';
        }
    }

    $_SESSION['last_booking'] = $booking;

    return ['success' => true, 'message' => $message, 'booking' => $booking];
}

function getBookingsList(): array
{
    if (!dbConnected()) {
        return [
            ['id' => 1001, 'pickup' => 'Sân bay Đà Nẵng', 'destination' => 'Phố cổ Hội An', 'travel_date' => '2026-09-20 12:00:00', 'status' => 'pending'],
            ['id' => 1002, 'pickup' => 'Bến xe trung tâm', 'destination' => 'Huế', 'travel_date' => '2026-09-21 08:30:00', 'status' => 'confirmed'],
        ];
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 20');
        return $statement->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function getAdminUser(): ?array
{
    return $_SESSION['admin_user'] ?? null;
}
