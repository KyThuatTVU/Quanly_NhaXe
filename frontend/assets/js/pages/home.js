import { get } from '../api.js';
import {
  initLayout,
  onSettingsChange,
  pageUrl,
  typeCard,
  vehicleCard,
  serviceCard,
  postCard,
  emptyState,
  setAlert,
} from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const settings = await initLayout('home');

function applyHero(current) {
  document.getElementById('hero-desc').textContent = current.description
    || 'Đội xe 4 - 45 chỗ đời mới, tài xế nhiều năm kinh nghiệm, phục vụ 24/7 tại Hà Nội và các tỉnh lân cận.';

  const hotline = current.hotline || current.phone || '';
  const cta = document.getElementById('cta-hotline');
  if (hotline) {
    cta.href = `tel:${String(hotline).replace(/[^\d+]/g, '')}`;
    cta.textContent = `Gọi ${hotline}`;
  } else {
    cta.href = pageUrl('/contact/');
    cta.textContent = 'Gửi yêu cầu';
  }
}

applyHero(settings);
onSettingsChange(applyHero);

let lastModel = '';

async function loadHome() {
  const [types, vehicles, services, posts] = await Promise.all([
    get('/api/vehicle-types', { auth: false }),
    get('/api/vehicles', { auth: false }),
    get('/api/services', { auth: false }),
    get('/api/posts/latest', { auth: false, query: { limit: 3 } }),
  ]);

  const model = {
    types: (types.data || []).slice(0, 4),
    vehicles: (vehicles.data || []).slice(0, 3),
    services: (services.data || []).slice(0, 3),
    posts: posts.data || [],
  };

  if (sameJson(model, lastModel)) return;
  lastModel = model;

  document.getElementById('type-grid').innerHTML =
    model.types.map(typeCard).join('') || emptyState('Chưa có dòng xe nào.');

  document.getElementById('vehicle-grid').innerHTML =
    model.vehicles.map(vehicleCard).join('') || emptyState('Chưa có xe sẵn sàng.');

  document.getElementById('service-grid').innerHTML =
    model.services.map(serviceCard).join('') || emptyState('Chưa có dịch vụ nào.');

  document.getElementById('post-grid').innerHTML =
    model.posts.map(postCard).join('') || emptyState('Chưa có bài viết nào.');
}

function initCarVideo() {
  const overlay = document.getElementById('video-poster-overlay');
  const video = document.getElementById('car-video-player');
  if (!overlay || !video) return;

  const startPlay = () => {
    overlay.classList.add('is-playing');
    video.play().catch(() => {});
  };

  overlay.addEventListener('click', startPlay);
  video.addEventListener('play', () => overlay.classList.add('is-playing'));
}

initCarVideo();

try {
  await loadHome();
  setAlert(alertBox, '');
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được dữ liệu từ máy chủ.');
}

liveRefresh(loadHome, { interval: 20, immediate: false });
