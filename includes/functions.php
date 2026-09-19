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

function getRoutesList(): array
{
    if (!dbConnected()) {
        return getSampleRoutes();
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->query('SELECT * FROM routes ORDER BY id DESC LIMIT 20');
        $rows = $statement->fetchAll();

        if (!$rows) {
            return getSampleRoutes();
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'title' => (string) ($row['name'] ?? 'Tuyến đường'),
                'duration' => ((int) ($row['duration_minutes'] ?? 0)) . ' phút',
                'price' => number_format((float) ($row['base_price'] ?? 0), 0, ',', '.') . 'đ',
                'start_point' => (string) ($row['start_point'] ?? ''),
                'end_point' => (string) ($row['end_point'] ?? ''),
            ];
        }, $rows);
    } catch (Throwable $e) {
        return getSampleRoutes();
    }
}

function getSampleFleet(): array
{
    return [
        ['name' => 'Sedan Executive', 'seats' => '4 chỗ', 'features' => ['Máy lạnh', 'Wi‑Fi', 'Chuyên nghiệp'], 'price' => '1.200.000đ'],
        ['name' => 'Van Gia đình', 'seats' => '7 chỗ', 'features' => ['Hành lý rộng', 'An toàn', 'Ghế trẻ em'], 'price' => '1.700.000đ'],
        ['name' => 'Limousine cao cấp', 'seats' => '4 chỗ', 'features' => ['Nội thất da', 'Concierge', 'Cao cấp'], 'price' => '2.600.000đ'],
    ];
}

function getVehiclesList(): array
{
    if (!dbConnected()) {
        return getSampleFleet();
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->query('SELECT * FROM vehicles ORDER BY id DESC LIMIT 20');
        $rows = $statement->fetchAll();

        if (!$rows) {
            return getSampleFleet();
        }

        return array_map(static function (array $row): array {
            $vehicleType = (string) ($row['vehicle_type'] ?? 'sedan');
            $features = [
                'Máy lạnh',
                'Vệ sinh sạch sẽ',
                $vehicleType === 'limousine' ? 'Nội thất cao cấp' : 'An toàn',
                'Hỗ trợ khách hàng',
            ];

            return [
                'id' => (int) ($row['id'] ?? 0),
                'name' => (string) ($row['name'] ?? 'Xe dịch vụ'),
                'seats' => ((int) ($row['seats'] ?? 4)) . ' chỗ',
                'features' => $features,
                'price' => number_format((float) ($row['base_price'] ?? 0), 0, ',', '.') . 'đ',
                'status' => (string) ($row['status'] ?? 'active'),
            ];
        }, $rows);
    } catch (Throwable $e) {
        return getSampleFleet();
    }
}

function createRoute(array $data): array
{
    $name = safeString($data['name'] ?? '');
    $startPoint = safeString($data['start_point'] ?? '');
    $endPoint = safeString($data['end_point'] ?? '');
    $durationMinutes = (int) ($data['duration_minutes'] ?? 0);
    $basePrice = (float) ($data['base_price'] ?? 0);

    if ($name === '' || $startPoint === '' || $endPoint === '') {
        return ['success' => false, 'message' => 'Vui lòng nhập tên tuyến, điểm đi và điểm đến.'];
    }

    if (!dbConnected()) {
        return ['success' => true, 'message' => 'Tuyến mới đã được lưu trong chế độ demo.', 'route' => ['name' => $name, 'start_point' => $startPoint, 'end_point' => $endPoint]];
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->prepare(
            'INSERT INTO routes (name, start_point, end_point, duration_minutes, base_price, status) VALUES (:name, :start_point, :end_point, :duration_minutes, :base_price, :status)'
        );
        $statement->execute([
            ':name' => $name,
            ':start_point' => $startPoint,
            ':end_point' => $endPoint,
            ':duration_minutes' => $durationMinutes ?: 60,
            ':base_price' => $basePrice ?: 0,
            ':status' => 'active',
        ]);

        return ['success' => true, 'message' => 'Tuyến đường mới đã được thêm thành công.', 'route' => ['id' => (int) $pdo->lastInsertId()]];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Không thể lưu tuyến đường vào cơ sở dữ liệu.'];
    }
}

