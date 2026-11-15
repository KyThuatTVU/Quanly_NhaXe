<section class="section">
    <div class="container narrow">
        <div class="card success-card">
            <div class="success-icon">✓</div>
            <h1>Đặt xe thành công</h1>
            <p class="muted">Cảm ơn bạn đã đặt xe. Nhân viên sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>

            <div class="code-box">
                <span class="small">Mã đơn của bạn</span>
                <strong><?= e($booking['booking_code']) ?></strong>
                <span class="small muted">Dùng mã đơn và số điện thoại để tra cứu trạng thái</span>
            </div>

            <dl class="info-list compact summary-list">
                <dt>Khách hàng</dt><dd><?= e($booking['customer_name']) ?> · <?= e(\App\Support\Format::phone($booking['customer_phone'] ?? null)) ?></dd>
                <dt>Điểm đón</dt><dd><?= e($booking['pickup_location']) ?></dd>
                <?php if (!empty($booking['dropoff_location'])): ?>
                    <dt>Điểm đến</dt><dd><?= e($booking['dropoff_location']) ?></dd>
                <?php endif; ?>
                <dt>Thời gian đón</dt><dd><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?> <?= e(substr((string) ($booking['pickup_time'] ?? ''), 0, 5)) ?></dd>
                <?php if (!empty($booking['return_date'])): ?>
                    <dt>Thời gian trả</dt><dd><?= e(\App\Support\Format::date($booking['return_date'])) ?> <?= e(substr((string) ($booking['return_time'] ?? ''), 0, 5)) ?></dd>
                <?php endif; ?>
                <dt>Trạng thái</dt><dd><span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>"><?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?></span></dd>
                <dt>Tạm tính</dt><dd><strong><?= e(\App\Support\Format::money($booking['total_amount'] ?? null)) ?></strong></dd>
            </dl>

            <div class="detail-actions center">
                <a class="btn btn-primary" href="<?= url('/tra-cuu-don') ?>">Tra cứu đơn</a>
                <a class="btn btn-outline" href="<?= url('/') ?>">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>
