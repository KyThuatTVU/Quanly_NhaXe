<section class="section">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= url('/') ?>">Trang chủ</a> <span>/</span>
            <a href="<?= url('/xe') ?>">Xe</a> <span>/</span>
            <span><?= e($car['name']) ?></span>
        </nav>

        <div class="detail-layout">
            <div class="detail-media card">
                <?php if (!empty($car['image'])): ?>
                    <img src="<?= e(media((string) $car['image'])) ?>" alt="<?= e($car['name']) ?>">
                <?php else: ?>
                    <span class="media-placeholder large">TV</span>
                <?php endif; ?>
            </div>
            <div class="detail-info">
                <span class="<?= e(\App\Support\Format::badge((string) $car['status'])) ?>"><?= e(\App\Support\Format::carStatus((string) $car['status'])) ?></span>
                <h1><?= e($car['name']) ?></h1>
                <p class="muted"><?= e($car['type_name'] ?? $car['type'] ?? '') ?><?php if (!empty($car['type_seats'])): ?> · <?= (int) $car['type_seats'] ?> chỗ<?php endif; ?></p>
                <div class="detail-price">
                    <span class="price"><?= e(\App\Support\Format::money($car['price_per_day'] ?? null)) ?></span><small>/ngày</small>
                    <?php if (!empty($car['price_per_km'])): ?>
                        <span class="muted small">· <?= e(\App\Support\Format::money($car['price_per_km'])) ?>/km</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($car['description'])): ?>
                    <p><?= e($car['description']) ?></p>
                <?php endif; ?>
                <dl class="info-list compact">
                    <?php if (!empty($car['driver_info'])): ?><dt>Tài xế</dt><dd><?= e($car['driver_info']) ?></dd><?php endif; ?>
                    <?php if (!empty($car['insurance_info'])): ?><dt>Bảo hiểm</dt><dd><?= e($car['insurance_info']) ?></dd><?php endif; ?>
                    <dt>Đánh giá</dt><dd><?= e((string) ($car['rating'] ?? '0')) ?> / 5</dd>
                </dl>
                <div class="detail-actions">
                    <a class="btn btn-primary" href="<?= url('/dat-xe') ?>?vehicle_id=<?= (int) $car['vehicle_id'] ?>">Đặt xe này</a>
                    <a class="btn btn-outline" href="tel:<?= e(preg_replace('/\D+/', '', (string) ($site['hotline'] ?? ''))) ?>">Gọi hotline</a>
                </div>
            </div>
        </div>

        <div class="reviews-block">
            <h2>Đánh giá từ khách hàng (<?= count($reviews) ?>)</h2>
            <?php if ($reviews === []): ?>
                <p class="muted">Chưa có đánh giá nào cho xe này.</p>
            <?php else: ?>
                <div class="review-list">
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-item">
                            <div class="review-head">
                                <strong><?= e($review['customer_name'] ?? 'Khách hàng') ?></strong>
                                <span class="stars"><?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?></span>
                            </div>
                            <?php if (!empty($review['comment'])): ?><p><?= e($review['comment']) ?></p><?php endif; ?>
                            <small class="muted"><?= e(\App\Support\Format::date($review['created_at'] ?? null)) ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a class="btn btn-outline" href="<?= url('/danh-gia') ?>">Gửi đánh giá của bạn</a>
        </div>
    </div>
</section>
