import { get } from '../api.js';
import {
  initLayout,
  queryParam,
  vehicleCard,
  emptyState,
  setAlert,
  escapeHtml,
} from '../site.js';

const alertBox = document.getElementById('page-alert');
const grid = document.getElementById('vehicle-grid');
const form = document.getElementById('filter-form');
const typeSelect = document.getElementById('type-filter');
const searchInput = document.getElementById('search-input');

await initLayout('cars');

const params = new URLSearchParams(window.location.search);
const initialType = params.get('vehicle_type_id') || '';
const initialSearch = params.get('search') || '';
searchInput.value = initialSearch;

try {
  const response = await get('/api/vehicle-types', { auth: false });
  typeSelect.innerHTML = '<option value="">Tất cả dòng xe</option>' + (response.data || [])
    .map((type) => `<option value="${type.id}"${String(type.id) === initialType ? ' selected' : ''}>${escapeHtml(type.name)}</option>`)
    .join('');
} catch {
  typeSelect.innerHTML = '<option value="">Tất cả dòng xe</option>';
}

async function loadVehicles() {
  const vehicleTypeId = typeSelect.value;
  const search = searchInput.value.trim();
  setAlert(alertBox, '');
  grid.innerHTML = '<p class="muted">Đang tải danh sách xe...</p>';

  try {
    const response = await get('/api/vehicles', {
      auth: false,
      query: { vehicle_type_id: vehicleTypeId, search },
    });
    const items = response.data || [];
    grid.innerHTML = items.length > 0
      ? items.map(vehicleCard).join('')
      : emptyState('Không có xe phù hợp với bộ lọc.');

    const next = new URLSearchParams();
    if (vehicleTypeId) next.set('vehicle_type_id', vehicleTypeId);
    if (search) next.set('search', search);
    const query = next.toString();
    history.replaceState(null, '', query ? `?${query}` : window.location.pathname);
  } catch (error) {
    grid.innerHTML = '';
    setAlert(alertBox, error.message || 'Không tải được danh sách xe.');
  }
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  loadVehicles();
});

loadVehicles();
