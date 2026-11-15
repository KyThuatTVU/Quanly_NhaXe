import {
  initLayout,
  escapeHtml,
  money,
  formatDate,
  formatTime,
  statusBadge,
  setAlert,
} from '../site.js';

const alertBox = document.getElementById('page-alert');
await initLayout('lookup');

const raw = sessionStorage.getItem('thaovy.lastBooking');
if (!raw) {
  setAlert(alertBox, 'Không tìm thấy thông tin đơn vừa đặt. Bạn có thể tra cứu đơn bằng mã và số điện thoại.', 'info');
  document.getElementById('success-card').style.display = 'none';
} else {
  try {
    const booking = JSON.parse(raw);
    document.getElementById('booking-code').textContent = booking.booking_code || '';
    const rows = [
      ['Khách hàng', booking.customer_name],
      ['Số điện thoại', booking.customer_phone],
      ['Xe', booking.vehicle_name],
      ['Dịch vụ', booking.service_name],
      ['Điểm đón', booking.pickup_location],
      ['Điểm đến', booking.dropoff_location],
      ['Ngày đón', `${formatDate(booking.pickup_date)} ${formatTime(booking.pickup_time)}`],
      ['Ngày trả', booking.return_date ? `${formatDate(booking.return_date)} ${formatTime(booking.return_time)}` : ''],
      ['Số hành khách', booking.number_of_passengers ? `${booking.number_of_passengers} người` : ''],
      ['Tạm tính', money(booking.total_amount)],
      ['Trạng thái', statusBadge(booking.status)],
    ];
    document.getElementById('booking-info').innerHTML = rows
      .filter(([, value]) => value !== null && value !== undefined && value !== '')
      .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${label === 'Trạng thái' ? value : escapeHtml(String(value))}</dd></div>`)
      .join('');
  } catch {
    setAlert(alertBox, 'Dữ liệu đơn không hợp lệ. Vui lòng tra cứu đơn bằng mã và số điện thoại.', 'info');
    document.getElementById('success-card').style.display = 'none';
  }
}
