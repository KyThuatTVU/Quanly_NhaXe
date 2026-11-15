<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Lỗi hệ thống</title>
</head>
<body class="error-body">
    <main class="error-wrap">
        <div class="error-code">500</div>
        <h1>Đã xảy ra lỗi</h1>
        <p><?= e($message ?? 'Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.') ?></p>
        <div class="detail-actions center">
            <a class="btn btn-primary" href="<?= url('/') ?>">Về trang chủ</a>
        </div>
    </main>
</body>
</html>
