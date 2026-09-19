<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
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
            <div class="route-table-wrap">
                <?php foreach (getSampleRoutes() as $route): ?>
                    <div class="route-row">
                        <div>
                            <strong><?= htmlspecialchars($route['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small><?= htmlspecialchars($route['duration'], ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                        <span><?= htmlspecialchars($route['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
