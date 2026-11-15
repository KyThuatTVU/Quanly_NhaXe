import { get, post } from '../api.js';
import { initLayout, pageUrl, queryParam, money, escapeHtml, setAlert } from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const form = document.getElementById('booking-form');
const vehicleSelect = document.getElementById('vehicle-select');
const serviceSelect = document.getElementById('service-select');
const passengersInput = document.getElementById('passengers');
const vehicleHint = document.getElementById('vehicle-hint');
const submitButton = document.getElementById('submit-btn');

await initLayout('cars');

function isoToday() {
  const now = new Date();
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
}

const today = isoToday();
document.getElementById('pickup-date').min = today;
document.getElementById('return-date').min = today;

function syncSeats() {
  const option = vehicleSelect.selectedOptions[0];
  const seats = option ? Number(option.dataset.seats || 0) : 0;
  passengersInput.max = seats > 0 ? String(seats) : '';
  vehicleHint.textContent = seats > 0 ? `Xe chở tối đa ${seats} hành khách.` : '';
}

const preselect = queryParam('vehicle_id');
let lastOptions = '';

function hasOption(select, value) {
  return value !== '' && [...select.options].some((option) => option.value === value);
}

function renderOptions(vehicles, services) {
  const currentVehicle = vehicleSelect.value || preselect;
  const currentService = serviceSelect.value;

  vehicleSelect.innerHTML = '<option value="">Chọn xe</option>' + vehicles
    .map((vehicle) => `<option value="${vehicle.vehicle_id}" data-seats="${Number(vehicle.type_seats || 0)}">${escapeHtml(vehicle.name)} — ${escapeHtml(vehicle.type_name || vehicle.type || '')} — ${money(vehicle.price_per_day)}/ngày</option>`)
    .join('');

  serviceSelect.innerHTML = '<option value="">Không chọn dịch vụ</option>' + services
    .map((service) => `<option value="${service.service_id}">${escapeHtml(service.service_name)} — từ ${money(service.price)}</option>`)
    .join('');

  if (hasOption(vehicleSelect, currentVehicle)) vehicleSelect.value = currentVehicle;
  if (hasOption(serviceSelect, currentService)) serviceSelect.value = currentService;
  syncSeats();
}

async function loadOptions() {
  const [vehiclesResponse, servicesResponse] = await Promise.all([
    get('/api/vehicles', { auth: false }),
    get('/api/services', { auth: false }),
  ]);
  const model = {
    vehicles: vehiclesResponse.data || [],
    services: servicesResponse.data || [],
  };
  if (sameJson(model, lastOptions)) return;
  lastOptions = model;
  renderOptions(model.vehicles, model.services);
}

try {
  await loadOptions();
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được danh sách xe/dịch vụ.');
}

liveRefresh(loadOptions, { interval: 30, immediate: false });

vehicleSelect.addEventListener('change', syncSeats);

function clearFieldErrors() {
  form.querySelectorAll('[data-error]').forEach((element) => {
    element.textContent = '';
  });
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  clearFieldErrors();
  setAlert(alertBox, '');
  submitButton.disabled = true;

  const data = new FormData(form);
  const text = (name) => String(data.get(name) ?? '').trim();
  const optional = (name) => {
    const value = text(name);
    return value === '' ? null : value;
  };

  const payload = {
    customer_name: text('customer_name'),
    customer_phone: text('customer_phone'),
    customer_email: optional('customer_email'),
    pickup_location: text('pickup_location'),
    dropoff_location: optional('dropoff_location'),
    pickup_date: text('pickup_date'),
    pickup_time: text('pickup_time'),
    return_date: optional('return_date'),
    return_time: optional('return_time'),
    number_of_passengers: text('number_of_passengers') === '' ? null : Number(text('number_of_passengers')),
    vehicle_id: Number(text('vehicle_id')),
    service_id: text('service_id') === '' ? null : Number(text('service_id')),
    notes: optional('notes'),
  };

  if (!payload.vehicle_id) {
    document.querySelector('[data-error="vehicle_id"]').textContent = 'Vui lòng chọn xe.';
    submitButton.disabled = false;
    return;
  }

  try {
    const result = await post('/api/bookings', payload);
    sessionStorage.setItem('thaovy.lastBooking', JSON.stringify(result.data));
    window.location.href = pageUrl('/booking/success/');
  } catch (error) {
    const fieldErrors = error.errors || {};
    Object.entries(fieldErrors).forEach(([field, message]) => {
      const element = form.querySelector(`[data-error="${field}"]`);
      if (element) element.textContent = String(message);
    });
    setAlert(alertBox, typeof error.firstError === 'function' ? error.firstError() : error.message);
    submitButton.disabled = false;
  }
});
