<?php /** @var string $content */ ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Trang chủ') ?> — <?= e($site['name'] ?? '') ?></title>
    <meta name="description" content="<?= e($site['description'] ?? '') ?>">
</head>
<body>
    <?php $view->partial('partials/header', ['site' => $site]); ?>
    <main class="site-main">
        <?php $view->partial('partials/flash'); ?>
        <?= $content ?>
    </main>
    <?php $view->partial('partials/footer', ['site' => $site]); ?>
</body>
</html>
