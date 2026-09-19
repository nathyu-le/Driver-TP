<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = safeString($_POST['phone'] ?? '');
    $password = safeString($_POST['password'] ?? '');

    $user = verifyDriverCredentials($phone, $password);
    if ($user !== null) {
        loginDriver($user);
        redirect('/driver/dashboard.php');
    }

    $error = 'Số điện thoại hoặc mật khẩu không đúng.';
}

$pageTitle = 'Đăng nhập tài xế';
require __DIR__ . '/../includes/header.php';
?>

<main class="auth-page">
    <div class="auth-box reveal">
        <div class="auth-header-block">
            <p>Driver portal</p>
            <h2>Đăng nhập tài xế</h2>
        </div>

        <?php if ($error): ?>
            <div class="form-status error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" action="/driver/login.php" class="admin-form">
            <label>
                <span>Số điện thoại</span>
                <input type="tel" name="phone" value="0909000001" required />
            </label>
            <label>
                <span>Mật khẩu</span>
                <input type="password" name="password" value="driver123" required />
            </label>
            <button type="submit" class="primary-btn full-width">Đăng nhập</button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
