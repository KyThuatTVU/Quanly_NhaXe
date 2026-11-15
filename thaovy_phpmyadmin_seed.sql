-- =====================================================================
-- DỰ ÁN     : Website thuê xe hợp đồng
-- FILE      : thaovy_phpmyadmin_seed.sql
-- MÔ TẢ     : Dữ liệu mẫu để chạy thử (import SAU khi đã import schema)
-- NGÀY TẠO  : 2026-11-14
--
-- CÁCH DÙNG (phpMyAdmin: chọn database thaovy_xe_hop_dong > tab Import)
--   1. Import thaovy_phpmyadmin_schema.sql  -> tạo 12 bảng rỗng
--   2. Import file này                      -> thêm dữ liệu mẫu
--
-- TÀI KHOẢN DEMO (mật khẩu là plaintext, CSDL lưu hash bcrypt):
--   admin        / Admin@123      (role admin)
--   doanhnghiep  / Business@123   (role business)
--   khachhang1   / Customer@123   (role customer)
--   khachhang2   / Customer@123   (role customer)
--
-- LƯU Ý:
--   - Chạy 1 lần trên database vừa import. Chạy lại sẽ báo trùng khóa
--     chính (1062) vì dùng id cố định.
--   - Thông tin công ty trong bảng settings là SỐ LIỆU MẪU,
--     phải thay bằng thông tin thật trước khi bàn giao.
--   - Ảnh trong vehicles/posts để NULL vì chưa có file trong storage/uploads;
--     upload ảnh thật qua trang quản trị sau.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

USE `thaovy_xe_hop_dong`;

START TRANSACTION;

-- =====================================================================
-- 1. SETTINGS - một dòng duy nhất, id = 1
-- =====================================================================
INSERT INTO `settings`
    (`id`, `company_name`, `address`, `phone`, `hotline`, `zalo`, `facebook`,
     `email`, `working_hours`, `map_location`, `description`)
VALUES
    (1, 'Nhà xe',
     'Số 1 Đường Trần Phú, Quận Hà Đông, TP. Hà Nội',
     '0987 654 321', '1900 1234', '0987654321',
     'https://facebook.com/xehopdongthaovy',
     'lienhe@thaovy.vn',
     'Thứ 2 - Chủ nhật: 07:00 - 21:00',
     'https://maps.google.com/?q=Ha+Noi',
     'Dịch vụ cho thuê xe hợp đồng 4 - 45 chỗ, xe đưa đón sân bay, xe du lịch và xe cưới hỏi tại Hà Nội.');

-- =====================================================================
-- 2. USERS - 1 admin, 1 doanh nghiệp, 2 khách hàng
--    hash bcrypt sinh bằng password_hash(..., PASSWORD_DEFAULT) PHP 8.2
-- =====================================================================
INSERT INTO `users`
    (`user_id`, `username`, `password`, `email`, `phone`, `role`, `is_active`, `created_at`)
VALUES
    (1, 'admin',
     '$2y$10$pVYqw3vZVtsQD8i3.U221uCls7L4TFHyQTncH3i.gf.r3LxKOdsVC',
     'admin@thaovy.vn', '0987654321', 'admin', 1, '2026-09-01 08:00:00'),
    (2, 'doanhnghiep',
     '$2y$10$KILDb3cC.9wDUx69gfvyieL0c9UToh0h5cWr4DNaeOO9doMukHblS',
     'kinhdoanh@thaovy.vn', '0912345670', 'business', 1, '2026-09-05 08:00:00'),
    (3, 'khachhang1',
     '$2y$10$oBw2nZQJXkO.B2wffn2raOMHPsLvmAkonfDC1uFJX9q7lsifFbJx.',
     'khach1@gmail.com', '0912345678', 'customer', 1, '2026-09-10 08:00:00'),
    (4, 'khachhang2',
     '$2y$10$oBw2nZQJXkO.B2wffn2raOMHPsLvmAkonfDC1uFJX9q7lsifFbJx.',
     'khach2@gmail.com', '0978123456', 'customer', 1, '2026-09-15 08:00:00');

