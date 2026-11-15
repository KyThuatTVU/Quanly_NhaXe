<div class="admin-page-head">
    <div>
        <h1>Đơn đặt xe</h1>
        <p class="muted">Theo dõi và cập nhật trạng thái đơn của khách</p>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/bookings') ?>">
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::bookingStatus($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field">
        <label for="from">Từ ngày</label>
        <input id="from" type="date" name="from" value="<?= e($filters['from']) ?>">
    </div>
    <div class="form-field">
        <label for="to">Đến ngày</label>
        <input id="to" type="date" name="to" value="<?= e($filters['to']) ?>">
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Mã đơn, tên, số điện thoại...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/bookings') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Xe</th>
                    <th>Ngày đón</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings['items'] as $booking): ?>
                    <tr>
                        <td><a href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>"><strong><?= e($booking['booking_code']) ?></strong></a></td>
                        <td>
                            <?= e($booking['customer_name']) ?>
                            <div class="muted small"><?= e(\App\Support\Format::phone($booking['customer_phone'] ?? null)) ?></div>
                        </td>
                        <td><?= e($booking['vehicle_name'] ?? '—') ?></td>
                        <td><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?><div class="muted small"><?= e(substr((string) ($booking['pickup_time'] ?? ''), 0, 5)) ?></div></td>
                        <td><?= e(\App\Support\Format::money($booking['total_amount'] ?? null)) ?></td>
                        <td><span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>"><?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?></span></td>
                        <td class="table-actions"><a class="btn btn-sm btn-light" href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>">Chi tiết</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($bookings['items'] === []): ?>
                    <tr><td colspan="7" class="muted">Không có đơn phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $bookings['total'] ?> đơn</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
