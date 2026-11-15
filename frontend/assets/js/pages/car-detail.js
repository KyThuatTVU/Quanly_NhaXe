import { get } from '../api.js';
import {
  initLayout,
  pageUrl,
  queryParam,
  money,
  formatDate,
  stars,
  mediaHtml,
  escapeHtml,
  emptyState,
  setAlert,
  siteName,
} from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
await initLayout('cars');

const id = queryParam('id');
let lastSignature = '';

function renderVehicle(vehicle, reviews) {
  const model = { vehicle, reviews };
  if (sameJson(model, lastSignature)) return;
  lastSignature = model;

  document.title = `${vehicle.name} — ${siteName()}`;
  document.getElementById('vehicle-media').innerHTML = mediaHtml(vehicle.image, vehicle.name);
  document.getElementById('vehicle-name').textContent = vehicle.name;
  document.getElementById('vehicle-description').textContent = vehicle.description || '';

  const carStatus = vehicle.status === 'available'
    ? '<span class="badge badge--success">Sẵn sàng</span>'
    : '<span class="badge badge--neutral">Tạm ngưng</span>';

  const specs = [
    ['Dòng xe', vehicle.type_name || vehicle.type || ''],
    ['Số chỗ', vehicle.type_seats ? `${vehicle.type_seats} chỗ` : ''],
    ['Giá theo ngày', money(vehicle.price_per_day)],
    ['Giá theo km', `${money(vehicle.price_per_km)} / km`],
    ['Đánh giá', `${stars(vehicle.rating)} (${Number(vehicle.rating || 0).toFixed(1)})`],
    ['Tài xế', vehicle.driver_info || ''],
    ['Bảo hiểm', vehicle.insurance_info || ''],
    ['Trạng thái', carStatus],
  ];

  document.getElementById('vehicle-specs').innerHTML = specs
    .filter(([, value]) => value !== '')
    .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${label === 'Đánh giá' || label === 'Trạng thái' ? value : escapeHtml(String(value))}</dd></div>`)
    .join('');

  if (vehicle.status === 'available') {
    document.getElementById('vehicle-price').textContent = `${money(vehicle.price_per_day)} / ngày`;
  } else {
    document.getElementById('vehicle-price').textContent = 'Xe đang tạm ngưng phục vụ';
  }
  document.getElementById('vehicle-km').textContent = `Giá km: ${money(vehicle.price_per_km)} / km`;
  document.getElementById('book-link').href = `${pageUrl('/booking/')}?vehicle_id=${encodeURIComponent(vehicle.vehicle_id)}`;

  document.getElementById('review-list').innerHTML = reviews.length > 0
    ? reviews.map((review) => `<article class="review">
        <div class="review-head">
          <strong>${escapeHtml(review.username || 'Khách hàng')}</strong>
          <span class="muted">${formatDate(review.created_at)}</span>
        </div>
        <p class="stars">${stars(review.rating)}</p>
        <p>${escapeHtml(review.comment || '')}</p>
      </article>`).join('')
    : emptyState('Chưa có đánh giá cho xe này.');
}

async function loadVehicle() {
  const [vehicleResponse, reviewResponse] = await Promise.all([
    get(`/api/vehicles/${encodeURIComponent(id)}`, { auth: false }),
    get(`/api/vehicles/${encodeURIComponent(id)}/reviews`, { auth: false }),
  ]);
  renderVehicle(vehicleResponse.data, reviewResponse.data || []);
}

if (!id) {
  setAlert(alertBox, 'Thiếu mã xe. Vui lòng quay lại danh sách xe.');
  document.getElementById('vehicle-detail').style.display = 'none';
} else {
  try {
    await loadVehicle();
  } catch (error) {
    setAlert(alertBox, error.message || 'Không tìm thấy xe.');
    document.getElementById('vehicle-detail').style.display = 'none';
  }

  liveRefresh(loadVehicle, { interval: 20, immediate: false });
}