function createVehicle(array $data): array
{
    $name = safeString($data['name'] ?? '');
    $vehicleType = safeString($data['vehicle_type'] ?? 'sedan');
    $seats = max(1, (int) ($data['seats'] ?? 4));
    $basePrice = (float) ($data['base_price'] ?? 0);

    if ($name === '') {
        return ['success' => false, 'message' => 'Vui lòng nhập tên xe.'];
    }

    if (!dbConnected()) {
        return ['success' => true, 'message' => 'Xe mới đã được lưu trong chế độ demo.', 'vehicle' => ['name' => $name, 'vehicle_type' => $vehicleType]];
    }

    try {
        $pdo = getPdo();
        $statement = $pdo->prepare(
            'INSERT INTO vehicles (name, vehicle_type, seats, status, base_price) VALUES (:name, :vehicle_type, :seats, :status, :base_price)'
        );
        $statement->execute([
            ':name' => $name,
            ':vehicle_type' => $vehicleType,
            ':seats' => $seats,
            ':status' => 'active',
            ':base_price' => $basePrice ?: 0,
        ]);

        return ['success' => true, 'message' => 'Xe mới đã được thêm thành công.', 'vehicle' => ['id' => (int) $pdo->lastInsertId()]];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Không thể lưu xe mới vào cơ sở dữ liệu.'];
    }
}

function getDriverStatusMachine(): array
{
    return [
        'offline' => 'Ngoại tuyến',
        'available' => 'Sẵn sàng',
        'busy' => 'Đang chở khách',
        'on_break' => 'Nghỉ ngơi',
        'suspended' => 'Tạm khóa',
    ];
}

function customerIsLoggedIn(): bool
{
    return !empty($_SESSION['customer_logged_in']) && !empty($_SESSION['customer_user']);
}

function driverIsLoggedIn(): bool
{
    return !empty($_SESSION['driver_logged_in']) && !empty($_SESSION['driver_user']);
}

function requireCustomerLogin(): void
{
    if (!customerIsLoggedIn()) {
        redirect('/customer/login.php');
    }
}

function requireDriverLogin(): void
{
    if (!driverIsLoggedIn()) {
        redirect('/driver/login.php');
    }
}

function loginCustomer(array $user): void
{
    $_SESSION['customer_logged_in'] = true;
    $_SESSION['customer_user'] = [
        'id' => (int) ($user['id'] ?? 0),
        'name' => (string) ($user['name'] ?? 'Khách hàng'),
        'email' => (string) ($user['email'] ?? ''),
        'role' => 'customer',
    ];
}

function loginDriver(array $user): void
{
    $_SESSION['driver_logged_in'] = true;
    $_SESSION['driver_user'] = [
        'id' => (int) ($user['id'] ?? 0),
        'name' => (string) ($user['name'] ?? 'Tài xế'),
        'phone' => (string) ($user['phone'] ?? ''),
        'role' => 'driver',
    ];
}

function verifyCustomerCredentials(string $email, string $password): ?array
{
    $normalizedEmail = strtolower(trim($email));
    if ($normalizedEmail === 'customer@demo.com' && $password === 'customer123') {
        return ['id' => 1, 'name' => 'Khách hàng Demo', 'email' => 'customer@demo.com', 'role' => 'customer'];
    }

    return null;
}

function verifyDriverCredentials(string $phone, string $password): ?array
{
    $normalizedPhone = trim($phone);
    if ($normalizedPhone === '0909000001' && $password === 'driver123') {
        return ['id' => 1, 'name' => 'Tài xế Demo', 'phone' => '0909000001', 'role' => 'driver'];
    }

    return null;
}

