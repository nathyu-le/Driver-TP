<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$flashMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = safeString($_POST['status'] ?? '');

    if ($bookingId > 0 && $status !== '') {
        $result = updateBookingStatus($bookingId, $status);
        $flashMessage = $result['message'] ?? 'Cập nhật trạng thái thành công.';
    }
}

$bookings = getBookingsList();
$pageTitle = 'Quản lý booking';
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a href="/admin/dashboard.php">Dashboard</a>
            <a class="active" href="/admin/bookings.php">Đặt xe</a>
            <a href="/admin/routes.php">Tuyến đường</a>
            <a href="/admin/vehicles.php">Xe & tài xế</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Quản lý</p>
                <h1>Booking</h1>
            </div>
        </div>

        <div class="admin-panel">
            <?php if ($flashMessage): ?>
                <div class="form-status success"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Điểm đón</th>
                        <th>Điểm đến</th>
                        <th>Ngày</th>
                        <th>Hành khách</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <?php $currentStatus = (string) ($booking['status'] ?? 'pending'); ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string) ($booking['id'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($booking['pickup_location'] ?? $booking['pickup'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($booking['destination_location'] ?? $booking['destination'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($booking['travel_date'] ?? $booking['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($booking['passenger_count'] ?? $booking['passengers'] ?? '2'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="status-pill"><?= htmlspecialchars((string) ($currentStatus), ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <form method="post" action="/admin/bookings.php" class="inline-form">
                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars((string) ($booking['id'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" />
                                    <select name="status">
                                        <?php foreach (getBookingStatusMachine() as $key => $label): ?>
                                            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?= $currentStatus === $key ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="small-btn">Cập nhật</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
