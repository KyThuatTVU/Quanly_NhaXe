<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Session;
use App\Support\Flash;

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(): string
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    if ($host !== '' && preg_match('/^[A-Za-z0-9.\-]+(?::\d+)?$/', $host) === 1) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
        if ($dir === '.' || $dir === '/') {
            $dir = '';
        }
        return $scheme . '://' . $host . $dir;
    }
    return rtrim((string) Config::get('app.url', ''), '/');
}

function url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }
    $base = rtrim(base_url(), '/');
    $path = trim($path, '/');
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = dirname(__DIR__, 3) . '/frontend/assets/' . $path;
    // Gắn phiên bản theo thời điểm sửa file để trình duyệt tải bản mới ngay
    $version = is_file($file) ? '?v=' . filemtime($file) : '';

    return url('assets/' . $path) . $version;
}

function media(string $path): string
{
    return url(ltrim($path, '/'));
}

function brand_initials(?string $name, string $fallback = 'NX'): string
{
    $name = trim((string) $name);
    if ($name === '') {
        return $fallback;
    }
    $initials = '';
    foreach (preg_split('/\s+/u', $name) ?: [] as $word) {
        if ($word === '') {
            continue;
        }
        $initials .= mb_substr($word, 0, 1);
        if (mb_strlen($initials) >= 2) {
            break;
        }
    }
    return mb_strtoupper($initials !== '' ? $initials : mb_substr($name, 0, 2));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Session::csrfToken()) . '">';
}

function old(string $key, mixed $default = null): mixed
{
    static $old = null;
    if ($old === null) {
        $old = (array) Flash::pull('old', []);
    }
    return array_key_exists($key, $old) ? $old[$key] : $default;
}

/**
 * @return array<string, string>
 */
function errors(): array
{
    static $errors = null;
    if ($errors === null) {
        $errors = (array) Flash::pull('errors', []);
    }
    return $errors;
}

function error_for(string $field): ?string
{
    $all = errors();
    return isset($all[$field]) ? (string) $all[$field] : null;
}

function flash_message(string $key): ?string
{
    $value = Flash::pull($key);
    return is_string($value) && $value !== '' ? $value : null;
}
