<section class="section">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= url('/') ?>">Trang chủ</a> <span>/</span>
            <a href="<?= url('/dich-vu') ?>">Dịch vụ</a> <span>/</span>
            <span><?= e($service['service_name']) ?></span>
        </nav>
        <div class="detail-layout">
            <div class="detail-media card">
                <?php if (!empty($service['image'])): ?>
                    <img src="<?= e(media((string) $service['image'])) ?>" alt="<?= e($service['service_name']) ?>">
                <?php else: ?>
                    <span class="media-placeholder large">Dịch vụ</span>
                <?php endif; ?>
            </div>
            <div class="detail-info">
                <h1><?= e($service['service_name']) ?></h1>
                <p class="price large"><?= e(\App\Support\Format::money($service['price'] ?? null)) ?></p>
                <p><?= nl2br(e($service['description'] ?? '')) ?></p>
                <div class="detail-actions">
                    <a class="btn btn-primary" href="<?= url('/dat-xe') ?>">Đặt xe ngay</a>
                    <a class="btn btn-outline" href="<?= url('/lien-he') ?>">Tư vấn thêm</a>
                </div>
            </div>
        </div>
    </div>
</section>
