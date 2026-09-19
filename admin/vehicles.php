<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
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
            <?php foreach (getSampleFleet() as $vehicle): ?>
                <div class="vehicle-card">
                    <div class="vehicle-thumb"></div>
                    <div>
                        <strong><?= htmlspecialchars($vehicle['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <p><?= htmlspecialchars(implode(' • ', $vehicle['features']), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <span><?= htmlspecialchars($vehicle['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
