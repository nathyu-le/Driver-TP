<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$vehicleMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_submit'])) {
    $result = createVehicle($_POST);
    $vehicleMessage = $result['message'];
}

$vehicles = getVehiclesList();
$pageTitle = 'Quản lý xe & tài xế';
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/bookings.php">Đặt xe</a>
            <a href="/admin/routes.php">Tuyến đường</a>
            <a class="active" href="/admin/vehicles.php">Xe & tài xế</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Quản lý</p>
                <h1>Xe & tài xế</h1>
            </div>
            <button class="primary-btn" type="button">Thêm mới</button>
        </div>

        <div class="admin-panel vehicles-panel">
            <?php if ($vehicleMessage): ?>
                <div class="form-status success"><?= htmlspecialchars($vehicleMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post" action="/admin/vehicles.php" class="admin-form compact-form">
                <input type="hidden" name="vehicle_submit" value="1" />
                <div class="two-col-grid">
                    <label>
                        <span>Tên xe</span>
                        <input type="text" name="name" placeholder="Ví dụ: SUV Executive" required />
                    </label>
                    <label>
                        <span>Loại xe</span>
                        <select name="vehicle_type">
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="van">Van</option>
                            <option value="limousine">Limousine</option>
                            <option value="rideshare">Ride Share</option>
                        </select>
                    </label>
                    <label>
                        <span>Số chỗ</span>
                        <input type="number" name="seats" value="4" min="1" max="16" />
                    </label>
                    <label>
                        <span>Giá cơ bản</span>
                        <input type="number" name="base_price" value="1200000" min="0" />
                    </label>
                </div>
                <button type="submit" class="primary-btn">Thêm xe</button>
            </form>

            <?php foreach ($vehicles as $vehicle): ?>
                <div class="vehicle-card">
                    <div class="vehicle-thumb"></div>
                    <div>
                        <strong><?= htmlspecialchars((string) ($vehicle['name'] ?? 'Xe dịch vụ'), ENT_QUOTES, 'UTF-8'); ?></strong>
                        <p><?= htmlspecialchars(implode(' • ', (array) ($vehicle['features'] ?? ['An toàn', 'Tiện nghi'])), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <span><?= htmlspecialchars((string) ($vehicle['price'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
