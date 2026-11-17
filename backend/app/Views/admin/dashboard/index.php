<?php
/**
 * View: Dashboard Admin - Phong cách Glassmorphism 3D Tone Xanh Lá
 * Dữ liệu vận hành thực tế của Nhà xe Thảo Vy Travel
 * @var string $monthLabel
 * @var array $stats
 * @var array $summary
 * @var array $chart
 */

// Chuẩn bị dữ liệu JSON an toàn cho Chart.js
$chartDates = array_map(static fn($r) => \App\Support\Format::date($r['label'] ?? ''), $chart);
$chartTotals = array_map(static fn($r) => (int) ($r['total'] ?? 0), $chart);
$chartAmounts = array_map(static fn($r) => (float) (($r['amount'] ?? 0) / 1000000), $chart); // Quy đổi triệu VNĐ cho trục biểu đồ gọn gàng

$pendingCount = (int) ($summary['pending'] ?? 0);
$confirmedCount = (int) ($summary['confirmed'] ?? 0);
$completedCount = (int) ($summary['completed'] ?? 0);
$cancelledCount = (int) ($summary['cancelled'] ?? 0);
?>

<div class="admin-page-head">
    <div>
        <h1>Tổng quan vận hành</h1>
        <p class="muted">Báo cáo số liệu tháng <?= e($monthLabel) ?> và tình hình đội xe Thảo Vy Travel</p>
    </div>
    <div class="page-head-actions">
        <a class="btn btn-outline" href="<?= url('/admin/cars') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            Quản lý đội xe
        </a>
        <a class="btn btn-primary" href="<?= url('/admin/bookings') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>
            Danh sách đơn đặt xe
        </a>
    </div>
</div>

<!-- ================= ROW 1: 4 KPI CARDS (Glassmorphism 3D Tone Xanh Lá) ================= -->
<div class="stat-grid">
    <!-- KPI 1: Đội xe -->
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Tổng số xe</span>
            <div class="stat-icon-wrap icon-car">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="13" rx="2"></rect><path d="M16 3H8l-3 4h14l-3-4z"></path><circle cx="7" cy="15" r="2"></circle><circle cx="17" cy="15" r="2"></circle></svg>
            </div>
        </div>
        <div class="stat-metric-box">
            <strong class="stat-value"><?= (int) $stats['cars']['total'] ?></strong>
            <span class="stat-unit">xe</span>
        </div>
        <div class="stat-growth-row">
            <span class="badge success">
                <span class="live-dot" style="width:6px; height:6px;"></span>
                <?= (int) $stats['cars']['available'] ?> xe sẵn sàng
            </span>
            <span class="stat-note-inline">Đội xe 4 - 45 chỗ</span>
        </div>
    </div>

    <!-- KPI 2: Đơn đặt xe -->
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Đơn đặt xe</span>
            <div class="stat-icon-wrap icon-booking">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            </div>
        </div>
        <div class="stat-metric-box">
            <strong class="stat-value"><?= (int) $stats['bookings']['total'] ?></strong>
            <span class="stat-unit">chuyến</span>
        </div>
        <div class="stat-growth-row">
            <?php if ($pendingCount > 0): ?>
                <span class="badge warning"><?= $pendingCount ?> chờ xác nhận</span>
            <?php else: ?>
                <span class="badge success">Đã xử lý hết</span>
            <?php endif; ?>
            <span class="stat-note-inline"><?= (int) ($summary['total'] ?? 0) ?> đơn tháng này</span>
        </div>
    </div>

    <!-- KPI 3: Doanh thu tạm tính -->
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Doanh thu tạm tính</span>
            <div class="stat-icon-wrap icon-money">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
        </div>
        <div class="stat-metric-box">
            <strong class="stat-value" style="font-size:1.6rem; color:var(--forest);"><?= e(\App\Support\Format::money($summary['amount'] ?? 0)) ?></strong>
        </div>
        <div class="stat-growth-row">
            <span class="badge info">Tháng <?= e($monthLabel) ?></span>
            <span class="stat-note-inline">Theo lịch đón khách</span>
        </div>
    </div>

    <!-- KPI 4: Liên hệ khách mới -->
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-label">Liên hệ mới</span>
            <div class="stat-icon-wrap icon-contact">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
        </div>
        <div class="stat-metric-box">
            <strong class="stat-value"><?= (int) $stats['contacts']['new'] ?></strong>
            <span class="stat-unit">tin nhắn</span>
        </div>
        <div class="stat-growth-row">
            <a class="badge info" href="<?= url('/admin/contacts') ?>">Xem danh sách →</a>
            <span class="stat-note-inline">Yêu cầu báo giá</span>
        </div>
    </div>
</div>

