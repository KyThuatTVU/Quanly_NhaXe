<?php

declare(strict_types=1);

namespace App\Support;

final class Format
{
    public static function money(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        return number_format((float) $value, 0, ',', '.') . ' ₫';
    }

    public static function date(?string $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        $time = strtotime($value);
        return $time === false ? $value : date('d/m/Y', $time);
    }

    public static function datetime(?string $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }
        $time = strtotime($value);
        return $time === false ? $value : date('d/m/Y H:i', $time);
    }

    public static function bookingStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => $status,
        };
    }

    public static function contactStatus(string $status): string
    {
        return match ($status) {
            'new' => 'Mới',
            'processing' => 'Đang xử lý',
            'replied' => 'Đã phản hồi',
            default => $status,
        };
    }

    public static function carStatus(string $status): string
    {
        return $status === 'available' ? 'Sẵn sàng' : 'Tạm ngưng';
    }

    public static function postStatus(string $status): string
    {
        return $status === 'published' ? 'Đã đăng' : 'Bản nháp';
    }

    public static function badge(string $status): string
    {
        return match ($status) {
            'pending', 'new' => 'badge warning',
            'confirmed', 'processing' => 'badge info',
            'completed', 'replied', 'published', 'available' => 'badge success',
            'cancelled', 'unavailable' => 'badge danger',
            'draft' => 'badge neutral',
            default => 'badge neutral',
        };
    }

    public static function phone(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '—';
        }
        if (preg_match('/^0\d{9}$/', $value) === 1) {
            return substr($value, 0, 4) . ' ' . substr($value, 4, 3) . ' ' . substr($value, 7);
        }
        return $value;
    }
}
