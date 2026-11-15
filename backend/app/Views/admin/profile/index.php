<div class="admin-page-head">
    <div>
        <h1>Thông tin cá nhân</h1>
        <p class="muted">Tài khoản đang đăng nhập</p>
    </div>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="admin-card-head"><h2>Hồ sơ</h2></div>
        <dl class="info-list">
            <dt>Tên đăng nhập</dt><dd><?= e($user['username']) ?></dd>
            <dt>Vai trò</dt><dd><?= e($user['role']) ?></dd>
            <dt>Email</dt><dd><?= e($user['email'] ?? '—') ?></dd>
            <dt>Số điện thoại</dt><dd><?= e(\App\Support\Format::phone($user['phone'] ?? null)) ?></dd>
            <dt>Đăng nhập cuối</dt><dd><?= e(\App\Support\Format::datetime($user['last_login_at'] ?? null)) ?></dd>
            <dt>Ngày tạo</dt><dd><?= e(\App\Support\Format::datetime($user['created_at'] ?? null)) ?></dd>
        </dl>
        <p class="muted small">Thông tin hồ sơ do quản trị viên cập nhật tại mục Tài khoản.</p>
    </section>

    <section class="admin-card">
        <div class="admin-card-head"><h2>Đổi mật khẩu</h2></div>
        <form method="post" action="<?= url('/admin/profile/password') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="form-field">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
                <?php if (error_for('current_password')): ?><small class="field-error"><?= e(error_for('current_password')) ?></small><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="new_password">Mật khẩu mới</label>
                <input id="new_password" name="new_password" type="password" required minlength="8" autocomplete="new-password">
                <?php if (error_for('new_password')): ?><small class="field-error"><?= e(error_for('new_password')) ?></small><?php endif; ?>
            </div>
            <button class="btn btn-primary" type="submit">Đổi mật khẩu</button>
            <p class="muted small">Sau khi đổi mật khẩu, bạn sẽ được đăng xuất khỏi mọi thiết bị.</p>
        </form>
    </section>
</div>
