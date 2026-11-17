(function () {
  'use strict';

  const CONTENT_SELECTOR = '.admin-content';
  const REFRESH_INTERVAL = 25000;
  const SKIP_REFRESH = /\/(create|edit)$|\/admin\/settings$|\/admin\/profile$/;

  function getContent() {
    return document.querySelector(CONTENT_SELECTOR);
  }

  function normalizePath(value) {
    return value.replace(/\/+$/, '') || '/';
  }

  function flashError(message) {
    const target = getContent();
    if (!target) return;
    const box = document.createElement('div');
    box.className = 'alert alert-danger';
    box.textContent = message;
    target.prepend(box);
  }

  function updateActiveNav() {
    const path = normalizePath(window.location.pathname);
    const links = Array.from(document.querySelectorAll('.admin-nav-link'));
    if (links.length === 0) return;

    let adminRoot = '';
    try {
      adminRoot = normalizePath(new URL(links[0].href, window.location.href).pathname);
    } catch {
      return;
    }

    let best = null;
    let bestLength = -1;

    links.forEach((link) => {
      let linkPath;
      try {
        linkPath = normalizePath(new URL(link.href, window.location.href).pathname);
      } catch {
        return;
      }

      const matched = linkPath === adminRoot
        ? path === adminRoot
        : path === linkPath || path.startsWith(linkPath + '/');

      if (matched && linkPath.length > bestLength) {
        best = link;
        bestLength = linkPath.length;
      }
    });

    links.forEach((link) => link.classList.toggle('active', link === best));
  }

  function syncContent(html, finalUrl) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const incoming = doc.querySelector(CONTENT_SELECTOR);
    const current = getContent();
    if (!incoming || !current) return false;

    if (current.innerHTML !== incoming.innerHTML) {
      current.innerHTML = incoming.innerHTML;
    }
    if (doc.title) {
      document.title = doc.title;
    }
    if (finalUrl) {
      const href = new URL(finalUrl, window.location.href).href;
      if (href !== window.location.href) {
        window.history.replaceState(null, '', href);
      }
    }
    updateActiveNav();
    return true;
  }

  function isLoginUrl(url) {
    try {
      return new URL(url, window.location.href).pathname.endsWith('/admin/login');
    } catch {
      return false;
    }
  }

  async function softNavigate(url, options) {
    const init = {
      method: (options && options.method) || 'GET',
      credentials: 'same-origin',
      headers: {
        Accept: 'text/html',
        'X-Requested-With': 'XMLHttpRequest',
      },
      redirect: 'follow',
    };
    if (options && options.body) {
      init.body = options.body;
    }

    const response = await fetch(url, init);
    const finalUrl = response.url || url;
    const contentType = response.headers.get('content-type') || '';

    if (isLoginUrl(finalUrl) || contentType.indexOf('text/html') === -1) {
      window.location.href = finalUrl;
      return false;
    }

    const html = await response.text();
    if (!syncContent(html, finalUrl)) {
      window.location.href = finalUrl;
      return false;
    }
    return true;
  }

  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (event.defaultPrevented) return;
    if (!form.closest('.admin-shell')) return;
    if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post') return;
    if (form.hasAttribute('data-no-async')) return;

    let action;
    try {
      action = new URL(form.action || window.location.href, window.location.href);
    } catch {
      return;
    }
    if (action.pathname.endsWith('/admin/logout')) return;

    event.preventDefault();
    if (form.dataset.busy === '1') return;

    form.dataset.busy = '1';
    form.classList.add('is-saving');

    const button = form.querySelector('button[type="submit"], button:not([type])');
    const original = button ? button.textContent : '';
    if (button) {
      button.disabled = true;
      button.textContent = 'Đang lưu...';
    }

    softNavigate(action.href, { method: 'POST', body: new FormData(form) })
      .catch(() => {
        flashError('Không thể kết nối máy chủ. Vui lòng thử lại.');
      })
      .finally(() => {
        if (button && button.isConnected) {
          button.disabled = false;
          button.textContent = original;
        }
        form.classList.remove('is-saving');
        delete form.dataset.busy;
      });
  });

  function isFormPage() {
    return SKIP_REFRESH.test(normalizePath(window.location.pathname));
  }

  function isEditing() {
    const element = document.activeElement;
    if (!element) return false;
    const tag = element.tagName;
    return tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA' || element.isContentEditable;
  }

  let refreshing = false;

  async function autoRefresh() {
    if (document.hidden || refreshing || isFormPage() || isEditing()) return;
    if (document.querySelector('form[data-busy="1"]')) return;
    if (!getContent()) return;

    refreshing = true;
    try {
      await softNavigate(window.location.href);
    } catch {
      // Bỏ qua lần này, chu kỳ sau sẽ thử lại.
    } finally {
      refreshing = false;
    }
  }

  function mountIndicator() {
    // Chỉ gắn indicator trên màn hình rộng để tránh chật chội trên mobile
    if (window.innerWidth <= 640) return;
    const target = document.querySelector('.admin-topbar-left > div');
    if (!target || target.querySelector('.live-indicator')) return;

    const indicator = document.createElement('span');
    indicator.className = 'live-indicator';
    indicator.title = 'Trang tự cập nhật dữ liệu mới, không cần tải lại';
    indicator.innerHTML = '<i></i><span>Tự động cập nhật</span>';
    target.appendChild(indicator);
  }

  // Khởi tạo Drawer Mobile Menu & Bottom Dock
  function initMobileMenu() {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');
    const backdrop = document.getElementById('sidebarBackdrop');
    const moreBtn = document.getElementById('dockMoreBtn');

    function openSidebar() {
      document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
      document.body.classList.remove('sidebar-open');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (moreBtn) moreBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // Tự động đóng drawer khi nhấp vào bất kỳ link nào
    document.querySelectorAll('.admin-nav-link').forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 900) {
          closeSidebar();
        }
      });
    });
  }

  initMobileMenu();
  mountIndicator();
  window.setInterval(autoRefresh, REFRESH_INTERVAL);
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) autoRefresh();
  });
})();
