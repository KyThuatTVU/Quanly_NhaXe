<div class="admin-page-head">
    <div>
        <h1>Nhật ký hệ thống</h1>
        <p class="muted">Ghi nhận thao tác quan trọng của người dùng quản trị</p>
    </div>
</div>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Thời gian</th><th>Người dùng</th><th>Hành động</th><th>Đối tượng</th><th>Mô tả</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs['items'] as $log): ?>
                    <tr>
                        <td><?= e(\App\Support\Format::datetime($log['created_at'] ?? null)) ?></td>
                        <td><?= e($log['username'] ?? 'Hệ thống') ?></td>
                        <td><code><?= e($log['action']) ?></code></td>
                        <td>
                            <?= e($log['entity_type'] ?? '—') ?>
                            <?php if (!empty($log['entity_id'])): ?><span class="muted small">#<?= (int) $log['entity_id'] ?></span><?php endif; ?>
                        </td>
                        <td class="cell-text"><?= e($log['description'] ?? '—') ?></td>
                        <td><?= e($log['ip_address'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($logs['items'] === []): ?>
                    <tr><td colspan="6" class="muted">Chưa có nhật ký.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $logs['total'] ?> bản ghi</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
