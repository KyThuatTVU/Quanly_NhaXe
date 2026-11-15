<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'session_name' => Config::env('SESSION_NAME', 'thaovy_admin'),
    // Phút: 1440 = 24 giờ, khớp DEFAULT của admin_sessions.expires_at
    'session_lifetime' => (int) Config::env('SESSION_LIFETIME', '1440'),
    'roles' => ['admin', 'business', 'customer'],
    'admin_roles' => ['admin', 'business'],
    'password_min_length' => 8,
    // Đăng nhập Google: điền GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET trong .env (không commit).
    'google' => [
        'client_id' => (string) Config::env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => (string) Config::env('GOOGLE_CLIENT_SECRET', ''),
    ],
];
