<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = safeString($_POST['email'] ?? '');
    $password = safeString($_POST['password'] ?? '');

    $user = verifyCustomerCredentials($email, $password);
    if ($user !== null) {
        loginCustomer($user);
        redirect('/customer/dashboard.php');
    }

    $error = 'Email hoặc mật khẩu không đúng.';
}

$pageTitle = 'Đăng nhập khách hàng';
require __DIR__ . '/../includes/header.php';
?>

<main class="auth-page">
    <div class="auth-box reveal">
        <div class="auth-header-block">
            <p>Customer portal</p>
            <h2>Đăng nhập khách hàng</h2>
        </div>

        <?php if ($error): ?>
            <div class="form-status error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" action="/customer/login.php" class="admin-form">
            <label>
                <span>Email</span>
                <input type="email" name="email" value="customer@demo.com" required />
            </label>
            <label>
                <span>Mật khẩu</span>
                <input type="password" name="password" value="customer123" required />
            </label>
            <button type="submit" class="primary-btn full-width">Đăng nhập</button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