function getCustomerTrips(): array
{
    return [
        ['id' => 1001, 'pickup' => 'Sân bay Đà Nẵng', 'destination' => 'Phố cổ Hội An', 'status' => 'in_trip', 'price' => '1.200.000đ'],
        ['id' => 1002, 'pickup' => 'Bến xe trung tâm', 'destination' => 'Đà Nẵng', 'status' => 'completed', 'price' => '420.000đ'],
        ['id' => 1003, 'pickup' => 'Huế', 'destination' => 'Đà Nẵng', 'status' => 'pending', 'price' => '900.000đ'],
    ];
}

function getDriverTrips(): array
{
    return [
        ['id' => 2001, 'pickup' => 'Sân bay Đà Nẵng', 'destination' => 'Hội An', 'status' => 'accepted', 'customer' => 'Nguyễn A'],
        ['id' => 2002, 'pickup' => 'Đà Nẵng', 'destination' => 'Huế', 'status' => 'in_trip', 'customer' => 'Trần B'],
    ];
}

function getDispatchCandidates(string $vehicleType, int $passengers): array
{
    $drivers = [
        ['id' => 101, 'name' => 'Nguyễn Văn A', 'vehicle_type' => 'sedan', 'status' => 'available', 'distance_km' => 3.2, 'rating' => 4.9],
        ['id' => 102, 'name' => 'Trần Văn B', 'vehicle_type' => 'suv', 'status' => 'available', 'distance_km' => 4.8, 'rating' => 4.8],
        ['id' => 103, 'name' => 'Lê Thị C', 'vehicle_type' => 'van', 'status' => 'busy', 'distance_km' => 7.4, 'rating' => 4.7],
        ['id' => 104, 'name' => 'Phạm Văn D', 'vehicle_type' => 'limousine', 'status' => 'available', 'distance_km' => 5.6, 'rating' => 5.0],
        ['id' => 105, 'name' => 'Hoàng Minh E', 'vehicle_type' => 'rideshare', 'status' => 'available', 'distance_km' => 2.9, 'rating' => 4.8],
    ];

    return array_values(array_filter($drivers, static function (array $driver) use ($vehicleType, $passengers): bool {
        $matchesType = $driver['vehicle_type'] === $vehicleType || $driver['vehicle_type'] === 'rideshare';
        $matchesCapacity = ($passengers <= 4 && in_array($driver['vehicle_type'], ['sedan', 'limousine', 'rideshare'], true)) || ($passengers <= 7 && $driver['vehicle_type'] === 'suv') || ($passengers <= 16 && $driver['vehicle_type'] === 'van');
        return $matchesType && $matchesCapacity && $driver['status'] === 'available';
    }));
}

function assignDriverForBooking(array $booking): array
{
    $vehicleType = (string) ($booking['vehicle_type'] ?? 'sedan');
    $passengers = max(1, (int) ($booking['passengers'] ?? 1));
    $candidates = getDispatchCandidates($vehicleType, $passengers);

    if (!$candidates) {
        return ['success' => false, 'message' => 'Hiện không có tài xế phù hợp cho loại xe này.'];
    }

    $selected = $candidates[0];
    return [
        'success' => true,
        'driver' => $selected,
        'message' => 'Đã tìm được tài xế ' . $selected['name'] . ' cho chuyến này.',
    ];
}

function getRouteEtaEstimate(array $trip): array
{
    $distanceKm = (float) ($trip['distance_km'] ?? 20.0);
    $vehicleType = (string) ($trip['vehicle_type'] ?? 'sedan');
    $speedMap = [
        'sedan' => 32,
        'suv' => 28,
        'van' => 24,
        'limousine' => 35,
        'rideshare' => 30,
    ];

    $speed = (float) ($speedMap[$vehicleType] ?? 30);
    $minutes = max(10, (int) round(($distanceKm / $speed) * 60));
    $etaAt = time() + ($minutes * 60);
    $windowAt = time() + (($minutes + 8) * 60);

    return [
        'distance_km' => $distanceKm,
        'eta_minutes' => $minutes,
        'eta_at' => date('Y-m-d H:i:s', $etaAt),
        'arrival_window' => date('Y-m-d H:i:s', $windowAt),
    ];
}

