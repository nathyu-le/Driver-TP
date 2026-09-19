<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$verificationMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['driver_verification_submit'])) {
    $driverId = (int) ($_POST['driver_id'] ?? 0);
    $status = safeString($_POST['status'] ?? 'pending');

    $result = updateDriverVerificationStatus($driverId, $status);
    $verificationMessage = $result['message'];
}

$drivers = getDriverVerificationList();
$pageTitle = 'Quản lý xác minh tài xế';
require __DIR__ . '/../includes/header.php';
?>

<main class="admin-shell container">
    <aside class="admin-sidebar">
        <div class="admin-brand">Airport Transfer</div>
        <nav>
            <a href="/admin/dashboard.php">Dashboard</a>
            <a href="/admin/bookings.php">Đặt xe</a>
            <a href="/admin/routes.php">Tuyến đường</a>
            <a href="/admin/vehicles.php">Xe & tài xế</a>
            <a class="active" href="/admin/drivers.php">Tài xế</a>
            <a href="/logout.php">Đăng xuất</a>
        </nav>
    </aside>

    <section class="admin-main">
        <div class="admin-topbar">
            <div>
                <p class="eyebrow">Xác minh</p>
                <h1>Tài xế</h1>
            </div>
        </div>

        <div class="admin-panel">
            <?php if ($verificationMessage): ?>
                <div class="form-status success"><?= htmlspecialchars($verificationMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tài xế</th>
                        <th>Loại giấy tờ</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td>#<?= htmlspecialchars((string) ($driver['id'] ?? 'n/a'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($driver['driver_name'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) ($driver['document'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) (getVerificationStatusMachine()[$driver['status']] ?? $driver['status']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="post" action="/admin/drivers.php" class="inline-form">
                                    <input type="hidden" name="driver_verification_submit" value="1" />
                                    <input type="hidden" name="driver_id" value="<?= htmlspecialchars((string) ($driver['id'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" />
                                    <select name="status">
                                        <?php foreach (getVerificationStatusMachine() as $key => $label): ?>
                                            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?= (($driver['status'] ?? 'pending') === $key) ? 'selected' : ''; ?>>
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
