import { get } from '../api.js';
import { initLayout, onSettingsChange, escapeHtml, typeCard, emptyState, setAlert } from '../site.js';
import { liveRefresh } from '../live.js';

const alertBox = document.getElementById('page-alert');
const settings = await initLayout('about');

function applySettings(current) {
  const descEl = document.getElementById('about-description');
  if (descEl) {
    descEl.textContent = current.description
      || 'Thảo Vy Travel là đơn vị chuyên cung cấp dịch vụ thuê xe du lịch, thuê xe hợp đồng chuyên nghiệp tại Việt Nam.';
  }

  const infoEl = document.getElementById('about-info');
  if (infoEl) {
    const rows = [
      ['Công ty', current.company_name || 'Thảo Vy Travel'],
      ['Địa chỉ', current.address || 'Hà Nội & các tỉnh lân cận'],
      ['Điện thoại', current.phone || '0787 972 075'],
      ['Hotline', current.hotline || '0787 972 075'],
      ['Email', current.email || 'duyphongtv123@gmail.com'],
      ['Giờ làm việc', current.working_hours || '24/7 (Cả ngày lễ, Tết)'],
    ];

    infoEl.innerHTML = rows
      .filter(([, value]) => value !== '')
      .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
      .join('');
  }

  // Cập nhật số điện thoại và email nếu có các phần tử dynamic
  const phoneEls = document.querySelectorAll('.dynamic-hotline');
  phoneEls.forEach(el => {
    if (current.hotline || current.phone) {
      el.textContent = current.hotline || current.phone;
      if (el.tagName === 'A') el.href = 'tel:' + (current.hotline || current.phone).replace(/\s+/g, '');
    }
  });

  const emailEls = document.querySelectorAll('.dynamic-email');
  emailEls.forEach(el => {
    if (current.email) {
      el.textContent = current.email;
      if (el.tagName === 'A') el.href = 'mailto:' + current.email;
    }
  });

  const addressEls = document.querySelectorAll('.dynamic-address');
  addressEls.forEach(el => {
    if (current.address) el.textContent = current.address;
  });
}

applySettings(settings);
onSettingsChange(applySettings);

async function loadTypes() {
  const gridEl = document.getElementById('type-grid');
  if (!gridEl) return;
  try {
    const response = await get('/api/vehicle-types', { auth: false });
    gridEl.innerHTML =
      (response.data || []).map(typeCard).join('') || emptyState('Chưa có dòng xe nào.');
  } catch (error) {
    if (alertBox) setAlert(alertBox, error.message || 'Không tải được danh sách dòng xe.');
  }
}

try {
  await loadTypes();
} catch (error) {
  if (alertBox) setAlert(alertBox, error.message || 'Không tải được danh sách dòng xe.');
}

liveRefresh(loadTypes, { interval: 30, immediate: false });
