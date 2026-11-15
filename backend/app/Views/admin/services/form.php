<?php
$isEdit = is_array($service);
$action = $isEdit ? url('/admin/services/' . (int) $service['service_id']) : url('/admin/services');
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/services') ?>">Dịch vụ</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa dịch vụ' : 'Thêm dịch vụ' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa dịch vụ' : 'Thêm dịch vụ' ?></h1>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="form-grid">
        <div class="form-field">
            <label for="service_name">Tên dịch vụ <span class="req">*</span></label>
            <input id="service_name" name="service_name" value="<?= e(old('service_name', $service['service_name'] ?? '')) ?>" required maxlength="100">
            <?php if (error_for('service_name')): ?><small class="field-error"><?= e(error_for('service_name')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="<?= e(old('slug', $service['slug'] ?? '')) ?>" maxlength="255" placeholder="Tự sinh nếu để trống">
            <?php if (error_for('slug')): ?><small class="field-error"><?= e(error_for('slug')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="price">Giá dịch vụ</label>
            <input id="price" name="price" type="number" min="0" step="1000" value="<?= e(old('price', $service['price'] ?? '')) ?>">
            <?php if (error_for('price')): ?><small class="field-error"><?= e(error_for('price')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="display_order">Thứ tự hiển thị</label>
            <input id="display_order" name="display_order" type="number" value="<?= e(old('display_order', $service['display_order'] ?? 0)) ?>">
            <?php if (error_for('display_order')): ?><small class="field-error"><?= e(error_for('display_order')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="image">Đường dẫn ảnh</label>
            <input id="image" name="image" value="<?= e(old('image', $service['image'] ?? '')) ?>" maxlength="255" placeholder="uploads/services/...">
            <?php if (error_for('image')): ?><small class="field-error"><?= e(error_for('image')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="image_file">Tải ảnh mới</label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (error_for('file')): ?><small class="field-error"><?= e(error_for('file')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="is_active">Hoạt động</label>
            <select id="is_active" name="is_active">
                <option value="1" <?= (string) old('is_active', $service['is_active'] ?? 1) === '1' ? 'selected' : '' ?>>Đang hiển thị</option>
                <option value="0" <?= (string) old('is_active', $service['is_active'] ?? 1) === '0' ? 'selected' : '' ?>>Tạm ẩn</option>
            </select>
            <?php if (error_for('is_active')): ?><small class="field-error"><?= e(error_for('is_active')) ?></small><?php endif; ?>
        </div>
        <?php if (!empty($service['image'])): ?>
            <div class="form-field">
                <label>Ảnh hiện tại</label>
                <img class="preview-img" src="<?= e(media((string) $service['image'])) ?>" alt="">
            </div>
        <?php endif; ?>
        <div class="form-field span-2">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="4" maxlength="5000"><?= e(old('description', $service['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm dịch vụ' ?></button>
        <a class="btn btn-light" href="<?= url('/admin/services') ?>">Hủy</a>
    </div>
</form>
