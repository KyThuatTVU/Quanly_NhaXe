<?php
$isEdit = is_array($car);
$action = $isEdit ? url('/admin/cars/' . (int) $car['vehicle_id']) : url('/admin/cars');
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/cars') ?>">Xe</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa xe' : 'Thêm xe' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa xe' : 'Thêm xe' ?></h1>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="form-grid">
        <div class="form-field">
            <label for="name">Tên xe <span class="req">*</span></label>
            <input id="name" name="name" value="<?= e(old('name', $car['name'] ?? '')) ?>" required maxlength="100">
            <?php if (error_for('name')): ?><small class="field-error"><?= e(error_for('name')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="vehicle_type_id">Loại xe <span class="req">*</span></label>
            <select id="vehicle_type_id" name="vehicle_type_id" required>
                <option value="">-- Chọn loại xe --</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= (int) $type['id'] ?>" <?= (string) old('vehicle_type_id', $car['vehicle_type_id'] ?? '') === (string) $type['id'] ? 'selected' : '' ?>><?= e($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (error_for('vehicle_type_id')): ?><small class="field-error"><?= e(error_for('vehicle_type_id')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="price_per_day">Giá theo ngày</label>
            <input id="price_per_day" name="price_per_day" type="number" min="0" step="1000" value="<?= e(old('price_per_day', $car['price_per_day'] ?? '')) ?>">
            <?php if (error_for('price_per_day')): ?><small class="field-error"><?= e(error_for('price_per_day')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="price_per_km">Giá theo km</label>
            <input id="price_per_km" name="price_per_km" type="number" min="0" step="500" value="<?= e(old('price_per_km', $car['price_per_km'] ?? '')) ?>">
            <?php if (error_for('price_per_km')): ?><small class="field-error"><?= e(error_for('price_per_km')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="status">Trạng thái</label>
            <select id="status" name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= (string) old('status', $car['status'] ?? 'available') === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::carStatus($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (error_for('status')): ?><small class="field-error"><?= e(error_for('status')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="type">Nhãn hiển thị (tuỳ chọn)</label>
            <input id="type" name="type" value="<?= e(old('type', $car['type'] ?? '')) ?>" maxlength="50" placeholder="VD: Xe 7 chỗ cao cấp">
            <?php if (error_for('type')): ?><small class="field-error"><?= e(error_for('type')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="image">Đường dẫn ảnh</label>
            <input id="image" name="image" value="<?= e(old('image', $car['image'] ?? '')) ?>" maxlength="255" placeholder="uploads/cars/...">
            <small class="muted small">Dán đường dẫn có sẵn hoặc tải ảnh mới bên dưới.</small>
            <?php if (error_for('image')): ?><small class="field-error"><?= e(error_for('image')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="image_file">Tải ảnh mới</label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (error_for('file')): ?><small class="field-error"><?= e(error_for('file')) ?></small><?php endif; ?>
        </div>
        <?php if (!empty($car['image'])): ?>
            <div class="form-field span-2">
                <label>Ảnh hiện tại</label>
                <img class="preview-img" src="<?= e(media((string) $car['image'])) ?>" alt="">
            </div>
        <?php endif; ?>
        <div class="form-field span-2">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="3" maxlength="5000"><?= e(old('description', $car['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="driver_info">Thông tin tài xế</label>
            <textarea id="driver_info" name="driver_info" rows="2" maxlength="2000"><?= e(old('driver_info', $car['driver_info'] ?? '')) ?></textarea>
            <?php if (error_for('driver_info')): ?><small class="field-error"><?= e(error_for('driver_info')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="insurance_info">Thông tin bảo hiểm</label>
            <textarea id="insurance_info" name="insurance_info" rows="2" maxlength="2000"><?= e(old('insurance_info', $car['insurance_info'] ?? '')) ?></textarea>
            <?php if (error_for('insurance_info')): ?><small class="field-error"><?= e(error_for('insurance_info')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm xe' ?></button>
        <a class="btn btn-light" href="<?= url('/admin/cars') ?>">Hủy</a>
    </div>
</form>
