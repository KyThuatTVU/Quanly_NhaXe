<div class="auth-card">
    <a class="auth-brand" href="<?= url('/') ?>">
        <img class="brand-logo" src="<?= asset('img/logo/logo_chinh_256.png') ?>" alt="<?= e($site['name'] ?? 'Quản trị') ?>" width="40" height="40">
        <span><?= e($site['name'] ?? 'Quản trị') ?></span>
    </a>
    <h1>Đăng nhập quản trị</h1>
    <p class="muted small">Dành cho quản trị viên và nhân viên vận hành</p>

    <a class="btn btn-google btn-block" href="<?= url('/admin/login/google') ?>">
        <svg viewBox="0 0 48 48" width="18" height="18" aria-hidden="true" focusable="false">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59a14.5 14.5 0 0 1 0-9.18l-7.98-6.19a24.01 24.01 0 0 0 0 21.56l7.98-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        Đăng nhập bằng Google
    </a>

    <div class="auth-divider"><span>hoặc đăng nhập bằng tài khoản</span></div>

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
