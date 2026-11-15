<section class="page-hero">
    <div class="container">
        <h1>Dịch vụ</h1>
        <p>Các gói dịch vụ vận chuyển phù hợp mọi nhu cầu</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid grid-4">
            <?php foreach ($services as $service): ?>
                <?php $view->partial('partials/service-card', ['service' => $service]); ?>
            <?php endforeach; ?>
        </div>
        <?php if ($services === []): ?>
            <div class="empty-state"><h3>Chưa có dịch vụ</h3><p>Vui lòng quay lại sau.</p></div>
        <?php endif; ?>
    </div>
</section>
