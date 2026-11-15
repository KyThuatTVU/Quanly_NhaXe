<?php
$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$isAdmin = ($currentUser['role'] ?? '') === 'admin';
$isActive = static function (string $target) use ($path): string {
    $base = rtrim((string) parse_url(url($target), PHP_URL_PATH), '/');
    if ($target === '/admin') {
        return $path === $base || $path === $base . '/' ? ' active' : '';
    }
    return str_starts_with($path, $base) ? ' active' : '';
};
?>
<aside class="admin-sidebar">
    <a class="admin-brand" href="<?= url('/admin') ?>">
        <span class="brand-mark">TV</span>
        <span>
            <strong>Tháo Vy</strong>
            <small>Trang quản trị</small>
        </span>
    </a>
    <nav class="admin-nav">
        <span class="admin-nav-label">Tổng quan</span>
        <a class="admin-nav-link<?= $isActive('/admin') ?>" href="<?= url('/admin') ?>">Bảng điều khiển</a>

        <span class="admin-nav-label">Vận hành</span>
        <a class="admin-nav-link<?= $isActive('/admin/bookings') ?>" href="<?= url('/admin/bookings') ?>">Đơn đặt xe</a>
        <a class="admin-nav-link<?= $isActive('/admin/contacts') ?>" href="<?= url('/admin/contacts') ?>">Liên hệ khách</a>
        <a class="admin-nav-link<?= $isActive('/admin/reviews') ?>" href="<?= url('/admin/reviews') ?>">Đánh giá</a>

        <span class="admin-nav-label">Danh mục</span>
        <a class="admin-nav-link<?= $isActive('/admin/cars') ?>" href="<?= url('/admin/cars') ?>">Xe</a>
        <a class="admin-nav-link<?= $isActive('/admin/car-types') ?>" href="<?= url('/admin/car-types') ?>">Loại xe</a>
        <a class="admin-nav-link<?= $isActive('/admin/services') ?>" href="<?= url('/admin/services') ?>">Dịch vụ</a>
        <a class="admin-nav-link<?= $isActive('/admin/posts') ?>" href="<?= url('/admin/posts') ?>">Bài viết</a>

        <span class="admin-nav-label">Hệ thống</span>
        <a class="admin-nav-link<?= $isActive('/admin/settings') ?>" href="<?= url('/admin/settings') ?>">Cài đặt website</a>
        <a class="admin-nav-link<?= $isActive('/admin/profile') ?>" href="<?= url('/admin/profile') ?>">Thông tin cá nhân</a>
        <?php if ($isAdmin): ?>
            <a class="admin-nav-link<?= $isActive('/admin/accounts') ?>" href="<?= url('/admin/accounts') ?>">Tài khoản</a>
            <a class="admin-nav-link<?= $isActive('/admin/audit-logs') ?>" href="<?= url('/admin/audit-logs') ?>">Nhật ký hệ thống</a>
        <?php endif; ?>
    </nav>
    <div class="admin-sidebar-foot">
        <strong><?= e($currentUser['username'] ?? '') ?></strong>
        <span><?= e($currentUser['role'] ?? '') ?></span>
    </div>
</aside>