-- =====================================================================
-- 3. VEHICLE_TYPES - 4, 7, 16, 29, 35-45 chỗ
-- =====================================================================
INSERT INTO `vehicle_types`
    (`id`, `name`, `slug`, `seats`, `seats_min`, `seats_max`, `description`,
     `icon`, `price_per_day`, `price_per_km`, `features`,
     `is_active`, `display_order`)
VALUES
    (1, 'Xe 4 chỗ', 'xe-4-cho', 4, 4, 4,
     'Phù hợp đi gia đình, công tác, đưa đón sân bay. Hành lý 2 - 3 vali.',
     'fa-car', 800000.00, 10000.00,
     '["Điều hoà", "Nước uống", "Sạc điện thoại", "Wifi 4G"]', 1, 1),
    (2, 'Xe 7 chỗ', 'xe-7-cho', 7, 7, 7,
     'Rộng rãi cho gia đình 5 - 6 người, phù hợp du lịch ngắn ngày.',
     'fa-car-side', 1100000.00, 13000.00,
     '["Điều hoà", "Nước uống", "Sạc điện thoại", "Wifi 4G", "Ghế trẻ em"]', 1, 2),
    (3, 'Xe 16 chỗ', 'xe-16-cho', 16, 16, 16,
     'Xe khách cỡ nhỏ, phù hợp đoàn 10 - 15 người đi tỉnh.',
     'fa-van-shuttle', 1600000.00, 16000.00,
     '["Điều hoà", "Nước uống", "Wifi 4G", "Khoang hành lý rộng"]', 1, 3),
    (4, 'Xe 29 chỗ', 'xe-29-cho', 29, 29, 29,
     'Xe khách trung bình, phù hợp đoàn 20 - 28 người, có khoang hành lý.',
     'fa-bus', 2200000.00, 20000.00,
     '["Điều hoà", "Wifi 4G", "Micro", "Khoang hành lý"]', 1, 4),
    (5, 'Xe 35 - 45 chỗ', 'xe-35-45-cho', 45, 35, 45,
     'Xe khách lớn cho đoàn đông, tour dài ngày, xe hợp đồng.',
     'fa-bus-simple', 3000000.00, 25000.00,
     '["Điều hoà", "Wifi 4G", "Micro", "Khoang hành lý", "Ghế ngả"]', 1, 5);

-- =====================================================================
-- 4. VEHICLES - 8 xe, gắn với loại xe
-- =====================================================================
INSERT INTO `vehicles`
    (`vehicle_id`, `vehicle_type_id`, `name`, `type`, `image`, `description`,
     `price_per_km`, `price_per_day`, `status`, `driver_info`, `rating`, `insurance_info`)
