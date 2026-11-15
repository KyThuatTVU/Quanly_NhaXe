<?php

declare(strict_types=1);

/**
 * Chạy toàn bộ test: php backend/tests/run.php
 * Không phụ thuộc Composer/PHPUnit; CSDL test được tạo lại mỗi lần chạy.
 */

require __DIR__ . '/bootstrap.php';

$failed = 0;
foreach (['Unit', 'Feature'] as $group) {
    foreach (glob(__DIR__ . '/' . $group . '/*.php') ?: [] as $file) {
        $tests = require $file;
        if (!is_array($tests) || $tests === []) {
            continue;
        }
        echo PHP_EOL . '== ' . $group . '/' . basename($file) . ' ==' . PHP_EOL;
        $failed += test_run($tests);
    }
}

echo PHP_EOL . ($failed === 0 ? 'Tất cả test đều đạt.' : $failed . ' test thất bại.') . PHP_EOL;
exit($failed === 0 ? 0 : 1);
