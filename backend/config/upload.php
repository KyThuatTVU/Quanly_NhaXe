<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'dir' => Config::env('UPLOAD_DIR', 'storage/uploads'),
    'max_size' => (int) Config::env('UPLOAD_MAX_SIZE', '5242880'),
    'folders' => ['cars', 'posts', 'services', 'avatars'],
    'mime' => [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ],
];
