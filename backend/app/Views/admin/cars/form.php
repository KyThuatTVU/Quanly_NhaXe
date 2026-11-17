<?php
$isEdit = is_array($car);
$action = $isEdit ? url('/admin/cars/' . (int) $car['vehicle_id']) : url('/admin/cars');
$priceDay = isset($car['price_per_day']) ? (int) $car['price_per_day'] : '';
$priceKm = isset($car['price_per_km']) ? (int) $car['price_per_km'] : '';
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/cars') ?>">Đội xe</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa xe #' . (int) $car['vehicle_id'] : 'Thêm xe mới' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Cập nhật xe: ' . e($car['name'] ?? '') : 'Thêm xe mới vào đội' ?></h1>
        <p class="muted">Thiết lập thông tin phương tiện, giá cước và hình ảnh xe phục vụ khách</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-light" href="<?= url('/admin/cars') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Quay lại danh sách
        </a>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><path d="M16 3H8l-3 4h14l-3-4z"></path><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            </div>
            <div>
                <h2><?= $isEdit ? 'Thông tin xe #' . (int) $car['vehicle_id'] : 'Thêm xe vào hệ thống' ?></h2>
                <p class="muted small" style="margin:2px 0 0;">Nhập đầy đủ thông tin để hiển thị trên giao diện đặt xe trực tuyến</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            <?= $isEdit ? 'Đang chỉnh sửa' : 'Thêm mới' ?>
        </span>
    </div>

    <div class="form-grid">
        <!-- PHẦN 1: THÔNG SỐ VÀ GIÁ CƯỚC -->
        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
                </span>
                1. Thông số &amp; Giá cước vận hành
            </span>
        </div>

        <div class="form-field">
            <label for="name">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path><circle cx="7" cy="17" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg></span>
                Tên xe <span class="req">*</span>
            </label>
            <input id="name" name="name" value="<?= e(old('name', $car['name'] ?? '')) ?>" required maxlength="100" placeholder="VD: Toyota Vios 2022, Sedona 7 chỗ...">
            <?php if (error_for('name')): ?><small class="field-error"><?= e(error_for('name')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="vehicle_type_id">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></span>
                Loại xe / Phân khúc <span class="req">*</span>
            </label>
            <select id="vehicle_type_id" name="vehicle_type_id" required>
                <option value="">-- Chọn phân khúc loại xe --</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= (int) $type['id'] ?>" <?= (string) old('vehicle_type_id', $car['vehicle_type_id'] ?? '') === (string) $type['id'] ? 'selected' : '' ?>>
                        <?= e($type['name']) ?><?= !empty($type['seats']) ? ' (' . (int) $type['seats'] . ' chỗ)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (error_for('vehicle_type_id')): ?><small class="field-error"><?= e(error_for('vehicle_type_id')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="price_per_day">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                Giá thuê theo ngày
            </label>
            <div class="input-glass-wrap">
                <input id="price_per_day" name="price_per_day" type="number" min="0" step="1000" value="<?= e(old('price_per_day', $priceDay)) ?>" placeholder="800000">
                <span class="input-glass-suffix">VNĐ / ngày</span>
            </div>
            <?php if (error_for('price_per_day')): ?><small class="field-error"><?= e(error_for('price_per_day')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="price_per_km">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg></span>
                Giá cước theo km
            </label>
            <div class="input-glass-wrap">
                <input id="price_per_km" name="price_per_km" type="number" min="0" step="500" value="<?= e(old('price_per_km', $priceKm)) ?>" placeholder="10000">
                <span class="input-glass-suffix">VNĐ / km</span>
            </div>
            <?php if (error_for('price_per_km')): ?><small class="field-error"><?= e(error_for('price_per_km')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="status">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>
                Trạng thái xe
            </label>
            <select id="status" name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= (string) old('status', $car['status'] ?? 'available') === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::carStatus($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (error_for('status')): ?><small class="field-error"><?= e(error_for('status')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="type">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg></span>
                Nhãn hiển thị nổi bật (tuỳ chọn)
            </label>
            <input id="type" name="type" value="<?= e(old('type', $car['type'] ?? '')) ?>" maxlength="50" placeholder="VD: Xe 4 chỗ tiện nghi, Đón sân bay...">
            <?php if (error_for('type')): ?><small class="field-error"><?= e(error_for('type')) ?></small><?php endif; ?>
        </div>

        <!-- PHẦN 2: HÌNH ẢNH PHƯƠNG TIỆN -->
        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </span>
                2. Hình ảnh &amp; Nhận diện xe
            </span>
        </div>

        <div class="form-field">
            <label for="image">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></span>
                Đường dẫn ảnh
            </label>
            <input id="image" name="image" value="<?= e(old('image', $car['image'] ?? '')) ?>" maxlength="255" placeholder="uploads/cars/... hoặc link ảnh">
            <small class="muted small">Dán đường dẫn ảnh có sẵn hoặc chọn tệp tải mới bên cạnh.</small>
            <?php if (error_for('image')): ?><small class="field-error"><?= e(error_for('image')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="image_file">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg></span>
                Tải ảnh mới từ máy tính
            </label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (error_for('file')): ?><small class="field-error"><?= e(error_for('file')) ?></small><?php endif; ?>
        </div>

        <?php if (!empty($car['image'])): ?>
            <div class="form-field span-2">
                <label>
                    <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></span>
                    Hình ảnh hiện tại
                </label>
                <div class="preview-img-wrap">
                    <img class="preview-img" src="<?= e(media((string) $car['image'])) ?>" alt="<?= e($car['name'] ?? 'Xe') ?>">
                </div>
            </div>
        <?php endif; ?>

        <!-- PHẦN 3: MÔ TẢ & THÔNG TIN BẢO HIỂM -->
        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </span>
                3. Mô tả chi tiết &amp; Vận hành
            </span>
        </div>

        <div class="form-field span-2">
            <label for="description">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></span>
                Mô tả tiện nghi xe
            </label>
            <textarea id="description" name="description" rows="3" maxlength="5000" placeholder="Xe sạch sẽ, nội thất da cao cấp, máy lạnh sâu, trang bị wifi nước uống..."><?= e(old('description', $car['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="driver_info">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                Thông tin tài xế
            </label>
            <textarea id="driver_info" name="driver_info" rows="2" maxlength="2000" placeholder="Tài xế nhiều năm kinh nghiệm, lịch sự, đúng giờ, am hiểu cung đường..."><?= e(old('driver_info', $car['driver_info'] ?? '')) ?></textarea>
            <?php if (error_for('driver_info')): ?><small class="field-error"><?= e(error_for('driver_info')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="insurance_info">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></span>
                Thông tin bảo hiểm &amp; An toàn
            </label>
            <textarea id="insurance_info" name="insurance_info" rows="2" maxlength="2000" placeholder="Đầy đủ bảo hiểm trách nhiệm dân sự và bảo hiểm người ngồi trên xe..."><?= e(old('insurance_info', $car['insurance_info'] ?? '')) ?></textarea>
            <?php if (error_for('insurance_info')): ?><small class="field-error"><?= e(error_for('insurance_info')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <?= $isEdit ? 'Lưu thay đổi' : 'Thêm xe mới' ?>
        </button>
        <a class="btn btn-light" href="<?= url('/admin/cars') ?>">Hủy bỏ</a>
    </div>
</form>
