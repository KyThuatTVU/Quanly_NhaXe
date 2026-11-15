import { get } from '../api.js';
import { initLayout, serviceCard, emptyState, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
await initLayout('services');

try {
  const response = await get('/api/services', { auth: false });
  document.getElementById('service-grid').innerHTML =
    (response.data || []).map(serviceCard).join('') || emptyState('Chưa có dịch vụ nào.');
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được danh sách dịch vụ.');
}
