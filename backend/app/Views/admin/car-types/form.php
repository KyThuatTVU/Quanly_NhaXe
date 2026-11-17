<?php
$isEdit = is_array($type);
$action = $isEdit ? url('/admin/car-types/' . (int) $type['id']) : url('/admin/car-types');
$features = '';
if ($isEdit && !empty($type['features'])) {
    $decoded = json_decode((string) $type['features'], true);
    $features = is_array($decoded) ? implode(', ', $decoded) : (string) $type['features'];
}
$priceDay = isset($type['price_per_day']) ? (int) $type['price_per_day'] : '';
$priceKm = isset($type['price_per_km']) ? (int) $type['price_per_km'] : '';
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/car-types') ?>">Loại xe</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa phân khúc: ' . e($type['name'] ?? '') : 'Thêm phân khúc xe mới' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa loại xe: ' . e($type['name'] ?? '') : 'Thêm loại xe mới' ?></h1>
        <p class="muted">Cấu hình phân khúc ghế ngồi, đơn giá tham chiếu và tiện ích đi kèm</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-light" href="<?= url('/admin/car-types') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Quay lại danh sách
        </a>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
            </div>
            <div>
                <h2><?= $isEdit ? 'Phân khúc: ' . e($type['name'] ?? '') : 'Thêm loại xe mới' ?></h2>
                <p class="muted small" style="margin:2px 0 0;">Thiết lập thông số kỹ thuật phân loại xe</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            <?= $isEdit ? 'Chỉnh sửa' : 'Tạo mới' ?>
        </span>
    </div>

    <div class="form-grid">
        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
                </span>
                1. Thông tin định danh &amp; Ghế ngồi
            </span>
        </div>

        <div class="form-field">
            <label for="name">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path></svg></span>
                Tên loại xe <span class="req">*</span>
            </label>
            <input id="name" name="name" value="<?= e(old('name', $type['name'] ?? '')) ?>" required maxlength="255" placeholder="VD: Xe 4 chỗ, Xe 7 chỗ, Xe 16 chỗ...">
            <?php if (error_for('name')): ?><small class="field-error"><?= e(error_for('name')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="slug">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></span>
                Slug định danh URL
            </label>
            <input id="slug" name="slug" value="<?= e(old('slug', $type['slug'] ?? '')) ?>" maxlength="255" placeholder="VD: xe-4-cho (tự sinh nếu để trống)">
            <?php if (error_for('slug')): ?><small class="field-error"><?= e(error_for('slug')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="seats">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                Số chỗ chuẩn <span class="req">*</span>
            </label>
            <input id="seats" name="seats" type="number" min="1" max="100" value="<?= e(old('seats', $type['seats'] ?? '')) ?>" required placeholder="VD: 4, 7, 16, 29, 45">
            <?php if (error_for('seats')): ?><small class="field-error"><?= e(error_for('seats')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="display_order">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line></svg></span>
                Thứ tự hiển thị
            </label>
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

        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </span>
                2. Đơn giá tham chiếu &amp; Trạng thái
            </span>
        </div>

        <div class="form-field">
            <label for="price_per_day">Giá tham chiếu theo ngày</label>
            <div class="input-glass-wrap">
                <input id="price_per_day" name="price_per_day" type="number" min="0" step="1000" value="<?= e(old('price_per_day', $priceDay)) ?>" placeholder="800000">
                <span class="input-glass-suffix">VNĐ / ngày</span>
            </div>
            <?php if (error_for('price_per_day')): ?><small class="field-error"><?= e(error_for('price_per_day')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="price_per_km">Giá tham chiếu theo km</label>
            <div class="input-glass-wrap">
                <input id="price_per_km" name="price_per_km" type="number" min="0" step="500" value="<?= e(old('price_per_km', $priceKm)) ?>" placeholder="10000">
                <span class="input-glass-suffix">VNĐ / km</span>
            </div>
            <?php if (error_for('price_per_km')): ?><small class="field-error"><?= e(error_for('price_per_km')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="icon">Biểu tượng</label>
            <input id="icon" name="icon" value="<?= e(old('icon', $type['icon'] ?? '')) ?>" maxlength="100" placeholder="VD: car, van, bus, limousine">
            <?php if (error_for('icon')): ?><small class="field-error"><?= e(error_for('icon')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="is_active">Trạng thái phân khúc</label>
            <select id="is_active" name="is_active">
                <option value="1" <?= (string) old('is_active', $type['is_active'] ?? 1) === '1' ? 'selected' : '' ?>>Đang hiển thị cho khách chọn</option>
                <option value="0" <?= (string) old('is_active', $type['is_active'] ?? 1) === '0' ? 'selected' : '' ?>>Tạm ẩn</option>
            </select>
            <?php if (error_for('is_active')): ?><small class="field-error"><?= e(error_for('is_active')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="features">Tiện ích đi kèm (phân cách bằng dấu phẩy)</label>
            <input id="features" name="features" value="<?= e(old('features', $features)) ?>" placeholder="VD: Wifi miễn phí, Nước khoáng, Ghế da cao cấp, Cổng sạc USB">
            <?php if (error_for('features')): ?><small class="field-error"><?= e(error_for('features')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="description">Mô tả phân khúc</label>
            <textarea id="description" name="description" rows="3" maxlength="5000" placeholder="Mô tả ưu điểm của dòng xe..."><?= e(old('description', $type['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <?= $isEdit ? 'Lưu thay đổi' : 'Thêm loại xe' ?>
        </button>
        <a class="btn btn-light" href="<?= url('/admin/car-types') ?>">Hủy bỏ</a>
    </div>
</form>
