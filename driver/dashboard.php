<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireDriverLogin();

$pageTitle = 'Dashboard tài xế';
$trips = getDriverTrips();
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a class="active" href="/driver/dashboard.php">Dashboard</a>
            <a href="/driver/login.php">Trạng thái</a>
            <a href="/admin/drivers.php">Xác minh</a>
            <a href="/pricing.php">Bảng giá</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Tài xế</p>
                <h1>Chuyến đang nhận</h1>
            </div>
        </div>

        <div class="admin-panel">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Điểm đón</th>
                        <th>Điểm đến</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string) ($trip['id'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['customer'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['pickup'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($trip['destination'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="status-pill"><?= htmlspecialchars((string) ($trip['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
