<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Không có quyền truy cập</title>
</head>
<body class="error-body">
    <main class="error-wrap">
        <div class="error-code">403</div>
        <h1>Không có quyền truy cập</h1>
        <p><?= e($message ?? 'Bạn không có quyền thực hiện thao tác này.') ?></p>
        <div class="detail-actions center">
            <a class="btn btn-primary" href="<?= url('/admin') ?>">Về trang quản trị</a>
            <a class="btn btn-outline" href="<?= url('/') ?>">Về trang chủ</a>
        </div>
    </main>
</body>
</html>
