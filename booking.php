<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Đặt xe - Airport Transfer';
$result = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = handleBookingSubmission($_POST);
}

require __DIR__ . '/includes/header.php';
?>

<main class="page-shell container">
    <section class="simple-page reveal">
        <div class="section-header">
            <p>Đặt xe</p>
            <h2>Yêu cầu của bạn đã được tiếp nhận.</h2>
        </div>

        <?php if ($result['success']): ?>
            <div class="form-status success"><?= htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="form-status error"><?= htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
