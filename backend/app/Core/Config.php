<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Đọc .env + config/*.php, truy cập bằng dot notation: Config::get('database.host').
 */
final class Config
{
    private static array $items = [];
    private static array $env = [];

    public static function bootstrap(string $basePath): void
    {
        self::loadEnvFile($basePath . '/.env');
        foreach (glob($basePath . '/config/*.php') ?: [] as $file) {
            $key = basename($file, '.php');
            $value = require $file;
            if (is_array($value)) {
                self::$items[$key] = $value;
            }
        }
        date_default_timezone_set((string) self::get('app.timezone', 'Asia/Ho_Chi_Minh'));
    }

    private static function loadEnvFile(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'")) {
                $value = substr($value, 1, -1);
            }
            self::$env[$key] = $value;
        }
    }

    public static function env(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, self::$env)) {
            return self::$env[$key];
        }
        $value = getenv($key);
        return $value === false ? $default : $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}
