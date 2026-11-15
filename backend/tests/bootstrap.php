<?php

declare(strict_types=1);

/**
 * Khởi tạo môi trường test: CSDL riêng <database>_test, cấu trúc lấy từ schema.sql.
 * Không bao giờ chạm vào database đang chạy thật.
 */

use App\Core\Config;
use App\Support\SqlSplitter;

$basePath = dirname(__DIR__);

require $basePath . '/bootstrap/autoload.php';
require $basePath . '/app/Support/helpers.php';

Config::bootstrap($basePath);

$config = Config::get('database');
$liveDatabase = (string) ($config['database'] ?? '');
if (preg_match('/^[A-Za-z0-9_]+$/', $liveDatabase) !== 1) {
    fwrite(STDERR, 'Tên database không hợp lệ để tạo bản test: ' . $liveDatabase . PHP_EOL);
    exit(1);
}
$testDatabase = $liveDatabase . '_test';

$server = new PDO(
    sprintf('%s:host=%s;port=%d;charset=%s', $config['driver'], $config['host'], $config['port'], $config['charset']),
    (string) $config['username'],
    (string) $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);

$server->exec('DROP DATABASE IF EXISTS `' . $testDatabase . '`');
$server->exec('CREATE DATABASE `' . $testDatabase . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$server->exec('USE `' . $testDatabase . '`');

$schema = (string) file_get_contents(dirname($basePath) . '/thaovy_phpmyadmin_schema.sql');
foreach (SqlSplitter::split($schema) as $statement) {
    if (preg_match('/^CREATE\s+DATABASE/i', $statement) === 1 || preg_match('/^USE\s+/i', $statement) === 1) {
        continue;
    }
    $server->exec($statement);
}

$server = null;

Config::set('database.database', $testDatabase);

require __DIR__ . '/helpers.php';
