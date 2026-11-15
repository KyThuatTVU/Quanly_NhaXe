<?php /** @var array<string, mixed> $post */ ?>
<article class="card post-card">
    <a class="card-media" href="<?= url('/bai-viet/' . e($post['slug'])) ?>">
        <?php if (!empty($post['image'])): ?>
            <img src="<?= e(media((string) $post['image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
        <?php else: ?>
            <span class="media-placeholder">Bài viết</span>
        <?php endif; ?>
    </a>
    <div class="card-body">
        <span class="eyebrow"><?= e($post['category'] ?? 'Tin tức') ?></span>
        <h3><a href="<?= url('/bai-viet/' . e($post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
        <p class="muted small"><?= e(mb_substr(trim(strip_tags((string) ($post['content'] ?? ''))), 0, 140)) ?></p>
        <div class="card-actions">
            <span class="muted small"><?= e(\App\Support\Format::date($post['created_at'] ?? null)) ?></span>
            <a class="btn btn-sm btn-outline" href="<?= url('/bai-viet/' . e($post['slug'])) ?>">Đọc tiếp</a>
        </div>
    </div>
</article>
