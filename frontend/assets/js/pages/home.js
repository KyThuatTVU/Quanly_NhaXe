import { get } from '../api.js';
import {
  initLayout,
  pageUrl,
  typeCard,
  vehicleCard,
  serviceCard,
  postCard,
  emptyState,
  setAlert,
} from '../site.js';

const alertBox = document.getElementById('page-alert');
const settings = await initLayout('home');

document.getElementById('hero-desc').textContent = settings.description
  || 'Đội xe 4 - 45 chỗ đời mới, tài xế nhiều năm kinh nghiệm, phục vụ 24/7 tại Hà Nội và các tỉnh lân cận.';

const hotline = settings.hotline || settings.phone || '';
const cta = document.getElementById('cta-hotline');
if (hotline) {
  cta.href = `tel:${String(hotline).replace(/[^\d+]/g, '')}`;
  cta.textContent = `Gọi ${hotline}`;
} else {
  cta.href = pageUrl('/contact/');
  cta.textContent = 'Gửi yêu cầu';
}

try {
  const [types, vehicles, services, posts] = await Promise.all([
    get('/api/vehicle-types', { auth: false }),
    get('/api/vehicles', { auth: false }),
    get('/api/services', { auth: false }),
    get('/api/posts/latest', { auth: false, query: { limit: 3 } }),
  ]);

  document.getElementById('type-grid').innerHTML =
    (types.data || []).slice(0, 4).map(typeCard).join('') || emptyState('Chưa có dòng xe nào.');

  document.getElementById('vehicle-grid').innerHTML =
    (vehicles.data || []).slice(0, 3).map(vehicleCard).join('') || emptyState('Chưa có xe sẵn sàng.');

  document.getElementById('service-grid').innerHTML =
    (services.data || []).slice(0, 3).map(serviceCard).join('') || emptyState('Chưa có dịch vụ nào.');

  document.getElementById('post-grid').innerHTML =
    (posts.data || []).map(postCard).join('') || emptyState('Chưa có bài viết nào.');
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được dữ liệu từ máy chủ.');
}
