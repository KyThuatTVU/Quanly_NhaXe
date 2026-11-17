import { get, assetUrl, siteBase } from './api.js';
import { sameJson } from './live.js';

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

export const SITE_BASE = siteBase();

export const FALLBACK_SITE_NAME = 'Nhà xe';

let currentSettings = {};
let renderedBrand = '';
let layoutActive = '';
let settingsTimer = null;
const settingsListeners = new Set();

export function brandName(settings = currentSettings) {
  const name = String(settings?.company_name ?? '').trim();
  return name || FALLBACK_SITE_NAME;
}

export function brandInitials(name = brandName()) {
  const words = String(name).trim().split(/\s+/).filter(Boolean);
  let initials = '';
  for (const word of words) {
    initials += Array.from(word)[0] || '';
    if (Array.from(initials).length >= 2) break;
  }
  return (initials || Array.from(String(name))[0] || FALLBACK_SITE_NAME).toUpperCase();
}

export function siteName() {
  return brandName();
}

export function currentSiteSettings() {
  return currentSettings;
}

/** Đăng ký nhận thông báo khi admin thay đổi cài đặt (tên nhà xe, hotline...). */
export function onSettingsChange(listener) {
  settingsListeners.add(listener);
  return () => settingsListeners.delete(listener);
}

export function pageUrl(path = '/') {
  return `${SITE_BASE}/${String(path).replace(/^\/+/, '')}`;
}