VALUES
    (1, 1, 'Toyota Vios 2022', '4 chỗ', NULL,
     'Xe sạch, nội thất da, máy lạnh sâu, phù hợp công tác và sân bay.',
     10000.00, 800000.00, 'available',
     'Tài xế Nguyễn Văn Hùng - 12 năm kinh nghiệm, GPLX hạng B2, thông thạo Hà Nội.',
     5.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm thân vỏ, hạn 12/2026.'),
    (2, 1, 'Hyundai Accent 2023', '4 chỗ', NULL,
     'Xe mới, tiết kiệm nhiên liệu, phù hợp đưa đón khách doanh nghiệp.',
     11000.00, 850000.00, 'available',
     'Tài xế Trần Quốc Đạt - 8 năm kinh nghiệm, ăn mặc lịch sự.',
     4.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm thân vỏ, hạn 03/2027.'),
    (3, 2, 'Toyota Innova 2021', '7 chỗ', NULL,
     'Xe 7 chỗ rộng rãi, khoang hành lý lớn, phù hợp gia đình đi du lịch.',
     13000.00, 1100000.00, 'available',
     'Tài xế Lê Minh Tuấn - 10 năm kinh nghiệm, đã chạy tour miền Bắc.',
     4.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm thân vỏ, hạn 08/2026.'),
    (4, 3, 'Ford Transit 2020', '16 chỗ', NULL,
     'Xe 16 chỗ đời 2020, ghế bọc da, điều hoà hai chiều.',
     16000.00, 1600000.00, 'available',
     'Tài xế Phạm Văn Sơn - 15 năm kinh nghiệm, GPLX hạng D.',
     4.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm hành khách, hạn 05/2027.'),
    (5, 3, 'Hyundai Solati 2022', '16 chỗ', NULL,
     'Xe 16 chỗ cao cấp, ghế rộng, phù hợp đoàn công tác.',
     17000.00, 1800000.00, 'available',
     'Tài xế Nguyễn Đình Khoa - 9 năm kinh nghiệm, phục vụ đoàn VIP.',
     4.5, 'Bảo hiểm TNDS bắt buộc + bảo hiểm hành khách, hạn 11/2026.'),
    (6, 4, 'Samco 29 chỗ', '29 chỗ', NULL,
     'Xe khách 29 chỗ, khoang hành lý dưới sàn, có micro hướng dẫn viên.',
     20000.00, 2200000.00, 'available',
     'Tài xế Đỗ Văn Thắng - 18 năm kinh nghiệm, chuyên tour Ninh Bình - Hạ Long.',
     5.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm hành khách, hạn 07/2026.'),
    (7, 5, 'Isuzu 45 chỗ', '35 - 45 chỗ', NULL,
     'Xe khách 45 chỗ, ghế ngả, điều hoà, phù hợp tour dài ngày.',
     25000.00, 3000000.00, 'available',
     'Tài xế Hoàng Văn Long - 20 năm kinh nghiệm, GPLX hạng E.',
     4.5, 'Bảo hiểm TNDS bắt buộc + bảo hiểm hành khách, hạn 02/2027.'),
    (8, 3, 'Mercedes Sprinter 2023', '16 chỗ', NULL,
     'Xe 16 chỗ cao cấp, nội thất VIP, đang bảo dưỡng định kỳ.',
     22000.00, 2500000.00, 'unavailable',
     'Tài xế Vũ Văn Nam - 11 năm kinh nghiệm, phục vụ khách doanh nghiệp.',
     5.0, 'Bảo hiểm TNDS bắt buộc + bảo hiểm hành khách, hạn 09/2027.');

-- =====================================================================
-- 5. SERVICES - 5 dịch vụ
-- =====================================================================
INSERT INTO `services`
    (`service_id`, `service_name`, `slug`, `description`, `price`, `image`,
     `is_active`, `display_order`)
VALUES
    (1, 'Đưa đón sân bay', 'dua-don-san-bay',
     'Đón trả khách tại Nội Bài và các sân bay, theo dõi giờ bay để đón đúng giờ.',
     550000.00, NULL, 1, 1),
    (2, 'Thuê xe theo ngày', 'thue-xe-theo-ngay',
     'Thuê xe trọn ngày kèm tài xế, tính theo số ngày và quãng đường thực tế.',
     800000.00, NULL, 1, 2),
    (3, 'Thuê xe hợp đồng dài hạn', 'thue-xe-hop-dong-dai-han',
     'Hợp đồng xe theo tháng cho doanh nghiệp, có hoá đơn VAT.',
     15000000.00, NULL, 1, 3),
    (4, 'Xe cưới hỏi', 'xe-cuoi-hoi',
     'Xe trang trí hoa cưới, tài xế mặc vest, phục vụ trọn gói trong ngày.',
     1700000.00, NULL, 1, 4),
    (5, 'Tour du lịch', 'tour-du-lich',
     'Xe kèm tài xế cho tour trong ngày hoặc nhiều ngày theo lịch trình khách chọn.',
     2200000.00, NULL, 1, 5);

-- =====================================================================
-- 6. BOOKINGS - 10 đơn trải đều trạng thái và tháng 9/10/11-2026
--    total_amount = giá chốt tại thời điểm đặt
-- =====================================================================
INSERT INTO `bookings`
    (`booking_id`, `booking_code`, `customer_id`, `vehicle_id`, `service_id`,
     `customer_name`, `customer_phone`, `customer_email`,
     `pickup_location`, `dropoff_location`,
     `pickup_date`, `pickup_time`, `return_date`, `return_time`,
     `number_of_passengers`, `service_type`,
     `price_per_day`, `price_per_km`, `total_amount`,
     `status`, `notes`, `created_at`)
VALUES
    (1, 'TV2609200001', 3, 1, 1,
     'Nguyễn Thu Hà', '0912345678', 'khach1@gmail.com',
     'Số 12 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'Sân bay Nội Bài',
     '2026-09-20', '06:00:00', NULL, NULL,
     2, 'Đưa đón sân bay',
     800000.00, 10000.00, 550000.00,
     'completed', 'Khách có 2 vali lớn, cần đón trước giờ bay 3 tiếng.',
     '2026-09-18 09:12:00'),

    (2, 'TV2610050001', 4, 3, 5,
     'Trần Minh Quân', '0978123456', 'khach2@gmail.com',
     'Số 45 Lê Lợi, Hà Đông, Hà Nội', 'Bãi Cháy, Hạ Long',
     '2026-10-05', '07:30:00', '2026-10-06', '17:00:00',
     6, 'Tour du lịch',
     1100000.00, 13000.00, 2400000.00,
     'completed', 'Tour 2 ngày 1 đêm cho gia đình 6 người.',
     '2026-10-01 14:05:00'),

    (3, 'TV2610180001', NULL, 6, 2,
     'Phạm Thanh Tùng', '0904567891', NULL,
     'Số 8 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'Tràng An, Ninh Bình',
     '2026-10-18', '08:00:00', '2026-10-18', '19:00:00',
     24, 'Thuê xe theo ngày',
     2200000.00, 20000.00, 4400000.00,
     'completed', 'Đoàn công ty 24 người, cần micro và nước uống.',
     '2026-10-14 10:30:00'),

    (4, 'TV2611010001', 3, 4, 1,
     'Nguyễn Thu Hà', '0912345678', 'khach1@gmail.com',
     'Số 12 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'Sân bay Nội Bài',
     '2026-11-01', '05:30:00', NULL, NULL,
     12, 'Đưa đón sân bay',
     1600000.00, 16000.00, 900000.00,
     'completed', 'Đoàn 12 người, chuyến bay lúc 08:15.',
     '2026-10-28 16:40:00'),

    (5, 'TV2611050001', 4, 2, 4,
     'Trần Minh Quân', '0978123456', 'khach2@gmail.com',
     'Số 45 Lê Lợi, Hà Đông, Hà Nội', 'Nhà thờ Hà Đông',
     '2026-11-05', '09:00:00', NULL, NULL,
     4, 'Xe cưới hỏi',
     850000.00, 11000.00, 1700000.00,
     'completed', 'Xe trang trí hoa cưới, tài xế mặc vest.',
     '2026-10-30 08:20:00'),

    (6, 'TV2611100001', 3, 7, 5,
     'Nguyễn Thu Hà', '0912345678', 'khach1@gmail.com',
     'Số 12 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'Sa Pa, Lào Cai',
     '2026-11-10', '14:00:00', '2026-11-13', '12:00:00',
     40, 'Tour du lịch',
     3000000.00, 25000.00, 9000000.00,
     'confirmed', 'Đoàn 40 người, khởi hành chiều, nghỉ 3 đêm tại Sa Pa.',
     '2026-11-02 11:15:00'),

    (7, 'TV2611120001', NULL, 1, 2,
     'Lê Hoàng Anh', '0938111222', NULL,
     'Số 22 Giải Phóng, Hai Bà Trưng, Hà Nội', 'Hải Phòng',
     '2026-11-12', '07:00:00', NULL, NULL,
     3, 'Thuê xe theo ngày',
     800000.00, 10000.00, 800000.00,
     'cancelled', 'Khách huỷ do thay đổi lịch công tác.',
     '2026-11-09 15:45:00'),

    (8, 'TV2611130001', 4, 3, 1,
     'Trần Minh Quân', '0978123456', 'khach2@gmail.com',
     'Số 45 Lê Lợi, Hà Đông, Hà Nội', 'Sân bay Nội Bài',
     '2026-11-13', '06:30:00', NULL, NULL,
     4, 'Đưa đón sân bay',
     1100000.00, 13000.00, 650000.00,
     'confirmed', 'Đón khách về từ chuyến bay Đà Nẵng.',
     '2026-11-10 20:05:00'),

    (9, 'TV2611140001', NULL, 5, 3,
     'Công ty TNHH An Phú', '0902222333', 'ketoan@anphu.vn',
     'Khu công nghiệp Quang Minh, Mê Linh, Hà Nội', 'Văn phòng Hà Nội',
     '2026-11-14', '10:00:00', '2026-11-30', '18:00:00',
     14, 'Thuê xe hợp đồng dài hạn',
     1800000.00, 17000.00, 3600000.00,
     'pending', 'Đang chờ xác nhận hợp đồng, cần hoá đơn VAT.',
     '2026-11-14 08:55:00'),

    (10, 'TV2611160001', NULL, 1, 2,
     'Vũ Thị Mai', '0945666777', NULL,
     'Số 5 Phạm Văn Đồng, Bắc Từ Liêm, Hà Nội', 'Tam Đảo, Vĩnh Phúc',
     '2026-11-16', '08:00:00', '2026-11-17', '17:00:00',
     2, 'Thuê xe theo ngày',
     800000.00, 10000.00, 1600000.00,
     'pending', 'Đi Tam Đảo 2 ngày, cần xe sạch.',
     '2026-11-14 10:20:00');

-- =====================================================================
-- 7. POSTS - 3 bài viết đã xuất bản
-- =====================================================================
INSERT INTO `posts`
    (`post_id`, `title`, `slug`, `content`, `image`, `category`,
     `status`, `created_by`, `created_at`)
VALUES
    (1, 'Kinh nghiệm thuê xe 16 chỗ đi du lịch tiết kiệm',
     'kinh-nghiem-thue-xe-16-cho',
     '<p>Xe 16 chỗ là lựa chọn phổ biến cho đoàn 10 - 15 người. Để tiết kiệm chi phí, bạn nên đặt xe trước 5 - 7 ngày, chốt rõ quãng đường và thời gian chờ.</p><ul><li>Chọn xe đời mới, kiểm tra điều hoà và khoang hành lý.</li><li>Hỏi rõ giá đã bao gồm phí cầu đường, bến bãi chưa.</li><li>Yêu cầu hợp đồng ghi rõ giờ khởi hành và điểm đón.</li></ul>',
     NULL, 'Kinh nghiệm', 'published', 1, '2026-11-12 09:00:00'),

    (2, 'Top 5 điểm du lịch gần Hà Nội đi trong ngày',
     'top-5-diem-du-lich-gan-ha-noi',
     '<p>Chỉ cần một ngày, bạn có thể đi Tràng An, Bái Đính, Hạ Long, Tam Đảo hoặc Bát Tràng. Thuê xe theo ngày giúp chủ động thời gian và không phải đổi phương tiện.</p><p>Gợi ý lịch trình: khởi hành 7:00, đến nơi 9:30, ăn trưa tại địa phương và về Hà Nội trước 19:00.</p>',
     NULL, 'Du lịch', 'published', 1, '2026-11-08 14:30:00'),

    (3, 'Quy trình đặt xe hợp đồng',
     'quy-trinh-dat-xe-hop-dong',
     '<p>Bước 1: Gửi yêu cầu qua form đặt xe hoặc hotline. Bước 2: Nhân viên tư vấn loại xe và báo giá trong 15 phút. Bước 3: Xác nhận đơn và nhận mã đơn. Bước 4: Tài xế liên hệ trước giờ đón 1 tiếng.</p>',
     NULL, 'Tin công ty', 'published', 1, '2026-11-01 08:15:00');

-- =====================================================================
-- 8. RATINGS - đánh giá cho các đơn đã hoàn thành (mỗi đơn 1 đánh giá)
-- =====================================================================
INSERT INTO `ratings`
    (`rating_id`, `booking_id`, `vehicle_id`, `customer_id`, `rating`,
     `comment`, `is_visible`, `created_at`)
VALUES
    (1, 1, 1, 3, 5,
     'Tài xế đến sớm 15 phút, xe sạch, chạy êm. Sẽ đặt lại lần sau.',
     1, '2026-09-20 18:30:00'),
    (2, 2, 3, 4, 4,
     'Xe 7 chỗ rộng, đi Hạ Long thoải mái. Tài xế thân thiện.',
     1, '2026-10-07 09:10:00'),
    (3, 3, 6, NULL, 5,
     'Đoàn 24 người đi Ninh Bình, xe đúng giờ, có micro và nước uống đầy đủ.',
     1, '2026-10-19 08:45:00'),
    (4, 4, 4, 3, 4,
     'Xe 16 chỗ ổn, đón đúng giờ. Điều hoà hơi yếu ở hàng ghế cuối.',
     0, '2026-11-01 12:20:00'),
    (5, 5, 2, 4, 5,
     'Xe cưới trang trí đẹp, tài xế mặc vest lịch sự, rất hài lòng.',
     1, '2026-11-05 20:00:00');

-- =====================================================================
-- 9. CONTACTS - 3 tin nhắn ở 3 trạng thái
-- =====================================================================
INSERT INTO `contacts`
    (`contact_id`, `name`, `email`, `phone`, `subject`, `message`,
     `status`, `replied_by`, `replied_at`, `created_at`)
VALUES
    (1, 'Nguyễn Văn An', 'an.nguyen@gmail.com', '0911222333',
     'Hỏi giá thuê xe 16 chỗ',
     'Cho tôi hỏi giá thuê xe 16 chỗ từ Hà Nội đi Hải Phòng trong 1 ngày, đoàn 14 người.',
     'new', NULL, NULL, '2026-11-14 09:12:00'),
    (2, 'Trần Thị Bích', 'bich.tran@gmail.com', '0988777666',
     'Báo giá tour Đà Nẵng 4 ngày',
     'Công ty tôi cần xe 29 chỗ cho đoàn 25 người đi Đà Nẵng 4 ngày 3 đêm, vui lòng báo giá.',
     'processing', NULL, NULL, '2026-11-13 15:40:00'),
    (3, 'Lê Văn Cường', 'cuong.le@gmail.com', '0903555444',
     'Cần hoá đơn VAT',
     'Thuê xe hợp đồng theo tháng cho công ty, cần xuất hoá đơn VAT. Xin liên hệ lại.',
     'replied', 1, '2026-11-12 10:05:00', '2026-11-11 11:20:00');

COMMIT;

-- =====================================================================
-- KIỂM TRA NHANH SAU KHI IMPORT
-- =====================================================================
SELECT 'users' AS bang, COUNT(*) AS so_dong FROM `users`
UNION ALL SELECT 'vehicle_types', COUNT(*) FROM `vehicle_types`
UNION ALL SELECT 'vehicles', COUNT(*) FROM `vehicles`
UNION ALL SELECT 'services', COUNT(*) FROM `services`
UNION ALL SELECT 'bookings', COUNT(*) FROM `bookings`
UNION ALL SELECT 'posts', COUNT(*) FROM `posts`
UNION ALL SELECT 'ratings', COUNT(*) FROM `ratings`
UNION ALL SELECT 'contacts', COUNT(*) FROM `contacts`
UNION ALL SELECT 'settings', COUNT(*) FROM `settings`;

-- =====================================================================
-- MUỐN RESET DỮ LIỆU MẪU: xoá theo đúng thứ tự khoá ngoại rồi import lại
--   DELETE FROM audit_logs; DELETE FROM ratings; DELETE FROM contacts;
--   DELETE FROM bookings; DELETE FROM posts; DELETE FROM vehicles;
--   DELETE FROM services; DELETE FROM vehicle_types;
--   DELETE FROM admin_sessions; DELETE FROM admin_google_accounts;
--   DELETE FROM users; DELETE FROM settings;
-- =====================================================================