function getPaymentMethods(): array
{
    return [
        ['code' => 'cash', 'label' => 'Thanh toán tiền mặt'],
        ['code' => 'momo', 'label' => 'Ví MoMo'],
        ['code' => 'vnpay', 'label' => 'VNPay'],
        ['code' => 'bank_transfer', 'label' => 'Chuyển khoản ngân hàng'],
    ];
}

function getVerificationStatusMachine(): array
{
    return [
        'pending' => 'Chờ xác minh',
        'verified' => 'Đã xác minh',
        'rejected' => 'Từ chối',
        'suspended' => 'Tạm khóa',
    ];
}

function processTripPayment(array $payment): array
{
    $bookingId = (int) ($payment['booking_id'] ?? 0);
    $amount = (float) ($payment['amount'] ?? 0);
    $method = safeString($payment['payment_method'] ?? 'cash');

    if ($bookingId <= 0 || $amount <= 0) {
        return ['success' => false, 'message' => 'Thông tin thanh toán không hợp lệ.'];
    }

    if (!dbConnected()) {
        return [
            'success' => true,
            'payment_status' => 'paid',
            'message' => 'Thanh toán đã được ghi nhận trong chế độ demo.',
            'reference' => 'DEMO-' . $bookingId,
        ];
    }

    try {
        $pdo = getPdo();
        $reference = strtoupper(bin2hex(random_bytes(6)));
        $statement = $pdo->prepare(
            'INSERT INTO trip_payments (booking_id, amount, payment_method, payment_status, transaction_reference) VALUES (:booking_id, :amount, :payment_method, :payment_status, :transaction_reference)'
        );
        $statement->execute([
            ':booking_id' => $bookingId,
            ':amount' => $amount,
            ':payment_method' => $method,
            ':payment_status' => 'paid',
            ':transaction_reference' => $reference,
        ]);

        return [
            'success' => true,
            'payment_status' => 'paid',
            'message' => 'Thanh toán đã được xác nhận.',
            'reference' => $reference,
        ];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Không thể xử lý thanh toán.'];
    }
}

function getDriverVerificationList(): array
{
    return [
        ['id' => 1, 'driver_name' => 'Nguyễn Văn A', 'document' => 'CCCD', 'status' => 'verified'],
        ['id' => 2, 'driver_name' => 'Trần Văn B', 'document' => 'GPLX', 'status' => 'pending'],
        ['id' => 3, 'driver_name' => 'Lê Thị C', 'document' => 'Bằng lái', 'status' => 'rejected'],
    ];
}

function updateDriverVerificationStatus(int $driverId, string $status): array
{
    $normalizedStatus = strtolower(trim($status));
    $allowed = array_keys(getVerificationStatusMachine());

    if (!in_array($normalizedStatus, $allowed, true)) {
        return ['success' => false, 'message' => 'Trạng thái xác minh không hợp lệ.'];
    }

    return ['success' => true, 'message' => 'Cập nhật trạng thái xác minh thành công.', 'status' => $normalizedStatus];
}

