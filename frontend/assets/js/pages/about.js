import { get } from '../api.js';
import { initLayout, escapeHtml, typeCard, emptyState, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
const settings = await initLayout('about');

document.getElementById('about-description').textContent = settings.description
  || 'Dịch vụ cho thuê xe hợp đồng 4 - 45 chỗ tại Hà Nội và các tỉnh lân cận.';

const rows = [
  ['Công ty', settings.company_name || ''],
  ['Địa chỉ', settings.address || ''],
  ['Điện thoại', settings.phone || ''],
  ['Hotline', settings.hotline || ''],
  ['Email', settings.email || ''],
  ['Giờ làm việc', settings.working_hours || ''],
];

document.getElementById('about-info').innerHTML = rows
  .filter(([, value]) => value !== '')
  .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
  .join('');

try {
  const response = await get('/api/vehicle-types', { auth: false });
  document.getElementById('type-grid').innerHTML =
    (response.data || []).map(typeCard).join('') || emptyState('Chưa có dòng xe nào.');
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được danh sách dòng xe.');
}
