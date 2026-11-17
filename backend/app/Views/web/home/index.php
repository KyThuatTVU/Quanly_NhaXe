<section class="hero">
    <div class="container hero-inner">
        <div class="hero-copy">
            <span class="eyebrow">Xe hợp đồng · Đưa đón sân bay · Du lịch · Cưới hỏi</span>
            <h1>Thuê xe hợp đồng<br>an toàn, đúng giờ, giá rõ ràng</h1>
            <p>Đội xe 4 - 45 chỗ đời mới, tài xế nhiều năm kinh nghiệm, phục vụ 24/7 tại Hà Nội và các tỉnh lân cận.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= url('/dat-xe') ?>">Đặt xe ngay</a>
                <a class="btn btn-outline" href="<?= url('/xe') ?>">Xem danh sách xe</a>
            </div>
            <ul class="hero-points">
                <li>Xe đời mới, bảo dưỡng định kỳ</li>
                <li>Tài xế chuyên nghiệp, thân thiện</li>
                <li>Báo giá minh bạch trước chuyến đi</li>
            </ul>
        </div>
        <aside class="hero-card">
            <h3>Hotline đặt xe</h3>
            <p class="hero-hotline"><?= e($site['hotline'] ?? $site['phone'] ?? '') ?></p>
            <p class="muted small"><?= e($site['working_hours'] ?? '') ?></p>
            <hr>
            <p class="small">Đã có mã đơn? Tra cứu trạng thái đơn đặt xe của bạn.</p>
            <a class="btn btn-outline btn-block" href="<?= url('/tra-cuu-don') ?>">Tra cứu đơn</a>
        </aside>
    </div>
</section>

<!-- Video giới thiệu xe -->
<section class="section section--video">
    <div class="container">
        <div class="video-intro-head text-center">
            <span class="video-eyebrow">VIDEO GIỚI THIỆU</span>
            <h2 class="video-heading">VINFAST VF6 PLUS - XE ĐIỆN HIỆN ĐẠI</h2>
        </div>
        <div class="video-player-wrap">
            <div class="video-player-card" id="car-video-card">
                <video
                    id="car-video-player"
                    controls
                    playsinline
                    preload="metadata">
                    <source src="<?= asset('img/logo/xe.mp4') ?>" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ phát định dạng video này.
                </video>
            </div>
            <div class="video-feature-note">
                <span class="note-icon" aria-hidden="true">⚠️</span>
                <span>Video giới thiệu xe điện VinFast VF6 Plus - Công nghệ hiện đại, thân thiện môi trường, trải nghiệm đẳng cấp.</span>
            </div>
        </div>
    </div>
</section>

