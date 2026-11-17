<?php
$isEdit = is_array($service);
$action = $isEdit ? url('/admin/services/' . (int) $service['service_id']) : url('/admin/services');
$servicePrice = isset($service['price']) ? (int) $service['price'] : '';
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/services') ?>">Dịch vụ</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa dịch vụ: ' . e($service['service_name'] ?? '') : 'Thêm dịch vụ mới' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa dịch vụ: ' . e($service['service_name'] ?? '') : 'Thêm dịch vụ mới' ?></h1>
        <p class="muted">Cấu hình các gói dịch vụ xe hợp đồng, đưa đón sân bay, du lịch và cưới hỏi</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-light" href="<?= url('/admin/services') ?>">
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
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
            <div>
                <h2><?= $isEdit ? 'Dịch vụ: ' . e($service['service_name'] ?? '') : 'Thêm gói dịch vụ mới' ?></h2>
                <p class="muted small" style="margin:2px 0 0;">Thông tin giới thiệu gói dịch vụ thuê xe</p>
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
                1. Thông tin gói dịch vụ
            </span>
        </div>

        <div class="form-field">
            <label for="service_name">Tên dịch vụ <span class="req">*</span></label>
            <input id="service_name" name="service_name" value="<?= e(old('service_name', $service['service_name'] ?? '')) ?>" required maxlength="100" placeholder="VD: Thuê xe đưa đón sân bay">
            <?php if (error_for('service_name')): ?><small class="field-error"><?= e(error_for('service_name')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="slug">Slug định danh URL</label>
            <input id="slug" name="slug" value="<?= e(old('slug', $service['slug'] ?? '')) ?>" maxlength="255" placeholder="Tự sinh nếu để trống">
            <?php if (error_for('slug')): ?><small class="field-error"><?= e(error_for('slug')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="price">Giá dịch vụ tham khảo</label>
            <div class="input-glass-wrap">
                <input id="price" name="price" type="number" min="0" step="1000" value="<?= e(old('price', $servicePrice)) ?>" placeholder="VD: 500000">
                <span class="input-glass-suffix">VNĐ</span>
            </div>
            <?php if (error_for('price')): ?><small class="field-error"><?= e(error_for('price')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="display_order">Thứ tự hiển thị</label>
            <input id="display_order" name="display_order" type="number" value="<?= e(old('display_order', $service['display_order'] ?? 0)) ?>">
            <?php if (error_for('display_order')): ?><small class="field-error"><?= e(error_for('display_order')) ?></small><?php endif; ?>
        </div>

        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </span>
                2. Hình ảnh &amp; Trạng thái
            </span>
        </div>

        <div class="form-field">
            <label for="image">Đường dẫn ảnh đại diện</label>
            <input id="image" name="image" value="<?= e(old('image', $service['image'] ?? '')) ?>" maxlength="255" placeholder="uploads/services/...">
            <?php if (error_for('image')): ?><small class="field-error"><?= e(error_for('image')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="image_file">Tải ảnh mới từ máy tính</label>
            <input id="image_file" name="image_file" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (error_for('file')): ?><small class="field-error"><?= e(error_for('file')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="is_active">Trạng thái dịch vụ</label>
            <select id="is_active" name="is_active">
                <option value="1" <?= (string) old('is_active', $service['is_active'] ?? 1) === '1' ? 'selected' : '' ?>>Đang cung cấp</option>
                <option value="0" <?= (string) old('is_active', $service['is_active'] ?? 1) === '0' ? 'selected' : '' ?>>Tạm dừng</option>
            </select>
            <?php if (error_for('is_active')): ?><small class="field-error"><?= e(error_for('is_active')) ?></small><?php endif; ?>
        </div>

        <?php if (!empty($service['image'])): ?>
            <div class="form-field">
                <label>Ảnh hiện tại</label>
                <div class="preview-img-wrap">
                    <img class="preview-img" src="<?= e(media((string) $service['image'])) ?>" alt="">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-field span-2">
            <label for="description">Mô tả chi tiết dịch vụ</label>
            <textarea id="description" name="description" rows="4" maxlength="5000" placeholder="Mô tả ưu điểm và lộ trình phục vụ của gói dịch vụ..."><?= e(old('description', $service['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <?= $isEdit ? 'Lưu thay đổi' : 'Thêm dịch vụ' ?>
        </button>
        <a class="btn btn-light" href="<?= url('/admin/services') ?>">Hủy bỏ</a>
    </div>
</form>
