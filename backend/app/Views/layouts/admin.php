<?php /** @var string $content */ ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Quản trị') ?> — <?= e($site['name'] ?? '') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <?php $view->partial('partials/admin-sidebar', ['currentUser' => $currentUser, 'site' => $site]); ?>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <div>
                        <strong><?= e($title ?? 'Quản trị') ?></strong>
                        <span class="muted">Xin chào, <?= e($currentUser['username'] ?? '') ?></span>
                    </div>
                </div>
                <div class="admin-topbar-actions">
                    <a class="btn btn-sm btn-light" href="<?= url('/') ?>" target="_blank" rel="noopener">Xem website</a>
                    <form method="post" action="<?= url('/admin/logout') ?>" class="inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline" type="submit">Đăng xuất</button>
                    </form>
                </div>
            </header>
            <div class="admin-content">
                <?php $view->partial('partials/flash'); ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                }
            });
        });
    </script>
    <script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