<!-- Khẳng định sự khác biệt với dịch vụ vượt trội -->
<section class="section section--why-us" id="why-choose-us">
    <div class="container">
        <div class="why-us-grid">
            <!-- Cột trái: Giới thiệu & Điểm vượt trội -->
            <div class="why-us-content">
                <span class="why-us-eyebrow">TẠI SAO CHỌN CHÚNG TÔI?</span>
                <h2 class="why-us-title">KHẲNG ĐỊNH SỰ KHÁC BIỆT<br>VỚI DỊCH VỤ VƯỢT TRỘI</h2>

                <ul class="why-us-list">
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Xe đời mới, sạch đẹp</strong> - 100% bảo trì định kỳ, đảm bảo an toàn</span>
                    </li>
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Giá cả cạnh tranh</strong> - Hợp đồng minh bạch, không phụ phí ẩn</span>
                    </li>
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Đa dạng xe từ 4 - 45 chỗ</strong> - Đáp ứng mọi nhu cầu, xe chuyên nghiệp</span>
                    </li>
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Khách hàng &amp; doanh nghiệp lớn tin tưởng</strong> - Cam kết chất lượng hàng đầu</span>
                    </li>
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Dịch vụ 24/7</strong> - Đặt xe mọi lúc, hỗ trợ nhanh chóng</span>
                    </li>
                    <li class="why-us-item">
                        <span class="why-us-check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span><strong>Bảo hiểm đầy đủ, an tâm di chuyển</strong> - Mọi chuyến đi đều có bảo hiểm</span>
                    </li>
                </ul>

                <div class="why-us-action">
                    <a class="why-us-btn" href="<?= url('/dat-xe') ?>">ĐẶT XE NGAY</a>
                </div>
            </div>

            <!-- Cột phải: Card hiển thị nổi bật -->
            <div class="why-us-visual">
                <div class="why-us-card">
                    <div class="why-us-card-logo">
                        <img src="<?= asset('img/logo/logo_chinh_256.png') ?>" alt="Nhà xe Thảo Vy" width="42" height="42" loading="lazy">
                    </div>

                    <div class="why-us-pills">
                        <div class="why-us-pill">
                            <span class="why-us-pill-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.3c.4-.2.6-.6.5-1.1z"/>
                                </svg>
                            </span>
                            <span class="why-us-pill-text">ĐƯA ĐÓN SÂN BAY</span>
                        </div>

                        <div class="why-us-pill">
                            <span class="why-us-pill-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C2.1 10.7 2 10.8 2 11v5c0 .6.4 1 1 1h2"/>
                                    <circle cx="7" cy="17" r="2"/>
                                    <path d="M9 17h6"/>
                                    <circle cx="17" cy="17" r="2"/>
                                </svg>
                            </span>
                            <span class="why-us-pill-text">ĐẶT XE ĐI LIÊN TỈNH</span>
                        </div>

                        <div class="why-us-pill">
                            <span class="why-us-pill-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </span>
                            <span class="why-us-pill-text">HẸN ĐÚNG GIỜ</span>
                        </div>

                        <div class="why-us-pill">
                            <span class="why-us-pill-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                                </svg>
                            </span>
                            <span class="why-us-pill-text">TƯ VẤN ONLINE 24/7</span>
                        </div>
                    </div>

                    <div class="why-us-car-frame">
                        <img src="<?= asset('img/cars/vf3.png') ?>" 
                             alt="VinFast VF3 hiện đại - Đội xe Thảo Vy" 
                             class="why-us-car-img" 
                             width="1672" 
                             height="941" 
                             loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Danh mục xe</span>
                <h2>Chọn loại xe phù hợp</h2>
            </div>
            <a class="link-more" href="<?= url('/xe') ?>">Tất cả xe →</a>
        </div>
        <div class="grid grid-4">
            <?php foreach ($types as $type): ?>
                <a class="type-card" href="<?= url('/xe') ?>?vehicle_type_id=<?= (int) $type['id'] ?>">
                    <span class="type-icon"><?= e($type['icon'] ?? '🚐') ?></span>
                    <h3><?= e($type['name']) ?></h3>
                    <p class="muted small">
                        <?= (int) $type['seats'] ?> chỗ
                        <?php if (!empty($type['seats_min']) && !empty($type['seats_max'])): ?>
                            · <?= (int) $type['seats_min'] ?>-<?= (int) $type['seats_max'] ?> chỗ
                        <?php endif; ?>
                    </p>
                    <p class="price small">Từ <?= e(\App\Support\Format::money($type['price_per_day'] ?? null)) ?>/ngày</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Đội xe nổi bật</span>
                <h2>Xe sẵn sàng phục vụ</h2>
            </div>
            <a class="link-more" href="<?= url('/xe') ?>">Xem tất cả →</a>
        </div>
        <div class="grid grid-3">
            <?php foreach ($cars as $car): ?>
                <?php $view->partial('partials/car-card', ['car' => $car, 'site' => $site]); ?>
            <?php endforeach; ?>
            <?php if ($cars === []): ?>
                <p class="muted">Hiện chưa có xe sẵn sàng. Vui lòng liên hệ hotline để được hỗ trợ.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Dịch vụ</span>
                <h2>Dịch vụ vận chuyển của chúng tôi</h2>
            </div>
            <a class="link-more" href="<?= url('/dich-vu') ?>">Tất cả dịch vụ →</a>
        </div>
        <div class="grid grid-4">
            <?php foreach ($services as $service): ?>
                <?php $view->partial('partials/service-card', ['service' => $service]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Đánh giá từ khách hàng -->
<section class="section section--reviews" id="customer-reviews">
    <div class="container">
        <div class="reviews-head">
            <span class="reviews-eyebrow">ĐÁNH GIÁ TỪ KHÁCH HÀNG</span>
            <h2 class="reviews-heading">KHÁCH HÀNG HÀI LÒNG NÓI GÌ VỀ CHÚNG TÔI?</h2>
        </div>
        <div class="reviews-grid">
            <div class="review-card">
                <div class="review-avatar-wrap">
                    <img src="<?= asset('img/home/avatar-tuyen-le.png') ?>" alt="Tuyền Lê" class="review-avatar" width="58" height="58" loading="lazy">
                </div>
                <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                <h3 class="review-name">Tuyền Lê</h3>
                <p class="review-quote">“Dịch vụ thuê xe này tôi dùng hoài mà vẫn mê: giá cạnh tranh, xe đời mới sáng bóng, sạch sẽ như mới.”</p>
            </div>

            <div class="review-card">
                <div class="review-avatar-wrap">
                    <img src="<?= asset('img/home/avatar-bich-thao.png') ?>" alt="Bích Thảo" class="review-avatar" width="58" height="58" loading="lazy">
                </div>
                <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                <h3 class="review-name">Bích Thảo</h3>
                <p class="review-quote">“Tôi thường hay đi công tác liên tỉnh nên luôn sử dụng dịch vụ thuê xe hợp đồng này luôn uy tín và đúng hẹn…”</p>
            </div>

            <div class="review-card">
                <div class="review-avatar-wrap">
                    <img src="<?= asset('img/home/avatar-anh-minh.png') ?>" alt="ANH MINH, TP.HCM" class="review-avatar" width="58" height="58" loading="lazy">
                </div>
                <div class="review-stars" aria-label="5 trên 5 sao">★★★★★</div>
                <h3 class="review-name">ANH MINH, TP.HCM</h3>
                <p class="review-quote">“Dịch vụ thuê xe rất chuyên nghiệp, tài xế thân thiện và lái xe an toàn. Xe mới, sạch sẽ, đúng giờ. Tôi sẽ tiếp tục sử dụng dịch vụ này!”</p>
            </div>
        </div>
    </div>
</section>

<!-- Đối tác & Khách hàng -->
<section class="section--partners" id="our-partners">
    <div class="container">
        <div class="partners-head">
            <span class="partners-sub">ĐỐI TÁC CỦA CHÚNG TÔI</span>
            <h2 class="partners-title">VÀ KHÁCH HÀNG</h2>
        </div>
        <div class="partners-grid">
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-shb.png') ?>" alt="SHB" loading="lazy">
            </div>
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-daisan.png') ?>" alt="DAISAN" loading="lazy">
            </div>
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-fpt.png') ?>" alt="FPT" loading="lazy">
            </div>
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-hsbc.png') ?>" alt="HSBC" loading="lazy">
            </div>
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-sunwell.png') ?>" alt="SUNWELL" loading="lazy">
            </div>
            <div class="partner-card">
                <img src="<?= asset('img/partners/partner-epevno.png') ?>" alt="EPEVNO" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Bài viết</span>
                <h2>Kinh nghiệm &amp; tin tức</h2>
            </div>
            <a class="link-more" href="<?= url('/bai-viet') ?>">Tất cả bài viết →</a>
        </div>
        <div class="grid grid-3">
            <?php foreach ($posts as $post): ?>
                <?php $view->partial('partials/post-card', ['post' => $post]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Câu hỏi thường gặp (FAQ) -->
<section class="section section--faq" id="faq-section">
    <div class="container">
        <div class="faq-head">
            <span class="faq-eyebrow">GIẢI ĐÁP THẮC MẮC</span>
            <h2 class="faq-heading">CÂU HỎI THƯỜNG GẶP (FAQ)</h2>
        </div>
        <div class="faq-wrap">
            <details class="faq-item" name="faq-accordion" open>
                <summary class="faq-question">
                    <span>Giá thuê xe được tính như thế nào?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Giá thuê xe được tính minh bạch dựa trên dòng xe (4, 7, 16, 29, 45 chỗ), lộ trình di chuyển (km), thời gian thuê (theo giờ, theo ngày hoặc dài hạn) và các yêu cầu phát sinh. Tất cả chi phí xăng dầu, lương tài xế và phí cầu đường đều được báo giá trọn gói rõ ràng trước chuyến đi, cam kết không phụ phí ẩn.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Chính sách đặt xe như thế nào?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Quý khách có thể đặt xe nhanh chóng online qua website hoặc gọi hotline 24/7. Sau khi tiếp nhận thông tin, nhà xe sẽ xác nhận hành trình, báo giá chính xác và gửi thông tin tài xế cùng biển số xe trước giờ khởi hành.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Nếu tôi muốn hủy xe thì sao?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Nhà xe hỗ trợ hủy chuyến hoặc thay đổi thời gian hoàn toàn miễn phí nếu quý khách thông báo trước giờ khởi hành tối thiểu 12 - 24 giờ. Trường hợp có phát sinh đột xuất, chúng tôi luôn nỗ lực hỗ trợ linh hoạt để đảm bảo quyền lợi tốt nhất cho bạn.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Tôi có thể thuê xe theo ngày hoặc theo tháng không?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Có. Chúng tôi cung cấp linh hoạt nhiều gói dịch vụ: thuê xe theo chuyến, theo ngày, đưa đón sân bay hoặc hợp đồng theo tháng phục vụ đưa đón học sinh, cán bộ nhân viên, chuyên gia với hợp đồng pháp lý rõ ràng và chiết khấu ưu đãi.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Tôi có thể tự lái xe không?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Hiện tại Thảo Vy chuyên cung cấp dịch vụ thuê xe hợp đồng kèm tài xế chuyên nghiệp, giàu kinh nghiệm nhằm đảm bảo an toàn tuyệt đối, đúng giờ và giúp quý khách hoàn toàn thư giãn trong suốt hành trình.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Khi nhận xe, tôi cần chuẩn bị giấy tờ gì?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Với dịch vụ xe có tài xế, quý khách không cần đặt cọc giấy tờ phức tạp. Chỉ cần cung cấp thông tin mã đơn đặt xe hoặc số điện thoại người liên hệ để tài xế đón đúng người và đúng địa điểm.
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Dịch vụ thuê xe áp dụng tại những khu vực nào?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Chúng tôi phục vụ đón trả khách tận nơi tại Hà Nội, TP.HCM và nhận các lộ trình liên tỉnh trên toàn quốc, bao gồm các tour du lịch, cưới hỏi, công tác và đón tiễn tại các sân bay lớn (Nội Bài, Tân Sơn Nhất, Cát Bi,...).
                </div>
            </details>

            <details class="faq-item" name="faq-accordion">
                <summary class="faq-question">
                    <span>Có chính sách ưu đãi cho khách hàng thân thiết không?</span>
                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="faq-answer">
                    Chắc chắn có. Chúng tôi luôn có chính sách giảm giá từ 5% - 15%, ưu tiên nâng cấp dòng xe đời mới và nhiều quà tặng tri ân dành riêng cho khách hàng thân thiết và doanh nghiệp hợp tác lâu dài.
                </div>
            </details>
        </div>

        <div class="faq-cta">
            <p class="faq-cta-prompt">Bạn còn thắc mắc khác?</p>
            <a class="faq-cta-btn" href="<?= url('/lien-he') ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                Liên hệ tư vấn ngay
            </a>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>Bạn cần xe cho chuyến đi sắp tới?</h2>
            <p>Đặt xe online chỉ trong 1 phút hoặc gọi hotline <?= e($site['hotline'] ?? '') ?> để được tư vấn.</p>
        </div>
        <div class="cta-actions">
            <a class="btn btn-light" href="<?= url('/dat-xe') ?>">Đặt xe online</a>
            <?php if (!empty($site['zalo'])): ?>
                <a class="btn btn-outline-light" href="https://zalo.me/<?= e(preg_replace('/\D+/', '', (string) $site['zalo'])) ?>" target="_blank" rel="noopener">Chat Zalo</a>
            <?php endif; ?>
        </div>
    </div>
</section>
