<div class="admin-page-head">
    <div>
        <h1>Tổng quan</h1>
        <p class="muted">Số liệu tháng <?= e($monthLabel) ?> và tình hình vận hành</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/bookings') ?>">Quản lý đơn</a>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-label">Tổng số xe</span>
        <strong class="stat-value"><?= (int) $stats['cars']['total'] ?></strong>
        <span class="stat-note"><?= (int) $stats['cars']['available'] ?> xe sẵn sàng</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Đơn đặt xe</span>
        <strong class="stat-value"><?= (int) $stats['bookings']['total'] ?></strong>
        <span class="stat-note"><?= (int) ($summary['pending'] ?? 0) ?> đơn chờ xác nhận tháng này</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Doanh thu tạm tính</span>
        <strong class="stat-value"><?= e(\App\Support\Format::money($summary['amount'] ?? 0)) ?></strong>
        <span class="stat-note">Theo ngày đón trong tháng</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Liên hệ mới</span>
        <strong class="stat-value"><?= (int) $stats['contacts']['new'] ?></strong>
        <span class="stat-note"><a href="<?= url('/admin/contacts') ?>">Xem danh sách</a></span>
    </div>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="admin-card-head">
            <h2>Trạng thái đơn tháng này</h2>
        </div>
        <div class="status-breakdown">
            <?php foreach (['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'] as $key => $label): ?>
                <div class="status-row">
                    <span class="<?= e(\App\Support\Format::badge($key)) ?>"><?= e($label) ?></span>
                    <strong><?= (int) ($summary[$key] ?? 0) ?> đơn</strong>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="muted small">Tổng cộng <?= (int) ($summary['total'] ?? 0) ?> đơn trong tháng.</p>
    </section>

    <section class="admin-card">
        <div class="admin-card-head">
            <h2>Đơn gần đây</h2>
            <a class="link-more" href="<?= url('/admin/bookings') ?>">Tất cả →</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead><tr><th>Mã đơn</th><th>Khách</th><th>Ngày đón</th><th>Trạng thái</th></tr></thead>
                <tbody>
                    <?php foreach ($stats['recent_bookings'] as $booking): ?>
                        <tr>
                            <td><a href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>"><?= e($booking['booking_code']) ?></a></td>
                            <td><?= e($booking['customer_name']) ?></td>
                            <td><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?></td>
                            <td><span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>"><?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($stats['recent_bookings'] === []): ?>
                        <tr><td colspan="4" class="muted">Chưa có đơn nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="admin-card">
    <div class="admin-card-head">
        <h2>Đơn theo ngày (tháng <?= e($monthLabel) ?>)</h2>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Ngày</th><th>Tổng</th><th>Chờ</th><th>Xác nhận</th><th>Hoàn thành</th><th>Hủy</th><th>Tạm tính</th></tr>
            </thead>
            <tbody>
                <?php foreach ($chart as $row): ?>
                    <tr>
                        <td><?= e(\App\Support\Format::date($row['label'])) ?></td>
                        <td><?= (int) $row['total'] ?></td>
                        <td><?= (int) $row['pending'] ?></td>
                        <td><?= (int) $row['confirmed'] ?></td>
                        <td><?= (int) $row['completed'] ?></td>
                        <td><?= (int) $row['cancelled'] ?></td>
                        <td><?= e(\App\Support\Format::money($row['amount'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($chart === []): ?>
                    <tr><td colspan="7" class="muted">Chưa có dữ liệu trong tháng.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
