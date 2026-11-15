<?php /** @var array<string, mixed> $car */ ?>
<article class="card car-card">
    <a class="card-media" href="<?= url('/xe/' . (int) $car['vehicle_id']) ?>">
        <?php if (!empty($car['image'])): ?>
            <img src="<?= e(media((string) $car['image'])) ?>" alt="<?= e($car['name']) ?>" loading="lazy">
        <?php else: ?>
            <span class="media-placeholder">TV</span>
        <?php endif; ?>
        <span class="<?= e(\App\Support\Format::badge((string) $car['status'])) ?> card-badge">
            <?= e(\App\Support\Format::carStatus((string) $car['status'])) ?>
        </span>
    </a>
    <div class="card-body">
        <h3><a href="<?= url('/xe/' . (int) $car['vehicle_id']) ?>"><?= e($car['name']) ?></a></h3>
        <p class="muted small">
            <?= e($car['type_name'] ?? $car['type'] ?? '') ?>
            <?php if (!empty($car['type_seats'])): ?> · <?= (int) $car['type_seats'] ?> chỗ<?php endif; ?>
        </p>
        <p class="price">
            <?= e(\App\Support\Format::money($car['price_per_day'] ?? null)) ?>
            <small>/ngày</small>
        </p>
        <div class="card-actions">
            <a class="btn btn-sm btn-outline" href="<?= url('/xe/' . (int) $car['vehicle_id']) ?>">Chi tiết</a>
            <a class="btn btn-sm btn-primary" href="<?= url('/dat-xe') ?>?vehicle_id=<?= (int) $car['vehicle_id'] ?>">Đặt xe</a>
        </div>
    </div>
</article>
