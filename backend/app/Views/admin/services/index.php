<div class="admin-page-head">
    <div>
        <h1>Quản lý dịch vụ</h1>
        <p class="muted">Các gói dịch vụ vận chuyển hiển thị trên website</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/services/create') ?>">Thêm dịch vụ</a>
</div>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Dịch vụ</th><th>Giá</th><th>Thứ tự</th><th>Hoạt động</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td>
                            <div class="table-media">
                                <?php if (!empty($service['image'])): ?>
                                    <img src="<?= e(media((string) $service['image'])) ?>" alt="" width="56" height="40">
                                <?php else: ?>
                                    <span class="table-media-ph">DV</span>
                                <?php endif; ?>
                                <span>
                                    <strong><?= e($service['service_name']) ?></strong>
                                    <div class="muted small"><?= e(mb_substr((string) ($service['description'] ?? ''), 0, 80)) ?></div>
                                </span>
                            </div>
                        </td>
                        <td><?= e(\App\Support\Format::money($service['price'] ?? null)) ?></td>
                        <td><?= (int) ($service['display_order'] ?? 0) ?></td>
                        <td>
                            <?php if ((int) $service['is_active'] === 1): ?>
                                <span class="badge success">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge neutral">Đã ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-light" href="<?= url('/admin/services/' . (int) $service['service_id'] . '/edit') ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/services/' . (int) $service['service_id']) ?>" data-confirm="Xóa dịch vụ này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($services === []): ?>
                    <tr><td colspan="5" class="muted">Chưa có dịch vụ.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
