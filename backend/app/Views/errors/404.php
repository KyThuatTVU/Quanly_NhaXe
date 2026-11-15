<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Không tìm thấy trang</title>
</head>
<body class="error-body">
    <main class="error-wrap">
        <div class="error-code">404</div>
        <h1>Không tìm thấy trang</h1>
        <p><?= e($message ?? 'Trang bạn tìm không tồn tại hoặc đã được di chuyển.') ?></p>
        <div class="detail-actions center">
            <a class="btn btn-primary" href="<?= url('/') ?>">Về trang chủ</a>
            <a class="btn btn-outline" href="<?= url('/xe') ?>">Xem danh sách xe</a>
        </div>
    </main>
</body>
</html>