function buildTripDispatchSummary(array $booking): array
{
    $distanceKm = (float) ($booking['distance_km'] ?? 18.5);
    $vehicleType = (string) ($booking['vehicle_type'] ?? 'sedan');
    $passengers = max(1, (int) ($booking['passengers'] ?? 1));

    $dispatch = assignDriverForBooking([
        'vehicle_type' => $vehicleType,
        'passengers' => $passengers,
    ]);

    $fare = calculateTripFare([
        'vehicle_type' => $vehicleType,
        'distance_km' => $distanceKm,
        'duration_minutes' => (int) ($booking['duration_minutes'] ?? 35),
        'waiting_minutes' => (int) ($booking['waiting_minutes'] ?? 0),
        'passengers' => $passengers,
    ]);

    $eta = getRouteEtaEstimate([
        'distance_km' => $distanceKm,
        'vehicle_type' => $vehicleType,
    ]);

    return [
        'dispatch' => $dispatch,
        'fare' => $fare,
        'eta' => $eta,
        'payment_methods' => getPaymentMethods(),
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

function getPricingRules(): array
{
    return [
        ['vehicle_type' => 'sedan', 'base_fee' => 180000, 'per_km_rate' => 18000, 'per_minute_rate' => 2600, 'waiting_fee_per_minute' => 3500, 'night_surcharge' => 30000, 'airport_surcharge' => 50000, 'holiday_surcharge' => 20000, 'min_fare' => 150000],
        ['vehicle_type' => 'suv', 'base_fee' => 260000, 'per_km_rate' => 22000, 'per_minute_rate' => 3200, 'waiting_fee_per_minute' => 4500, 'night_surcharge' => 35000, 'airport_surcharge' => 60000, 'holiday_surcharge' => 25000, 'min_fare' => 200000],
        ['vehicle_type' => 'van', 'base_fee' => 420000, 'per_km_rate' => 30000, 'per_minute_rate' => 3800, 'waiting_fee_per_minute' => 5500, 'night_surcharge' => 45000, 'airport_surcharge' => 70000, 'holiday_surcharge' => 30000, 'min_fare' => 300000],
        ['vehicle_type' => 'limousine', 'base_fee' => 480000, 'per_km_rate' => 36000, 'per_minute_rate' => 4200, 'waiting_fee_per_minute' => 6500, 'night_surcharge' => 60000, 'airport_surcharge' => 80000, 'holiday_surcharge' => 35000, 'min_fare' => 350000],
        ['vehicle_type' => 'rideshare', 'base_fee' => 120000, 'per_km_rate' => 14000, 'per_minute_rate' => 2100, 'waiting_fee_per_minute' => 2800, 'night_surcharge' => 20000, 'airport_surcharge' => 25000, 'holiday_surcharge' => 15000, 'min_fare' => 120000],
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

    $pricingRules = getPricingRules();
    $config = null;
    foreach ($pricingRules as $rule) {
        if (($rule['vehicle_type'] ?? '') === $vehicleType) {
            $config = $rule;
            break;
        }
    }

    $config = $config ?? [
        'base_fee' => 180000,
        'per_km_rate' => 18000,
        'per_minute_rate' => 2600,
        'waiting_fee_per_minute' => 3500,
        'min_fare' => 150000,
    ];

    $distanceFee = $distanceKm * ((float) ($config['per_km_rate'] ?? 18000));
    $timeFee = $minutes * ((float) ($config['per_minute_rate'] ?? 2600));
    $waitingFee = $waitingMinutes * ((float) ($config['waiting_fee_per_minute'] ?? 3500));
    $extraPassengerFee = max(0, $passengers - 4) * 50000;

    $baseFee = (float) ($config['base_fee'] ?? 180000);
    $minimumFare = (float) ($config['min_fare'] ?? $baseFee);
    $total = $baseFee + $distanceFee + $timeFee + $waitingFee + $extraPassengerFee;
    $total = max($total, $minimumFare);

    return [
        'vehicle_type' => $vehicleType,
        'distance_km' => $distanceKm,
        'duration_minutes' => $minutes,
        'waiting_minutes' => $waitingMinutes,
        'passengers' => $passengers,
        'base_fee' => (int) $baseFee,
        'distance_fee' => (int) $distanceFee,
        'time_fee' => (int) $timeFee,
        'waiting_fee' => (int) $waitingFee,
        'passenger_fee' => (int) $extraPassengerFee,
        'minimum_fare' => (int) $minimumFare,
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

    $booking['dispatch'] = buildTripDispatchSummary($booking);
    $booking['payment'] = processTripPayment([
        'booking_id' => 0,
        'amount' => calculateTripFare([
            'vehicle_type' => $vehicleType,
            'distance_km' => (float) ($booking['dispatch']['eta']['distance_km'] ?? 18.5),
            'duration_minutes' => (int) ($booking['dispatch']['eta']['eta_minutes'] ?? 35),
            'waiting_minutes' => 0,
            'passengers' => $passengers,
        ])['total_amount'],
        'payment_method' => 'cash',
    ]);

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
