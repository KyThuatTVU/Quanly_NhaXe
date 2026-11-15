<section class="page-hero">
    <div class="container">
        <h1>Đánh giá chuyến đi</h1>
        <p>Chia sẻ cảm nhận của bạn để chúng tôi phục vụ tốt hơn</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <form class="card form-card" method="post" action="<?= url('/danh-gia') ?>">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-field">
                    <label for="booking_code">Mã đơn <span class="req">*</span></label>
                    <input id="booking_code" name="booking_code" value="<?= e(old('booking_code')) ?>" required placeholder="Mã đơn đã hoàn thành">
                    <?php if (error_for('booking_code')): ?><small class="field-error"><?= e(error_for('booking_code')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="phone">Số điện thoại <span class="req">*</span></label>
                    <input id="phone" name="phone" value="<?= e(old('phone')) ?>" required inputmode="tel">
                    <?php if (error_for('phone')): ?><small class="field-error"><?= e(error_for('phone')) ?></small><?php endif; ?>
                </div>
                <div class="form-field span-2">
                    <label>Chất lượng chuyến đi <span class="req">*</span></label>
                    <div class="rating-input">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="rating-<?= $i ?>" name="rating" value="<?= $i ?>" <?= (string) old('rating', '5') === (string) $i ? 'checked' : '' ?> required>
                            <label for="rating-<?= $i ?>" title="<?= $i ?> sao"><?= str_repeat('★', $i) ?></label>
                        <?php endfor; ?>
                    </div>
                    <?php if (error_for('rating')): ?><small class="field-error"><?= e(error_for('rating')) ?></small><?php endif; ?>
                </div>
                <div class="form-field span-2">
                    <label for="comment">Nhận xét</label>
                    <textarea id="comment" name="comment" rows="5" maxlength="2000" placeholder="Tài xế, chất lượng xe, đúng giờ..."><?= e(old('comment')) ?></textarea>
                    <?php if (error_for('comment')): ?><small class="field-error"><?= e(error_for('comment')) ?></small><?php endif; ?>
                </div>
            </div>
            <p class="small muted">Chỉ có thể đánh giá khi đơn đã hoàn thành và mỗi đơn một lần.</p>
            <button class="btn btn-primary" type="submit">Gửi đánh giá</button>
        </form>
    </div>
</section>
