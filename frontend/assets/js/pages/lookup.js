import { get } from '../api.js';
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
const form = document.getElementById('lookup-form');
const resultBox = document.getElementById('lookup-result');
const submitButton = document.getElementById('lookup-btn');

await initLayout('lookup');

function renderBooking(booking) {
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
    ['Ghi chú', booking.notes],
  ];

  resultBox.innerHTML = `<div class="info-card">
    <div class="section-head">
      <h2>Đơn ${escapeHtml(booking.booking_code)}</h2>
      ${statusBadge(booking.status)}
    </div>
    <dl class="info-list">
      ${rows.filter(([, value]) => value !== null && value !== undefined && value !== '')
        .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(String(value))}</dd></div>`)
        .join('')}
    </dl>
  </div>`;
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  setAlert(alertBox, '');
  resultBox.innerHTML = '';
  submitButton.disabled = true;

  const data = new FormData(form);
  const code = String(data.get('code') || '').trim();
  const phone = String(data.get('phone') || '').trim();

  if (!code || !phone) {
    setAlert(alertBox, 'Vui lòng nhập mã đơn và số điện thoại.');
    submitButton.disabled = false;
    return;
  }

  try {
    const response = await get('/api/bookings/lookup', {
      auth: false,
      query: { code, phone },
    });
    renderBooking(response.data);
  } catch (error) {
    setAlert(alertBox, typeof error.firstError === 'function' ? error.firstError() : error.message);
  } finally {
    submitButton.disabled = false;
  }
});
