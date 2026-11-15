<section class="page-hero">
    <div class="container">
        <h1>Bài viết</h1>
        <p>Kinh nghiệm thuê xe, tin tức và cẩm nang du lịch</p>
    </div>
</section>

<section class="section">
    <div class="container posts-layout">
        <div>
            <form class="filter-bar" method="get" action="<?= url('/bai-viet') ?>">
                <div class="form-field">
                    <label for="category">Danh mục</label>
                    <select id="category" name="category">
                        <option value="">Tất cả</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['name']) ?>" <?= $category['name'] === $filters['category'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?> (<?= (int) $category['total'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field grow">
                    <label for="search">Tìm kiếm</label>
                    <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tiêu đề bài viết...">
                </div>
                <button class="btn btn-primary" type="submit">Tìm</button>
            </form>

            <div class="grid grid-2">
                <?php foreach ($posts['items'] as $post): ?>
                    <?php $view->partial('partials/post-card', ['post' => $post]); ?>
                <?php endforeach; ?>
            </div>
            <?php if ($posts['items'] === []): ?>
                <div class="empty-state"><h3>Không có bài viết</h3><p>Thử từ khóa khác hoặc quay lại sau.</p></div>
            <?php endif; ?>
            <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
        </div>
        <aside class="info-card">
            <h3>Danh mục</h3>
            <ul class="footer-links">
                <li><a href="<?= url('/bai-viet') ?>">Tất cả bài viết</a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="<?= url('/bai-viet') ?>?category=<?= e(urlencode($category['name'])) ?>"><?= e($category['name']) ?> (<?= (int) $category['total'] ?>)</a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>
