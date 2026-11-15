<section class="page-hero">
    <div class="container">
        <h1>Tra cứu đơn</h1>
        <p>Nhập mã đơn và số điện thoại đã dùng khi đặt xe</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <form class="card form-card" method="post" action="<?= url('/tra-cuu-don') ?>">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-field">
                    <label for="code">Mã đơn <span class="req">*</span></label>
                    <input id="code" name="code" value="<?= e($code) ?>" required placeholder="VD: DX2611140001">
                </div>
                <div class="form-field">
                    <label for="phone">Số điện thoại <span class="req">*</span></label>
                    <input id="phone" name="phone" value="<?= e($phone) ?>" required inputmode="tel">
                </div>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <button class="btn btn-primary" type="submit">Tra cứu</button>
        </form>

        <?php if (is_array($booking)): ?>
            <div class="card detail-card">
                <div class="detail-card-head">
                    <div>
                        <span class="eyebrow">Mã đơn</span>
                        <h2><?= e($booking['booking_code']) ?></h2>
                    </div>
                    <span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>"><?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?></span>
                </div>
                <dl class="info-list compact">
                    <dt>Khách hàng</dt><dd><?= e($booking['customer_name']) ?></dd>
                    <dt>Số điện thoại</dt><dd><?= e(\App\Support\Format::phone($booking['customer_phone'] ?? null)) ?></dd>
                    <dt>Điểm đón</dt><dd><?= e($booking['pickup_location']) ?></dd>
                    <?php if (!empty($booking['dropoff_location'])): ?>
                        <dt>Điểm đến</dt><dd><?= e($booking['dropoff_location']) ?></dd>
                    <?php endif; ?>
                    <dt>Thời gian đón</dt><dd><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?> <?= e(substr((string) ($booking['pickup_time'] ?? ''), 0, 5)) ?></dd>
                    <?php if (!empty($booking['return_date'])): ?>
                        <dt>Thời gian trả</dt><dd><?= e(\App\Support\Format::date($booking['return_date'])) ?> <?= e(substr((string) ($booking['return_time'] ?? ''), 0, 5)) ?></dd>
                    <?php endif; ?>
                    <dt>Số hành khách</dt><dd><?= e($booking['number_of_passengers'] ?? '—') ?></dd>
                    <dt>Tạm tính</dt><dd><strong><?= e(\App\Support\Format::money($booking['total_amount'] ?? null)) ?></strong></dd>
                </dl>
                <?php if (!empty($booking['notes'])): ?>
                    <p class="muted small">Ghi chú: <?= e($booking['notes']) ?></p>
                <?php endif; ?>
                <p class="small muted">Cần hỗ trợ? Gọi hotline <?= e($site['hotline'] ?? $site['phone'] ?? '') ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
