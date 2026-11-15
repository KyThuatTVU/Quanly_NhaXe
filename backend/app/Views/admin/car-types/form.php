<?php
$isEdit = is_array($type);
$action = $isEdit ? url('/admin/car-types/' . (int) $type['id']) : url('/admin/car-types');
$features = '';
if ($isEdit && !empty($type['features'])) {
    $decoded = json_decode((string) $type['features'], true);
    $features = is_array($decoded) ? implode(', ', $decoded) : (string) $type['features'];
}
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/car-types') ?>">Loại xe</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa loại xe' : 'Thêm loại xe' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa loại xe' : 'Thêm loại xe' ?></h1>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="form-grid">
        <div class="form-field">
            <label for="name">Tên loại xe <span class="req">*</span></label>
            <input id="name" name="name" value="<?= e(old('name', $type['name'] ?? '')) ?>" required maxlength="255">
            <?php if (error_for('name')): ?><small class="field-error"><?= e(error_for('name')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="<?= e(old('slug', $type['slug'] ?? '')) ?>" maxlength="255" placeholder="Tự sinh nếu để trống">
            <?php if (error_for('slug')): ?><small class="field-error"><?= e(error_for('slug')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="seats">Số chỗ <span class="req">*</span></label>
            <input id="seats" name="seats" type="number" min="1" max="100" value="<?= e(old('seats', $type['seats'] ?? '')) ?>" required>
            <?php if (error_for('seats')): ?><small class="field-error"><?= e(error_for('seats')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="display_order">Thứ tự hiển thị</label>
            <input id="display_order" name="display_order" type="number" value="<?= e(old('display_order', $type['display_order'] ?? 0)) ?>">
            <?php if (error_for('display_order')): ?><small class="field-error"><?= e(error_for('display_order')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="seats_min">Số chỗ tối thiểu</label>
            <input id="seats_min" name="seats_min" type="number" min="1" max="100" value="<?= e(old('seats_min', $type['seats_min'] ?? '')) ?>">
            <?php if (error_for('seats_min')): ?><small class="field-error"><?= e(error_for('seats_min')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="seats_max">Số chỗ tối đa</label>
            <input id="seats_max" name="seats_max" type="number" min="1" max="100" value="<?= e(old('seats_max', $type['seats_max'] ?? '')) ?>">
            <?php if (error_for('seats_max')): ?><small class="field-error"><?= e(error_for('seats_max')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="price_per_day">Giá theo ngày</label>
            <input id="price_per_day" name="price_per_day" type="number" min="0" step="1000" value="<?= e(old('price_per_day', $type['price_per_day'] ?? '')) ?>">
            <?php if (error_for('price_per_day')): ?><small class="field-error"><?= e(error_for('price_per_day')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="price_per_km">Giá theo km</label>
            <input id="price_per_km" name="price_per_km" type="number" min="0" step="500" value="<?= e(old('price_per_km', $type['price_per_km'] ?? '')) ?>">
            <?php if (error_for('price_per_km')): ?><small class="field-error"><?= e(error_for('price_per_km')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="icon">Biểu tượng</label>
            <input id="icon" name="icon" value="<?= e(old('icon', $type['icon'] ?? '')) ?>" maxlength="100" placeholder="VD: bus, van, limousine">
            <?php if (error_for('icon')): ?><small class="field-error"><?= e(error_for('icon')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="is_active">Hoạt động</label>
            <select id="is_active" name="is_active">
                <option value="1" <?= (string) old('is_active', $type['is_active'] ?? 1) === '1' ? 'selected' : '' ?>>Đang hiển thị</option>
                <option value="0" <?= (string) old('is_active', $type['is_active'] ?? 1) === '0' ? 'selected' : '' ?>>Tạm ẩn</option>
            </select>
            <?php if (error_for('is_active')): ?><small class="field-error"><?= e(error_for('is_active')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="features">Tiện ích (phân cách bằng dấu phẩy)</label>
            <input id="features" name="features" value="<?= e(old('features', $features)) ?>" placeholder="Wifi, nước uống, ghế trẻ em">
            <?php if (error_for('features')): ?><small class="field-error"><?= e(error_for('features')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="3" maxlength="5000"><?= e(old('description', $type['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm loại xe' ?></button>
        <a class="btn btn-light" href="<?= url('/admin/car-types') ?>">Hủy</a>
    </div>
</form>
