<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Airport Transfers';
$message = '';
$booking = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_submit'])) {
    $result = handleBookingSubmission($_POST);
    $message = $result['message'];
    $booking = $result['booking'] ?? null;
}

$data = getHomepageData();
require __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="hero-section container">
        <div class="hero-panel reveal">
            <div class="hero-content">
                <p class="mini-tag">Chuyến xe sân bay cao cấp</p>
                <h1>Di chuyển thông minh với trải nghiệm đẳng cấp.</h1>
                <p class="hero-text">
                    Đặt xe nhanh, đúng giờ và tiện nghi cho mọi hành trình, từ sân bay đến trung tâm thành phố và các tuyến quốc lộ.
                </p>
                <div class="hero-cta-row">
                    <a href="#booking" class="primary-btn">Đặt xe ngay</a>
                    <a href="/services.php" class="secondary-btn">Khám phá dịch vụ</a>
                </div>

                <div class="trust-row" aria-label="Trust indicators">
                    <span>✓ Đúng giờ</span>
                    <span>✓ Xe sạch</span>
                    <span>✓ Hỗ trợ 24/7</span>
                </div>
            </div>

            <div class="hero-visual" aria-hidden="true">
                <div class="hero-badge-card">
                    <span>Hỗ trợ 24/7</span>
                    <strong>Đánh giá 4.9/5</strong>
                </div>
                <div class="person-card">
                    <div class="person-avatar"></div>
                    <div class="person-badge">at</div>
                </div>
                <div class="car-visual"></div>
            </div>
        </div>

        <div class="trip-toggle reveal" aria-label="Booking mode">
            <button class="toggle active" type="button">Một chiều</button>
            <button class="toggle" type="button">Khứ hồi</button>
        </div>

        <form id="booking" class="booking-bar reveal" method="post" action="/index.php#booking">
            <input type="hidden" name="booking_submit" value="1" />

            <div class="input-block">
                <label>Điểm đón</label>
                <div class="input-field">
                    <span class="field-icon field-pick"></span>
                    <input type="text" name="pickup" value="Sân bay Đà Nẵng" aria-label="Điểm đón" required />
                </div>
            </div>

            <button class="swap-btn" type="button" aria-label="Đổi điểm">⇄</button>

            <div class="input-block">
                <label>Điểm đến</label>
                <div class="input-field">
                    <span class="field-icon field-drop"></span>
                    <input type="text" name="destination" value="Phố cổ Hội An" aria-label="Điểm đến" required />
                </div>
            </div>

            <div class="input-block">
                <label>Giờ khởi hành</label>
                <div class="input-field">
                    <span class="field-icon field-date"></span>
                    <input type="text" name="travel_date" value="10/02/2026 12:00" aria-label="Giờ khởi hành" />
                </div>
            </div>

            <div class="input-block">
                <label>Hành khách</label>
                <div class="input-field">
                    <span class="field-icon field-passenger"></span>
                    <input type="number" name="passengers" value="2" min="1" max="12" aria-label="Hành khách" />
                </div>
            </div>

            <button class="search-btn" type="submit">Tìm chuyến</button>
        </form>

        <?php if ($message): ?>
            <div class="form-status <?= $booking ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="stats-strip container reveal">
        <?php foreach ($data['stats'] as $stat): ?>
            <div class="stat-item">
                <strong><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="why-choose container reveal">
        <div class="section-header">
            <p>Vì sao chọn chúng tôi</p>
            <h2>Vận chuyển tiện nghi, đúng giờ, chạm tay là xong.</h2>
        </div>

        <div class="showcase-layout">
            <div class="showcase-panel">
                <div class="showcase-topbar">
                    <span>Tổng quan đặt xe</span>
                    <span class="status-tag">Đã xác nhận</span>
                </div>

                <div class="summary-grid">
                    <div>
                        <label>Tuyến</label>
                        <strong>Đà Nẵng → Hội An</strong>
                    </div>
                    <div>
                        <label>Phương tiện</label>
                        <strong>Sedan cao cấp</strong>
                    </div>
                    <div>
                        <label>Thời gian</label>
                        <strong>09:45 SA</strong>
                    </div>
                    <div>
                        <label>Giá</label>
                        <strong>1.200.000đ</strong>
                    </div>
                </div>

                <div class="driver-row">
                    <div class="driver-avatar"></div>
                    <div>
                        <strong>Tài xế Nguyễn</strong>
                        <small>Chauffeur chuyên nghiệp</small>
                    </div>
                </div>
            </div>

            <div class="benefit-grid">
                <article class="benefit-card">
                    <div class="benefit-icon">01</div>
                    <h3>Giá minh bạch</h3>
                    <p>Rõ ràng trước khi đặt.</p>
                </article>
                <article class="benefit-card">
                    <div class="benefit-icon">02</div>
                    <h3>Tài xế chuyên nghiệp</h3>
                    <p>Đúng giờ, lịch sự và thân thiện.</p>
                </article>
                <article class="benefit-card">
                    <div class="benefit-icon">03</div>
                    <h3>Hỗ trợ chuyến bay</h3>
                    <p>Điều chỉnh lịch đón theo thời gian thực.</p>
                </article>
                <article class="benefit-card">
                    <div class="benefit-icon">04</div>
                    <h3>Đặt xe linh hoạt</h3>
                    <p>Đặc biệt cho sân bay và liên tỉnh.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="routes-section container reveal">
        <div class="section-header compact">
            <p>Tuyến phổ biến</p>
            <h2>Những chặng đường được khách hàng yêu thích.</h2>
        </div>

        <div class="route-grid">
            <?php foreach ($data['routes'] as $route): ?>
                <article class="route-card">
                    <span class="route-badge">Sân bay</span>
                    <h3><?= htmlspecialchars($route['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <div class="route-meta"><span><?= htmlspecialchars($route['duration'], ENT_QUOTES, 'UTF-8'); ?></span><span><?= htmlspecialchars($route['price'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <button type="button">Đặt ngay</button>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="fleet-section container reveal">
        <div class="section-header compact">
            <p>Đội xe</p>
            <h2>Chọn xe theo nhu cầu.</h2>
        </div>

        <div class="fleet-grid">
            <?php foreach ($data['fleet'] as $vehicle): ?>
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

    <section class="steps-section container reveal">
        <div class="section-header compact">
            <p>Quy trình</p>
            <h2>Đặt xe dễ như vậy.</h2>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <span>01</span>
                <h3>Chọn tuyến</h3>
                <p>Chọn điểm đón, điểm đến và thời gian di chuyển.</p>
            </div>
            <div class="step-card">
                <span>02</span>
                <h3>Chọn xe</h3>
                <p>Lựa chọn phương tiện phù hợp với nhóm và ngân sách.</p>
            </div>
            <div class="step-card">
                <span>03</span>
                <h3>Xác nhận</h3>
                <p>Kiểm tra chi tiết, thanh toán an toàn và nhận xác nhận ngay.</p>
            </div>
            <div class="step-card">
                <span>04</span>
                <h3>Trải nghiệm</h3>
                <p>Đồng hành cùng tài xế và theo dõi vị trí xe theo thời gian thực.</p>
            </div>
        </div>
    </section>

    <section class="testimonial-section container reveal">
        <div class="section-header compact">
            <p>Khách hàng nói gì</p>
            <h2>Khách tin tưởng vì quá rõ ràng.</h2>
        </div>

        <div class="testimonial-grid">
            <article class="quote-card">
                <div class="stars">★★★★★</div>
                <p>“Tài xế rất lịch sự, xe sạch sẽ và đặt xe rất nhanh. Đây là lựa chọn đáng tin cậy cho chuyến sân bay.”</p>
                <div class="person">Linh N.</div>
            </article>
            <article class="quote-card">
                <div class="stars">★★★★★</div>
                <p>“Chúng tôi dùng cho chuyến công tác và mọi thứ đều đúng giờ, chuyên nghiệp từ lúc đón đến lúc trả khách.”</p>
                <div class="person">Daniel C.</div>
            </article>
            <article class="quote-card">
                <div class="stars">★★★★★</div>
                <p>“Xe sang, dịch vụ tốt, và bộ phận hỗ trợ phản hồi rất nhanh khi có thay đổi lịch trình.”</p>
                <div class="person">Minh P.</div>
            </article>
        </div>
    </section>

    <section class="pricing-section container reveal">
        <div class="section-header compact">
            <p>Bảng giá</p>
            <h2>Mức giá rõ ràng, dễ chọn.</h2>
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

    <section class="faq-section container reveal">
        <div class="section-header compact">
            <p>Câu hỏi thường gặp</p>
            <h2>Thông tin nhanh để bạn dễ quyết định.</h2>
        </div>

        <div class="faq-list">
            <details open>
                <summary>Đặt xe có cần thanh toán trước không?</summary>
                <p>Bạn có thể thanh toán trước hoặc đặt cọc tùy phương thức, và hệ thống sẽ xác nhận nhanh qua tin nhắn hoặc email.</p>
            </details>
            <details>
                <summary>Xe có hỗ trợ trẻ em hoặc hành lý lớn không?</summary>
                <p>Chúng tôi hỗ trợ ghế trẻ em, hành lý lớn và lựa chọn xe theo số hành khách, bạn có thể thông báo khi đặt.</p>
            </details>
            <details>
                <summary>Nếu chuyến bay bị chậm trễ thì sao?</summary>
                <p>Hệ thống sẽ tự động cập nhật thời gian đón và tài xế sẽ chờ theo thông tin thực tế của hành trình nếu có thay đổi.</p>
            </details>
            <details>
                <summary>Chuyến đi liên tỉnh có được đặt tối đa bao nhiêu người?</summary>
                <p>Phụ thuộc từng dòng xe, nhưng bạn có thể chọn từ 4 chỗ, 7 chỗ hoặc xe phù hợp nhóm từ 10–16 chỗ theo nhu cầu.</p>
            </details>
        </div>
    </section>

    <section class="cta-section container reveal">
        <div class="cta-box">
            <div>
                <p>Thuê xe riêng</p>
                <h2>Đặt xe nhanh trong 2 phút.</h2>
            </div>
            <a href="#booking" class="primary-btn">Bắt đầu</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
