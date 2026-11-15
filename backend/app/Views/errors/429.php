<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 — Thao tác quá nhanh</title>
</head>
<body class="error-body">
    <main class="error-wrap">
        <div class="error-code">429</div>
        <h1>Thao tác quá nhanh</h1>
        <p><?= e($message ?? 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau ít phút.') ?></p>
        <div class="detail-actions center">
            <a class="btn btn-primary" href="<?= url('/') ?>">Về trang chủ</a>
            <a class="btn btn-outline" href="<?= url('/lien-he') ?>">Liên hệ nhà xe</a>
        </div>
    </main>
</body>
</html>
