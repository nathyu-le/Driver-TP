<?php

declare(strict_types=1);

if (!isset($pageTitle)) {
    $pageTitle = siteName();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="Premium transportation booking website for airport and intercity transfer services." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/app.css" />
</head>
<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a href="/index.php" class="brand" aria-label="Airport transfer brand">
                <div class="brand-mark">at</div>
                <div class="brand-wordmark">
                    <span>airport</span>
                    <small>transfer.com</small>
                </div>
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="/index.php">Trang chủ</a>
                <a href="/services.php">Dịch vụ</a>
                <a href="/routes.php">Tuyến đường</a>
                <a href="/fleet.php">Đội xe</a>
                <a href="/pricing.php">Bảng giá</a>
                <a href="/contact.php">Liên hệ</a>
            </nav>

            <div class="nav-tools">
                <button class="lang-btn" type="button">VI</button>
                <button class="ghost-search" type="button">Tìm kiếm</button>
                <a href="/admin/login.php" class="sign-in">Đăng nhập</a>
                <a href="/index.php#booking" class="cta-pill" aria-label="Đặt xe ngay">→</a>
            </div>
        </div>
    </header>
