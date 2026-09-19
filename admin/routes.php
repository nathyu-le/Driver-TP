<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$routeMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['route_submit'])) {
    $result = createRoute($_POST);
    $routeMessage = $result['message'];
}

$routes = getRoutesList();
$pricingRules = getPricingRules();
$pageTitle = 'Quản lý tuyến đường';
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/bookings.php">Đặt xe</a>
            <a class="active" href="/admin/routes.php">Tuyến đường</a>
            <a href="/admin/vehicles.php">Xe & tài xế</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Quản lý</p>
                <h1>Tuyến đường</h1>
            </div>
            <button class="primary-btn" type="button">Thêm tuyến</button>
        </div>

        <div class="admin-panel">
            <?php if ($routeMessage): ?>
                <div class="form-status success"><?= htmlspecialchars($routeMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post" action="/admin/routes.php" class="admin-form compact-form">
                <input type="hidden" name="route_submit" value="1" />
                <div class="two-col-grid">
                    <label>
                        <span>Tên tuyến</span>
                        <input type="text" name="name" placeholder="Ví dụ: Đà Nẵng → Hội An" required />
                    </label>
                    <label>
                        <span>Điểm đi</span>
                        <input type="text" name="start_point" placeholder="Sân bay Đà Nẵng" required />
                    </label>
                    <label>
                        <span>Điểm đến</span>
                        <input type="text" name="end_point" placeholder="Phố cổ Hội An" required />
                    </label>
                    <label>
                        <span>Thời gian (phút)</span>
                        <input type="number" name="duration_minutes" value="60" min="15" />
                    </label>
                    <label>
                        <span>Giá cơ bản</span>
                        <input type="number" name="base_price" value="1200000" min="0" />
                    </label>
                </div>
                <button type="submit" class="primary-btn">Thêm tuyến</button>
            </form>

            <div class="route-table-wrap">
                <?php foreach ($routes as $route): ?>
                    <div class="route-row">
                        <div>
                            <strong><?= htmlspecialchars((string) ($route['title'] ?? $route['name'] ?? 'Tuyến đường'), ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small><?= htmlspecialchars((string) ($route['duration'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                        <span><?= htmlspecialchars((string) ($route['price'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pricing-rules-wrap">
                <h3>Quy tắc giá theo loại xe</h3>
                <?php foreach ($pricingRules as $rule): ?>
                    <div class="route-row">
                        <div>
                            <strong><?= htmlspecialchars((string) ($rule['vehicle_type'] ?? 'vehicle'), ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small>Base <?= number_format((float) ($rule['base_fee'] ?? 0), 0, ',', '.'); ?>đ</small>
                        </div>
                        <span><?= number_format((float) ($rule['per_km_rate'] ?? 0), 0, ',', '.'); ?>đ/km</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
