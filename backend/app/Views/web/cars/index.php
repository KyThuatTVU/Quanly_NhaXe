<section class="page-hero">
    <div class="container">
        <h1>Danh sách xe</h1>
        <p>Xe hợp đồng 4 - 45 chỗ, sẵn sàng cho mọi hành trình</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form class="filter-bar" method="get" action="<?= url('/xe') ?>">
            <div class="form-field">
                <label for="vehicle_type_id">Loại xe</label>
                <select id="vehicle_type_id" name="vehicle_type_id">
                    <option value="">Tất cả loại xe</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= (int) $type['id'] ?>" <?= (string) $type['id'] === $filters['vehicle_type_id'] ? 'selected' : '' ?>>
                            <?= e($type['name']) ?> (<?= (int) $type['seats'] ?> chỗ)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field grow">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Tên xe, loại xe...">
            </div>
            <button class="btn btn-primary" type="submit">Lọc xe</button>
            <?php if ($filters['vehicle_type_id'] !== '' || $filters['search'] !== ''): ?>
                <a class="btn btn-light" href="<?= url('/xe') ?>">Xóa lọc</a>
            <?php endif; ?>
        </form>

        <div class="grid grid-3">
            <?php foreach ($cars as $car): ?>
                <?php $view->partial('partials/car-card', ['car' => $car]); ?>
            <?php endforeach; ?>
        </div>
        <?php if ($cars === []): ?>
            <div class="empty-state">
                <h3>Không tìm thấy xe phù hợp</h3>
                <p>Vui lòng thử bộ lọc khác hoặc liên hệ hotline <?= e($site['hotline'] ?? '') ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
