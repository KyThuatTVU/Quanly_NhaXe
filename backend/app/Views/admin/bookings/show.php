<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/bookings') ?>">Đơn đặt xe</a> <span>/</span>
            <span><?= e($booking['booking_code']) ?></span>
        </nav>
        <h1>Đơn <?= e($booking['booking_code']) ?></h1>
    </div>
    <span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>"><?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?></span>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="admin-card-head"><h2>Thông tin khách hàng</h2></div>
        <dl class="info-list">
            <dt>Họ tên</dt><dd><?= e($booking['customer_name']) ?></dd>
            <dt>Số điện thoại</dt><dd><?= e(\App\Support\Format::phone($booking['customer_phone'] ?? null)) ?></dd>
            <dt>Email</dt><dd><?= e($booking['customer_email'] ?? '—') ?></dd>
            <dt>Điểm đón</dt><dd><?= e($booking['pickup_location']) ?></dd>
            <dt>Điểm đến</dt><dd><?= e($booking['dropoff_location'] ?? '—') ?></dd>
            <dt>Số hành khách</dt><dd><?= e($booking['number_of_passengers'] ?? '—') ?></dd>
            <dt>Dịch vụ</dt><dd><?= e($booking['service_name'] ?? $booking['service_type'] ?? '—') ?></dd>
            <dt>Ghi chú</dt><dd><?= e($booking['notes'] ?? '—') ?></dd>
        </dl>
    </section>

    <section class="admin-card">
        <div class="admin-card-head"><h2>Chuyến đi &amp; thanh toán</h2></div>
        <dl class="info-list">
            <dt>Ngày đón</dt><dd><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?> <?= e(substr((string) ($booking['pickup_time'] ?? ''), 0, 5)) ?></dd>
            <dt>Ngày trả</dt><dd><?= e(\App\Support\Format::date($booking['return_date'] ?? null)) ?> <?= e(substr((string) ($booking['return_time'] ?? ''), 0, 5)) ?></dd>
            <dt>Xe</dt><dd><?= e($booking['vehicle_name'] ?? '—') ?></dd>
            <dt>Giá theo ngày</dt><dd><?= e(\App\Support\Format::money($booking['price_per_day'] ?? null)) ?></dd>
            <dt>Giá theo km</dt><dd><?= e(\App\Support\Format::money($booking['price_per_km'] ?? null)) ?></dd>
            <dt>Tổng tiền</dt><dd><strong><?= e(\App\Support\Format::money($booking['total_amount'] ?? null)) ?></strong></dd>
            <dt>Ngày tạo</dt><dd><?= e(\App\Support\Format::datetime($booking['created_at'] ?? null)) ?></dd>
        </dl>
    </section>
</div>

<section class="admin-card">
    <div class="admin-card-head"><h2>Xử lý đơn</h2></div>
    <div class="action-row">
        <form method="post" action="<?= url('/admin/bookings/' . (int) $booking['booking_id'] . '/status') ?>" class="inline-form">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PATCH">
            <label for="status">Chuyển trạng thái</label>
            <select id="status" name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::bookingStatus($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Cập nhật</button>
        </form>
        <?php if ($booking['status'] === 'cancelled'): ?>
            <form method="post" action="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>" data-confirm="Xóa vĩnh viễn đơn này?">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-danger" type="submit">Xóa đơn</button>
            </form>
        <?php endif; ?>
    </div>
    <p class="muted small">Luồng trạng thái: Chờ xác nhận → Đã xác nhận → Hoàn thành. Có thể hủy khi đơn chưa hoàn thành; chỉ đơn đã hủy mới được xóa.</p>
</section>

<a class="btn btn-light" href="<?= url('/admin/bookings') ?>">← Quay lại danh sách</a>
