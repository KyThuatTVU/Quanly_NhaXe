<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= url('/') ?>">
            <span class="brand-mark">TV</span>
            <span class="brand-text">
                <strong><?= e($site['name'] ?? 'Xe Hợp Đồng Tháo Vy') ?></strong>
                <small>Thuê xe hợp đồng 4 - 45 chỗ</small>
            </span>
        </a>
        <nav class="site-nav">
            <a href="<?= url('/') ?>">Trang chủ</a>
            <a href="<?= url('/gioi-thieu') ?>">Giới thiệu</a>
            <a href="<?= url('/xe') ?>">Xe</a>
            <a href="<?= url('/dich-vu') ?>">Dịch vụ</a>
            <a href="<?= url('/bai-viet') ?>">Bài viết</a>
            <a href="<?= url('/tra-cuu-don') ?>">Tra cứu đơn</a>
            <a href="<?= url('/lien-he') ?>">Liên hệ</a>
            <a class="btn btn-primary btn-sm" href="<?= url('/dat-xe') ?>">Đặt xe</a>
        </nav>
        <div class="header-hotline">
            <small>Hotline</small>
            <strong><?= e($site['hotline'] ?? $site['phone'] ?? '') ?></strong>
        </div>
    </div>
</header>
