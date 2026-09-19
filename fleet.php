<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Đội xe - Airport Transfer';
require __DIR__ . '/includes/header.php';
?>

<main class="page-shell container">
    <section class="simple-page reveal">
        <div class="section-header">
            <p>Đội xe</p>
            <h2>Phương tiện phù hợp với từng hành trình.</h2>
        </div>

        <div class="fleet-grid fleet-grid-large">
            <?php foreach (getSampleFleet() as $vehicle): ?>
                <article class="fleet-card">
                    <div class="fleet-image car-sedan"></div>
                    <div class="fleet-body">
                        <h3><?= htmlspecialchars($vehicle['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <ul>
                            <?php foreach ($vehicle['features'] as $feature): ?>
                                <li><?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="fleet-meta"><strong><?= htmlspecialchars($vehicle['price'], ENT_QUOTES, 'UTF-8'); ?></strong><button type="button">Xem</button></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