export function assetPath(path) {
  const clean = String(path).replace(/^\/+/, '');
  const version = window.__ASSET_V ? `?v=${window.__ASSET_V}` : '';
  return `${SITE_BASE}/assets/${clean}${version}`;
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
  return `<div class="media media--empty" aria-hidden="true">${escapeHtml(brandInitials())}</div>`;
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
  const name = escapeHtml(brandName(settings));
  const logo = assetPath('img/logo/logo_chinh_256.png');
  const hotline = settings.hotline || settings.phone || '';
  const phoneHref = String(hotline).replace(/[^\d+]/g, '');
  const links = NAV_ITEMS.map((item) => {
    const className = item.key === active ? 'active' : '';
    return `<a class="${className}" href="${pageUrl(item.path)}">${escapeHtml(item.label)}</a>`;
  }).join('');

  return `<header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="${pageUrl('/home/')}">
        <img class="brand-logo" src="${logo}" alt="${name}" width="42" height="42">
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
  const name = escapeHtml(brandName(settings));
  const logo = assetPath('img/logo/logo_chinh_256.png');
  const address = escapeHtml(settings.address || '');
  const phone = escapeHtml(settings.phone || '');
  const email = escapeHtml(settings.email || '');
  const hours = escapeHtml(settings.working_hours || '');
  const description = escapeHtml(settings.description || '');

  return `<footer class="site-footer">
    <div class="container footer-grid">
      <div>
        <div class="footer-brand">
          <img class="footer-logo" src="${logo}" alt="${name}" width="44" height="44" loading="lazy">
          <h3>${name}</h3>
        </div>
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
        <a href="${SITE_BASE}/admin/login">Quản trị</a>
      </div>
    </div>
    <div class="container footer-bottom">© ${new Date().getFullYear()} ${name}. All rights reserved.</div>
  </footer>`;
}

function applyBrandTitle(settings) {
  const brand = brandName(settings);
  const previousSuffix = `— ${renderedBrand}`;
  let base = document.title.trim();
  if (renderedBrand !== '' && base.endsWith(previousSuffix)) {
    base = base.slice(0, -previousSuffix.length).trim();
  }
  if (base !== '' && !base.endsWith(`— ${brand}`)) {
    document.title = `${base} — ${brand}`;
  }
  renderedBrand = brand;
}

function bindNavToggle() {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');
  if (!toggle || !nav) return;
  toggle.addEventListener('click', () => {
    const opened = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
  });
}

function renderFloatingContact(settings) {
  const phone = String(settings.hotline || settings.phone || '0787972075').trim();
  const phoneHref = phone.replace(/[^\d+]/g, '') || '0787972075';
  const zalo = String(settings.zalo || phone).trim() || '0787972075';
  const zaloHref = zalo.replace(/[^\d+]/g, '') || '0787972075';
  const rawFb = String(settings.facebook || '').trim();
  const facebook = rawFb || 'https://facebook.com/xethaovy';
  const fbHref = facebook.startsWith('http') ? facebook : `https://${facebook}`;

  return `
<!-- Floating Contact Buttons Component -->
<div id="floatingContact" class="fixed left-4 bottom-20 z-50 flex flex-col space-y-8">
    <!-- Zalo Button -->
    <div class="floating-btn zalo-btn group">
        <a href="https://zalo.me/${escapeHtml(zaloHref)}" data-zalo-link target="_blank" rel="noopener" class="relative block">
            <!-- Main Button -->
            <div
                class="w-16 h-16 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-2xl transform transition-all duration-500 group-hover:scale-125 group-hover:shadow-blue-500/50 group-hover:shadow-2xl border-2 border-blue-300/30">
                <!-- Zalo Icon -->
                <div class="relative flex items-center justify-center">
                    <!-- Zalo Text Logo -->
                    <div class="text-white font-bold text-lg tracking-wide drop-shadow-lg">
                        Zalo
                    </div>
                    <!-- Inner glow -->
                    <div
                        class="absolute inset-0 rounded-full bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                </div>
            </div>

            <!-- Enhanced Ripple Effects -->
            <div class="absolute inset-0 rounded-full bg-blue-400 opacity-40 animate-ping"></div>
            <div class="absolute inset-0 rounded-full bg-blue-300 opacity-25 animate-ping animation-delay-700"></div>
            <div class="absolute inset-0 rounded-full bg-blue-500 opacity-15 animate-ping animation-delay-1400"></div>

            <!-- Enhanced Tooltip -->
            <div
                class="absolute left-20 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-gray-800 to-gray-900 text-white px-4 py-3 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none shadow-xl border border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span>Chat Zalo</span>
                </div>
                <div
                    class="absolute right-full top-1/2 transform -translate-y-1/2 border-8 border-transparent border-r-gray-800">
                </div>
            </div>
        </a>
    </div>

    <!-- Phone Button -->
    <div class="floating-btn phone-btn group">
        <a href="tel:${escapeHtml(phoneHref)}" data-phone-link class="relative block">
            <!-- Main Button -->
            <div
                class="w-16 h-16 bg-gradient-to-br from-green-400 via-green-500 to-green-600 rounded-full flex items-center justify-center shadow-2xl transform transition-all duration-500 group-hover:scale-125 group-hover:shadow-green-500/50 group-hover:shadow-2xl border-2 border-green-300/30">
                <!-- Phone Icon -->
                <div class="relative">
                    <svg class="w-9 h-9 text-white drop-shadow-lg transform group-hover:rotate-12 group-hover:scale-110 transition-all duration-500"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    <!-- Inner glow -->
                    <div
                        class="absolute inset-0 rounded-full bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                </div>
            </div>

            <!-- Enhanced Ripple Effects -->
            <div class="absolute inset-0 rounded-full bg-green-400 opacity-40 animate-ping"></div>
            <div class="absolute inset-0 rounded-full bg-green-300 opacity-25 animate-ping animation-delay-600"></div>
            <div class="absolute inset-0 rounded-full bg-green-500 opacity-15 animate-ping animation-delay-1200"></div>

            <!-- Enhanced Tooltip -->
            <div
                class="absolute left-20 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-gray-800 to-gray-900 text-white px-4 py-3 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none shadow-xl border border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span>Gọi: <span data-phone>${escapeHtml(phone)}</span></span>
                </div>
                <div
                    class="absolute right-full top-1/2 transform -translate-y-1/2 border-8 border-transparent border-r-gray-800">
                </div>
            </div>
        </a>
    </div>

    <!-- Facebook Button -->
    <div class="floating-btn facebook-btn group">
        <a href="${escapeHtml(fbHref)}" data-facebook-link target="_blank" rel="noopener" class="relative block">
            <!-- Main Button -->
            <div
                class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-2xl transform transition-all duration-500 group-hover:scale-125 group-hover:shadow-blue-600/50 group-hover:shadow-2xl border-2 border-blue-400/30">
                <!-- Facebook Icon -->
                <div class="relative">
                    <svg class="w-9 h-9 text-white drop-shadow-lg transform group-hover:scale-110 transition-all duration-500"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <!-- Inner glow -->
                    <div
                        class="absolute inset-0 rounded-full bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>
                </div>
            </div>

            <!-- Enhanced Ripple Effects -->
            <div class="absolute inset-0 rounded-full bg-blue-400 opacity-40 animate-ping"></div>
            <div class="absolute inset-0 rounded-full bg-blue-300 opacity-25 animate-ping animation-delay-700"></div>
            <div class="absolute inset-0 rounded-full bg-blue-500 opacity-15 animate-ping animation-delay-1400"></div>

            <!-- Enhanced Tooltip -->
            <div
                class="absolute left-20 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-gray-800 to-gray-900 text-white px-4 py-3 rounded-xl text-sm font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none shadow-xl border border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span>Facebook</span>
                </div>
                <div
                    class="absolute right-full top-1/2 transform -translate-y-1/2 border-8 border-transparent border-r-gray-800">
                </div>
            </div>
        </a>
    </div>
</div>`;
}

