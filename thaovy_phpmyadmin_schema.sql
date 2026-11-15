-- =====================================================================
-- DỰ ÁN     : Website thuê xe hợp đồng
-- FILE      : thaovy_phpmyadmin_schema.sql
-- MÔ TẢ     : CSDL chuẩn hóa - CHỈ CẤU TRÚC, KHÔNG KÈM DỮ LIỆU
-- NGÀY SỬA  : 2026-11-14 (bản 2)
-- MySQL     : 8.0.16+ / MariaDB 10.4+ (CHECK và DEFAULT biểu thức được thực thi)
-- CHARSET   : utf8mb4 / utf8mb4_unicode_ci
--
-- NHỮNG ĐIỂM ĐÃ SỬA SO VỚI BẢN TRƯỚC:
--   1. Giữ tên bảng vehicles / vehicle_types / ratings (đã chốt, docs cập nhật theo).
--   2. Thêm bảng audit_logs (chức năng 37 - ghi vết thao tác quản trị).
--   3. bookings: thêm booking_code UNIQUE (BookingService sinh mã đơn),
--      return_date + return_time (kiểm tra trùng lịch nhiều ngày),
--      price_per_day / price_per_km / total_amount (chốt giá tại thời điểm đặt).
--   4. Khóa ngoại đổi sang RESTRICT cho dữ liệu nghiệp vụ: không cho xóa
--      tài khoản/xe/dịch vụ/loại xe đang được tham chiếu.
--      ratings.vehicle_id bỏ CASCADE để xóa xe không nuốt mất đánh giá.
--   5. admin_sessions.session_id: VARCHAR(128) CHARACTER SET ascii
--      (tránh vượt giới hạn 767 byte khóa chính ở row_format COMPACT).
--   6. users.password cho phép NULL (tài khoản chỉ đăng nhập Google).
--   7. services.slug và posts.slug: NOT NULL + UNIQUE (nhất quán với vehicle_types).
--   8. vehicle_types: thêm seats_min/seats_max cho loại "35-45 chỗ".
--   9. Index tổ hợp idx_bookings_vehicle_time cho truy vấn trùng lịch xe.
--
-- LƯU Ý:
--   - Chỉ dùng CREATE ... IF NOT EXISTS, KHÔNG DROP, KHÔNG INSERT.
--   - Nên import vào database mới/trống để tránh trùng cấu trúc cũ.
--   - CREATE TABLE IF NOT EXISTS không tự bổ sung cột cho bảng đã tồn tại;
--     muốn nâng cấp database đang có thì phải DROP rồi import lại
--     (an toàn khi bảng còn rỗng) hoặc tự viết ALTER TABLE.
--   - File chỉ tạo bảng và cột, KHÔNG INSERT dữ liệu mẫu nên các bảng ban đầu rỗng.
--   - Các cột type (vehicles) và service_type (bookings) được giữ lại
--     để tương thích code cũ; nguồn dữ liệu chuẩn là vehicle_type_id/service_id.
--   - Sinh mã đơn / slug là việc của tầng ứng dụng (App\Support\Str),
--     không dùng trigger trong CSDL.
-- =====================================================================

SET NAMES utf8mb4;
-- Chỉ áp dụng cho session import. PDO của ứng dụng phải set cùng timezone
-- (config/database.php) nếu không created_at sẽ lệch 7 giờ.
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `thaovy_xe_hop_dong`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `thaovy_xe_hop_dong`;

