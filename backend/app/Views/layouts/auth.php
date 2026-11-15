<?php /** @var string $content */ ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Đăng nhập') ?> — <?= e($site['name'] ?? '') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="auth-body">
    <main class="auth-wrap">
        <?php $view->partial('partials/flash'); ?>
        <?= $content ?>
    </main>
</body>
</html>
