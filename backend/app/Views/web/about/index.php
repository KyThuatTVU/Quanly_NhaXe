<?php
/**
 * View: Trang Giới thiệu (About Page) - Hiệu ứng 3D Glassmorphism sang trọng
 * @var array $site
 */
$siteName = !empty($site['name']) ? $site['name'] : 'Thảo Vy Travel';
$hotline = !empty($site['hotline']) ? $site['hotline'] : (!empty($site['phone']) ? $site['phone'] : '0787 972 075');
$hotlineClean = preg_replace('/\s+/', '', $hotline);
$email = !empty($site['email']) ? $site['email'] : 'duyphongtv123@gmail.com';
$address = !empty($site['address']) ? $site['address'] : 'Số 123 Đường ABC, Quận XYZ, TP. Hà Nội';
$workingHours = !empty($site['working_hours']) ? $site['working_hours'] : '24/7 (Kể cả ngày lễ, Tết)';
?>

<div class="about-page-wrap">
    <!-- Hero Header -->
    <section class="about-hero">
        <div class="container">
            <span class="about-hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Về Chúng Tôi
            </span>
            <h1><?= e($siteName) ?></h1>
            <p>Đơn vị chuyên cung cấp dịch vụ thuê xe du lịch, thuê xe hợp đồng chuyên nghiệp hàng đầu tại Việt Nam.</p>
        </div>
    </section>

    <div class="container">
        <!-- Section 1: Tổng Quan Về Công Ty -->
        <section class="glass-card-3d about-overview-grid" aria-label="Tổng quan về công ty">
            <div class="about-overview-img-wrap">
                <img src="<?= asset('img/about/fleet-overview.png') ?>" alt="Đội xe <?= e($siteName) ?>" class="about-overview-img" loading="lazy">
            </div>
            <div class="about-overview-content">
                <span class="about-tag-pill">TỔNG QUAN VỀ CÔNG TY</span>
                <h2 class="about-overview-title"><?= e($siteName) ?></h2>
                <p class="about-overview-desc">
                    <strong><?= e($siteName) ?></strong> là đơn vị chuyên cung cấp dịch vụ <strong>thuê xe du lịch</strong>, <strong>thuê xe hợp đồng</strong> chuyên nghiệp tại Việt Nam. Với nhiều năm kinh nghiệm, chúng tôi tự hào là đối tác tin cậy của hàng nghìn khách hàng cá nhân, doanh nghiệp và tổ chức, mang đến những chuyến đi an toàn, thoải mái với mức giá hợp lý.
                </p>
                <div class="about-stats-grid">
                    <div class="about-stat-box">
                        <div class="about-stat-num">500+</div>
                        <div class="about-stat-label">Khách hàng & Đối tác</div>
                    </div>
                    <div class="about-stat-box">
                        <div class="about-stat-num">10+</div>
                        <div class="about-stat-label">Năm kinh nghiệm</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Điểm Mạnh Của Thảo Vy Travel -->
        <section class="glass-card-3d about-strengths-card" aria-label="Điểm mạnh">
            <h2 class="about-section-heading">Điểm Mạnh Của <?= e($siteName) ?></h2>
            <div class="about-strengths-grid">
                <div class="about-strength-item">
                    <div class="about-strength-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="13" rx="2"></rect>
                            <path d="M16 3H8l-3 4h14l-3-4z"></path>
                            <circle cx="7" cy="15" r="2"></circle>
                            <circle cx="17" cy="15" r="2"></circle>
                        </svg>
                    </div>
                    <div class="about-strength-body">
                        <h3>Đội xe đa dạng, chất lượng cao</h3>
                        <p>Các dòng xe từ 4 chỗ đến 45 chỗ đời mới, tiện nghi hiện đại và được bảo trì, kiểm định định kỳ.</p>
                    </div>
                </div>

                <div class="about-strength-item">
                    <div class="about-strength-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="about-strength-body">
                        <h3>Tài xế chuyên nghiệp, tận tâm</h3>
                        <p>Được đào tạo bài bản, am hiểu tuyến đường, lịch sự, đúng giờ và luôn đặt an toàn của hành khách lên hàng đầu.</p>
                    </div>
                </div>

                <div class="about-strength-item">
                    <div class="about-strength-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="about-strength-body">
                        <h3>Dịch vụ linh hoạt, đáp ứng mọi nhu cầu</h3>
                        <p>Cho thuê xe theo chuyến, theo ngày, theo tháng với hợp đồng rõ ràng, minh bạch, xuất hoá đơn VAT đầy đủ.</p>
                    </div>
                </div>

                <div class="about-strength-item">
                    <div class="about-strength-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div class="about-strength-body">
                        <h3>Hỗ trợ 24/7</h3>
                        <p>Đội ngũ tổng đài viên luôn sẵn sàng phục vụ, tư vấn và báo giá nhanh chóng kể cả ngày nghỉ và lễ Tết.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Tầm Nhìn & Sứ Mệnh -->
        <section aria-label="Tầm nhìn và sứ mệnh">
            <!-- Tầm nhìn -->
            <div class="glass-card-3d about-vm-row">
                <div>
                    <h2 class="about-vm-title">TẦM NHÌN</h2>
                    <p class="about-vm-desc">
                        <strong><?= e($siteName) ?></strong> luôn cố gắng hoàn thiện với mục tiêu trở thành đơn vị dịch vụ vận chuyển hành khách chuyên nghiệp hàng đầu tại Việt Nam. Chúng tôi đề ra các tiêu chí sau để định hướng nâng cao các giá trị phục vụ:
                    </p>
                    <ul class="about-vm-list">
                        <li class="about-vm-item">
                            <span class="about-vm-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span><strong><?= e($siteName) ?></strong> đảm bảo vận chuyển an toàn cho hành khách, tối ưu chi phí cho các doanh nghiệp đối tác.</span>
                        </li>
                        <li class="about-vm-item">
                            <span class="about-vm-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span>Từ việc những công tác huấn luyện bài bản, <strong><?= e($siteName) ?></strong> sẽ lan truyền, tạo môi trường văn hóa giao thông lịch sự, góp phần đưa Việt Nam thành điểm đến sáng giá trong khu vực.</span>
                        </li>
                        <li class="about-vm-item">
                            <span class="about-vm-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span><strong><?= e($siteName) ?></strong> đáp ứng các yêu cầu của khách hàng với tất cả khả năng và sự nỗ lực để khẳng định giá trị thương hiệu. Chúng tôi luôn xây dựng và tăng cường mối quan hệ tốt đẹp lâu dài với khách hàng bằng sự uy tín trong cam kết, tin tưởng, tôn trọng và hợp tác.</span>
                        </li>
                    </ul>
                </div>
                <div class="about-vm-img-frame">
                    <img src="<?= asset('img/about/vision-hands.png') ?>" alt="Tầm nhìn <?= e($siteName) ?>" class="about-vm-img" loading="lazy">
                </div>
            </div>

            <!-- Sứ mệnh -->
            <div class="glass-card-3d about-vm-row reverse">
                <div class="about-vm-img-frame">
                    <img src="<?= asset('img/about/mission-fleet.png') ?>" alt="Sứ mệnh <?= e($siteName) ?>" class="about-vm-img" loading="lazy">
                </div>
                <div>
                    <h2 class="about-vm-title">SỨ MỆNH</h2>
                    <ul class="about-vm-list">
                        <li class="about-vm-item">
                            <span class="about-vm-star">★</span>
                            <span>Cung cấp dịch vụ thuê xe <strong>chất lượng cao, an toàn tuyệt đối</strong>.</span>
                        </li>
                        <li class="about-vm-item">
                            <span class="about-vm-star">★</span>
                            <span>Đáp ứng mọi nhu cầu di chuyển của cá nhân, doanh nghiệp với <strong>giải pháp linh hoạt, tiết kiệm chi phí</strong>.</span>
                        </li>
                        <li class="about-vm-item">
                            <span class="about-vm-star">★</span>
                            <span>Xây dựng đội ngũ <strong>tài xế tận tâm, chuyên nghiệp</strong>, mang đến trải nghiệm dịch vụ tốt nhất.</span>
                        </li>
                        <li class="about-vm-item">
                            <span class="about-vm-star">★</span>
                            <span>Ứng dụng <strong>công nghệ hiện đại</strong> để nâng cao chất lượng phục vụ, đặt xe nhanh chóng, thanh toán tiện lợi.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Section 4: Dịch Vụ & Vì Sao Chọn -->
        <section class="about-services-grid" aria-label="Dịch vụ và lý do chọn">
            <!-- Box trái: Dịch vụ -->
            <div class="glass-card-3d about-services-box">
                <h2>Dịch Vụ Thuê Xe Tại <?= e($siteName) ?></h2>

                <div class="about-service-group">
                    <h3>Thuê Xe Du Lịch – Đồng Hành Cùng Bạn Trên Mọi Cung Đường</h3>
                    <ul class="about-service-bullets">
                        <li>Xe 4-7 chỗ cho nhóm nhỏ, xe 16-45 chỗ cho đoàn đông.</li>
                        <li>Đáp ứng nhu cầu du lịch cá nhân, gia đình, công ty, đoàn thể.</li>
                        <li>Xe đời mới, thoải mái, tài xế chuyên nghiệp, giá cả hợp lý.</li>
                    </ul>
                </div>

                <div class="about-service-group">
                    <h3>Thuê Xe Hợp Đồng – Giải Pháp Tiết Kiệm Cho Doanh Nghiệp</h3>
                    <ul class="about-service-bullets">
                        <li>Hợp đồng thuê xe theo ngày, tháng hoặc năm cho doanh nghiệp, trường học.</li>
                        <li>Phục vụ đưa đón nhân viên, học sinh, hội nghị, sự kiện.</li>
                        <li>Giá cả cạnh tranh, hợp đồng minh bạch, rõ ràng.</li>
                    </ul>
                </div>

                <div class="about-service-group">
                    <h3>Thuê Xe Đưa Đón Sân Bay – Đúng Giờ, An Toàn, Tiện Lợi</h3>
                    <ul class="about-service-bullets">
                        <li>Xe từ 4-16 chỗ đưa đón sân bay Tân Sơn Nhất, Nội Bài, Đà Nẵng...</li>
                        <li>Đón tiễn tận nơi, đảm bảo đúng giờ, không lo trễ chuyến.</li>
                    </ul>
                </div>

                <div class="about-service-group">
                    <h3>Thuê Xe Cưới Hỏi – Sang Trọng, Đẳng Cấp</h3>
                    <ul class="about-service-bullets">
                        <li>Các dòng xe cao cấp như Mercedes, BMW, Audi, Camry... cho ngày trọng đại.</li>
                        <li>Dịch vụ tài xế chuyên nghiệp, đúng giờ, phục vụ tận tâm.</li>
                    </ul>
                </div>
            </div>

            <!-- Box phải: Vì sao chọn -->
            <div class="glass-card-3d about-why-box">
                <h2>Vì Sao Chọn <?= e($siteName) ?>?</h2>
                <div class="about-why-list">
                    <div class="about-why-item">
                        <strong>Xe đời mới, đa dạng</strong>
                        <span>Luôn cập nhật các dòng xe hiện đại, đầy đủ tiện nghi, sạch sẽ như mới.</span>
                    </div>
                    <div class="about-why-item">
                        <strong>Dịch vụ chuyên nghiệp</strong>
                        <span>Đội ngũ tài xế có nhiều năm kinh nghiệm, phục vụ chu đáo, lịch sự.</span>
                    </div>
                    <div class="about-why-item">
                        <strong>Giá cả cạnh tranh</strong>
                        <span>Cam kết mức giá tốt nhất thị trường, hợp đồng rõ ràng, không có phí ẩn.</span>
                    </div>
                    <div class="about-why-item">
                        <strong>Hỗ trợ 24/7</strong>
                        <span>Phục vụ khách hàng bất cứ khi nào cần, xử lý nhanh chóng mọi tình huống.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 5: Thông Tin Liên Hệ -->
        <section class="glass-card-3d about-contact-box" aria-label="Thông tin liên hệ">
            <h2>Thông Tin Liên Hệ</h2>
            <div class="about-contact-list">
                <div class="about-contact-row">
                    <div class="about-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div><strong>Địa chỉ:</strong> <span><?= e($address) ?></span></div>
                </div>
                <div class="about-contact-row">
                    <div class="about-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div><strong>Hotline:</strong> <a href="tel:<?= e($hotlineClean) ?>" style="font-weight:700; color:var(--green)"><?= e($hotline) ?></a></div>
                </div>
                <div class="about-contact-row">
                    <div class="about-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div><strong>Email:</strong> <a href="mailto:<?= e($email) ?>" style="color:var(--forest)"><?= e($email) ?></a></div>
                </div>
                <div class="about-contact-row">
                    <div class="about-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <div><strong>Website:</strong> <span style="font-weight:600">XeThaoVy.com</span></div>
                </div>
                <div class="about-contact-row">
                    <div class="about-contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div><strong>Giờ làm việc:</strong> <span><?= e($workingHours) ?></span></div>
                </div>
            </div>
            <div class="about-contact-slogan">
                <?= e($siteName) ?> – Đồng hành cùng bạn trên mọi hành trình!
            </div>
        </section>

        <!-- Section 6: Kết Nối Ngay Với Thảo Vy Travel & CTA Banner -->
        <section class="about-connect-banner" aria-label="Kết nối với chúng tôi">
            <div class="about-connect-top">
                <div class="about-connect-icon-card">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                        <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                    </svg>
                </div>
                <div class="about-connect-top-body">
                    <h3>KẾT NỐI NGAY VỚI <?= mb_strtoupper(e($siteName), 'UTF-8') ?></h3>
                    <p>Bạn đang tìm kiếm dịch vụ thuê xe uy tín, chất lượng? Đừng ngần ngại liên hệ ngay với chúng tôi để được tư vấn và nhận báo giá tốt nhất!</p>
                    <div class="about-connect-actions">
                        <a href="tel:<?= e($hotlineClean) ?>" class="about-phone-pill">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Hotline: <span><?= e($hotline) ?></span>
                        </a>
                        <div class="about-connect-meta">
                            <span>📧 <strong><?= e($email) ?></strong></span>
                            <span>•</span>
                            <span>⚡ Hỗ trợ 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-connect-bottom">
                <h2>Sẵn Sàng Cho Chuyến Đi Tiếp Theo?</h2>
                <p>Đặt xe ngay hôm nay để nhận ưu đãi đặc biệt và trải nghiệm hành trình an tâm, tiện nghi tuyệt đối.</p>
                <div class="about-bottom-cta-btns">
                    <a href="<?= url('/dat-xe') ?>" class="about-btn-light">Đặt Xe Ngay</a>
                    <a href="tel:<?= e($hotlineClean) ?>" class="about-btn-light about-btn-call">Gọi Hotline: <?= e($hotline) ?></a>
                </div>
            </div>
        </section>
    </div>
</div>