-- =====================================================================
-- 1. USERS - Tài khoản quản trị / doanh nghiệp / khách hàng
-- =====================================================================
CREATE TABLE IF NOT EXISTS `users` (
    `user_id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL,
    `password`      VARCHAR(255) DEFAULT NULL COMMENT 'Hash bcrypt/argon2; NULL khi chỉ đăng nhập Google',
    `email`         VARCHAR(255) DEFAULT NULL,
    `phone`         VARCHAR(20)  DEFAULT NULL,
    `role`          ENUM('admin','business','customer') NOT NULL DEFAULT 'customer',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at` DATETIME     DEFAULT NULL,
    `created_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_phone` (`phone`),
    KEY `idx_users_role_active` (`role`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 2. ADMIN_SESSIONS - Phiên đăng nhập quản trị
-- =====================================================================
CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `session_id`    VARCHAR(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
    `user_id`       INT UNSIGNED NOT NULL,
    `ip_address`    VARCHAR(45)  DEFAULT NULL,
    `user_agent`    VARCHAR(512) DEFAULT NULL,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `expires_at`    DATETIME     NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 24 HOUR),
    `created_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `last_activity` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`session_id`),
    KEY `idx_sessions_user_active` (`user_id`, `is_active`),
    KEY `idx_sessions_expires` (`expires_at`),
    CONSTRAINT `fk_sessions_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 3. ADMIN_GOOGLE_ACCOUNTS - Liên kết đăng nhập Google cho admin
--
-- Luồng chuẩn:
--   - Xác thực Google ID token, lấy sub/email/name/picture.
--   - Tìm theo google_sub; nếu chưa có thì tìm users theo email
--     và chỉ liên kết khi users.role = 'admin' và users.is_active = 1.
--   - Không lưu access_token/refresh_token.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `admin_google_accounts` (
    `google_account_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED NOT NULL,
    `google_sub`        VARCHAR(255) NOT NULL COMMENT 'sub trong Google ID token',
    `email`             VARCHAR(255) NOT NULL,
    `email_verified`    TINYINT(1)   NOT NULL DEFAULT 0,
    `full_name`         VARCHAR(255) DEFAULT NULL,
    `avatar_url`        VARCHAR(500) DEFAULT NULL,
    `is_active`         TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at`     DATETIME     DEFAULT NULL,
    `last_login_ip`     VARCHAR(45)  DEFAULT NULL,
    `created_at`        TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`google_account_id`),
    UNIQUE KEY `uq_google_sub` (`google_sub`),
    UNIQUE KEY `uq_google_email` (`email`),
    UNIQUE KEY `uq_google_user` (`user_id`),
    KEY `idx_google_active` (`is_active`),
    CONSTRAINT `fk_google_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 4. VEHICLE_TYPES - Loại xe (4, 7, 16, 29, 35-45 chỗ)
--    Loại "35-45 chỗ" dùng seats_min/seats_max; seats giữ giá trị đại diện
--    để tương thích code cũ.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `vehicle_types` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(255) NOT NULL,
    `slug`          VARCHAR(255) NOT NULL,
    `seats`         SMALLINT UNSIGNED NOT NULL COMMENT 'Số chỗ đại diện để hiển thị',
    `seats_min`     SMALLINT UNSIGNED DEFAULT NULL,
    `seats_max`     SMALLINT UNSIGNED DEFAULT NULL,
    `description`   TEXT,
    `icon`          VARCHAR(100) DEFAULT NULL,
    `image_url`     VARCHAR(500) DEFAULT NULL,
    `price_per_day` DECIMAL(12,2) DEFAULT NULL,
    `price_per_km`  DECIMAL(12,2) DEFAULT NULL,
    `features`      JSON         DEFAULT NULL,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `display_order` INT          NOT NULL DEFAULT 0,
    `created_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_vehicle_types_slug` (`slug`),
    KEY `idx_vehicle_types_active_order` (`is_active`, `display_order`),
    CONSTRAINT `chk_vehicle_types_seats` CHECK (`seats` > 0),
    CONSTRAINT `chk_vehicle_types_seats_range`
        CHECK (`seats_min` IS NULL OR `seats_max` IS NULL OR `seats_max` >= `seats_min`),
    CONSTRAINT `chk_vehicle_types_price_day` CHECK (`price_per_day` IS NULL OR `price_per_day` >= 0),
    CONSTRAINT `chk_vehicle_types_price_km` CHECK (`price_per_km` IS NULL OR `price_per_km` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 5. VEHICLES - Xe cụ thể
--    rating là số tổng hợp (cache) do ReviewService cập nhật từ bảng ratings;
--    khi cần số chính xác tuyệt đối thì AVG() trực tiếp trên ratings.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `vehicles` (
    `vehicle_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vehicle_type_id` INT UNSIGNED DEFAULT NULL,
    `name`            VARCHAR(100) NOT NULL,
    `type`            VARCHAR(50)  DEFAULT NULL COMMENT 'Nhãn hiển thị cũ; nguồn chuẩn là vehicle_type_id',
    `image`           VARCHAR(255) DEFAULT NULL,
    `description`     TEXT,
    `price_per_km`    DECIMAL(12,2) DEFAULT NULL,
    `price_per_day`   DECIMAL(12,2) DEFAULT NULL,
    `status`          ENUM('available','unavailable') NOT NULL DEFAULT 'available',
    `driver_info`     TEXT,
    `rating`          DECIMAL(2,1) NOT NULL DEFAULT 0.0 COMMENT 'Cache trung bình từ ratings',
    `insurance_info`  TEXT,
    `created_at`      TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`vehicle_id`),
    KEY `idx_vehicles_vehicle_type` (`vehicle_type_id`),
    KEY `idx_vehicles_status` (`status`),
    KEY `idx_vehicles_type` (`type`),
    CONSTRAINT `fk_vehicles_type`
        FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `chk_vehicles_rating` CHECK (`rating` BETWEEN 0 AND 5),
    CONSTRAINT `chk_vehicles_price_day` CHECK (`price_per_day` IS NULL OR `price_per_day` >= 0),
    CONSTRAINT `chk_vehicles_price_km` CHECK (`price_per_km` IS NULL OR `price_per_km` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 6. SERVICES - Dịch vụ cho thuê xe
-- =====================================================================
CREATE TABLE IF NOT EXISTS `services` (
    `service_id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_name`  VARCHAR(100) NOT NULL,
    `slug`          VARCHAR(255) NOT NULL,
    `description`   TEXT,
    `price`         DECIMAL(12,2) DEFAULT NULL,
    `image`         VARCHAR(255) DEFAULT NULL,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `display_order` INT          NOT NULL DEFAULT 0,
    `created_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`service_id`),
    UNIQUE KEY `uq_services_slug` (`slug`),
    KEY `idx_services_active_order` (`is_active`, `display_order`),
    KEY `idx_services_name` (`service_name`),
    CONSTRAINT `chk_services_price` CHECK (`price` IS NULL OR `price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 7. BOOKINGS - Đơn đặt xe
--    - booking_code: mã hiển thị cho khách (BookingService sinh, UNIQUE).
--    - return_date/return_time: dùng cho kiểm tra trùng lịch nhiều ngày;
--      NULL nghĩa là đơn một chiều (chỉ có ngày đón).
--    - price_per_day / price_per_km / total_amount: chốt giá tại thời điểm
--      đặt, không đổi khi admin sửa giá xe.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `bookings` (
    `booking_id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_code`         VARCHAR(20)  NOT NULL COMMENT 'Mã đơn hiển thị, ví dụ DX2611140001',
    `customer_id`          INT UNSIGNED DEFAULT NULL,
    `vehicle_id`           INT UNSIGNED DEFAULT NULL,
    `service_id`           INT UNSIGNED DEFAULT NULL,
    `customer_name`        VARCHAR(100) NOT NULL,
    `customer_phone`       VARCHAR(20)  NOT NULL,
    `customer_email`       VARCHAR(255) DEFAULT NULL,
    `pickup_location`      VARCHAR(255) NOT NULL,
    `dropoff_location`     VARCHAR(255) DEFAULT NULL,
    `pickup_date`          DATE         NOT NULL,
    `pickup_time`          TIME         NOT NULL,
    `return_date`          DATE         DEFAULT NULL,
    `return_time`          TIME         DEFAULT NULL,
    `number_of_passengers` SMALLINT UNSIGNED DEFAULT NULL,
    `service_type`         VARCHAR(50)  DEFAULT NULL COMMENT 'Nhãn cũ; nguồn chuẩn là service_id',
    `price_per_day`        DECIMAL(12,2) DEFAULT NULL COMMENT 'Giá chốt tại thời điểm đặt',
    `price_per_km`         DECIMAL(12,2) DEFAULT NULL COMMENT 'Giá chốt tại thời điểm đặt',
    `total_amount`         DECIMAL(12,2) DEFAULT NULL,
    `status`               ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    `notes`                TEXT,
    `created_at`           TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`booking_id`),
    UNIQUE KEY `uq_bookings_code` (`booking_code`),
    KEY `idx_bookings_customer` (`customer_id`),
    KEY `idx_bookings_vehicle` (`vehicle_id`),
    KEY `idx_bookings_service` (`service_id`),
    KEY `idx_bookings_status_date` (`status`, `pickup_date`),
    KEY `idx_bookings_created_at` (`created_at`),
    KEY `idx_bookings_phone` (`customer_phone`),
    KEY `idx_bookings_vehicle_time` (`vehicle_id`, `pickup_date`, `return_date`),
    CONSTRAINT `fk_bookings_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_bookings_vehicle`
        FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_bookings_service`
        FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE RESTRICT,
    CONSTRAINT `chk_bookings_passengers`
        CHECK (`number_of_passengers` IS NULL OR `number_of_passengers` > 0),
    CONSTRAINT `chk_bookings_return_after_pickup`
        CHECK (`return_date` IS NULL OR `return_date` >= `pickup_date`),
    CONSTRAINT `chk_bookings_total_amount`
        CHECK (`total_amount` IS NULL OR `total_amount` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 8. POSTS - Bài viết / tin tức
-- =====================================================================
CREATE TABLE IF NOT EXISTS `posts` (
    `post_id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255) NOT NULL,
    `slug`       VARCHAR(255) NOT NULL,
    `content`    MEDIUMTEXT,
    `image`      VARCHAR(255) DEFAULT NULL,
    `category`   VARCHAR(50)  DEFAULT NULL,
    `status`     ENUM('draft','published') NOT NULL DEFAULT 'published',
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`post_id`),
    UNIQUE KEY `uq_posts_slug` (`slug`),
    KEY `idx_posts_created_by` (`created_by`),
    KEY `idx_posts_category` (`category`),
    KEY `idx_posts_status_created` (`status`, `created_at`),
    CONSTRAINT `fk_posts_user`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 9. RATINGS - Đánh giá xe (gắn với đơn đặt để chống đánh giá trùng)
--    - uq_ratings_booking: mỗi đơn chỉ 1 đánh giá; NULL = đánh giá không
--      gắn đơn (luật "chỉ đánh giá khi đơn hoàn thành" do ReviewService chặn).
--    - vehicle_id RESTRICT: xóa xe không được nuốt mất đánh giá.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `ratings` (
    `rating_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id`  INT UNSIGNED DEFAULT NULL,
    `vehicle_id`  INT UNSIGNED NOT NULL,
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `rating`      TINYINT UNSIGNED NOT NULL,
    `comment`     TEXT,
    `is_visible`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`rating_id`),
    UNIQUE KEY `uq_ratings_booking` (`booking_id`),
    KEY `idx_ratings_vehicle` (`vehicle_id`),
    KEY `idx_ratings_customer` (`customer_id`),
    CONSTRAINT `fk_ratings_booking`
        FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_ratings_vehicle`
        FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_ratings_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT,
    CONSTRAINT `chk_ratings_value` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 10. CONTACTS - Tin nhắn liên hệ
-- =====================================================================
CREATE TABLE IF NOT EXISTS `contacts` (
    `contact_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(255) DEFAULT NULL,
    `phone`      VARCHAR(20)  NOT NULL,
    `subject`    VARCHAR(255) DEFAULT NULL,
    `message`    TEXT,
    `status`     ENUM('new','processing','replied') NOT NULL DEFAULT 'new',
    `replied_by` INT UNSIGNED DEFAULT NULL,
    `replied_at` DATETIME     DEFAULT NULL,
    `created_at` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`contact_id`),
    KEY `idx_contacts_status` (`status`),
    KEY `idx_contacts_created_at` (`created_at`),
    KEY `idx_contacts_replied_by` (`replied_by`),
    CONSTRAINT `fk_contacts_replied_by`
        FOREIGN KEY (`replied_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 11. SETTINGS - Cấu hình website (một dòng duy nhất, id = 1)
--     Đã gộp các trường của bảng company cũ vào đây.
--     Cần seeder tạo sẵn dòng id = 1 trước khi chạy trang public.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id`            TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `company_name`  VARCHAR(200) NOT NULL DEFAULT '',
    `address`       VARCHAR(500) DEFAULT NULL,
    `phone`         VARCHAR(20)  NOT NULL DEFAULT '0123 456 789',
    `hotline`       VARCHAR(20)  DEFAULT NULL,
    `zalo`          VARCHAR(20)  NOT NULL DEFAULT '0123456789',
    `facebook`      VARCHAR(500) DEFAULT NULL,
    `email`         VARCHAR(255) DEFAULT NULL,
    `working_hours` VARCHAR(100) DEFAULT NULL,
    `map_location`  VARCHAR(500) DEFAULT NULL,
    `description`   TEXT,
    `created_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `chk_settings_single_row` CHECK (`id` = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 12. AUDIT_LOGS - Ghi vết thao tác quản trị (chức năng 37)
--     Append-only: chỉ INSERT, chỉ xóa khi dọn log cũ.
-- =====================================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `log_id`      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `action`      VARCHAR(50)  NOT NULL COMMENT 'create/update/delete/login/logout/change_status...',
    `entity_type` VARCHAR(50)  DEFAULT NULL COMMENT 'users, bookings, vehicles, posts...',
    `entity_id`   INT UNSIGNED DEFAULT NULL,
    `description` TEXT,
    `ip_address`  VARCHAR(45)  DEFAULT NULL,
    `user_agent`  VARCHAR(512) DEFAULT NULL,
    `created_at`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_audit_user` (`user_id`),
    KEY `idx_audit_entity` (`entity_type`, `entity_id`),
    KEY `idx_audit_created_at` (`created_at`),
    CONSTRAINT `fk_audit_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- KẾT THÚC SCHEMA - 12 BẢNG, CHƯA CÓ DỮ LIỆU
-- =====================================================================
