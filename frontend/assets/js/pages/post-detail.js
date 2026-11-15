import { get } from '../api.js';
import { initLayout, queryParam, formatDate, sanitizeHtml, setAlert, siteName } from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
await initLayout('posts');

const slug = queryParam('slug');
let lastSignature = '';

function renderPost(data) {
  if (sameJson(data, lastSignature)) return;
  lastSignature = data;

  document.title = `${data.title} — ${siteName()}`;
  document.getElementById('post-meta').textContent = `${data.category || ''} · ${formatDate(data.created_at)}`;
  document.getElementById('post-title').textContent = data.title;
  document.getElementById('post-content').innerHTML = sanitizeHtml(data.content || '');
}

async function loadPost() {
  const response = await get(`/api/posts/${encodeURIComponent(slug)}`, { auth: false });
  renderPost(response.data);
}

if (!slug) {
  setAlert(alertBox, 'Thiếu đường dẫn bài viết.');
  document.getElementById('post-detail').style.display = 'none';
} else {
  try {
    await loadPost();
  } catch (error) {
    setAlert(alertBox, error.message || 'Không tìm thấy bài viết.');
    document.getElementById('post-detail').style.display = 'none';
  }

  liveRefresh(loadPost, { interval: 30, immediate: false });
}