<!-- ================= ROW 2: BIỂU ĐỒ CHART.JS (Tone Xanh Lá Thương Hiệu) ================= -->
<div class="admin-grid-charts">
    <!-- Chart 1: Doanh thu & Số đơn theo ngày -->
    <section class="admin-card chart-card-main">
        <div class="admin-card-head">
            <div>
                <h2>Doanh thu & Số đơn đặt xe theo ngày (Tháng <?= e($monthLabel) ?>)</h2>
                <p class="muted small" style="margin:2px 0 0;">Dữ liệu trực quan hóa từ các chuyến xe trong tháng</p>
            </div>
            <span class="badge success">Thời gian thực</span>
        </div>
        <div class="chart-canvas-wrap">
            <canvas id="bookingsTrendChart" height="260"></canvas>
        </div>
    </section>

    <!-- Chart 2: Tỷ lệ phân bổ trạng thái đơn đặt xe -->
    <section class="admin-card chart-card-donut">
        <div class="admin-card-head">
            <div>
                <h2>Tỷ lệ trạng thái đơn</h2>
                <p class="muted small" style="margin:2px 0 0;">Tháng <?= e($monthLabel) ?></p>
            </div>
        </div>
        <div class="chart-donut-wrap">
            <canvas id="statusDonutChart" height="190"></canvas>
        </div>
        <div class="status-legend-grid">
            <div class="legend-item"><span class="legend-dot" style="background:#f59e0b;"></span> Chờ duyệt: <strong><?= $pendingCount ?></strong></div>
            <div class="legend-item"><span class="legend-dot" style="background:#0ea5e9;"></span> Đã xác nhận: <strong><?= $confirmedCount ?></strong></div>
            <div class="legend-item"><span class="legend-dot" style="background:#10b981;"></span> Hoàn thành: <strong><?= $completedCount ?></strong></div>
            <div class="legend-item"><span class="legend-dot" style="background:#ef4444;"></span> Đã hủy: <strong><?= $cancelledCount ?></strong></div>
        </div>
    </section>
</div>

