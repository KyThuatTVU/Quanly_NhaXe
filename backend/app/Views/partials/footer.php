<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h4><?= e($site['name'] ?? '') ?></h4>
            <p><?= e($site['description'] ?? '') ?></p>
        </div>
        <div>
            <h4>Liên kết</h4>
            <ul class="footer-links">
                <li><a href="<?= url('/xe') ?>">Danh sách xe</a></li>
                <li><a href="<?= url('/dich-vu') ?>">Dịch vụ</a></li>
                <li><a href="<?= url('/bai-viet') ?>">Bài viết</a></li>
                <li><a href="<?= url('/tra-cuu-don') ?>">Tra cứu đơn</a></li>
                <li><a href="<?= url('/danh-gia') ?>">Gửi đánh giá</a></li>
            </ul>
        </div>
        <div>
            <h4>Thông tin liên hệ</h4>
            <ul class="footer-links">
                <li>Địa chỉ: <?= e($site['address'] ?? '—') ?></li>
                <li>Điện thoại: <?= e($site['phone'] ?? '—') ?></li>
                <li>Hotline: <?= e($site['hotline'] ?? '—') ?></li>
                <li>Email: <?= e($site['email'] ?? '—') ?></li>
                <li>Giờ làm việc: <?= e($site['working_hours'] ?? '—') ?></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>© <?= date('Y') ?> <?= e($site['name'] ?? '') ?>. All rights reserved.</span>
            <a href="<?= url('/admin/login') ?>">Quản trị</a>
        </div>
    </div>
</footer>
