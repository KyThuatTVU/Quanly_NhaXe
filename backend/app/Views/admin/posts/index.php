<div class="admin-page-head">
    <div>
        <h1>Bài viết</h1>
        <p class="muted">Tin tức, kinh nghiệm thuê xe và cẩm nang du lịch</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/posts/create') ?>">Thêm bài viết</a>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/posts') ?>">
    <div class="form-field">
        <label for="status">Trạng thái</label>
        <select id="status" name="status">
            <option value="">Tất cả</option>
            <option value="published" <?= $filters['status'] === 'published' ? 'selected' : '' ?>>Đã đăng</option>
            <option value="draft" <?= $filters['status'] === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
        </select>
    </div>
    <div class="form-field">
        <label for="category">Danh mục</label>
        <select id="category" name="category">
            <option value="">Tất cả</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['name']) ?>" <?= $filters['category'] === $category['name'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tiêu đề bài viết...">
    </div>
    <button class="btn btn-primary" type="submit">Lọc</button>
    <a class="btn btn-light" href="<?= url('/admin/posts') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Bài viết</th><th>Danh mục</th><th>Trạng thái</th><th>Ngày tạo</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($posts['items'] as $post): ?>
                    <tr>
                        <td>
                            <div class="table-media">
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?= e(media((string) $post['image'])) ?>" alt="" width="56" height="40">
                                <?php else: ?>
                                    <span class="table-media-ph">BV</span>
                                <?php endif; ?>
                                <span>
                                    <strong><?= e($post['title']) ?></strong>
                                    <div class="muted small"><?= e($post['slug']) ?></div>
                                </span>
                            </div>
                        </td>
                        <td><?= e($post['category'] ?? '—') ?></td>
                        <td><span class="<?= e(\App\Support\Format::badge((string) $post['status'])) ?>"><?= e(\App\Support\Format::postStatus((string) $post['status'])) ?></span></td>
                        <td><?= e(\App\Support\Format::date($post['created_at'] ?? null)) ?></td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-light" href="<?= url('/admin/posts/' . (int) $post['post_id'] . '/edit') ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/posts/' . (int) $post['post_id']) ?>" data-confirm="Xóa bài viết này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($posts['items'] === []): ?>
                    <tr><td colspan="5" class="muted">Không có bài viết phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $posts['total'] ?> bài viết</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
