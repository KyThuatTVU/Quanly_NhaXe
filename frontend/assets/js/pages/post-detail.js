import { get } from '../api.js';
import { initLayout, queryParam, formatDate, escapeHtml, sanitizeHtml, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
await initLayout('posts');

const slug = queryParam('slug');
if (!slug) {
  setAlert(alertBox, 'Thiếu đường dẫn bài viết.');
  document.getElementById('post-detail').style.display = 'none';
} else {
  try {
    const { data } = await get(`/api/posts/${encodeURIComponent(slug)}`, { auth: false });
    document.title = `${data.title} — Xe Hợp Đồng Tháo Vy`;
    document.getElementById('post-meta').textContent = `${data.category || ''} · ${formatDate(data.created_at)}`;
    document.getElementById('post-title').textContent = data.title;
    document.getElementById('post-content').innerHTML = sanitizeHtml(data.content || '');
  } catch (error) {
    setAlert(alertBox, error.message || 'Không tìm thấy bài viết.');
    document.getElementById('post-detail').style.display = 'none';
  }
}
