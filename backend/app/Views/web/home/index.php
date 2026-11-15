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
