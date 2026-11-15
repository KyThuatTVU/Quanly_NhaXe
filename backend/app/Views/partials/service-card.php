<?php /** @var array<string, mixed> $service */ ?>
<article class="card service-card">
    <div class="card-media">
        <?php if (!empty($service['image'])): ?>
            <img src="<?= e(media((string) $service['image'])) ?>" alt="<?= e($service['service_name']) ?>" loading="lazy">
        <?php else: ?>
            <span class="media-placeholder">Dịch vụ</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <h3><a href="<?= url('/dich-vu/' . (int) $service['service_id']) ?>"><?= e($service['service_name']) ?></a></h3>
        <p class="muted small"><?= e(mb_substr((string) ($service['description'] ?? ''), 0, 120)) ?></p>
        <p class="price"><?= e(\App\Support\Format::money($service['price'] ?? null)) ?></p>
        <a class="btn btn-sm btn-outline" href="<?= url('/dich-vu/' . (int) $service['service_id']) ?>">Xem chi tiết</a>
    </div>
</article>
