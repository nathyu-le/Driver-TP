<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Dashboard - Admin';
$stats = getDashboardStats();
$bookings = getBookingsList();
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a class="active" href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/bookings.php">Đặt xe</a>
            <a href="/admin/routes.php">Tuyến đường</a>
            <a href="/admin/vehicles.php">Xe & tài xế</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Tổng quan</p>
                <h1>Dashboard</h1>
            </div>
            <a href="/admin/login.php" class="primary-btn">Xem hệ thống</a>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <span>Tổng booking</span>
                <strong><?= htmlspecialchars((string) $stats['total_bookings'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="admin-card">
                <span>Hôm nay</span>
                <strong><?= htmlspecialchars((string) $stats['today_bookings'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="admin-card">
                <span>Xe hoạt động</span>
                <strong><?= htmlspecialchars((string) $stats['active_vehicles'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="admin-card accent">
                <span>Doanh thu</span>
                <strong><?= htmlspecialchars((string) $stats['revenue'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        </div>

        <div class="admin-panel">
            <div class="panel-header">
                <h3>Booking gần đây</h3>
                <a href="/admin/bookings.php">Xem tất cả</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Điểm đón</th>
                        <th>Điểm đến</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $row): ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string) ($row['id'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($row['pickup_location'] ?? $row['pickup'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($row['destination_location'] ?? $row['destination'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($row['travel_date'] ?? $row['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="status-pill"><?= htmlspecialchars((string) ($row['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
