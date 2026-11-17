<div class="admin-page-head">
    <div>
        <h1>Cài đặt website</h1>
        <p class="muted">Thông tin hiển thị trên header, footer, trang liên hệ và hoá đơn</p>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= url('/admin/settings') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">

    <div class="admin-card-head">
        <div class="head-title-wrap">
            <div class="head-icon-glass">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>
            <div>
                <h2>Cấu hình thương hiệu &amp; Kênh liên lạc</h2>
                <p class="muted small" style="margin:2px 0 0;">Cập nhật thông tin nhận diện thương hiệu Thảo Vy Travel</p>
            </div>
        </div>
        <span class="badge success">
            <span class="live-dot" style="width:6px;height:6px;"></span>
            Cấu hình chung
        </span>
    </div>

    <div class="form-grid">
        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                1. Thông tin thương hiệu &amp; Liên hệ
            </span>
        </div>

        <div class="form-field span-2">
            <label for="company_name">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>
                Tên nhà xe / Thương hiệu
            </label>
            <input id="company_name" name="company_name" value="<?= e(old('company_name', $settings['company_name'] ?? '')) ?>" maxlength="200" placeholder="VD: Nhà xe Thảo Vy Travel">
            <small class="muted small">Tên này hiển thị ở tiêu đề tab trình duyệt, header, footer, trang đăng nhập và quản trị.</small>
            <?php if (error_for('company_name')): ?><small class="field-error"><?= e(error_for('company_name')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="phone">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></span>
                Số điện thoại
            </label>
            <input id="phone" name="phone" value="<?= e(old('phone', $settings['phone'] ?? '')) ?>" maxlength="20" placeholder="0787 972 075">
            <?php if (error_for('phone')): ?><small class="field-error"><?= e(error_for('phone')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="hotline">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                Hotline 24/7
            </label>
            <input id="hotline" name="hotline" value="<?= e(old('hotline', $settings['hotline'] ?? '')) ?>" maxlength="20" placeholder="0787 972 075">
            <?php if (error_for('hotline')): ?><small class="field-error"><?= e(error_for('hotline')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="zalo">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></span>
                Số Zalo tư vấn
            </label>
            <input id="zalo" name="zalo" value="<?= e(old('zalo', $settings['zalo'] ?? '')) ?>" maxlength="20" placeholder="0787 972 075">
            <?php if (error_for('zalo')): ?><small class="field-error"><?= e(error_for('zalo')) ?></small><?php endif; ?>
        </div>

        <div class="form-field">
            <label for="email">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
                Email liên hệ
            </label>
            <input id="email" name="email" type="email" value="<?= e(old('email', $settings['email'] ?? '')) ?>" maxlength="255" placeholder="duyphongtv123@gmail.com">
            <?php if (error_for('email')): ?><small class="field-error"><?= e(error_for('email')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="working_hours">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>
                Giờ làm việc
            </label>
            <input id="working_hours" name="working_hours" value="<?= e(old('working_hours', $settings['working_hours'] ?? '')) ?>" maxlength="100" placeholder="VD: 24/7 - Phục vụ mọi lúc, kể cả ngày lễ">
            <?php if (error_for('working_hours')): ?><small class="field-error"><?= e(error_for('working_hours')) ?></small><?php endif; ?>
        </div>

        <div class="form-section-head span-2">
            <span>
                <span class="section-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </span>
                2. Địa chỉ &amp; Kênh mạng xã hội
            </span>
        </div>

        <div class="form-field span-2">
            <label for="address">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span>
                Địa chỉ văn phòng / bến bãi
            </label>
            <input id="address" name="address" value="<?= e(old('address', $settings['address'] ?? '')) ?>" maxlength="500" placeholder="267 Ấp Ngãi Lợi, Xã Hưng Mỹ, Huyện Châu Thành, Tỉnh Trà Vinh">
            <?php if (error_for('address')): ?><small class="field-error"><?= e(error_for('address')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="facebook">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></span>
                Trang Facebook (Fanpage)
            </label>
            <input id="facebook" name="facebook" value="<?= e(old('facebook', $settings['facebook'] ?? '')) ?>" maxlength="500" placeholder="https://facebook.com/...">
            <?php if (error_for('facebook')): ?><small class="field-error"><?= e(error_for('facebook')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="map_location">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon></svg></span>
                Bản đồ Google Maps (mã nhúng iframe hoặc link)
            </label>
            <textarea id="map_location" name="map_location" rows="2" maxlength="500"><?= e(old('map_location', $settings['map_location'] ?? '')) ?></textarea>
            <?php if (error_for('map_location')): ?><small class="field-error"><?= e(error_for('map_location')) ?></small><?php endif; ?>
        </div>

        <div class="form-field span-2">
            <label for="description">
                <span class="label-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line></svg></span>
                Mô tả giới thiệu ngắn
            </label>
            <textarea id="description" name="description" rows="3" maxlength="5000"><?= e(old('description', $settings['description'] ?? '')) ?></textarea>
            <?php if (error_for('description')): ?><small class="field-error"><?= e(error_for('description')) ?></small><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Lưu thay đổi cài đặt
        </button>
    </div>
</form>
