<div class="admin-page-head">
    <div>
        <nav class="breadcrumb">
            <a href="<?= url('/admin/contacts') ?>">Liên hệ</a> <span>/</span>
            <span>#<?= (int) $contact['contact_id'] ?></span>
        </nav>
        <h1><?= e($contact['subject'] ?? 'Liên hệ #' . $contact['contact_id']) ?></h1>
    </div>
    <span class="<?= e(\App\Support\Format::badge((string) $contact['status'])) ?>"><?= e(\App\Support\Format::contactStatus((string) $contact['status'])) ?></span>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="admin-card-head"><h2>Nội dung</h2></div>
        <p class="message-body"><?= nl2br(e($contact['message'] ?? '')) ?></p>
    </section>
    <section class="admin-card">
        <div class="admin-card-head"><h2>Thông tin người gửi</h2></div>
        <dl class="info-list">
            <dt>Họ tên</dt><dd><?= e($contact['name']) ?></dd>
            <dt>Số điện thoại</dt><dd><?= e(\App\Support\Format::phone($contact['phone'] ?? null)) ?></dd>
            <dt>Email</dt><dd><?= e($contact['email'] ?? '—') ?></dd>
            <dt>Ngày gửi</dt><dd><?= e(\App\Support\Format::datetime($contact['created_at'] ?? null)) ?></dd>
            <dt>Cập nhật</dt><dd><?= e(\App\Support\Format::datetime($contact['updated_at'] ?? null)) ?></dd>
        </dl>
    </section>
</div>

<section class="admin-card">
    <div class="admin-card-head"><h2>Xử lý</h2></div>
    <div class="action-row">
        <form method="post" action="<?= url('/admin/contacts/' . (int) $contact['contact_id'] . '/status') ?>" class="inline-form">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PATCH">
            <label for="status">Trạng thái</label>
            <select id="status" name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>" <?= $contact['status'] === $status ? 'selected' : '' ?>><?= e(\App\Support\Format::contactStatus($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Cập nhật</button>
        </form>
        <form method="post" action="<?= url('/admin/contacts/' . (int) $contact['contact_id']) ?>" data-confirm="Xóa liên hệ này?">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button class="btn btn-danger" type="submit">Xóa liên hệ</button>
        </form>
    </div>
</section>

<a class="btn btn-light" href="<?= url('/admin/contacts') ?>">← Quay lại danh sách</a>
