<?php
$isEdit = is_array($user);
$action = $isEdit ? url('/admin/accounts/' . (int) $user['user_id']) : url('/admin/accounts');
?>
<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/accounts') ?>">Tài khoản</a> <span>/</span>
            <span><?= $isEdit ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></span>
        </nav>
        <h1><?= $isEdit ? 'Sửa tài khoản' : 'Thêm tài khoản' ?></h1>
    </div>
</div>

<form class="admin-card form-card" method="post" action="<?= $action ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

    <div class="form-grid">
        <div class="form-field">
            <label for="username">Tên đăng nhập <span class="req">*</span></label>
            <?php if ($isEdit): ?>
                <input id="username" value="<?= e($user['username']) ?>" disabled>
            <?php else: ?>
                <input id="username" name="username" value="<?= e(old('username')) ?>" required minlength="3" maxlength="50" autocomplete="off">
            <?php endif; ?>
            <?php if (error_for('username')): ?><small class="field-error"><?= e(error_for('username')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="password">Mật khẩu <?= $isEdit ? '' : '<span class="req">*</span>' ?></label>
            <input id="password" name="password" type="password" <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
            <?php if ($isEdit): ?><small class="muted small">Để trống nếu không đổi mật khẩu.</small><?php endif; ?>
            <?php if (error_for('password')): ?><small class="field-error"><?= e(error_for('password')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e(old('email', $user['email'] ?? '')) ?>" maxlength="255">
            <?php if (error_for('email')): ?><small class="field-error"><?= e(error_for('email')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="phone">Số điện thoại</label>
            <input id="phone" name="phone" value="<?= e(old('phone', $user['phone'] ?? '')) ?>" inputmode="tel">
            <?php if (error_for('phone')): ?><small class="field-error"><?= e(error_for('phone')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="role">Vai trò <span class="req">*</span></label>
            <select id="role" name="role" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= e($role) ?>" <?= (string) old('role', $user['role'] ?? 'business') === $role ? 'selected' : '' ?>>
                        <?= $role === 'admin' ? 'Quản trị viên' : ($role === 'business' ? 'Nhân viên vận hành' : 'Khách hàng') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (error_for('role')): ?><small class="field-error"><?= e(error_for('role')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="is_active">Trạng thái</label>
            <select id="is_active" name="is_active">
                <option value="1" <?= (string) old('is_active', $user['is_active'] ?? 1) === '1' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="0" <?= (string) old('is_active', $user['is_active'] ?? 1) === '0' ? 'selected' : '' ?>>Khóa tài khoản</option>
            </select>
            <?php if (error_for('is_active')): ?><small class="field-error"><?= e(error_for('is_active')) ?></small><?php endif; ?>
        </div>
    </div>

    <p class="muted small">Không thể khóa hoặc xóa tài khoản đang đăng nhập, và luôn phải còn ít nhất một quản trị viên đang hoạt động.</p>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm tài khoản' ?></button>
        <a class="btn btn-light" href="<?= url('/admin/accounts') ?>">Hủy</a>
    </div>
</form>
