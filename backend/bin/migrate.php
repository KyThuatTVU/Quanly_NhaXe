<?php

declare(strict_types=1);

/**
 * Chạy migration còn thiếu: php backend/bin/migrate.php
 * Mỗi file .sql trong app/Infrastructure/Persistence/Migrations được áp dụng một lần.
 */

use App\Core\Config;
use App\Infrastructure\Persistence\Connection;
use App\Support\SqlSplitter;

$basePath = dirname(__DIR__);

require $basePath . '/bootstrap/autoload.php';
Config::bootstrap($basePath);

try {
    $pdo = Connection::pdo();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Không kết nối được CSDL: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        migration VARCHAR(191) NOT NULL,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
);

$directory = $basePath . '/app/Infrastructure/Persistence/Migrations';
$files = glob($directory . '/*.sql') ?: [];
sort($files);

$applied = array_map('strval', $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN));

$ran = 0;
foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        continue;
    }

    $statements = SqlSplitter::split((string) file_get_contents($file));
    if ($statements === []) {
        continue;
    }

    try {
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
        $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:migration)')
            ->execute(['migration' => $name]);
    } catch (Throwable $exception) {
        fwrite(STDERR, "Lỗi khi áp dụng {$name}: {$exception->getMessage()}" . PHP_EOL);
        exit(1);
    }

    echo "Đã áp dụng: {$name}" . PHP_EOL;
    $ran++;
}

echo $ran === 0
    ? 'Không có migration mới. CSDL đang ở bản mới nhất.' . PHP_EOL
    : "Hoàn tất: {$ran} migration." . PHP_EOL;
