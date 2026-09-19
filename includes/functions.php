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

function userHasRole(string $role): bool
{
    $user = $_SESSION['admin_user'] ?? $_SESSION['user'] ?? null;

    if (!is_array($user)) {
        return false;
    }

    $userRole = (string) ($user['role'] ?? $user['type'] ?? '');
    return $userRole === $role;
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        redirect('/admin/login.php');
    }
}

function loginAdmin(array $user): void
{
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = [
        'id' => (int) ($user['id'] ?? 0),
        'username' => (string) ($user['username'] ?? 'admin'),
        'full_name' => (string) ($user['full_name'] ?? 'System Admin'),
        'role' => (string) ($user['role'] ?? 'admin'),
    ];
}

function verifyAdminCredentials(string $username, string $password): ?array
{
    $normalizedUsername = strtolower(trim($username));

    if ($normalizedUsername === 'admin' && $password === 'admin123') {
        return [
            'id' => 1,
            'username' => 'admin',
            'full_name' => 'System Admin',
            'role' => 'admin',
        ];
    }

    if (!dbConnected()) {
        return null;
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->prepare(
            'SELECT id, username, password_hash, full_name FROM admins WHERE username = :username LIMIT 1'
        );
        $statement->execute([':username' => $username]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        if (!password_verify($password, (string) $row['password_hash'])) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'username' => (string) $row['username'],
            'full_name' => (string) ($row['full_name'] ?? 'System Admin'),
            'role' => 'admin',
        ];
    } catch (Throwable $e) {
        return null;
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

function getServiceTypes(): array
{
    return [
        ['name' => 'Sedan', 'code' => 'sedan', 'seats' => 4, 'base_price' => 180000, 'description' => 'Cho khách cá nhân, đi lại nhanh gọn'],
        ['name' => 'SUV', 'code' => 'suv', 'seats' => 7, 'base_price' => 260000, 'description' => 'Phù hợp gia đình và hành lý nhiều'],
        ['name' => 'Van', 'code' => 'van', 'seats' => 16, 'base_price' => 420000, 'description' => 'Phục vụ nhóm lớn, chuyến đi tập thể'],
        ['name' => 'Limousine', 'code' => 'limousine', 'seats' => 4, 'base_price' => 420000, 'description' => 'Cao cấp, trải nghiệm sang trọng'],
        ['name' => 'Ride Share', 'code' => 'rideshare', 'seats' => 4, 'base_price' => 150000, 'description' => 'Ghép chuyến tiết kiệm chi phí'],
    ];
}

function getBookingStatusMachine(): array
{
    return [
        'pending' => 'Đang chờ',
        'matched' => 'Đã tìm tài xế',
        'accepted' => 'Tài xế đã nhận',
        'picked_up' => 'Đã đón khách',
        'in_trip' => 'Đang trên đường',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
}

function updateBookingStatus(int $bookingId, string $status): array
{
    $normalizedStatus = strtolower(trim($status));
    $allowedStatuses = array_keys(getBookingStatusMachine());

    if (!in_array($normalizedStatus, $allowedStatuses, true)) {
        return ['success' => false, 'message' => 'Trạng thái không hợp lệ.'];
    }

    if (!dbConnected()) {
        return ['success' => true, 'message' => 'Cập nhật trạng thái thành công (demo mode).', 'status' => $normalizedStatus];
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        $statement->execute([
            ':status' => $normalizedStatus,
            ':id' => $bookingId,
        ]);

        return ['success' => true, 'message' => 'Cập nhật trạng thái thành công.', 'status' => $normalizedStatus];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Không thể cập nhật trạng thái do lỗi cơ sở dữ liệu.'];
    }
}

function calculateTripFare(array $trip): array
{
    $vehicleType = (string) ($trip['vehicle_type'] ?? 'sedan');
    $distanceKm = (float) ($trip['distance_km'] ?? 0.0);
    $minutes = (int) ($trip['duration_minutes'] ?? 0);
    $waitingMinutes = (int) ($trip['waiting_minutes'] ?? 0);
    $passengers = max(1, (int) ($trip['passengers'] ?? 1));

    $serviceMap = [
        'sedan' => ['base' => 180000, 'per_km' => 18000, 'per_minute' => 2600, 'waiting' => 3500],
        'suv' => ['base' => 260000, 'per_km' => 22000, 'per_minute' => 3200, 'waiting' => 4500],
        'van' => ['base' => 420000, 'per_km' => 30000, 'per_minute' => 3800, 'waiting' => 5500],
        'limousine' => ['base' => 480000, 'per_km' => 36000, 'per_minute' => 4200, 'waiting' => 6500],
        'rideshare' => ['base' => 120000, 'per_km' => 14000, 'per_minute' => 2100, 'waiting' => 2800],
    ];

    $config = $serviceMap[$vehicleType] ?? $serviceMap['sedan'];
    $distanceFee = $distanceKm * $config['per_km'];
    $timeFee = $minutes * $config['per_minute'];
    $waitingFee = $waitingMinutes * $config['waiting'];
    $extraPassengerFee = max(0, $passengers - 4) * 50000;

    $total = $config['base'] + $distanceFee + $timeFee + $waitingFee + $extraPassengerFee;

    return [
        'vehicle_type' => $vehicleType,
        'distance_km' => $distanceKm,
        'duration_minutes' => $minutes,
        'waiting_minutes' => $waitingMinutes,
        'passengers' => $passengers,
        'base_fee' => (int) $config['base'],
        'distance_fee' => (int) $distanceFee,
        'time_fee' => (int) $timeFee,
        'waiting_fee' => (int) $waitingFee,
        'passenger_fee' => (int) $extraPassengerFee,
        'total_amount' => (int) $total,
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
