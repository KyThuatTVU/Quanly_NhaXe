<?php

declare(strict_types=1);

/**
 * Trả về URL sạch tương ứng với đường dẫn cũ /backend/public/...,
 * hoặc null nếu đường dẫn không thuộc nhóm cũ.
 */
function legacy_backend_target(string $route): ?string
{
    if ($route !== 'backend/public' && !str_starts_with($route, 'backend/public/')) {
        return null;
    }

    $legacy = trim(substr($route, strlen('backend/public')), '/');
    $segments = $legacy === '' ? [] : explode('/', $legacy);
    $first = $segments[0] ?? '';
    $second = $segments[1] ?? '';

    return match (true) {
        $legacy === '', $legacy === 'index.php' => 'home/',
        $first === 'api', $first === 'uploads', $first === 'assets' => $legacy,
        $first === 'admin' => $legacy,
        $first === 'gioi-thieu' => 'about/',
        $first === 'lien-he' => 'contact/',
        $first === 'danh-gia' => 'reviews/',
        $first === 'tra-cuu-don' => 'booking/lookup/',
        $first === 'dat-xe' && $second === 'thanh-cong' => 'booking/success/',
        $first === 'dat-xe' => 'booking/',
        $first === 'xe' && $second !== '' => 'cars/detail/?id=' . rawurlencode($second),
        $first === 'xe' => 'cars/',
        $first === 'dich-vu' && $second !== '' => 'services/detail/?id=' . rawurlencode($second),
        $first === 'dich-vu' => 'services/',
        $first === 'bai-viet' && $second !== '' => 'posts/detail/?slug=' . rawurlencode($second),
        $first === 'bai-viet' => 'posts/',
        default => 'home/',
    };
}
