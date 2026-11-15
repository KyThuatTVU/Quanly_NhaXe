import { get } from '../api.js';
import { initLayout, postCard, emptyState, escapeHtml, setAlert } from '../site.js';
import { liveRefresh, sameJson } from '../live.js';

const alertBox = document.getElementById('page-alert');
const grid = document.getElementById('post-grid');
const pagination = document.getElementById('pagination');
const form = document.getElementById('filter-form');
const categorySelect = document.getElementById('category-filter');
const searchInput = document.getElementById('search-input');

await initLayout('posts');

const params = new URLSearchParams(window.location.search);
const state = {
  page: Math.max(1, Number(params.get('page') || 1)),
  category: params.get('category') || '',
  search: params.get('search') || '',
};
searchInput.value = state.search;

let categories = [];
let lastSignature = '';

function buildQuery() {
  const next = new URLSearchParams();
  if (state.category) next.set('category', state.category);
  if (state.search) next.set('search', state.search);
  if (state.page > 1) next.set('page', String(state.page));
  return next.toString();
}

function syncUrl() {
  const query = buildQuery();
  const target = query ? `?${query}` : window.location.pathname;
  if (window.location.pathname + window.location.search !== target) {
    history.replaceState(null, '', target);
  }
}

function pageUrlFor(targetPage) {
  const next = new URLSearchParams();
  if (state.category) next.set('category', state.category);
  if (state.search) next.set('search', state.search);
  if (targetPage > 1) next.set('page', String(targetPage));
  const query = next.toString();
  return query ? `?${query}` : window.location.pathname;
}

function renderCategories() {
  const current = state.category;
  categorySelect.innerHTML = '<option value="">Tất cả danh mục</option>' + categories
    .map((item) => `<option value="${escapeHtml(item.name)}"${item.name === current ? ' selected' : ''}>${escapeHtml(item.name)} (${Number(item.total || 0)})</option>`)
    .join('');
}

async function loadCategories() {
  const response = await get('/api/posts/categories', { auth: false });
  const items = (response.data || []).map((item) => ({ name: item.name, total: Number(item.total || 0) }));
  if (sameJson(items, categories)) return;
  categories = items;
  renderCategories();
}

try {
  await loadCategories();
} catch {
  categorySelect.innerHTML = '<option value="">Tất cả danh mục</option>';
}

async function loadPosts() {
  const response = await get('/api/posts', {
    auth: false,
    query: {
      page: state.page,
      per_page: 6,
      category: state.category,
      search: state.search,
    },
  });
  const data = response.data || {};
  const items = data.items || [];
  const page = Number(data.page || state.page);
  const lastPage = Math.max(1, Number(data.last_page || 1));
  const model = { items, page, lastPage };

  if (!sameJson(model, lastSignature)) {
    lastSignature = model;
    grid.innerHTML = items.length > 0 ? items.map(postCard).join('') : emptyState('Chưa có bài viết phù hợp.');
    pagination.innerHTML = lastPage > 1
      ? `<a href="${pageUrlFor(Math.max(1, page - 1))}" data-page="${Math.max(1, page - 1)}">‹ Trước</a>
         <span class="current">${page} / ${lastPage}</span>
         <a href="${pageUrlFor(Math.min(lastPage, page + 1))}" data-page="${Math.min(lastPage, page + 1)}">Sau ›</a>`
      : '';
  }

  syncUrl();
}

try {
  await loadPosts();
} catch (error) {
  setAlert(alertBox, error.message || 'Không tải được bài viết.');
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  state.category = categorySelect.value;
  state.search = searchInput.value.trim();
  state.page = 1;
  lastSignature = '';
  setAlert(alertBox, '');
  loadPosts();
});

pagination.addEventListener('click', (event) => {
  const link = event.target.closest('a[data-page]');
  if (!link) return;
  event.preventDefault();
  const targetPage = Number(link.dataset.page || 1);
  if (targetPage === state.page) return;
  state.page = targetPage;
  setAlert(alertBox, '');
  loadPosts();
});

liveRefresh(loadPosts, { interval: 30, immediate: false });
liveRefresh(loadCategories, { interval: 60, immediate: false });