function initFloatingContactEvents() {
  const floatingContact = document.getElementById('floatingContact');
  if (!floatingContact || floatingContact.__bound) return;
  floatingContact.__bound = true;

  // Luôn cố định trên màn hình, không bị ẩn khi cuộn trang
  floatingContact.style.transform = 'none';
  floatingContact.style.opacity = '1';

  const buttons = floatingContact.querySelectorAll('a');
  buttons.forEach((button) => {
    button.addEventListener('click', function () {
      const btn = this.querySelector('.w-16, .w-14');
      if (btn) {
        btn.style.transform = 'scale(0.95)';
        setTimeout(() => {
          btn.style.transform = '';
        }, 150);
      }
      const badge = this.querySelector('.notification-badge');
      if (badge) badge.remove();
    });
    button.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
    });
  });

  window.toggleFloatingContact = function (show = true) {
    const el = document.getElementById('floatingContact');
    if (el) el.style.display = show ? 'flex' : 'none';
  };

  window.addContactNotification = function (type, count) {
    const el = document.getElementById('floatingContact');
    const button = el?.querySelector(`.${type}-btn`);
    if (button) {
      const existingBadge = button.querySelector('.notification-badge');
      if (existingBadge) existingBadge.remove();
      if (count > 0) {
        const badge = document.createElement('div');
        badge.className = 'notification-badge';
        badge.textContent = count > 9 ? '9+' : count;
        button.appendChild(badge);
      }
    }
  };
}

function applySettings(settings, active) {
  const previous = currentSettings;
  currentSettings = settings;
  applyBrandTitle(settings);

  const header = document.getElementById('site-header');
  if (header) header.innerHTML = renderHeader(settings, active);

  const footer = document.getElementById('site-footer');
  if (footer) footer.innerHTML = renderFooter(settings);

  const floatingHtml = renderFloatingContact(settings);
  const existingFloating = document.getElementById('floatingContact');
  if (existingFloating) {
    existingFloating.outerHTML = floatingHtml;
  } else {
    document.body.insertAdjacentHTML('beforeend', floatingHtml);
  }
  initFloatingContactEvents();

  bindNavToggle();

  if (!sameJson(previous, settings)) {
    settingsListeners.forEach((listener) => {
      try {
        listener(settings);
      } catch {
        // Một listener lỗi không được làm hỏng phần còn lại của trang.
      }
    });
  }
}

async function refreshSettings() {
  try {
    const settings = await loadSettings(true);
    if (!sameJson(settings, currentSettings)) {
      applySettings(settings, layoutActive);
    }
  } catch {
    // Mất mạng tạm thời: giữ cài đặt đang hiển thị và thử lại ở chu kỳ sau.
  }
}

function watchSettings() {
  if (settingsTimer !== null) return;
  settingsTimer = window.setInterval(() => {
    if (!document.hidden) refreshSettings();
  }, 45000);
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) refreshSettings();
  });
}

export async function initLayout(active = '') {
  layoutActive = active;
  let settings = {};
  try {
    settings = await loadSettings();
  } catch {
    settings = {};
  }

  applySettings(settings, active);
  watchSettings();

  return settings;
}
