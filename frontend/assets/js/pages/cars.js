import { get } from '../api.js';
import {
  initLayout,
  queryParam,
  vehicleCard,
  emptyState,
  setAlert,
  escapeHtml,
} from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const grid = document.getElementById('vehicle-grid');
const form = document.getElementById('filter-form');
const typeSelect = document.getElementById('type-filter');
const searchInput = document.getElementById('search-input');

await initLayout('cars');

const initialType = queryParam('vehicle_type_id');
const initialSearch = queryParam('search');
searchInput.value = initialSearch;

let typeOptions = [];
let lastListSignature = '';

function renderTypeOptions() {
  const current = typeSelect.value;
  typeSelect.innerHTML = '<option value="">Tất cả dòng xe</option>' + typeOptions
    .map((type) => `<option value="${type.id}">${escapeHtml(type.name)}</option>`)
    .join('');
  typeSelect.value = current || initialType;
}

async function loadVehicleTypes() {
  const response = await get('/api/vehicle-types', { auth: false });
  const options = (response.data || []).map((type) => ({ id: type.id, name: type.name }));
  if (sameJson(options, typeOptions)) return;
  typeOptions = options;
  renderTypeOptions();
}

try {
  await loadVehicleTypes();
} catch {
  typeSelect.innerHTML = '<option value="">Tất cả dòng xe</option>';
}

async function loadVehicles({ showLoading = false } = {}) {
  const vehicleTypeId = typeSelect.value;
  const search = searchInput.value.trim();
  if (showLoading) {
    setAlert(alertBox, '');
    grid.innerHTML = '<p class="muted">Đang tải danh sách xe...</p>';
  }

  try {
    const response = await get('/api/vehicles', {
      auth: false,
      query: { vehicle_type_id: vehicleTypeId, search },
    });
    const items = response.data || [];
    const model = { vehicleTypeId, search, items };

    if (!sameJson(model, lastListSignature)) {
      lastListSignature = model;
      grid.innerHTML = items.length > 0
        ? items.map(vehicleCard).join('')
        : emptyState('Không có xe phù hợp với bộ lọc.');
    }

    const next = new URLSearchParams();
    if (vehicleTypeId) next.set('vehicle_type_id', vehicleTypeId);
    if (search) next.set('search', search);
    const query = next.toString();
    const target = query ? `?${query}` : window.location.pathname;
    if (window.location.pathname + window.location.search !== target) {
      history.replaceState(null, '', target);
    }
  } catch (error) {
    if (showLoading) {
      grid.innerHTML = '';
      setAlert(alertBox, error.message || 'Không tải được danh sách xe.');
    }
  }
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  loadVehicles({ showLoading: true });
});

await loadVehicles({ showLoading: true });

liveRefresh(loadVehicles, { interval: 20, immediate: false });
liveRefresh(loadVehicleTypes, { interval: 60, immediate: false });
