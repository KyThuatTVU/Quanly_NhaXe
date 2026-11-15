<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Session;
use App\Support\Flash;

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }
    $base = rtrim((string) Config::get('app.url', ''), '/');
    $path = trim($path, '/');
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function media(string $path): string
{
    return url(ltrim($path, '/'));
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
