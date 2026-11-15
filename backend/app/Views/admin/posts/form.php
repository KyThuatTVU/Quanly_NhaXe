<?php
$isEdit = is_array($post);
$action = $isEdit ? url('/admin/posts/' . (int) $post['post_id']) : url('/admin/posts');
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/posts') ?>">Bài viết</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa bài viết' : 'Thêm bài viết' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa bài viết' : 'Thêm bài viết' ?></h1>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="form-grid">
        <div class="form-field span-2">
            <label for="title">Tiêu đề <span class="req">*</span></label>
            <input id="title" name="title" value="<?= e(old('title', $post['title'] ?? '')) ?>" required maxlength="255">
            <?php if (error_for('title')): ?><small class="field-error"><?= e(error_for('title')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="<?= e(old('slug', $post['slug'] ?? '')) ?>" maxlength="255" placeholder="Tự sinh nếu để trống">
            <?php if (error_for('slug')): ?><small class="field-error"><?= e(error_for('slug')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="category">Danh mục</label>
            <input id="category" name="category" list="post-categories" value="<?= e(old('category', $post['category'] ?? '')) ?>" maxlength="50">
            <datalist id="post-categories">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['name']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <?php if (error_for('category')): ?><small class="field-error"><?= e(error_for('category')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="status">Trạng thái</label>
            <select id="status" name="status">
                <option value="published" <?= (string) old('status', $post['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Đã đăng</option>
                <option value="draft" <?= (string) old('status', $post['status'] ?? 'published') === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
            </select>
            <?php if (error_for('status')): ?><small class="field-error"><?= e(error_for('status')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="image">Đường dẫn ảnh</label>
            <input id="image" name="image" value="<?= e(old('image', $post['image'] ?? '')) ?>" maxlength="255" placeholder="uploads/posts/...">
            <?php if (error_for('image')): ?><small class="field-error"><?= e(error_for('image')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="image_file">Tải ảnh mới</label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (error_for('file')): ?><small class="field-error"><?= e(error_for('file')) ?></small><?php endif; ?>
        </div>
        <?php if (!empty($post['image'])): ?>
            <div class="form-field span-2">
                <label>Ảnh hiện tại</label>
                <img class="preview-img" src="<?= e(media((string) $post['image'])) ?>" alt="">
            </div>
        <?php endif; ?>
        <div class="form-field span-2">
            <label for="content">Nội dung</label>
            <textarea id="content" name="content" rows="14"><?= e(old('content', $post['content'] ?? '')) ?></textarea>
            <?php if (error_for('content')): ?><small class="field-error"><?= e(error_for('content')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm bài viết' ?></button>
        <a class="btn btn-light" href="<?= url('/admin/posts') ?>">Hủy</a>
    </div>
</form>
