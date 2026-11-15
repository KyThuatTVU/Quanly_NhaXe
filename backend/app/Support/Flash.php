<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Session;

/**
 * Thông báo một lần giữa hai request: put ở request hiện tại, rotate ở request kế tiếp.
 */
final class Flash
{
    public static function put(string $key, mixed $value): void
    {
        $next = Session::get('_flash_next', []);
        $next = is_array($next) ? $next : [];
        $next[$key] = $value;
        Session::set('_flash_next', $next);
    }

    public static function rotate(): void
    {
        $next = Session::get('_flash_next', []);
        if (is_array($next) && $next !== []) {
            $current = Session::get('_flash', []);
            Session::set('_flash', array_merge(is_array($current) ? $current : [], $next));
            Session::forget('_flash_next');
        }
    }

    public static function pull(string $key, mixed $default = null): mixed
    {
        $all = Session::get('_flash', []);
        if (!is_array($all) || !array_key_exists($key, $all)) {
            return $default;
        }
        $value = $all[$key];
        unset($all[$key]);
        Session::set('_flash', $all);
        return $value;
    }
}
