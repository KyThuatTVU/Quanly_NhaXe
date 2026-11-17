<div class="admin-page-head">
    <div>
        <h1>Quản lý xe</h1>
        <p class="muted">Danh sách xe hợp đồng và trạng thái phục vụ khách hàng</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-primary" href="<?= url('/admin/cars/create') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Thêm xe mới
        </a>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/cars') ?>">
    <div class="form-field">
        <label for="vehicle_type_id">Loại xe / Phân khúc</label>
        <select id="vehicle_type_id" name="vehicle_type_id">
            <option value="">Tất cả phân khúc</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= (int) $type['id'] ?>" <?= $filters['vehicle_type_id'] === (string) $type['id'] ? 'selected' : '' ?>><?= e($type['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::carStatus($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tên xe, loại xe...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/cars') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><path d="M16 3H8l-3 4h14l-3-4z"></path><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            </div>
            <div>
                <h2>Đội xe phục vụ khách</h2>
                <p class="muted small" style="margin:2px 0 0;">Danh sách phương tiện đang vận hành của Thảo Vy Travel</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Tổng <?= count($cars) ?> phương tiện
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Phương tiện</th>
                    <th>Phân khúc</th>
                    <th>Giá / ngày</th>
                    <th>Giá / km</th>
                    <th>Đánh giá</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>
                            <div class="table-media">
                                <?php if (!empty($car['image'])): ?>
                                    <img src="<?= e(media((string) $car['image'])) ?>" alt="" width="60" height="42">
                                <?php else: ?>
                                    <span class="table-media-ph"><?= e(brand_initials((string) ($site['name'] ?? ''))) ?></span>
                                <?php endif; ?>
                                <div>
                                    <strong style="font-size:0.96rem; color:var(--forest);"><?= e($car['name']) ?></strong>
                                    <?php if (!empty($car['type'])): ?>
                                        <div class="muted small" style="margin-top:2px;"><?= e($car['type']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="table-subject-pill">
                                <?= e($car['type_name'] ?? $car['type'] ?? '—') ?>
                                <?php if (!empty($car['type_seats'])): ?>
                                    <span style="color:#059669; font-weight:700; font-size:0.8rem;">(<?= (int) $car['type_seats'] ?> chỗ)</span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><span class="data-glass-badge"><?= e(\App\Support\Format::money($car['price_per_day'] ?? null)) ?></span></td>
                        <td><span class="data-glass-badge"><?= e(\App\Support\Format::money($car['price_per_km'] ?? null)) ?></span></td>
                        <td><span class="data-glass-badge" style="color:#ca8a04;">★ <?= e((string) ($car['rating'] ?? '5.0')) ?> / 5</span></td>
                        <td><span class="<?= e(\App\Support\Format::badge((string) $car['status'])) ?>"><?= e(\App\Support\Format::carStatus((string) $car['status'])) ?></span></td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/cars/' . (int) $car['vehicle_id'] . '/edit') ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Sửa
                            </a>
                            <form method="post" action="<?= url('/admin/cars/' . (int) $car['vehicle_id']) ?>" data-confirm="Xóa xe này khỏi hệ thống?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit" title="Xóa xe">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($cars === []): ?>
                    <tr><td colspan="7" class="muted" style="text-align:center; padding:32px;">Không có xe phù hợp nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
