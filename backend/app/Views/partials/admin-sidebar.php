<?php
$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$isAdmin = ($currentUser['role'] ?? '') === 'admin';
$brandName = trim((string) ($site['name'] ?? '')) ?: 'Thảo Vy Travel';
$isActive = static function (string $target) use ($path): string {
    $base = rtrim((string) parse_url(url($target), PHP_URL_PATH), '/');
    if ($target === '/admin') {
        return $path === $base || $path === $base . '/' ? ' active' : '';
    }
    return str_starts_with($path, $base) ? ' active' : '';
};
$initial = mb_strtoupper(mb_substr((string) ($currentUser['username'] ?? 'A'), 0, 1, 'UTF-8'), 'UTF-8');
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-head">
        <a class="admin-brand" href="<?= url('/admin') ?>">
            <img class="brand-logo" src="<?= asset('img/logo/logo_chinh_256.png') ?>" alt="<?= e($brandName) ?>" width="40" height="40">
            <span>
                <strong><?= e($brandName) ?></strong>
                <small>Quản Trị Nhà Xe</small>
            </span>
        </a>
        <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Đóng menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <nav class="admin-nav">
        <span class="admin-nav-label">Tổng quan</span>
        <a class="admin-nav-link<?= $isActive('/admin') ?>" href="<?= url('/admin') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
            Bảng điều khiển
        </a>

        <span class="admin-nav-label">Vận hành</span>
        <a class="admin-nav-link<?= $isActive('/admin/bookings') ?>" href="<?= url('/admin/bookings') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            Đơn đặt xe
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/contacts') ?>" href="<?= url('/admin/contacts') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Liên hệ khách
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/reviews') ?>" href="<?= url('/admin/reviews') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            Đánh giá
        </a>

        <span class="admin-nav-label">Đội xe & Dịch vụ</span>
        <a class="admin-nav-link<?= $isActive('/admin/cars') ?>" href="<?= url('/admin/cars') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><path d="M16 3H8l-3 4h14l-3-4z"></path><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            Danh sách xe
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/car-types') ?>" href="<?= url('/admin/car-types') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            Loại xe (4 - 45 chỗ)
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/services') ?>" href="<?= url('/admin/services') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            Dịch vụ thuê xe
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/posts') ?>" href="<?= url('/admin/posts') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            Bài viết & Tin tức
        </a>

        <span class="admin-nav-label">Hệ thống</span>
        <a class="admin-nav-link<?= $isActive('/admin/settings') ?>" href="<?= url('/admin/settings') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            Cài đặt website
        </a>
        <a class="admin-nav-link<?= $isActive('/admin/profile') ?>" href="<?= url('/admin/profile') ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Thông tin cá nhân
        </a>
        <?php if ($isAdmin): ?>
            <a class="admin-nav-link<?= $isActive('/admin/accounts') ?>" href="<?= url('/admin/accounts') ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Tài khoản quản trị
            </a>
            <a class="admin-nav-link<?= $isActive('/admin/audit-logs') ?>" href="<?= url('/admin/audit-logs') ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Nhật ký hệ thống
            </a>
        <?php endif; ?>
    </nav>
    <div class="admin-sidebar-foot">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar">
                <?= $initial ?>
                <span class="user-status-dot"></span>
            </div>
            <div class="sidebar-user-info">
                <strong><?= e($currentUser['username'] ?? '') ?></strong>
                <span><?= e($currentUser['role'] ?? '') ?></span>
            </div>
        </div>
    </div>
</aside>
