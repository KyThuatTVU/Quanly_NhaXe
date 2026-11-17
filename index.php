<?php

declare(strict_types=1);

/**
 * Front controller của website.
 * URL công khai: /home/, /cars/, /booking/, ... (không lộ frontend/pages).
 */

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($base === '.' || $base === '/') {
    $base = '';
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($base !== '' && str_starts_with($uri, $base . '/')) {
    $uri = substr($uri, strlen($base));
} elseif ($uri === $base) {
    $uri = '/';
}

$route = trim(rawurldecode($uri), '/');
if ($route === 'index.php') {
    $route = '';
}

// URL cũ /backend/public/... chuyển hẳn (301) sang URL sạch tương ứng
require_once __DIR__ . '/backend/bootstrap/legacy-redirect.php';
$legacyTarget = legacy_backend_target($route);
if ($legacyTarget !== null) {
    $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
    if ($query !== '') {
        $legacyTarget .= (str_contains($legacyTarget, '?') ? '&' : '?') . $query;
    }

    header('Location: ' . $base . '/' . $legacyTarget, true, 301);
    exit;
}

// API, tệp tải lên và quản trị: URL sạch /api/..., /uploads/..., /admin/... (không lộ backend/public)
if (
    $route === 'api' || str_starts_with($route, 'api/')
    || $route === 'uploads' || str_starts_with($route, 'uploads/')
    || $route === 'admin' || str_starts_with($route, 'admin/')
) {
    require __DIR__ . '/backend/public/index.php';
    exit;
}

$routes = [
    '' => 'home/index.html',
    'home' => 'home/index.html',
    'about' => 'about/index.html',
    'cars' => 'cars/index.html',
    'cars/detail' => 'cars/detail/index.html',
    'services' => 'services/index.html',
    'services/detail' => 'services/detail/index.html',
    'posts' => 'posts/index.html',
    'posts/detail' => 'posts/detail/index.html',
    'booking' => 'booking/index.html',
    'booking/lookup' => 'booking/lookup/index.html',
    'booking/success' => 'booking/success/index.html',
    'contact' => 'contact/index.html',
    'reviews' => 'reviews/index.html',
    'admin-demo' => 'admin-glassmorphism-demo.html',
];

if (str_starts_with($route, 'frontend/pages/')) {
    $legacy = preg_replace('#/index\.html?$#', '', substr($route, strlen('frontend/pages/')));
    if (isset($routes[$legacy])) {
        $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
        header('Location: ' . $base . '/' . $legacy . '/' . ($query !== '' ? '?' . $query : ''), true, 301);
        exit;
    }
}

$exists = isset($routes[$route]) && is_file(__DIR__ . '/frontend/pages/' . $routes[$route]);

// Trang gốc phải chuyển về /home/ để đường dẫn tương đối của asset không bị sai
if ($route === '' && $exists) {
    $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
    header('Location: ' . $base . '/home/' . ($query !== '' ? '?' . $query : ''), true, 301);
    exit;
}

if ($exists && $route !== '' && !str_ends_with($uri, '/')) {
    $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
    header('Location: ' . $base . '/' . $route . '/' . ($query !== '' ? '?' . $query : ''), true, 301);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
// HTML phải được kiểm tra lại mỗi lần tải để thay đổi hiện ra ngay
header('Cache-Control: no-cache, must-revalidate');

$assetDir = __DIR__ . '/frontend/assets';
$assetVersion = (static function (string $dir): int {
    $latest = 0;
    if (!is_dir($dir)) {
        return $latest;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $latest = max($latest, (int) $file->getMTime());
        }
    }

    return $latest;
})($assetDir);

if ($exists) {
    $html = (string) file_get_contents(__DIR__ . '/frontend/pages/' . $routes[$route]);
    // Gắn ?v=<mtime> vào CSS/JS để trình duyệt không dùng bản cache cũ
    $html = preg_replace_callback(
        '#(?:\.\./)+assets/([\w./-]+\.(?:css|js))#i',
        static function (array $match) use ($assetDir): string {
            $file = $assetDir . '/' . $match[1];
            $version = is_file($file) ? (int) filemtime($file) : 0;

            return $match[0] . '?v=' . $version;
        },
        $html,
    ) ?? $html;
    // Ảnh do JS chèn (logo, banner) cũng lấy đúng phiên bản hiện tại
    $html = preg_replace(
        '#<head>#i',
        '<head>' . "\n    <script>window.__ASSET_V=" . $assetVersion . ';</script>',
        $html,
        1,
    ) ?? $html;
    echo $html;
    exit;
}

http_response_code(404);
$home = htmlspecialchars($base . '/home/', ENT_QUOTES, 'UTF-8');
$css = htmlspecialchars($base . '/assets/css/app.css?v=' . $assetVersion, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Không tìm thấy trang</title>
    <link rel="stylesheet" href="<?= $css ?>">
</head>
<body>
<main class="site-main">
    <section class="section">
        <div class="container">
            <p class="eyebrow">LỖI 404</p>
            <h1>Không tìm thấy trang</h1>
            <p>Đường dẫn bạn mở không tồn tại hoặc đã được thay đổi.</p>
            <p><a class="btn btn-primary" href="<?= $home ?>">Về trang chủ</a></p>
        </div>
    </section>
</main>
</body>
</html>
