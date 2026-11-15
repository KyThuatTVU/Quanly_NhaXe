import { post } from '../api.js';
import { initLayout, escapeHtml, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
const form = document.getElementById('contact-form');
const submitButton = document.getElementById('contact-btn');

const settings = await initLayout('contact');

const infoRows = [
  ['Công ty', settings.company_name || ''],
  ['Địa chỉ', settings.address || ''],
  ['Điện thoại', settings.phone || ''],
  ['Hotline', settings.hotline || ''],
  ['Email', settings.email || ''],
  ['Giờ làm việc', settings.working_hours || ''],
];

document.getElementById('contact-info').innerHTML = infoRows
  .filter(([, value]) => value !== '')
  .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
  .join('');

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
    name: text('name'),
    phone: text('phone'),
    email: optional('email'),
    subject: optional('subject'),
    message: text('message'),
  };

  try {
    await post('/api/contacts', payload);
    form.reset();
    setAlert(alertBox, 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.', 'success');
  } catch (error) {
    const fieldErrors = error.errors || {};
    Object.entries(fieldErrors).forEach(([field, message]) => {
      const element = form.querySelector(`[data-error="${field}"]`);
      if (element) element.textContent = String(message);
    });
    setAlert(alertBox, typeof error.firstError === 'function' ? error.firstError() : error.message);
  } finally {
    submitButton.disabled = false;
  }
});
