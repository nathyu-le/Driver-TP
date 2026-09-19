<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireCustomerLogin();

$pageTitle = 'Dashboard khách hàng';
$trips = getCustomerTrips();
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a class="active" href="/customer/dashboard.php">Dashboard</a>
            <a href="/index.php#booking">Đặt xe</a>
            <a href="/routes.php">Tuyến đường</a>
            <a href="/pricing.php">Bảng giá</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Khách hàng</p>
                <h1>Chuyến đi của tôi</h1>
            </div>
        </div>

        <div class="admin-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Điểm đón</th>
                        <th>Điểm đến</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string) ($trip['id'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['pickup'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['destination'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['price'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="status-pill"><?= htmlspecialchars((string) ($trip['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
