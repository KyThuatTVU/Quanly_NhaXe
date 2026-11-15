<div class="admin-page-head">
    <div>
        <h1>Quản lý xe</h1>
        <p class="muted">Danh sách xe hợp đồng và trạng thái phục vụ</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/cars/create') ?>">Thêm xe</a>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/cars') ?>">
    <div class="form-field">
        <label for="vehicle_type_id">Loại xe</label>
        <select id="vehicle_type_id" name="vehicle_type_id">
            <option value="">Tất cả</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= (int) $type['id'] ?>" <?= $filters['vehicle_type_id'] === (string) $type['id'] ? 'selected' : '' ?>><?= e($type['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả</option>
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
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Xe</th><th>Loại</th><th>Giá/ngày</th><th>Giá/km</th><th>Đánh giá</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>
                            <div class="table-media">
                                <?php if (!empty($car['image'])): ?>
                                    <img src="<?= e(media((string) $car['image'])) ?>" alt="" width="56" height="40">
                                <?php else: ?>
                                    <span class="table-media-ph">TV</span>
                                <?php endif; ?>
                                <strong><?= e($car['name']) ?></strong>
                            </div>
                        </td>
                        <td><?= e($car['type_name'] ?? $car['type'] ?? '—') ?><?php if (!empty($car['type_seats'])): ?><div class="muted small"><?= (int) $car['type_seats'] ?> chỗ</div><?php endif; ?></td>
                        <td><?= e(\App\Support\Format::money($car['price_per_day'] ?? null)) ?></td>
                        <td><?= e(\App\Support\Format::money($car['price_per_km'] ?? null)) ?></td>
                        <td><?= e((string) ($car['rating'] ?? '0')) ?> / 5</td>
                        <td><span class="<?= e(\App\Support\Format::badge((string) $car['status'])) ?>"><?= e(\App\Support\Format::carStatus((string) $car['status'])) ?></span></td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-light" href="<?= url('/admin/cars/' . (int) $car['vehicle_id'] . '/edit') ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/cars/' . (int) $car['vehicle_id']) ?>" data-confirm="Xóa xe này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($cars === []): ?>
                    <tr><td colspan="7" class="muted">Không có xe phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
