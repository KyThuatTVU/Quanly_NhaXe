<section class="section">
    <div class="container article-layout">
        <article class="article card">
            <nav class="breadcrumb">
                <a href="<?= url('/') ?>">Trang chủ</a> <span>/</span>
                <a href="<?= url('/bai-viet') ?>">Bài viết</a> <span>/</span>
                <span><?= e($post['title']) ?></span>
            </nav>
            <span class="eyebrow"><?= e($post['category'] ?? 'Tin tức') ?></span>
            <h1><?= e($post['title']) ?></h1>
            <p class="muted small">Đăng ngày <?= e(\App\Support\Format::date($post['created_at'] ?? null)) ?></p>
            <?php if (!empty($post['image'])): ?>
                <img class="article-cover" src="<?= e(media((string) $post['image'])) ?>" alt="<?= e($post['title']) ?>">
            <?php endif; ?>
            <div class="article-content"><?= nl2br(e($post['content'] ?? '')) ?></div>
            <a class="btn btn-outline" href="<?= url('/bai-viet') ?>">← Quay lại danh sách</a>
        </article>
        <aside class="info-card">
            <h3>Bài viết mới</h3>
            <ul class="footer-links">
                <?php foreach ($related as $item): ?>
                    <li><a href="<?= url('/bai-viet/' . e($item['slug'])) ?>"><?= e($item['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn-primary btn-block" href="<?= url('/dat-xe') ?>">Đặt xe ngay</a>
        </aside>
    </div>
</section>
