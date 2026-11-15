<section class="page-hero">
    <div class="container">
        <h1>Giới thiệu</h1>
        <p>Đơn vị cho thuê xe hợp đồng uy tín tại Hà Nội</p>
    </div>
</section>

<section class="section">
    <div class="container prose-layout">
        <article class="prose">
            <h2><?= e($site['name'] ?? '') ?></h2>
            <p><?= e($site['description'] ?? '') ?></p>
            <p>Chúng tôi cung cấp dịch vụ cho thuê xe hợp đồng 4 - 45 chỗ phục vụ nhu cầu đi lại, du lịch, đưa đón sân bay, xe cưới hỏi và xe đưa đón nhân viên. Mọi chuyến đi đều được báo giá rõ ràng, xe đời mới và tài xế chuyên nghiệp.</p>
            <h3>Cam kết của chúng tôi</h3>
            <ul>
                <li>Xe đời mới, được bảo dưỡng và kiểm định định kỳ.</li>
                <li>Tài xế nhiều năm kinh nghiệm, đúng giờ, thân thiện.</li>
                <li>Giá cước minh bạch, không phát sinh chi phí ẩn.</li>
                <li>Hỗ trợ 24/7 qua hotline và Zalo.</li>
            </ul>
        </article>
        <aside class="info-card">
            <h3>Thông tin công ty</h3>
            <dl class="info-list">
                <dt>Địa chỉ</dt><dd><?= e($site['address'] ?? '—') ?></dd>
                <dt>Điện thoại</dt><dd><?= e($site['phone'] ?? '—') ?></dd>
                <dt>Hotline</dt><dd><?= e($site['hotline'] ?? '—') ?></dd>
                <dt>Email</dt><dd><?= e($site['email'] ?? '—') ?></dd>
                <dt>Giờ làm việc</dt><dd><?= e($site['working_hours'] ?? '—') ?></dd>
            </dl>
            <a class="btn btn-primary btn-block" href="<?= url('/lien-he') ?>">Liên hệ ngay</a>
        </aside>
    </div>
</section>
