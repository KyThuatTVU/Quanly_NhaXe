<div class="admin-page-head">
    <div>
        <h1>Cài đặt website</h1>
        <p class="muted">Thông tin hiển thị trên header, footer và trang liên hệ</p>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= url('/admin/settings') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="form-grid">
        <div class="form-field">
            <label for="company_name">Tên nhà xe / thương hiệu</label>
            <input id="company_name" name="company_name" value="<?= e(old('company_name', $settings['company_name'] ?? '')) ?>" maxlength="200" placeholder="VD: Nhà xe An Phú">
            <small class="muted">Tên này hiển thị ở tiêu đề tab, header, footer, trang đăng nhập và trang quản trị.</small>
            <?php if (error_for('company_name')): ?><small class="field-error"><?= e(error_for('company_name')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="phone">Điện thoại</label>
            <input id="phone" name="phone" value="<?= e(old('phone', $settings['phone'] ?? '')) ?>" maxlength="20">
            <?php if (error_for('phone')): ?><small class="field-error"><?= e(error_for('phone')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="hotline">Hotline</label>
            <input id="hotline" name="hotline" value="<?= e(old('hotline', $settings['hotline'] ?? '')) ?>" maxlength="20">
            <?php if (error_for('hotline')): ?><small class="field-error"><?= e(error_for('hotline')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="zalo">Zalo</label>
            <input id="zalo" name="zalo" value="<?= e(old('zalo', $settings['zalo'] ?? '')) ?>" maxlength="20">
            <?php if (error_for('zalo')): ?><small class="field-error"><?= e(error_for('zalo')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e(old('email', $settings['email'] ?? '')) ?>" maxlength="255">
            <?php if (error_for('email')): ?><small class="field-error"><?= e(error_for('email')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="working_hours">Giờ làm việc</label>
            <input id="working_hours" name="working_hours" value="<?= e(old('working_hours', $settings['working_hours'] ?? '')) ?>" maxlength="100" placeholder="VD: 7:00 - 21:00 hằng ngày">
            <?php if (error_for('working_hours')): ?><small class="field-error"><?= e(error_for('working_hours')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="address">Địa chỉ</label>
            <input id="address" name="address" value="<?= e(old('address', $settings['address'] ?? '')) ?>" maxlength="500">
            <?php if (error_for('address')): ?><small class="field-error"><?= e(error_for('address')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="facebook">Facebook</label>
            <input id="facebook" name="facebook" value="<?= e(old('facebook', $settings['facebook'] ?? '')) ?>" maxlength="500">
            <?php if (error_for('facebook')): ?><small class="field-error"><?= e(error_for('facebook')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="map_location">Bản đồ (mã nhúng hoặc đường dẫn)</label>
            <textarea id="map_location" name="map_location" rows="2" maxlength="500"><?= e(old('map_location', $settings['map_location'] ?? '')) ?></textarea>
            <?php if (error_for('map_location')): ?><small class="field-error"><?= e(error_for('map_location')) ?></small><?php endif; ?>
        </div>
        <div class="form-field span-2">
            <label for="description">Mô tả ngắn</label>
            <textarea id="description" name="description" rows="3" maxlength="5000"><?= e(old('description', $settings['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
    </div>
</form>
