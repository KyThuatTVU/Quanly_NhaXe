<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\Config;
use App\Core\Exceptions\NotFoundException;
use App\Core\Response;

final class AssetController
{
    private const MIME = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'mjs' => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'map' => 'application/json; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'eot' => 'application/vnd.ms-fontobject',
    ];

    public function single(string $file): Response
    {
        return $this->serve($file);
    }

    public function nested(string $dir, string $file): Response
    {
        return $this->serve($dir . '/' . $file);
    }

    public function deep(string $dir, string $sub, string $file): Response
    {
        return $this->serve($dir . '/' . $sub . '/' . $file);
    }

    private function serve(string $relative): Response
    {
        if (preg_match('#[\\\\\x00]#', $relative) === 1 || str_contains($relative, '..')) {
            throw new NotFoundException('Không tìm thấy tài nguyên');
        }
        $root = realpath(rtrim((string) Config::get('app.base_path'), '/\\') . '/../frontend/assets');
        if ($root === false) {
            throw new NotFoundException('Không tìm thấy tài nguyên');
        }
        $path = realpath($root . '/' . ltrim($relative, '/'));
        if ($path === false || !is_file($path) || !str_starts_with($path, $root . DIRECTORY_SEPARATOR)) {
            throw new NotFoundException('Không tìm thấy tài nguyên');
        }

        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $mime = self::MIME[$ext] ?? null;
        if ($mime === null) {
            throw new NotFoundException('Không tìm thấy tài nguyên');
        }

        return Response::raw((string) file_get_contents($path), 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
