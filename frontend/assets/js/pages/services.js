import { get } from '../api.js';
import { initLayout, serviceCard, emptyState, setAlert } from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const grid = document.getElementById('service-grid');

await initLayout('services');

let lastSignature = '';

async function loadServices() {
  const response = await get('/api/services', { auth: false });
  const items = response.data || [];
  if (sameJson(items, lastSignature)) return;
  lastSignature = items;
  grid.innerHTML = items.map(serviceCard).join('') || emptyState('Chưa có dịch vụ nào.');
}

try {
  await loadServices();
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được danh sách dịch vụ.');
}

liveRefresh(loadServices, { interval: 20, immediate: false });
