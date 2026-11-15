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
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const form = document.getElementById('lookup-form');
const resultBox = document.getElementById('lookup-result');
const submitButton = document.getElementById('lookup-btn');

await initLayout('lookup');

let activeQuery = null;
let lastBooking = '';
let poller = null;

function renderBooking(booking) {
  if (sameJson(booking, lastBooking)) return;
  lastBooking = booking;

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
    <p class="muted small">Trạng thái đơn tự động cập nhật, bạn không cần tải lại trang.</p>
  </div>`;
}

async function fetchBooking(query) {
  const response = await get('/api/bookings/lookup', { auth: false, query });
  return response.data;
}

function startPoller() {
  if (poller !== null) return;
  poller = liveRefresh(async () => {
    if (!activeQuery) return;
    renderBooking(await fetchBooking(activeQuery));
  }, { interval: 15, immediate: false });
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
    activeQuery = { code, phone };
    lastBooking = '';
    renderBooking(await fetchBooking(activeQuery));
    startPoller();
  } catch (error) {
    setAlert(alertBox, typeof error.firstError === 'function' ? error.firstError() : error.message);
  } finally {
    submitButton.disabled = false;
  }
});
