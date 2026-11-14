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
];
