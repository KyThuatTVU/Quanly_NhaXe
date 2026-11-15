import { get, assetUrl, API_BASE } from './api.js';

const NAV_ITEMS = [
  { key: 'home', label: 'Trang chủ', path: '/home/' },
  { key: 'about', label: 'Giới thiệu', path: '/about/' },
  { key: 'cars', label: 'Xe', path: '/cars/' },
  { key: 'services', label: 'Dịch vụ', path: '/services/' },
  { key: 'posts', label: 'Bài viết', path: '/posts/' },
  { key: 'lookup', label: 'Tra cứu đơn', path: '/booking/lookup/' },
  { key: 'contact', label: 'Liên hệ', path: '/contact/' },
];

const STATUS_LABELS = {
  pending: ['Chờ xác nhận', 'warning'],
  confirmed: ['Đã xác nhận', 'info'],
  completed: ['Hoàn thành', 'success'],
  cancelled: ['Đã huỷ', 'neutral'],
};

function frontendRoot() {
  const marker = window.location.pathname.indexOf('/frontend/');
  if (marker >= 0) {
    return window.location.origin + window.location.pathname.slice(0, marker) + '/frontend';
  }
  return window.location.origin;
}

export const FRONTEND_ROOT = frontendRoot();

export function pageUrl(path = '/') {
  return `${FRONTEND_ROOT}/pages${path.startsWith('/') ? path : `/${path}`}`;
}

export function assetPath(path) {
  return `${FRONTEND_ROOT}/assets/${String(path).replace(/^\/+/, '')}`;
}

const ESCAPE_MAP = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

export function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, (char) => ESCAPE_MAP[char]);
}

export function money(value) {
  const number = Number(value);
  if (!Number.isFinite(number)) return '—';
  return `${number.toLocaleString('vi-VN')} ₫`;
}

export function formatDate(value) {
  const text = String(value ?? '').slice(0, 10);
  const parts = text.split('-');
  return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : '';
}

export function formatTime(value) {
  return String(value ?? '').slice(0, 5);
}

export function statusLabel(status) {
  return STATUS_LABELS[status] ? STATUS_LABELS[status][0] : String(status ?? '');
}

export function statusBadge(status) {
  const [label, tone] = STATUS_LABELS[status] || [String(status ?? ''), 'neutral'];
  return `<span class="badge badge--${tone}">${escapeHtml(label)}</span>`;
}

export function stars(rating) {
  const value = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
  return '★'.repeat(value) + '☆'.repeat(5 - value);
}

export function mediaHtml(source, alt = '') {
  if (source) {
    return `<div class="media"><img src="${escapeHtml(assetUrl(source))}" alt="${escapeHtml(alt)}" loading="lazy"></div>`;
  }
  return '<div class="media media--empty" aria-hidden="true">THÁO VY</div>';
}

export function vehicleCard(vehicle) {
  return `<article class="card">
    ${mediaHtml(vehicle.image, vehicle.name)}
    <div class="card-body">
      <div class="card-top">
        <h3>${escapeHtml(vehicle.name)}</h3>
        <span class="badge badge--info">${escapeHtml(vehicle.type_name || vehicle.type || '')}</span>
      </div>
      <p class="muted">${escapeHtml(vehicle.description || '')}</p>
      <p class="card-meta">${vehicle.type_seats ? `${Number(vehicle.type_seats)} chỗ · ` : ''}<span class="stars">${stars(vehicle.rating)}</span></p>
      <div class="card-foot">
        <span><span class="price">${money(vehicle.price_per_day)}</span> <span class="muted">/ngày</span></span>
        <a class="btn btn-outline btn-sm" href="${pageUrl('/cars/detail/')}?id=${encodeURIComponent(vehicle.vehicle_id)}">Chi tiết</a>
      </div>
    </div>
  </article>`;
}

export function serviceCard(service) {
  return `<article class="card">
    ${mediaHtml(service.image, service.service_name)}
    <div class="card-body">
      <h3>${escapeHtml(service.service_name)}</h3>
      <p class="muted">${escapeHtml(service.description || '')}</p>
      <div class="card-foot">
        <span><span class="price">${money(service.price)}</span> <span class="muted">từ</span></span>
        <a class="btn btn-outline btn-sm" href="${pageUrl('/services/detail/')}?id=${encodeURIComponent(service.service_id)}">Xem thêm</a>
      </div>
    </div>
  </article>`;
}

export function postCard(post) {
  return `<article class="card">
    ${mediaHtml(post.image, post.title)}
    <div class="card-body">
      <p class="card-meta">${escapeHtml(post.category || '')} · ${formatDate(post.created_at)}</p>
      <h3>${escapeHtml(post.title)}</h3>
      <div class="card-foot">
        <a class="btn btn-outline btn-sm" href="${pageUrl('/posts/detail/')}?slug=${encodeURIComponent(post.slug)}">Đọc bài</a>
      </div>
    </div>
  </article>`;
}

