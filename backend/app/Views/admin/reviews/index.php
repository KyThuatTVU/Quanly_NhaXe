<div class="admin-page-head">
    <div>
        <h1>Đánh giá</h1>
        <p class="muted">Đánh giá của khách sau chuyến đi</p>
    </div>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/reviews') ?>">
    <div class="form-field">
        <label for="visible">Hiển thị</label>
        <select id="visible" name="visible">
            <option value="">Tất cả</option>
            <option value="1" <?= $visible === '1' ? 'selected' : '' ?>>Đang hiển thị</option>
            <option value="0" <?= $visible === '0' ? 'selected' : '' ?>>Đang ẩn</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/reviews') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Xe</th><th>Khách</th><th>Số sao</th><th>Nhận xét</th><th>Hiển thị</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($reviews['items'] as $review): ?>
                    <tr>
                        <td><?= e($review['vehicle_name'] ?? '—') ?></td>
                        <td><?= e($review['username'] ?? 'Khách vãng lai') ?></td>
                        <td><span class="stars"><?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?></span></td>
                        <td class="cell-text"><?= e($review['comment'] ?? '—') ?></td>
                        <td>
                            <?php if ((int) $review['is_visible'] === 1): ?>
                                <span class="badge success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge neutral">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <form method="post" action="<?= url('/admin/reviews/' . (int) $review['rating_id'] . '/visible') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="visible" value="<?= (int) $review['is_visible'] === 1 ? '0' : '1' ?>">
                                <button class="btn btn-sm btn-light" type="submit"><?= (int) $review['is_visible'] === 1 ? 'Ẩn' : 'Hiện' ?></button>
                            </form>
                            <form method="post" action="<?= url('/admin/reviews/' . (int) $review['rating_id']) ?>" data-confirm="Xóa đánh giá này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($reviews['items'] === []): ?>
                    <tr><td colspan="6" class="muted">Không có đánh giá phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $reviews['total'] ?> đánh giá</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
