<div class="admin-page-head">
    <div>
        <h1>Đơn đặt xe</h1>
        <p class="muted">Theo dõi và cập nhật trạng thái đơn đặt xe trực tuyến của khách hàng</p>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/bookings') ?>">
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::bookingStatus($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field">
        <label for="from">Từ ngày đón</label>
        <input id="from" type="date" name="from" value="<?= e($filters['from']) ?>">
    </div>
    <div class="form-field">
        <label for="to">Đến ngày đón</label>
        <input id="to" type="date" name="to" value="<?= e($filters['to']) ?>">
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Mã đơn, tên khách, số điện thoại...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/bookings') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            </div>
            <div>
                <h2>Danh sách đơn đặt xe</h2>
                <p class="muted small" style="margin:2px 0 0;">Quản lý toàn bộ chuyến xe và tiến độ thực hiện</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Tổng <?= (int) $bookings['total'] ?> đơn
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Xe yêu cầu</th>
                    <th>Ngày đón</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings['items'] as $booking): ?>
                    <tr>
                        <td>
                            <a href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>" class="data-glass-badge" style="color:var(--forest); font-weight:800; text-decoration:none;">
                                #<?= e($booking['booking_code']) ?>
                            </a>
                        </td>
                        <td>
                            <div class="table-user-cell">
                                <div class="user-avatar-glass">
                                    <?= e(brand_initials((string) $booking['customer_name'])) ?>
                                </div>
                                <div>
                                    <strong class="user-name"><?= e($booking['customer_name']) ?></strong>
                                    <div class="user-meta">
                                        <?php if (!empty($booking['customer_phone'])): ?>
                                            <span class="user-phone">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                                <?= e(\App\Support\Format::phone($booking['customer_phone'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="table-subject-pill">
                                <?= e($booking['vehicle_name'] ?? 'Chưa chỉ định') ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:2px;">
                                <strong style="font-weight:700; color:var(--ink);"><?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?></strong>
                                <?php if (!empty($booking['pickup_time'])): ?>
                                    <span class="muted small" style="color:#059669; font-weight:600;"><?= e(substr((string) $booking['pickup_time'], 0, 5)) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="data-glass-badge" style="background:linear-gradient(140deg, rgba(255,255,255,0.95), rgba(220,252,231,0.85));">
                                <?= e(\App\Support\Format::money($booking['total_amount'] ?? null)) ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>">
                                <?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($bookings['items'] === []): ?>
                    <tr>
                        <td colspan="7" class="muted" style="text-align:center; padding:32px;">
                            Không có đơn đặt xe phù hợp nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Hiển thị <?= count($bookings['items']) ?> / <?= (int) $bookings['total'] ?> đơn</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
