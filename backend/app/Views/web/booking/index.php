<section class="page-hero">
    <div class="container">
        <h1>Đặt xe</h1>
        <p>Điền thông tin chuyến đi, chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất</p>
    </div>
</section>

<section class="section">
    <div class="container booking-layout">
        <form class="card form-card" method="post" action="<?= url('/dat-xe') ?>">
            <?= csrf_field() ?>
            <h2>Thông tin chuyến đi</h2>
            <div class="form-grid">
                <div class="form-field">
                    <label for="vehicle_id">Chọn xe <span class="req">*</span></label>
                    <select id="vehicle_id" name="vehicle_id" required>
                        <option value="">-- Chọn xe --</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= (int) $vehicle['vehicle_id'] ?>" <?= (int) $vehicle['vehicle_id'] === $selectedVehicle ? 'selected' : '' ?>>
                                <?= e($vehicle['name']) ?>
                                · <?= e($vehicle['type_name'] ?? $vehicle['type'] ?? '') ?>
                                · <?= e(\App\Support\Format::money($vehicle['price_per_day'] ?? null)) ?>/ngày
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (error_for('vehicle_id')): ?><small class="field-error"><?= e(error_for('vehicle_id')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="service_id">Dịch vụ</label>
                    <select id="service_id" name="service_id">
                        <option value="">-- Không chọn --</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= (int) $service['service_id'] ?>" <?= (string) $service['service_id'] === (string) old('service_id', '') ? 'selected' : '' ?>>
                                <?= e($service['service_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (error_for('service_id')): ?><small class="field-error"><?= e(error_for('service_id')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="pickup_location">Điểm đón <span class="req">*</span></label>
                    <input id="pickup_location" name="pickup_location" value="<?= e(old('pickup_location')) ?>" required maxlength="255" placeholder="Số nhà, đường, quận/huyện...">
                    <?php if (error_for('pickup_location')): ?><small class="field-error"><?= e(error_for('pickup_location')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="dropoff_location">Điểm đến</label>
                    <input id="dropoff_location" name="dropoff_location" value="<?= e(old('dropoff_location')) ?>" maxlength="255" placeholder="Điểm trả khách (nếu có)">
                    <?php if (error_for('dropoff_location')): ?><small class="field-error"><?= e(error_for('dropoff_location')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="pickup_date">Ngày đón <span class="req">*</span></label>
                    <input id="pickup_date" name="pickup_date" type="date" value="<?= e(old('pickup_date')) ?>" required>
                    <?php if (error_for('pickup_date')): ?><small class="field-error"><?= e(error_for('pickup_date')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="pickup_time">Giờ đón <span class="req">*</span></label>
                    <input id="pickup_time" name="pickup_time" type="time" value="<?= e(old('pickup_time', '08:00')) ?>" required>
                    <?php if (error_for('pickup_time')): ?><small class="field-error"><?= e(error_for('pickup_time')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="return_date">Ngày trả</label>
                    <input id="return_date" name="return_date" type="date" value="<?= e(old('return_date')) ?>">
                    <?php if (error_for('return_date')): ?><small class="field-error"><?= e(error_for('return_date')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="return_time">Giờ trả</label>
                    <input id="return_time" name="return_time" type="time" value="<?= e(old('return_time')) ?>">
                    <?php if (error_for('return_time')): ?><small class="field-error"><?= e(error_for('return_time')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="number_of_passengers">Số hành khách</label>
                    <input id="number_of_passengers" name="number_of_passengers" type="number" min="1" value="<?= e(old('number_of_passengers')) ?>">
                    <?php if (error_for('number_of_passengers')): ?><small class="field-error"><?= e(error_for('number_of_passengers')) ?></small><?php endif; ?>
                </div>
            </div>

            <h2>Thông tin liên hệ</h2>
            <div class="form-grid">
                <div class="form-field">
                    <label for="customer_name">Họ tên <span class="req">*</span></label>
                    <input id="customer_name" name="customer_name" value="<?= e(old('customer_name')) ?>" required maxlength="100">
                    <?php if (error_for('customer_name')): ?><small class="field-error"><?= e(error_for('customer_name')) ?></small><?php endif; ?>
                </div>
                <div class="form-field">
                    <label for="customer_phone">Số điện thoại <span class="req">*</span></label>
                    <input id="customer_phone" name="customer_phone" value="<?= e(old('customer_phone')) ?>" required inputmode="tel">
                    <?php if (error_for('customer_phone')): ?><small class="field-error"><?= e(error_for('customer_phone')) ?></small><?php endif; ?>
                </div>
                <div class="form-field span-2">
                    <label for="customer_email">Email</label>
                    <input id="customer_email" name="customer_email" type="email" value="<?= e(old('customer_email')) ?>" maxlength="255">
                    <?php if (error_for('customer_email')): ?><small class="field-error"><?= e(error_for('customer_email')) ?></small><?php endif; ?>
                </div>
                <div class="form-field span-2">
                    <label for="notes">Ghi chú</label>
                    <textarea id="notes" name="notes" rows="4" maxlength="2000" placeholder="Yêu cầu thêm: ghế trẻ em, đưa đón sân bay..."><?= e(old('notes')) ?></textarea>
                    <?php if (error_for('notes')): ?><small class="field-error"><?= e(error_for('notes')) ?></small><?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary btn-lg" type="submit">Gửi yêu cầu đặt xe</button>
        </form>

        <aside class="info-card">
            <h3>Quy trình đặt xe</h3>
            <ol class="step-list">
                <li>Gửi yêu cầu đặt xe online.</li>
                <li>Nhân viên gọi xác nhận trong 15 phút.</li>
                <li>Tài xế liên hệ trước giờ đón.</li>
                <li>Thanh toán sau chuyến đi.</li>
            </ol>
            <hr>
            <dl class="info-list">
                <dt>Hotline</dt><dd><?= e($site['hotline'] ?? $site['phone'] ?? '—') ?></dd>
                <dt>Zalo</dt><dd><?= e($site['zalo'] ?? '—') ?></dd>
                <dt>Giờ làm việc</dt><dd><?= e($site['working_hours'] ?? '—') ?></dd>
            </dl>
            <p class="small muted">Đã đặt xe trước đó? <a href="<?= url('/tra-cuu-don') ?>">Tra cứu đơn</a> để xem trạng thái.</p>
        </aside>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().slice(0, 10);
        document.querySelectorAll('input[type="date"]').forEach((input) => {
            input.min = today;
        });
    });
</script>
