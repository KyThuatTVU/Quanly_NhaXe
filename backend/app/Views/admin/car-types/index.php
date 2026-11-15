<div class="admin-page-head">
    <div>
        <h1>Loại xe</h1>
        <p class="muted">Danh mục loại xe hiển thị trên website</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/car-types/create') ?>">Thêm loại xe</a>
</div>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Tên loại xe</th><th>Số chỗ</th><th>Giá/ngày</th><th>Giá/km</th><th>Thứ tự</th><th>Hoạt động</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td>
                            <strong><?= e($type['name']) ?></strong>
                            <div class="muted small"><?= e($type['slug'] ?? '') ?></div>
                        </td>
                        <td>
                            <?= (int) $type['seats'] ?>
                            <?php if (!empty($type['seats_min']) || !empty($type['seats_max'])): ?>
                                <div class="muted small"><?= e($type['seats_min'] ?? '?') ?> - <?= e($type['seats_max'] ?? '?') ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= e(\App\Support\Format::money($type['price_per_day'] ?? null)) ?></td>
                        <td><?= e(\App\Support\Format::money($type['price_per_km'] ?? null)) ?></td>
                        <td><?= (int) ($type['display_order'] ?? 0) ?></td>
                        <td>
                            <?php if ((int) $type['is_active'] === 1): ?>
                                <span class="badge success">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge neutral">Đã ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-light" href="<?= url('/admin/car-types/' . (int) $type['id'] . '/edit') ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/car-types/' . (int) $type['id']) ?>" data-confirm="Xóa loại xe này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($types === []): ?>
                    <tr><td colspan="7" class="muted">Chưa có loại xe.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
