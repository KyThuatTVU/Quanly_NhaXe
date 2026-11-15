<?php /** @var array<string, mixed> $pagination */ ?>
<?php if (($pagination['last'] ?? 1) > 1): ?>
    <nav class="pagination" aria-label="Phân trang">
        <?php if (!empty($pagination['prev'])): ?>
            <a href="<?= e($pagination['prev']) ?>">« Trước</a>
        <?php endif; ?>
        <?php foreach ($pagination['pages'] as $item): ?>
            <?php if ($item['current']): ?>
                <span class="current"><?= (int) $item['page'] ?></span>
            <?php else: ?>
                <a href="<?= e($item['url']) ?>"><?= (int) $item['page'] ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!empty($pagination['next'])): ?>
            <a href="<?= e($pagination['next']) ?>">Sau »</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>
