import { post } from '../api.js';
import { initLayout, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
const form = document.getElementById('review-form');
const submitButton = document.getElementById('review-btn');

await initLayout('');

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
  const ratingValue = text('rating');

  if (!ratingValue) {
    document.querySelector('[data-error="rating"]').textContent = 'Vui lòng chọn số sao.';
    submitButton.disabled = false;
    return;
  }

  const payload = {
    booking_code: text('booking_code'),
    phone: text('phone'),
    rating: Number(ratingValue),
    comment: text('comment') === '' ? null : text('comment'),
  };

  try {
    await post('/api/reviews', payload);
    form.reset();
    setAlert(alertBox, `Cảm ơn bạn đã gửi đánh giá ${payload.rating} sao.`, 'success');
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
