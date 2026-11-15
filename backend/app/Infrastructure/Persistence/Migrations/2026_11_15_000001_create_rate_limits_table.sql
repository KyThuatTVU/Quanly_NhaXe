-- Bảng đếm giới hạn tần suất cho RateLimitMiddleware.
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `bucket_key` VARCHAR(64)  CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    `scope`      VARCHAR(50)  CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
    `hits`       INT UNSIGNED NOT NULL DEFAULT 0,
    `reset_at`   DATETIME     NOT NULL,
    `updated_at` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`bucket_key`),
    KEY `idx_rate_limits_reset` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
