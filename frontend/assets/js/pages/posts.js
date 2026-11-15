import { get } from '../api.js';
import { initLayout, postCard, emptyState, escapeHtml, setAlert } from '../site.js';

const alertBox = document.getElementById('page-alert');
const grid = document.getElementById('post-grid');
const pagination = document.getElementById('pagination');
const form = document.getElementById('filter-form');
const categorySelect = document.getElementById('category-filter');
const searchInput = document.getElementById('search-input');

await initLayout('posts');

const params = new URLSearchParams(window.location.search);
const page = Math.max(1, Number(params.get('page') || 1));
const category = params.get('category') || '';
const search = params.get('search') || '';
searchInput.value = search;

try {
  const response = await get('/api/posts/categories', { auth: false });
  categorySelect.innerHTML = '<option value="">Tất cả danh mục</option>' + (response.data || [])
    .map((item) => `<option value="${escapeHtml(item.name)}"${item.name === category ? ' selected' : ''}>${escapeHtml(item.name)} (${Number(item.total || 0)})</option>`)
    .join('');
} catch {
  categorySelect.innerHTML = '<option value="">Tất cả danh mục</option>';
}

function buildPageUrl(targetPage) {
  const next = new URLSearchParams();
  if (category) next.set('category', category);
  if (search) next.set('search', search);
  if (targetPage > 1) next.set('page', String(targetPage));
  const query = next.toString();
  return query ? `?${query}` : window.location.pathname;
}

try {
  const response = await get('/api/posts', {
    auth: false,
    query: { page, per_page: 6, category, search },
  });
  const data = response.data || {};
  const items = data.items || [];

  grid.innerHTML = items.length > 0 ? items.map(postCard).join('') : emptyState('Chưa có bài viết phù hợp.');
  pagination.innerHTML = Number(data.last_page || 1) > 1
    ? `<a href="${buildPageUrl(Math.max(1, page - 1))}">‹ Trước</a>
       <span class="current">${Number(data.page || page)} / ${Number(data.last_page || 1)}</span>
       <a href="${buildPageUrl(Math.min(Number(data.last_page || 1), page + 1))}">Sau ›</a>`
    : '';
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được bài viết.');
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  const next = new URLSearchParams();
  if (categorySelect.value) next.set('category', categorySelect.value);
  if (searchInput.value.trim()) next.set('search', searchInput.value.trim());
  const query = next.toString();
  window.location.search = query ? `?${query}` : '';
});
