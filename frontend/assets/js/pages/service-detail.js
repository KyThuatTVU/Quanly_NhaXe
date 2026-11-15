import { get } from '../api.js';
import { initLayout, queryParam, money, escapeHtml, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
await initLayout('services');

const id = queryParam('id');
if (!id) {
  setAlert(alertBox, 'Thiếu mã dịch vụ. Vui lòng quay lại danh sách dịch vụ.');
  document.getElementById('service-detail').style.display = 'none';
} else {
  try {
    const { data } = await get(`/api/services/${encodeURIComponent(id)}`, { auth: false });
    document.title = `${data.service_name} — Xe Hợp Đồng Tháo Vy`;
    document.getElementById('service-name').textContent = data.service_name;
    document.getElementById('service-price').textContent = `Giá từ ${money(data.price)}`;
    document.getElementById('service-description').textContent = data.description || 'Thông tin dịch vụ đang được cập nhật.';
  } catch (error) {
    setAlert(alertBox, error.message || 'Không tìm thấy dịch vụ.');
    document.getElementById('service-detail').style.display = 'none';
  }
}
