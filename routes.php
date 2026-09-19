<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Tuyến đường - Airport Transfer';
require __DIR__ . '/includes/header.php';
?>

<main class="page-shell container">
    <section class="simple-page reveal">
        <div class="section-header">
            <p>Tuyến đường</p>
            <h2>Những tuyến phổ biến và đáng tin cậy.</h2>
        </div>

        <div class="route-grid route-grid-large">
            <?php foreach (getSampleRoutes() as $route): ?>
                <article class="route-card">
                    <span class="route-badge">Đường bay</span>
                    <h3><?= htmlspecialchars($route['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <div class="route-meta"><span><?= htmlspecialchars($route['duration'], ENT_QUOTES, 'UTF-8'); ?></span><span><?= htmlspecialchars($route['price'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <button type="button">Đặt ngay</button>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
