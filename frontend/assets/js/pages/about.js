import { get } from '../api.js';
import { initLayout, onSettingsChange, escapeHtml, typeCard, emptyState, setAlert } from '../site.js';
import { liveRefresh } from '../live.js';

const alertBox = document.getElementById('page-alert');
const settings = await initLayout('about');

function applySettings(current) {
  document.getElementById('about-description').textContent = current.description
    || 'Dịch vụ cho thuê xe hợp đồng 4 - 45 chỗ tại Hà Nội và các tỉnh lân cận.';

  const rows = [
    ['Công ty', current.company_name || ''],
    ['Địa chỉ', current.address || ''],
    ['Điện thoại', current.phone || ''],
    ['Hotline', current.hotline || ''],
    ['Email', current.email || ''],
    ['Giờ làm việc', current.working_hours || ''],
  ];

  document.getElementById('about-info').innerHTML = rows
    .filter(([, value]) => value !== '')
    .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
    .join('');
}

applySettings(settings);
onSettingsChange(applySettings);

async function loadTypes() {
  const response = await get('/api/vehicle-types', { auth: false });
  document.getElementById('type-grid').innerHTML =
    (response.data || []).map(typeCard).join('') || emptyState('Chưa có dòng xe nào.');
}

try {
  await loadTypes();
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được danh sách dòng xe.');
}

liveRefresh(loadTypes, { interval: 30, immediate: false });
