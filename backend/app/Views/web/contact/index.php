<section class="page-hero">
    <div class="container">
        <h1>Liên hệ</h1>
        <p>Gửi yêu cầu, chúng tôi sẽ phản hồi trong thời gian sớm nhất</p>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <aside class="info-card">
            <h3>Thông tin liên hệ</h3>
            <dl class="info-list">
                <dt>Địa chỉ</dt><dd><?= e($site['address'] ?? '—') ?></dd>
                <dt>Điện thoại</dt><dd><?= e($site['phone'] ?? '—') ?></dd>
                <dt>Hotline</dt><dd><?= e($site['hotline'] ?? '—') ?></dd>
                <dt>Email</dt><dd><?= e($site['email'] ?? '—') ?></dd>
                <dt>Zalo</dt><dd><?= e($site['zalo'] ?? '—') ?></dd>
                <dt>Giờ làm việc</dt><dd><?= e($site['working_hours'] ?? '—') ?></dd>
            </dl>
        </aside>

        <form class="card form-card" method="post" action="<?= url('/lien-he') ?>">
            <?= csrf_field() ?>
            <h2>Gửi liên hệ</h2>
            <div class="form-grid">
                <div class="form-field">
                    <label for="name">Họ tên <span class="req">*</span></label>
                    <input id="name" name="name" value="<?= e(old('name')) ?>" required maxlength="100">
                    <?php if (error_for('name')): ?><small class="field-error"><?= e(error_for('name')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="phone">Số điện thoại <span class="req">*</span></label>
                    <input id="phone" name="phone" value="<?= e(old('phone')) ?>" required inputmode="tel">
                    <?php if (error_for('phone')): ?><small class="field-error"><?= e(error_for('phone')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?= e(old('email')) ?>">
                    <?php if (error_for('email')): ?><small class="field-error"><?= e(error_for('email')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="subject">Tiêu đề</label>
                    <input id="subject" name="subject" value="<?= e(old('subject')) ?>" maxlength="255">
                    <?php if (error_for('subject')): ?><small class="field-error"><?= e(error_for('subject')) ?></small><?php endif; ?>
                </div>
                <div class="form-field span-2">
                    <label for="message">Nội dung <span class="req">*</span></label>
                    <textarea id="message" name="message" rows="5" required><?= e(old('message')) ?></textarea>
                    <?php if (error_for('message')): ?><small class="field-error"><?= e(error_for('message')) ?></small><?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Gửi liên hệ</button>
        </form>
    </div>
</section>