export function typeCard(type) {
  return `<article class="card type-card">
    <div class="card-body">
      <h3>${escapeHtml(type.name)}</h3>
      <p class="type-seats">${Number(type.seats || 0)} chỗ</p>
      <p class="muted">${escapeHtml(type.description || '')}</p>
      <p><span class="price">${money(type.price_per_day)}</span> <span class="muted">/ngày</span></p>
      <a class="btn btn-outline btn-sm" href="${pageUrl('/cars/')}?vehicle_type_id=${encodeURIComponent(type.id)}">Xem xe</a>
    </div>
  </article>`;
}

export function emptyState(text) {
  return `<div class="empty">${escapeHtml(text)}</div>`;
}

export function setAlert(container, message, type = 'error') {
  if (!container) return;
  container.innerHTML = message ? `<div class="alert alert--${type}">${escapeHtml(message)}</div>` : '';
}

export function queryParam(name) {
  return new URLSearchParams(window.location.search).get(name) || '';
}

export function sanitizeHtml(html) {
  const template = document.createElement('template');
  template.innerHTML = String(html ?? '');
  template.content.querySelectorAll('script, style, iframe, object, embed, link, meta').forEach((node) => node.remove());
  template.content.querySelectorAll('*').forEach((element) => {
    [...element.attributes].forEach((attribute) => {
      const name = attribute.name.toLowerCase();
      const value = attribute.value.trim().toLowerCase();
      if (name.startsWith('on')) {
        element.removeAttribute(attribute.name);
      } else if ((name === 'href' || name === 'src') && value.startsWith('javascript:')) {
        element.removeAttribute(attribute.name);
      }
    });
  });
  return template.innerHTML;
}

let settingsPromise = null;

export function loadSettings(force = false) {
  if (force || !settingsPromise) {
    settingsPromise = get('/api/settings', { auth: false })
      .then((result) => result.data || {})
      .catch((error) => {
        settingsPromise = null;
        throw error;
      });
  }
  return settingsPromise;
}

function renderHeader(settings, active) {
  const name = escapeHtml(settings.company_name || 'Xe Hợp Đồng Tháo Vy');
  const hotline = settings.hotline || settings.phone || '';
  const phoneHref = String(hotline).replace(/[^\d+]/g, '');
  const links = NAV_ITEMS.map((item) => {
    const className = item.key === active ? 'active' : '';
    return `<a class="${className}" href="${pageUrl(item.path)}">${escapeHtml(item.label)}</a>`;
  }).join('');

  return `<header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="${pageUrl('/home/')}">
        <span class="brand-mark">TV</span>
        <span>
          <strong>${name}</strong>
          <small>Thuê xe hợp đồng 4 - 45 chỗ</small>
        </span>
      </a>
      <button class="nav-toggle" type="button" aria-label="Mở menu" aria-expanded="false">☰</button>
      <nav class="site-nav">
        ${links}
        <a class="btn btn-primary btn-sm" href="${pageUrl('/booking/')}">Đặt xe</a>
      </nav>
      ${hotline ? `<a class="header-hotline" href="tel:${escapeHtml(phoneHref)}"><small>Hotline</small><strong>${escapeHtml(hotline)}</strong></a>` : ''}
    </div>
  </header>`;
}

function renderFooter(settings) {
  const name = escapeHtml(settings.company_name || 'Xe Hợp Đồng Tháo Vy');
  const address = escapeHtml(settings.address || '');
  const phone = escapeHtml(settings.phone || '');
  const email = escapeHtml(settings.email || '');
  const hours = escapeHtml(settings.working_hours || '');
  const description = escapeHtml(settings.description || '');

  return `<footer class="site-footer">
    <div class="container footer-grid">
      <div>
        <h3>${name}</h3>
        <p>${description}</p>
        ${hours ? `<p class="muted">${hours}</p>` : ''}
      </div>
      <div>
        <h4>Liên hệ</h4>
        ${address ? `<p>${address}</p>` : ''}
        ${phone ? `<p>Điện thoại: ${phone}</p>` : ''}
        ${email ? `<p>Email: ${email}</p>` : ''}
      </div>
      <div>
        <h4>Liên kết</h4>
        <a href="${pageUrl('/booking/')}">Đặt xe</a>
        <a href="${pageUrl('/booking/lookup/')}">Tra cứu đơn</a>
        <a href="${pageUrl('/reviews/')}">Gửi đánh giá</a>
        <a href="${API_BASE}/admin/login">Quản trị</a>
      </div>
    </div>
    <div class="container footer-bottom">© ${new Date().getFullYear()} ${name}. All rights reserved.</div>
  </footer>`;
}

export async function initLayout(active = '') {
  let settings = {};
  try {
    settings = await loadSettings();
  } catch {
    settings = {};
  }

  const header = document.getElementById('site-header');
  if (header) header.innerHTML = renderHeader(settings, active);

  const footer = document.getElementById('site-footer');
  if (footer) footer.innerHTML = renderFooter(settings);

  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const opened = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
    });
  }

  return settings;
}
