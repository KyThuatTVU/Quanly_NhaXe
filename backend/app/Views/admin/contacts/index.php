<div class="admin-page-head">
    <div>
        <h1>Liên hệ khách hàng</h1>
        <p class="muted">Yêu cầu tư vấn gửi từ website</p>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/contacts') ?>">
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::contactStatus($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tên, số điện thoại, email...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/contacts') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Khách hàng</th><th>Tiêu đề</th><th>Trạng thái</th><th>Ngày gửi</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($contacts['items'] as $contact): ?>
                    <tr>
                        <td>
                            <strong><?= e($contact['name']) ?></strong>
                            <div class="muted small"><?= e(\App\Support\Format::phone($contact['phone'] ?? null)) ?><?php if (!empty($contact['email'])): ?> · <?= e($contact['email']) ?><?php endif; ?></div>
                        </td>
                        <td><?= e($contact['subject'] ?? '—') ?></td>
                        <td><span class="<?= e(\App\Support\Format::badge((string) $contact['status'])) ?>"><?= e(\App\Support\Format::contactStatus((string) $contact['status'])) ?></span></td>
                        <td><?= e(\App\Support\Format::datetime($contact['created_at'] ?? null)) ?></td>
                        <td class="table-actions"><a class="btn btn-sm btn-light" href="<?= url('/admin/contacts/' . (int) $contact['contact_id']) ?>">Xem</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($contacts['items'] === []): ?>
                    <tr><td colspan="5" class="muted">Không có liên hệ phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $contacts['total'] ?> liên hệ</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
