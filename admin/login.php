<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = safeString($_POST['username'] ?? '');
    $password = safeString($_POST['password'] ?? '');

    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = ['name' => 'System Admin'];
        redirect('/admin/dashboard.php');
    }

    $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
}

$pageTitle = 'Đăng nhập quản trị';
require __DIR__ . '/../includes/header.php';
?>

<main class="auth-page">
    <div class="auth-box reveal">
        <div class="auth-header-block">
            <p>Admin access</p>
            <h2>Đăng nhập quản trị</h2>
        </div>

        <?php if ($error): ?>
            <div class="form-status error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" action="/admin/login.php" class="admin-form">
            <label>
                <span>Tài khoản</span>
                <input type="text" name="username" value="admin" required />
            </label>
            <label>
                <span>Mật khẩu</span>
                <input type="password" name="password" value="admin123" required />
            </label>
            <button type="submit" class="primary-btn full-width">Đăng nhập</button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
