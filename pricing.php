<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Bảng giá - Airport Transfer';
require __DIR__ . '/includes/header.php';
?>

<main class="page-shell container">
    <section class="simple-page reveal">
        <div class="section-header">
            <p>Bảng giá</p>
            <h2>Giá rõ ràng, dễ chọn và minh bạch.</h2>
        </div>

        <div class="pricing-grid">
            <article class="pricing-card">
                <span class="pricing-tag">Tiêu chuẩn</span>
                <h3>Xe 4 chỗ</h3>
                <div class="price">1.200.000đ</div>
                <ul>
                    <li>Đón sân bay nội thành</li>
                    <li>Hỗ trợ 1 hành lý</li>
                    <li>Giá đã bao gồm VAT</li>
                </ul>
            </article>
            <article class="pricing-card featured">
                <span class="pricing-tag">Phổ biến</span>
                <h3>Xe 7 chỗ</h3>
                <div class="price">1.700.000đ</div>
                <ul>
                    <li>Phù hợp gia đình và nhóm nhỏ</li>
                    <li>Không gian hành lý rộng</li>
                    <li>Hỗ trợ ưu tiên khứ hồi</li>
                </ul>
            </article>
            <article class="pricing-card">
                <span class="pricing-tag">Cao cấp</span>
                <h3>Limousine</h3>
                <div class="price">2.600.000đ</div>
                <ul>
                    <li>Nội thất da cao cấp</li>
                    <li>Chấp nhận đặt xe riêng</li>
                    <li>Phục vụ doanh nghiệp</li>
                </ul>
            </article>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
