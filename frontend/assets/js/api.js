/**
 * Client gọi REST API của backend (PHP mini-framework).
 * Dùng ES module: import { request, ApiError } from '../assets/js/api.js';
 */

const DEFAULT_API_BASE = 'http://127.0.0.1/DuAn_QuanLyNhaXe/backend/public';
const AUTH_STORAGE_KEY = 'thaovy.auth';
const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];
const ROUTE_ROOTS = ['home', 'about', 'cars', 'services', 'posts', 'booking', 'contact', 'reviews'];

/** Thư mục gốc website, ví dụ '' hoặc '/DuAn_QuanLyNhaXe'. */
export function siteBase() {
  if (typeof window === 'undefined') return '';
  const path = window.location.pathname;
  const legacy = path.indexOf('/frontend/');
  if (legacy >= 0) return path.slice(0, legacy);
  const segments = path.split('/').filter(Boolean);
  const index = segments.findIndex((segment) => ROUTE_ROOTS.includes(segment));
  return index > 0 ? `/${segments.slice(0, index).join('/')}` : '';
}

function resolveApiBase() {
  if (typeof window !== 'undefined' && typeof window.API_BASE_URL === 'string' && window.API_BASE_URL.trim() !== '') {
    return window.API_BASE_URL.trim().replace(/\/+$/, '');
  }
  if (typeof window !== 'undefined' && /^https?:$/.test(window.location.protocol)) {
    return window.location.origin + siteBase();
  }
  return DEFAULT_API_BASE;
}

export const API_BASE = resolveApiBase();

let csrfToken = null;

export class ApiError extends Error {
  constructor(message, status = 0, errors = {}, payload = null) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.errors = errors && typeof errors === 'object' ? errors : {};
    this.payload = payload;
  }

  get isNetwork() {
    return this.status === 0;
  }

  get isUnauthorized() {
    return this.status === 401;
  }

  get isForbidden() {
    return this.status === 403;
  }

  get isValidation() {
    return this.status === 422;
  }

  get isConflict() {
    return this.status === 409;
  }

  get isNotFound() {
    return this.status === 404;
  }

  firstError() {
    const keys = Object.keys(this.errors);
    return keys.length > 0 ? this.errors[keys[0]] : this.message;
  }
}

export function getStoredAuth() {
  try {
    const raw = localStorage.getItem(AUTH_STORAGE_KEY);
    if (!raw) return null;
    const auth = JSON.parse(raw);
    return auth && typeof auth.token === 'string' && auth.token !== '' ? auth : null;
  } catch {
    return null;
  }
}

export function setStoredAuth(auth) {
  localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(auth));
}

export function clearStoredAuth() {
  localStorage.removeItem(AUTH_STORAGE_KEY);
  csrfToken = null;
}

export function getToken() {
  const auth = getStoredAuth();
  return auth ? auth.token : null;
}

function buildQuery(query) {
  if (!query) return '';
  const params = new URLSearchParams();
  Object.entries(query).forEach(([key, value]) => {
    if (value === undefined || value === null || value === '') return;
    if (Array.isArray(value)) {
      value.forEach((item) => params.append(key, String(item)));
    } else {
      params.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : String(value));
    }
  });
  const text = params.toString();
  return text === '' ? '' : `?${text}`;
}

async function parseBody(response) {
  if (response.status === 204) return null;
  const text = await response.text();
  if (text === '') return null;
  try {
    return JSON.parse(text);
  } catch {
    return text;
  }
}

async function fetchCsrf() {
  let response;
  try {
    response = await fetch(`${API_BASE}/api/auth/csrf`, {
      headers: { Accept: 'application/json' },
      credentials: 'include',
    });
  } catch {
    throw new ApiError('Không thể kết nối máy chủ. Vui lòng kiểm tra Apache/XAMPP.', 0);
  }
  const payload = await parseBody(response);
  const token = payload && payload.data ? payload.data.csrf_token : null;
  if (!response.ok || !token) {
    throw new ApiError('Không lấy được CSRF token', response.status, {}, payload);
  }
  csrfToken = token;
  return csrfToken;
}

export async function getCsrfToken(force = false) {
  if (force || csrfToken === null) {
    return fetchCsrf();
  }
  return csrfToken;
}

/**
 * Gọi API. Trả về { success, message, data }.
 * @param {'GET'|'POST'|'PUT'|'PATCH'|'DELETE'} method
 * @param {string} path
 * @param {{query?: object, body?: any, formData?: FormData, headers?: object, auth?: boolean, signal?: AbortSignal}} [options]
 */
export async function request(method, path, options = {}) {
  const upperMethod = String(method).toUpperCase();
  const url = API_BASE + (path.startsWith('/') ? path : `/${path}`) + buildQuery(options.query);
  const token = getToken();
  const headers = { Accept: 'application/json', ...(options.headers || {}) };
  const unsafe = !SAFE_METHODS.includes(upperMethod);

  let body;
  if (options.formData) {
    body = options.formData;
  } else if (options.body !== undefined) {
    body = JSON.stringify(options.body);
    headers['Content-Type'] = 'application/json';
  }

  const useAuth = options.auth !== false;
  if (useAuth && token) {
    headers.Authorization = `Bearer ${token}`;
  } else if (unsafe) {
    headers['X-CSRF-Token'] = await getCsrfToken();
  }

  const send = () => fetch(url, {
    method: upperMethod,
    headers,
    body,
    credentials: 'include',
    signal: options.signal,
  });

  let response;
  try {
    response = await send();
  } catch (error) {
    if (error && error.name === 'AbortError') throw error;
    throw new ApiError('Không thể kết nối máy chủ. Vui lòng kiểm tra Apache/XAMPP.', 0);
  }

  let payload = await parseBody(response);

  const csrfFailed = response.status === 403
    && unsafe
    && !(useAuth && token)
    && typeof payload === 'object'
    && payload !== null
    && String(payload.message || '').includes('CSRF');

  if (csrfFailed) {
    headers['X-CSRF-Token'] = await getCsrfToken(true);
    try {
      response = await send();
    } catch {
      throw new ApiError('Không thể kết nối máy chủ. Vui lòng kiểm tra Apache/XAMPP.', 0);
    }
    payload = await parseBody(response);
  }

  if (response.status === 401 && useAuth && token) {
    clearStoredAuth();
  }

  if (!response.ok || (payload !== null && typeof payload === 'object' && payload.success === false)) {
    const message = payload !== null && typeof payload === 'object' && payload.message
      ? String(payload.message)
      : `Yêu cầu thất bại (HTTP ${response.status})`;
    const errors = payload !== null && typeof payload === 'object' && payload.errors ? payload.errors : {};
    throw new ApiError(message, response.status, errors, payload);
  }

  if (payload !== null && typeof payload === 'object' && 'success' in payload) {
    return {
      success: payload.success !== false,
      message: typeof payload.message === 'string' ? payload.message : '',
      data: payload.data !== undefined ? payload.data : null,
    };
  }

  return { success: true, message: '', data: payload };
}

export const get = (path, options = {}) => request('GET', path, options);
export const post = (path, body, options = {}) => request('POST', path, { ...options, body });
export const put = (path, body, options = {}) => request('PUT', path, { ...options, body });
export const patch = (path, body, options = {}) => request('PATCH', path, { ...options, body });
export const del = (path, options = {}) => request('DELETE', path, options);

export function assetUrl(path) {
  if (!path) return '';
  if (/^https?:\/\//i.test(path) || path.startsWith('//') || path.startsWith('data:')) {
    return path;
  }
  return `${siteBase()}/${String(path).replace(/^\/+/, '')}`;
}