<!-- ================= ROW 3: BẢNG ĐƠN ĐẶT GẦN ĐÂY & THỐNG KÊ CHI TIẾT ================= -->
<div class="admin-grid-2">
    <!-- Box 1: Đơn đặt xe gần đây -->
    <section class="admin-card">
        <div class="admin-card-head">
            <div>
                <h2>Đơn đặt xe mới nhất</h2>
                <p class="muted small" style="margin:2px 0 0;">Khách vừa đặt trực tuyến qua website</p>
            </div>
            <a class="link-more" href="<?= url('/admin/bookings') ?>">Xem tất cả đơn →</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đón</th><th>Trạng thái</th><th style="text-align:right;">Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_bookings'] as $booking): ?>
                        <tr>
                            <td>
                                <a href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>" class="data-glass-badge" style="color:var(--forest); font-weight:800; text-decoration:none;">
                                    #<?= e($booking['booking_code']) ?>
                                </a>
                            </td>
                            <td>
                                <strong style="color:var(--forest); font-size:0.95rem;"><?= e($booking['customer_name']) ?></strong>
                            </td>
                            <td>
                                <span class="data-glass-badge">
                                    <?= e(\App\Support\Format::date($booking['pickup_date'] ?? null)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= e(\App\Support\Format::badge((string) $booking['status'])) ?>">
                                    <?= e(\App\Support\Format::bookingStatus((string) $booking['status'])) ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <a class="btn btn-sm btn-action-glass" href="<?= url('/admin/bookings/' . (int) $booking['booking_id']) ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($stats['recent_bookings'] === []): ?>
                        <tr><td colspan="5" class="muted" style="text-align:center; padding:24px;">Chưa có đơn đặt xe nào gần đây.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Box 2: Tóm tắt trạng thái tháng này -->
    <section class="admin-card">
        <div class="admin-card-head">
            <div>
                <h2>Thống kê chi tiết theo trạng thái</h2>
                <p class="muted small" style="margin:2px 0 0;">Tổng số: <?= (int) ($summary['total'] ?? 0) ?> đơn hàng</p>
            </div>
        </div>
        <div class="status-breakdown">
            <?php foreach (['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Hoàn thành chuyến', 'cancelled' => 'Đã hủy chuyến'] as $key => $label): ?>
                <div class="status-row">
                    <span class="<?= e(\App\Support\Format::badge($key)) ?>"><?= e($label) ?></span>
                    <strong><?= (int) ($summary[$key] ?? 0) ?> đơn</strong>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="padding:16px 24px; border-top:1px solid rgba(220, 235, 224, 0.7); background:rgba(240, 253, 244, 0.5); display:flex; justify-content:space-between; align-items:center;">
            <span class="muted small">Tỷ lệ hoàn thành:</span>
            <strong style="color:var(--forest); font-size:1.05rem;">
                <?= ($summary['total'] ?? 0) > 0 ? round(($completedCount / $summary['total']) * 100, 1) : 0 ?>%
            </strong>
        </div>
    </section>
</div>

<!-- Box 4: Bảng chi tiết theo ngày trong tháng -->
<section class="admin-card">
    <div class="admin-card-head">
        <div>
            <h2>Số liệu theo ngày (tháng <?= e($monthLabel) ?>)</h2>
            <p class="muted small" style="margin:2px 0 0;">Bảng kê chi tiết từng ngày phục vụ trong tháng</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ngày đón</th>
                    <th style="text-align:center;">Tổng chuyến</th>
                    <th style="text-align:center;">Chờ duyệt</th>
                    <th style="text-align:center;">Xác nhận</th>
                    <th style="text-align:center;">Hoàn thành</th>
                    <th style="text-align:center;">Đã hủy</th>
                    <th style="text-align:right;">Doanh thu tạm tính</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chart as $row): ?>
                    <tr>
                        <td><strong><?= e(\App\Support\Format::date($row['label'])) ?></strong></td>
                        <td style="text-align:center;"><span class="badge neutral"><?= (int) $row['total'] ?></span></td>
                        <td style="text-align:center;"><span style="color:#d97706; font-weight:700;"><?= (int) $row['pending'] ?></span></td>
                        <td style="text-align:center;"><span style="color:#0284c7; font-weight:700;"><?= (int) $row['confirmed'] ?></span></td>
                        <td style="text-align:center;"><span style="color:#10b981; font-weight:700;"><?= (int) $row['completed'] ?></span></td>
                        <td style="text-align:center;"><span style="color:#ef4444; font-weight:700;"><?= (int) $row['cancelled'] ?></span></td>
                        <td style="text-align:right;"><span class="data-glass-badge"><?= e(\App\Support\Format::money($row['amount'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($chart === []): ?>
                    <tr><td colspan="7" class="muted" style="text-align:center; padding:24px;">Chưa có dữ liệu chuyến xe nào trong tháng <?= e($monthLabel) ?>.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ================= SCRIPT VẼ BIỂU ĐỒ CHART.JS THEO DỮ LIỆU THẬT ================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    // 1. Biểu đồ đường: Doanh thu và Số chuyến xe theo ngày (Tone Xanh Lá)
    const ctxTrend = document.getElementById('bookingsTrendChart');
    if (ctxTrend) {
        const ctx = ctxTrend.getContext('2d');
        const gradientGreen = ctx.createLinearGradient(0, 0, 0, 250);
        gradientGreen.addColorStop(0, 'rgba(16, 185, 129, 0.40)');
        gradientGreen.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        const dates = <?= json_encode($chartDates, JSON_UNESCAPED_UNICODE) ?>;
        const totals = <?= json_encode($chartTotals) ?>;
        const amounts = <?= json_encode($chartAmounts) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates.length > 0 ? dates : ['Đầu tháng', 'Giữa tháng', 'Cuối tháng'],
                datasets: [
                    {
                        label: 'Số chuyến xe (đơn)',
                        data: totals.length > 0 ? totals : [0, 0, 0],
                        borderColor: '#10b981',
                        borderWidth: 3,
                        backgroundColor: gradientGreen,
                        fill: true,
                        tension: 0.38,
                        pointBackgroundColor: '#0b5130',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Doanh thu (triệu đ)',
                        data: amounts.length > 0 ? amounts : [0, 0, 0],
                        borderColor: '#0b7a3b',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        backgroundColor: 'transparent',
                        pointBackgroundColor: '#0b7a3b',
                        pointBorderColor: '#ffffff',
                        pointRadius: 3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            color: '#334155',
                            boxWidth: 14,
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(11, 81, 48, 0.90)',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { weight: 'bold' },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Số đơn', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                        grid: { color: 'rgba(220, 235, 224, 0.6)' },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b', stepSize: 1 }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Triệu đồng', font: { size: 11, weight: 'bold' }, color: '#64748b' },
                        grid: { drawOnChartArea: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
    }

    // 2. Biểu đồ tròn Donut: Trạng thái đơn đặt xe tháng này
    const ctxDonut = document.getElementById('statusDonutChart');
    if (ctxDonut) {
        new Chart(ctxDonut.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Chờ duyệt', 'Đã xác nhận', 'Hoàn thành', 'Đã hủy'],
                datasets: [{
                    data: [<?= $pendingCount ?>, <?= $confirmedCount ?>, <?= $completedCount ?>, <?= $cancelledCount ?>],
                    backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981', '#ef4444'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(11, 81, 48, 0.92)',
                        padding: 10,
                        cornerRadius: 10,
                    }
                }
            }
        });
    }
});
</script>
