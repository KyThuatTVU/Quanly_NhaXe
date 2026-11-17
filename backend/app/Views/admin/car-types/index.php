<div class="admin-page-head">
    <div>
        <h1>Phân khúc &amp; Loại xe</h1>
        <p class="muted">Danh mục các dòng xe từ 4 đến 45 chỗ hiển thị trên website</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-primary" href="<?= url('/admin/car-types/create') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Thêm phân khúc mới
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
            </div>
            <div>
                <h2>Danh sách các loại xe phục vụ</h2>
                <p class="muted small" style="margin:2px 0 0;">Phân loại theo số chỗ ngồi và mức giá tham chiếu</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Tổng <?= count($types) ?> phân khúc
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tên phân khúc</th>
                    <th style="text-align:center;">Số chỗ ngồi</th>
                    <th>Giá / ngày</th>
                    <th>Giá / km</th>
                    <th style="text-align:center;">Thứ tự</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td>
                            <strong style="color:var(--forest); font-size:0.96rem;"><?= e($type['name']) ?></strong>
                            <?php if (!empty($type['slug'])): ?>
                                <div class="muted small" style="margin-top:2px;"><?= e($type['slug']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <span class="data-glass-badge">
                                <?= (int) $type['seats'] ?> chỗ
                                <?php if (!empty($type['seats_min']) || !empty($type['seats_max'])): ?>
                                    <span style="font-size:0.75rem; color:#64748b;">(<?= e($type['seats_min'] ?? '?') ?>-<?= e($type['seats_max'] ?? '?') ?>)</span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><span class="data-glass-badge"><?= e(\App\Support\Format::money($type['price_per_day'] ?? null)) ?></span></td>
                        <td><span class="data-glass-badge"><?= e(\App\Support\Format::money($type['price_per_km'] ?? null)) ?></span></td>
                        <td style="text-align:center;"><span class="data-glass-badge"><?= (int) ($type['display_order'] ?? 0) ?></span></td>
                        <td>
                            <?php if ((int) $type['is_active'] === 1): ?>
                                <span class="badge success">Đang hiển thị</span>
                            <?php else: ?>
                                <span class="badge neutral">Đã tạm ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/car-types/' . (int) $type['id'] . '/edit') ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Sửa
                            </a>
                            <form method="post" action="<?= url('/admin/car-types/' . (int) $type['id']) ?>" data-confirm="Xóa phân khúc xe này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit" title="Xóa phân khúc">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($types === []): ?>
                    <tr><td colspan="7" class="muted" style="text-align:center; padding:32px;">Chưa có phân khúc xe nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
