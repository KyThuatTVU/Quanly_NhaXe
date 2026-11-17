<div class="admin-page-head">
    <div>
        <h1>Liên hệ khách hàng</h1>
        <p class="muted">Yêu cầu tư vấn và báo giá thuê xe từ khách hàng gửi qua website</p>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/contacts') ?>">
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::contactStatus($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tên khách, số điện thoại, email...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/contacts') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
                <h2>Danh sách yêu cầu tư vấn</h2>
                <p class="muted small" style="margin:2px 0 0;">Nhấn vào chi tiết để xem nội dung và phản hồi khách hàng</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Tổng <?= (int) $contacts['total'] ?> yêu cầu
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Tiêu đề yêu cầu</th>
                    <th>Trạng thái</th>
                    <th>Thời gian gửi</th>
                    <th style="text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts['items'] as $contact): ?>
                    <tr>
                        <td>
                            <div class="table-user-cell">
                                <div class="user-avatar-glass">
                                    <?= e(brand_initials((string) $contact['name'])) ?>
                                </div>
                                <div>
                                    <strong class="user-name"><?= e($contact['name']) ?></strong>
                                    <div class="user-meta">
                                        <?php if (!empty($contact['phone'])): ?>
                                            <span class="user-phone">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                                <?= e(\App\Support\Format::phone($contact['phone'])) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($contact['email'])): ?>
                                            <span class="user-email"><?= e($contact['email']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="table-subject-pill">
                                <?= e($contact['subject'] ?? 'Yêu cầu tư vấn') ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?= e(\App\Support\Format::badge((string) $contact['status'])) ?>">
                                <?= e(\App\Support\Format::contactStatus((string) $contact['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="data-glass-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= e(\App\Support\Format::datetime($contact['created_at'] ?? null)) ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/contacts/' . (int) $contact['contact_id']) ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($contacts['items'] === []): ?>
                    <tr>
                        <td colspan="5" class="muted" style="text-align:center; padding:32px;">
                            Không có liên hệ phù hợp nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Hiển thị <?= count($contacts['items']) ?> / <?= (int) $contacts['total'] ?> liên hệ</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
