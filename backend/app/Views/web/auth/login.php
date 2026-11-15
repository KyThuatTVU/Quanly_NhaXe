<div class="auth-card">
    <a class="auth-brand" href="<?= url('/') ?>">
        <span class="brand-mark">TV</span>
        <span><?= e($site['name'] ?? 'Quản trị') ?></span>
    </a>
    <h1>Đăng nhập quản trị</h1>
    <p class="muted small">Dành cho quản trị viên và nhân viên vận hành</p>

    <form method="post" action="<?= url('/admin/login') ?>">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="login">Tên đăng nhập</label>
            <input id="login" name="login" value="<?= e(old('login')) ?>" required autofocus autocomplete="username">
            <?php if (error_for('login')): ?><small class="field-error"><?= e(error_for('login')) ?></small><?php endif; ?>
        </div>
        <div class="form-field">
            <label for="password">Mật khẩu</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            <?php if (error_for('password')): ?><small class="field-error"><?= e(error_for('password')) ?></small><?php endif; ?>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Đăng nhập</button>
    </form>
    <a class="auth-back" href="<?= url('/') ?>">← Về trang chủ</a>
</div>
