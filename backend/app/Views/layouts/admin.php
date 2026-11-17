<?php
/** @var string $content */
$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$adminBase = rtrim((string) parse_url(url('/admin'), PHP_URL_PATH), '/');
$isDockActive = static function (string $target) use ($path, $adminBase): string {
    $full = rtrim((string) parse_url(url($target), PHP_URL_PATH), '/');
    if ($target === '/admin') {
        return ($path === $full || $path === $full . '/') ? ' active' : '';
    }
    return str_starts_with($path, $full) ? ' active' : '';
};
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title ?? 'Quản trị') ?> — <?= e($site['name'] ?? 'Thảo Vy Travel') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <!-- Chart.js cho các biểu đồ phân tích trực quan -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
</head>
<body class="admin-body">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="admin-shell">
        <?php $view->partial('partials/admin-sidebar', ['currentUser' => $currentUser, 'site' => $site]); ?>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Mở menu quản trị" title="Menu">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                    <div>
                        <strong><?= e($title ?? 'Quản trị') ?></strong>
                        <span class="muted desktop-only">Hệ thống quản lý nhà xe Thảo Vy Travel</span>
                    </div>
                </div>

                <!-- Đèn trạng thái trực tiếp 10% Emerald (chỉ hiển thị trên desktop để tiết kiệm không gian di động) -->
                <div class="live-indicator desktop-only">
                    <i></i>
                    <span>Hệ thống trực tuyến • 24/7</span>
                </div>

                <div class="admin-topbar-actions">
                    <a class="btn btn-sm btn-light desktop-only" href="<?= url('/') ?>" target="_blank" rel="noopener">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <span>Website</span>
                    </a>
                    <a class="btn btn-sm btn-primary" href="<?= url('/admin/bookings') ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span class="btn-text">Đơn xe</span>
                    </a>
                    <form method="post" action="<?= url('/admin/logout') ?>" class="inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline" type="submit" title="Đăng xuất khỏi tài khoản">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span class="desktop-only">Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </header>
            <div class="admin-content">
                <?php $view->partial('partials/flash'); ?>
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- THANH ĐIỀU HƯỚNG DƯỚI ĐÁY CHO DI ĐỘNG (MOBILE DOCK 3D GLASS) -->
    <nav class="admin-mobile-dock" id="adminMobileDock">
        <a href="<?= url('/admin') ?>" class="dock-item<?= $isDockActive('/admin') ?>">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
            <span>Tổng quan</span>
        </a>
        <a href="<?= url('/admin/bookings') ?>" class="dock-item<?= $isDockActive('/admin/bookings') ?>">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            <span>Đơn xe</span>
        </a>
        <a href="<?= url('/admin/cars') ?>" class="dock-item<?= $isDockActive('/admin/cars') ?>">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            <span>Đội xe</span>
        </a>
        <a href="<?= url('/admin/contacts') ?>" class="dock-item<?= $isDockActive('/admin/contacts') ?>">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Liên hệ</span>
        </a>
        <button type="button" class="dock-item" id="dockMoreBtn" title="Xem tất cả menu">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle><circle cx="5" cy="12" r="1.5"></circle></svg>
            <span>Thêm</span>
        </button>
    </nav>

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
