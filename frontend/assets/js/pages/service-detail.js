import { get } from '../api.js';
import { initLayout, queryParam, money, setAlert, siteName } from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
await initLayout('services');

const id = queryParam('id');
let lastSignature = '';

function renderService(data) {
  if (sameJson(data, lastSignature)) return;
  lastSignature = data;

  document.title = `${data.service_name} — ${siteName()}`;
  document.getElementById('service-name').textContent = data.service_name;
  document.getElementById('service-price').textContent = `Giá từ ${money(data.price)}`;
  document.getElementById('service-description').textContent = data.description || 'Thông tin dịch vụ đang được cập nhật.';
}

async function loadService() {
  const response = await get(`/api/services/${encodeURIComponent(id)}`, { auth: false });
  renderService(response.data);
}

if (!id) {
  setAlert(alertBox, 'Thiếu mã dịch vụ. Vui lòng quay lại danh sách dịch vụ.');
  document.getElementById('service-detail').style.display = 'none';
} else {
  try {
    await loadService();
  } catch (error) {
    setAlert(alertBox, error.message || 'Không tìm thấy dịch vụ.');
    document.getElementById('service-detail').style.display = 'none';
  }

  liveRefresh(loadService, { interval: 30, immediate: false });
}
