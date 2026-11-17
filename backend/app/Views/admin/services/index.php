<div class="admin-page-head">
    <div>
        <h1>Quản lý dịch vụ</h1>
        <p class="muted">Các gói dịch vụ vận chuyển hành khách hiển thị trên website</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-primary" href="<?= url('/admin/services/create') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Thêm dịch vụ mới
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
            <div>
                <h2>Danh mục các gói dịch vụ</h2>
                <p class="muted small" style="margin:2px 0 0;">Cung cấp cho khách hàng lựa chọn theo nhu cầu di chuyển</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Tổng <?= count($services) ?> gói dịch vụ
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Gói dịch vụ</th>
                    <th>Giá tham chiếu</th>
                    <th style="text-align:center;">Thứ tự</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td>
                            <div class="table-media">
                                <?php if (!empty($service['image'])): ?>
                                    <img src="<?= e(media((string) $service['image'])) ?>" alt="" width="60" height="42">
                                <?php else: ?>
                                    <span class="table-media-ph">DV</span>
                                <?php endif; ?>
                                <div>
                                    <strong style="font-size:0.96rem; color:var(--forest);"><?= e($service['service_name']) ?></strong>
                                    <?php if (!empty($service['description'])): ?>
                                        <div class="muted small" style="margin-top:2px;"><?= e(mb_substr((string) $service['description'], 0, 75)) ?>...</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="data-glass-badge">
                                <?= !empty($service['price']) ? e(\App\Support\Format::money($service['price'])) : 'Thỏa thuận' ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="data-glass-badge"><?= (int) ($service['display_order'] ?? 0) ?></span>
                        </td>
                        <td>
                            <?php if ((int) $service['is_active'] === 1): ?>
                                <span class="badge success">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge neutral">Đã tạm ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/services/' . (int) $service['service_id'] . '/edit') ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Sửa
                            </a>
                            <form method="post" action="<?= url('/admin/services/' . (int) $service['service_id']) ?>" data-confirm="Xóa dịch vụ này khỏi hệ thống?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit" title="Xóa dịch vụ">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($services === []): ?>
                    <tr><td colspan="5" class="muted" style="text-align:center; padding:32px;">Chưa có dịch vụ nào trong hệ thống.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
