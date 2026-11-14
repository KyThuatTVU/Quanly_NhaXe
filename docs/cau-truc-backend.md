# Cấu trúc thư mục Backend — Hệ thống quản lý nhà xe & đặt xe trực tuyến

## Bối cảnh đã chốt

| Hạng mục | Giá trị áp dụng |
|---|---|
| Dự án | Hệ thống quản lý nhà xe & đặt xe trực tuyến (QuanLyNhaXe) |
| Nghiệp vụ | Xe, loại xe, dịch vụ, đặt xe, bài viết, liên hệ, đánh giá, tài khoản & phân quyền, thống kê, cài đặt liên hệ, upload ảnh, email, audit log |
| Người dùng | ~10–20 nhân viên nội bộ (Admin, Doanh nghiệp) + khách tra cứu/đặt xe |
| Môi trường | PHP 8.2, MySQL 8.0, Apache + .htaccess (shared hosting), Composer + PSR-4 |
| Ràng buộc kỹ thuật | Không dùng framework, tự viết mini-framework MVC |
| Bề mặt phục vụ | Web (trả HTML cho khách + quản trị) và API (trả JSON cho fetch) |

> Giả định trên là mặc định hợp lý cho đồ án; nếu đổi sang VPS/Nginx thì chỉ thay file cấu hình máy chủ, không đổi cấu trúc thư mục.

## Quy ước đặt tên

- Namespace gốc `App\` ánh xạ tới `app/` (PSR-4), một file = một class, tên file trùng tên class.
- Class/Interface: PascalCase (`BookingService`, `CarRepositoryInterface`).
- Controller kết thúc bằng `Controller`, Service bằng `Service`, Repository bằng `Repository`, Middleware bằng `Middleware`, Request bằng `Request`.
- Bảng CSDL: snake_case, số nhiều (`vehicle_types`, `bookings`, `audit_logs`); cột snake_case (`full_name`, `created_at`).
- Tên bảng chốt theo `thaovy_phpmyadmin_schema.sql`: `users`, `admin_sessions`, `admin_google_accounts`, `vehicle_types`, `vehicles`, `services`, `bookings`, `posts`, `ratings`, `contacts`, `settings`, `audit_logs`. Class Entity vẫn đặt theo nghiệp vụ (`Car` ↔ bảng `vehicles`, `CarType` ↔ `vehicle_types`, `Review` ↔ `ratings`).
- View: thư mục và file kebab-case hoặc snake_case (`admin/car-types/index.php`, `booking/success.php`).
- Route path: kebab-case (`/admin/car-types`); tham số route đặt trong ngoặc nhọn.
- Biến môi trường: SCREAMING_SNAKE_CASE trong `.env`; khoá config: snake_case.

---

## Phần 1 — Cây thư mục đầy đủ

```text
DuAn_QuanLyNhaXe/
├── backend/                                      # toàn bộ mã nguồn PHP — KHÔNG nằm trong webroot
│   ├── app/                                      # mã nguồn, namespace App\ (PSR-4)
│   │   ├── Core/                                 # mini-framework, không chứa nghiệp vụ nhà xe
│   │   │   ├── Application.php                   # bootstrap container, nạp config, chạy kernel
│   │   │   ├── Container.php                     # DI container tối giản: bind, resolve singleton
│   │   │   ├── Router.php                        # khớp method + path, gắn middleware, gọi controller
│   │   │   ├── Request.php                       # bọc query, body, file upload, header; chuẩn hoá input
│   │   │   ├── Response.php                      # trả HTML / JSON / redirect / file + status, header
│   │   │   ├── View.php                          # render template, bọc layout, escape dữ liệu khi in
│   │   │   ├── Session.php                       # phiên đăng nhập, flash message, old input, CSRF token
│   │   │   ├── Config.php                        # đọc config/*.php và biến môi trường từ .env
│   │   │   └── Exceptions/                       # lỗi ở tầng giao thức
│   │   │       ├── HttpException.php             # lỗi HTTP chung (status + message)
│   │   │       ├── NotFoundException.php         # 404
│   │   │       ├── ForbiddenException.php        # 403
│   │   │       └── ValidationException.php       # 422 kèm danh sách lỗi field
│   │   ├── Http/                                 # tầng Application: chỉ lo giao tiếp HTTP
│   │   │   ├── Controllers/
│   │   │   │   ├── Web/                          # trả HTML cho khách truy cập
│   │   │   │   │   ├── HomeController.php        # trang chủ: xe nổi bật, bài mới, loại xe, dịch vụ
│   │   │   │   │   ├── ServiceCatalogController.php  # danh sách + chi tiết dịch vụ
│   │   │   │   │   ├── BookingController.php     # trang đặt xe, trang xác nhận thành công
│   │   │   │   │   ├── PostController.php        # danh sách + chi tiết bài viết
│   │   │   │   │   ├── PageController.php        # giới thiệu, liên hệ (dữ liệu từ bảng settings)
│   │   │   │   │   ├── AuthController.php        # đăng nhập, đăng xuất, đổi mật khẩu
│   │   │   │   │   └── MediaController.php       # phục vụ ảnh từ storage/uploads qua route
│   │   │   │   ├── Api/                          # trả JSON cho fetch từ giao diện
│   │   │   │   │   ├── BookingApiController.php  # tạo đơn đặt xe + kiểm tra trùng lịch
│   │   │   │   │   ├── CarApiController.php      # xe theo loại, chi tiết xe cho modal
│   │   │   │   │   ├── ReviewApiController.php   # khách gửi đánh giá xe
│   │   │   │   │   ├── ContactApiController.php  # form gửi liên hệ
│   │   │   │   │   └── HealthApiController.php   # health check: DB, storage, phiên bản
│   │   │   │   └── Admin/                        # khu quản trị (HTML + thao tác ghi)
│   │   │   │       ├── DashboardController.php   # số đơn, đơn chờ, số xe, số dịch vụ, liên hệ mới
│   │   │   │       ├── BookingController.php     # danh sách, chi tiết, đổi trạng thái, xoá đơn
│   │   │   │       ├── StatisticController.php   # lọc theo ngày, nhóm ngày/tháng/năm, bảng số liệu
│   │   │   │       ├── CarController.php         # CRUD xe, trạng thái sẵn sàng, giá km/ngày, tài xế
│   │   │   │       ├── CarTypeController.php     # CRUD loại xe (4/7/16/29/35–45 chỗ)
│   │   │   │       ├── ServiceController.php     # CRUD dịch vụ
│   │   │   │       ├── PostController.php        # CRUD bài viết + danh mục
│   │   │   │       ├── ContactController.php     # danh sách, chi tiết, đánh dấu đã trả lời
│   │   │   │       ├── UserController.php        # CRUD tài khoản, vai trò, khoá/mở khoá
│   │   │   │       ├── SettingController.php     # sửa hotline, Zalo, Facebook, xem trước
│   │   │   │       ├── UploadController.php      # upload ảnh dùng chung (JPEG/PNG/GIF/WEBP ≤ 5MB)
│   │   │   │       ├── ExportController.php      # xuất Excel thống kê / danh sách đơn
│   │   │   │       ├── ReviewController.php      # duyệt / ẩn / xoá đánh giá
│   │   │   │       └── ProfileController.php     # hồ sơ cá nhân, đổi mật khẩu
│   │   │   ├── Requests/                         # chỉ validate + chuẩn hoá input, không chứa nghiệp vụ
│   │   │   │   ├── Web/
│   │   │   │   │   ├── StoreBookingRequest.php   # SĐT, ngày đón, loại xe, điểm đi/đến
│   │   │   │   │   ├── StoreContactRequest.php   # họ tên, SĐT, nội dung
│   │   │   │   │   ├── StoreReviewRequest.php    # số sao, nội dung, mã đơn (nếu có)
│   │   │   │   │   └── LoginRequest.php          # username/email + password
│   │   │   │   └── Admin/
│   │   │   │       ├── StoreCarRequest.php
│   │   │   │       ├── UpdateCarRequest.php
│   │   │   │       ├── StoreCarTypeRequest.php
│   │   │   │       ├── StoreServiceRequest.php
│   │   │   │       ├── StorePostRequest.php
│   │   │   │       ├── StoreUserRequest.php
│   │   │   │       ├── UpdateUserRequest.php
│   │   │   │       ├── UpdateBookingStatusRequest.php
│   │   │   │       ├── UpdateSettingRequest.php
│   │   │   │       ├── ChangePasswordRequest.php
│   │   │   │       ├── UploadImageRequest.php
│   │   │   │       └── ExportStatisticRequest.php
│   │   │   └── Middleware/
│   │   │       ├── AuthMiddleware.php            # chặn khi chưa đăng nhập, kiểm tra phiên hết hạn
│   │   │       ├── GuestMiddleware.php           # chặn người đã đăng nhập vào trang login
│   │   │       ├── RoleMiddleware.php            # giới hạn theo vai trò admin / doanh nghiệp
│   │   │       ├── CsrfMiddleware.php            # xác thực token với mọi POST/PUT/DELETE
│   │   │       ├── JsonMiddleware.php            # ép response JSON + header cho nhóm route /api
│   │   │       └── SecurityHeadersMiddleware.php # optional – bật khi deploy thật (CSP, X-Frame-Options)
│   │   ├── Domain/                               # nghiệp vụ thuần, không biết HTTP, không biết PDO
│   │   │   ├── Entities/                         # đối tượng dữ liệu, không chứa câu SQL
│   │   │   │   ├── User.php
│   │   │   │   ├── Car.php
│   │   │   │   ├── CarType.php
│   │   │   │   ├── ServicePackage.php
│   │   │   │   ├── Booking.php
│   │   │   │   ├── Post.php
│   │   │   │   ├── Contact.php
│   │   │   │   ├── Review.php
│   │   │   │   ├── Setting.php
│   │   │   │   └── AuditLog.php
│   │   │   ├── Enums/                            # tập giá trị cố định, tránh chuỗi rải rác
│   │   │   │   ├── Role.php                      # admin, business, customer
│   │   │   │   ├── BookingStatus.php             # pending, confirmed, completed, cancelled
│   │   │   │   ├── CarStatus.php                 # available, unavailable
│   │   │   │   └── ContactStatus.php             # new, processing, replied
│   │   │   ├── Contracts/                        # interface repository — Domain chỉ biết hợp đồng
│   │   │   │   ├── UserRepositoryInterface.php
│   │   │   │   ├── CarRepositoryInterface.php
│   │   │   │   ├── CarTypeRepositoryInterface.php
│   │   │   │   ├── ServiceRepositoryInterface.php
│   │   │   │   ├── BookingRepositoryInterface.php
│   │   │   │   ├── PostRepositoryInterface.php
│   │   │   │   ├── ContactRepositoryInterface.php
│   │   │   │   ├── ReviewRepositoryInterface.php
│   │   │   │   ├── SettingRepositoryInterface.php
│   │   │   │   ├── StatisticRepositoryInterface.php
│   │   │   │   └── AuditLogRepositoryInterface.php
│   │   │   ├── Services/                         # nơi duy nhất chứa luật nghiệp vụ
│   │   │   │   ├── AuthService.php               # xác thực, tạo/huỷ phiên, đổi mật khẩu, Google OAuth
│   │   │   │   ├── BookingService.php            # sinh mã đơn, kiểm tra trùng lịch xe, chuyển trạng thái
│   │   │   │   ├── CarService.php                # giá theo km/ngày, ràng buộc khi xoá xe có đơn
│   │   │   │   ├── CarTypeService.php
│   │   │   │   ├── ServiceCatalogService.php     # dịch vụ công khai + sắp xếp hiển thị
│   │   │   │   ├── PostService.php               # bài mới nhất cho trang chủ, ẩn/hiện
│   │   │   │   ├── ContactService.php            # tạo liên hệ, đánh dấu đã trả lời
│   │   │   │   ├── ReviewService.php             # chỉ cho đánh giá khi có đơn hoàn thành
│   │   │   │   ├── SettingService.php            # đọc/ghi hotline, Zalo, Facebook
│   │   │   │   ├── UploadService.php             # kiểm tra loại file, kích thước, sinh tên an toàn
│   │   │   │   ├── AccountService.php            # khoá/xoá tài khoản, chặn xoá admin cuối cùng
│   │   │   │   ├── StatisticService.php          # gom số liệu theo kỳ, tính tổng theo trạng thái
│   │   │   │   └── AuditLogService.php           # ghi vết thao tác quản trị
│   │   │   └── Exceptions/                       # lỗi nghiệp vụ, controller bắt và hiển thị thông báo
│   │   │       ├── BusinessRuleException.php
│   │   │       ├── LastAdminException.php
│   │   │       ├── CarScheduleConflictException.php
│   │   │       └── AccountInUseException.php
│   │   ├── Infrastructure/                       # chi tiết kỹ thuật, thay được mà không sửa Domain
│   │   │   ├── Persistence/
│   │   │   │   ├── Connection.php                # tạo PDO dùng chung từ config/database.php
│   │   │   │   ├── QueryBuilder.php              # optional – chỉ thêm khi có truy vấn lọc động phức tạp
│   │   │   │   ├── Repositories/                 # hiện thực Contracts bằng PDO, trả Entity
│   │   │   │   │   ├── PdoUserRepository.php
│   │   │   │   │   ├── PdoCarRepository.php
│   │   │   │   │   ├── PdoCarTypeRepository.php
│   │   │   │   │   ├── PdoServiceRepository.php
│   │   │   │   │   ├── PdoBookingRepository.php
│   │   │   │   │   ├── PdoPostRepository.php
│   │   │   │   │   ├── PdoContactRepository.php
│   │   │   │   │   ├── PdoReviewRepository.php
│   │   │   │   │   ├── PdoSettingRepository.php
│   │   │   │   │   ├── PdoStatisticRepository.php
│   │   │   │   │   └── PdoAuditLogRepository.php
│   │   │   │   ├── Migrations/                   # file .sql đánh số thứ tự, chạy tay theo thứ tự
│   │   │   │   │   ├── 001_create_users_table.sql
│   │   │   │   │   ├── 002_create_vehicle_types_table.sql
│   │   │   │   │   ├── 003_create_vehicles_table.sql
│   │   │   │   │   ├── 004_create_services_table.sql
│   │   │   │   │   ├── 005_create_bookings_table.sql
│   │   │   │   │   ├── 006_create_posts_table.sql
│   │   │   │   │   ├── 007_create_contacts_table.sql
│   │   │   │   │   ├── 008_create_ratings_table.sql
│   │   │   │   │   ├── 009_create_settings_table.sql
│   │   │   │   │   ├── 010_create_audit_logs_table.sql
│   │   │   │   │   ├── 011_create_admin_sessions_table.sql
│   │   │   │   │   └── 012_create_admin_google_accounts_table.sql
│   │   │   │   │   # Bản gộp đang dùng: thaovy_phpmyadmin_schema.sql ở gốc dự án.
│   │   │   │   └── Seeders/                      # dữ liệu mẫu: admin đầu tiên, loại xe, settings
│   │   │   │       ├── admin_seeder.sql
│   │   │   │       ├── car_type_seeder.sql
│   │   │   │       └── setting_seeder.sql
│   │   │   ├── Auth/
│   │   │   │   ├── SessionGuard.php              # kiểm tra phiên + hạn phiên + tài khoản bị khoá
│   │   │   │   └── GoogleOAuthClient.php         # luồng OAuth cho admin/khách (bật bằng config)
│   │   │   ├── Storage/
│   │   │   │   ├── LocalStorage.php              # ghi/đọc/xoá file trong storage/uploads
│   │   │   │   └── ImageValidator.php            # kiểm tra MIME thật, đuôi file, dung lượng
│   │   │   ├── Mail/
│   │   │   │   └── SmtpMailer.php                # gửi mail qua SMTP cấu hình trong .env
│   │   │   ├── Logging/
│   │   │   │   ├── FileLogger.php                # ghi log ứng dụng theo ngày
│   │   │   │   └── AuditLogger.php               # ghi audit log thao tác quản trị
│   │   │   ├── Excel/
│   │   │   │   └── SpreadsheetExporter.php       # xuất .xlsx bằng thư viện Composer
│   │   │   └── Cache/                            # optional – chỉ tạo khi dashboard/trang chủ chậm
│   │   │       └── FileCache.php
│   │   ├── Support/                              # helper dùng chung, không phụ thuộc tầng nào
│   │   │   ├── helpers.php                       # hàm tiện ích: e(), url(), asset(), old(), csrf_field()
│   │   │   ├── Str.php                           # cắt chuỗi, slug không dấu, sinh mã đơn
│   │   │   ├── Format.php                        # định dạng tiền VNĐ, ngày giờ, số điện thoại
│   │   │   └── Paginator.php                     # tính trang, cửa sổ trang, dữ liệu cho view
│   │   ├── Providers/                            # optional – tách đăng ký binding khi số service tăng
│   │   │   └── optional – chỉ tạo khi bootstrap/app.php quá dài
│   │   └── Views/                                # Presentation: chỉ in dữ liệu đã chuẩn bị
│   │       ├── layouts/
│   │       │   ├── app.php                       # khung trang khách: head, header, footer, vùng nội dung
│   │       │   ├── admin.php                     # khung quản trị: sidebar, topbar, vùng nội dung
│   │       │   └── auth.php                      # khung trang đăng nhập tối giản
│   │       ├── partials/                         # mảnh dùng lại giữa nhiều view
│   │       │   ├── head-meta.php
│   │       │   ├── header.php
│   │       │   ├── footer.php
│   │       │   ├── admin-sidebar.php
│   │       │   ├── flash.php                     # hiển thị thông báo thành công / lỗi
│   │       │   ├── pagination.php
│   │       │   ├── breadcrumb.php
│   │       │   └── csrf-field.php
│   │       ├── web/                              # view khu khách
│   │       │   ├── home/index.php
│   │       │   ├── services/index.php
│   │       │   ├── services/show.php
│   │       │   ├── booking/index.php
│   │       │   ├── booking/success.php
│   │       │   ├── posts/index.php
│   │       │   ├── posts/show.php
│   │       │   ├── about/index.php
│   │       │   ├── contact/index.php
│   │       │   └── auth/login.php
│   │       ├── admin/                            # view khu quản trị, mỗi module một thư mục
│   │       │   ├── dashboard/index.php
│   │       │   ├── bookings/index.php
│   │       │   ├── bookings/show.php
│   │       │   ├── statistics/index.php
│   │       │   ├── cars/index.php
│   │       │   ├── cars/form.php                 # dùng chung cho thêm và sửa
│   │       │   ├── car-types/index.php
│   │       │   ├── services/index.php
│   │       │   ├── services/form.php
│   │       │   ├── posts/index.php
│   │       │   ├── posts/form.php
│   │       │   ├── contacts/index.php
│   │       │   ├── contacts/show.php
│   │       │   ├── users/index.php
│   │       │   ├── users/form.php
│   │       │   ├── reviews/index.php
│   │       │   ├── settings/index.php
│   │       │   └── profile/index.php
│   │       ├── mail/                             # template email HTML (soạn bằng View)
│   │       │   ├── welcome.php
│   │       │   ├── reset-password.php
│   │       │   └── security-alert.php
│   │       └── errors/
│   │           ├── 404.php
│   │           ├── 403.php
│   │           └── 500.php
│   ├── bootstrap/
│   │   └── app.php                               # ráp config, container, provider, nạp routes; trả Application
│   ├── config/                                   # cấu hình thuần dữ liệu, không chứa logic
│   │   ├── app.php                               # tên app, url, timezone, debug, session lifetime
│   │   ├── database.php                          # driver, host, dbname, user, pass (đọc từ .env)
│   │   ├── auth.php                              # vai trò, thời hạn phiên, chính sách mật khẩu
│   │   ├── upload.php                            # MIME cho phép, dung lượng tối đa, thư mục đích
│   │   ├── mail.php                              # SMTP, người gửi, bật/tắt template
│   │   └── google.php                            # optional – client id/secret, redirect uri
│   ├── routes/
│   │   ├── web.php                               # route trang khách (trả HTML)
│   │   ├── admin.php                             # route quản trị, gắn Auth + Role + Csrf
│   │   └── api.php                               # route JSON, gắn Json + Throttle (nếu bật)
│   ├── public/                                   # WEBROOT DUY NHẤT — trỏ VirtualHost vào đây
│   │   ├── index.php                             # single entry point: nạp bootstrap, dispatch, không logic
│   │   ├── .htaccess                             # rewrite mọi request về index.php, chặn file ẩn
│   │   └── assets/                               # tài nguyên tĩnh copy từ frontend/ khi ghép View
│   │       ├── css/                              # CSS đã biên dịch, giữ nguyên cấu trúc tầng
│   │       ├── js/
│   │       ├── vendor/                           # bootstrap, icon, font self-host
│   │       ├── img/
│   │       └── fonts/
│   ├── storage/                                  # dữ liệu ghi runtime — NGOÀI webroot
│   │   ├── logs/                                 # log ứng dụng theo ngày, audit log
│   │   ├── exports/                              # file Excel tạm, xoá sau khi khách tải
│   │   ├── cache/                                # optional – cache file
│   │   ├── sessions/                             # optional – khi không dùng session mặc định của PHP
│   │   └── uploads/                              # ảnh người dùng upload, phục vụ qua MediaController
│   │       ├── cars/
│   │       ├── posts/
│   │       ├── services/
│   │       ├── avatars/
│   │       └── tmp/                              # file chờ xác thực, dọn định kỳ
│   ├── tests/
│   │   ├── bootstrap.php                         # nạp ứng dụng ở chế độ test
│   │   ├── Unit/                                 # test Service, Entity, PriceCalculator
│   │   └── Feature/                              # test route → controller → repository
│   ├── vendor/                                   # thư viện Composer — không commit
│   ├── .env.example                              # mẫu biến môi trường, commit; .env thật không commit
│   ├── .gitignore                                # bỏ vendor/, .env, storage/*
│   ├── composer.json                             # PSR-4: App\ => app/, script autoload helper
│   └── README.md                                 # cách chạy local + quy ước
├── frontend/                                     # bộ UI tĩnh độc lập (xem cau-truc-frontend.md)
└── docs/                                         # tài liệu kiến trúc, quy ước, kịch bản demo
```

---

## Phần 2 — Trách nhiệm và luật phụ thuộc một chiều

| Đường dẫn | Trách nhiệm | Được phép phụ thuộc vào |
|---|---|---|
| `public/index.php` | Điểm vào duy nhất: nạp autoload, bootstrap, nhận request, gửi response | `bootstrap/app.php`, `app/Core/Application.php` |
| `public/.htaccess` | Đưa mọi URL về `index.php`, chặn truy cập file ẩn và thư mục hệ thống | Không (cấu hình máy chủ) |
| `public/assets/*` | File tĩnh: CSS, JS, ảnh, font | Không (chỉ được trình duyệt tải) |
| `bootstrap/app.php` | Ráp config, container, repository binding, nạp file route | `config/*`, `routes/*`, `app/Core`, `app/Infrastructure` |
| `config/*.php` | Trả mảng cấu hình | `app/Core/Config` (đọc `.env`) |
| `routes/web.php`, `api.php`, `admin.php` | Khai báo method, path, middleware, controller action; không chứa logic | Controller, Middleware |
| `app/Core/*` | Router, Request, Response, View, Session, Container, Config | Thư viện PHP chuẩn, `app/Support` |
| `app/Http/Middleware/*` | Chặn/cho request đi tiếp; xác thực, phân quyền, CSRF | `Core/Request`, `Core/Response`, `Core/Session`, `Domain/Services/AuthService` |
| `app/Http/Requests/*` | Validate và chuẩn hoá input, trả lỗi 422 | `Core/Request`, `Support` |
| `app/Http/Controllers/Web/*` | Nhận request, gọi service, chọn view, gắn dữ liệu đã chuẩn bị | `Http/Requests`, `Domain/Services`, `Core/View`, `Core/Response` |
| `app/Http/Controllers/Api/*` | Nhận request, gọi service, trả JSON + status code | `Http/Requests`, `Domain/Services`, `Core/Response` |
| `app/Http/Controllers/Admin/*` | Điều phối CRUD quản trị, gọi service, render view admin | `Http/Requests`, `Domain/Services`, `Core/View`, `Core/Session` |
| `app/Domain/Entities/*` | Giữ dữ liệu và hành vi nhỏ của chính nó; không chứa SQL, không biết HTTP | `Domain/Enums` |
| `app/Domain/Enums/*` | Tập giá trị cố định của trạng thái, vai trò | Không |
| `app/Domain/Contracts/*` | Khai báo hợp đồng truy vấn mà Domain cần | `Domain/Entities` |
| `app/Domain/Services/*` | Toàn bộ luật nghiệp vụ: trùng lịch, admin cuối cùng, mã đơn, trạng thái | `Domain/Entities`, `Domain/Enums`, `Domain/Contracts`, `Domain/Exceptions`, `Support` |
| `app/Domain/Exceptions/*` | Mô tả lỗi nghiệp vụ có thể hiển thị cho người dùng | Không |
| `app/Infrastructure/Persistence/Connection.php` | Tạo kết nối PDO, cấu hình lỗi, charset | `Core/Config` |
| `app/Infrastructure/Persistence/Repositories/*` | Thực thi SQL, ánh xạ dòng dữ liệu thành Entity | `Domain/Contracts`, `Domain/Entities`, `Connection` |
| `app/Infrastructure/Persistence/Migrations/*`, `Seeders/*` | Tạo bảng, dữ liệu mẫu; chạy tay theo thứ tự | Không |
| `app/Infrastructure/Auth/*` | Kiểm tra phiên, hạn phiên, OAuth Google | `Core/Session`, `Core/Config`, `Domain/Contracts` |
| `app/Infrastructure/Storage/*`, `Excel/*`, `Mail/*`, `Logging/*` | Ghi file, xuất Excel, gửi mail, ghi log | `Core/Config`, `Domain/Entities` |
| `app/Infrastructure/Cache/*` | Đọc/ghi cache file | `Core/Config` |
| `app/Support/*` | Hàm tiện ích thuần, không trạng thái | Thư viện PHP chuẩn |
| `app/Views/layouts/*`, `partials/*`, `web/*`, `admin/*`, `mail/*`, `errors/*` | In HTML từ biến đã chuẩn bị, escape mọi dữ liệu động | `View`, `Support/helpers.php` (chỉ hàm định dạng) |
| `storage/*` | Nơi chứa dữ liệu runtime; ứng dụng ghi, không ai require | Không |
| `tests/*` | Kiểm thử tự động tầng Domain và luồng HTTP | Tất cả tầng, chỉ trong môi trường test |
| `vendor/*` | Thư viện bên thứ ba | Không |

**Luật bắt buộc:** Route → Middleware → Controller → Service → Repository → Entity. Không được đảo ngược. View không gọi DB, không gọi Service; Entity không biết Controller tồn tại.

---

## Phần 3 — Luồng xử lý một request điển hình (khách gửi form đặt xe)

1. Trình duyệt gửi `POST /dat-xe` (hoặc fetch `POST /api/bookings`).
2. Apache đọc `.htaccess`, chuyển mọi request về `public/index.php`; không file nội bộ nào bị gọi trực tiếp.
3. `index.php` nạp Composer autoload rồi `bootstrap/app.php`: đọc `config/*` + `.env`, đăng ký binding repository, nạp file route.
4. `Router` khớp method + path, thu thập danh sách middleware của route: `JsonMiddleware` (nếu API), `CsrfMiddleware`, `ThrottleMiddleware` (nếu bật).
5. `Request` gom dữ liệu, `StoreBookingRequest` validate (SĐT, ngày đón không ở quá khứ, loại xe tồn tại); lỗi trả 422 kèm field.
6. `BookingController` gọi `BookingService::create()`; service kiểm tra trùng lịch xe qua `CarRepository`, sinh mã đơn, gọi `BookingRepository::save()`.
7. Repository chạy truy vấn qua PDO, trả Entity `Booking`; service trả kết quả cho controller.
8. Controller chọn đầu ra: Web render `web/booking/success.php` qua `View` (bọc layout `app.php`); API trả JSON kèm `201`. Lỗi nghiệp vụ (`CarScheduleConflictException`) được bắt và chuyển thành thông báo 409 hoặc flash message.

---

## Phần 4 — File bắt buộc để chạy "Hello World" và file tạo sau

| Nhóm | Đường dẫn | Ghi chú |
|---|---|---|
| Bắt buộc | `public/index.php` | Điểm vào duy nhất |
| Bắt buộc | `public/.htaccess` | Rewrite + chặn file ẩn |
| Bắt buộc | `composer.json` | Khai báo PSR-4 `App\ => app/` và autoload helper |
| Bắt buộc | `.env.example`, `.env` | Cấu hình môi trường; `.env` không commit |
| Bắt buộc | `bootstrap/app.php` | Ráp ứng dụng |
| Bắt buộc | `config/app.php`, `config/database.php` | Tên app, URL, kết nối CSDL |
| Bắt buộc | `app/Core/Application.php`, `Container.php`, `Router.php`, `Request.php`, `Response.php`, `View.php`, `Config.php`, `Session.php` | Hạ tầng tối thiểu để dispatch và render |
| Bắt buộc | `routes/web.php` | Một route `/` trỏ tới `HomeController@index` |
| Bắt buộc | `app/Http/Controllers/Web/HomeController.php` | Trả về view trang chủ |
| Bắt buộc | `app/Views/layouts/app.php`, `app/Views/web/home/index.php` | Chuỗi render tối thiểu |
| Bắt buộc | `storage/logs/` (quyền ghi) | Nơi ghi log lỗi |
| Tạo sau | `app/Domain/*`, `app/Infrastructure/*` cho từng module | Thêm dần khi làm module tương ứng |
| Tạo sau | `app/Http/Controllers/Api/*`, `routes/api.php`, `JsonMiddleware` | Chỉ khi trang cần gọi fetch JSON |
| Tạo sau | `app/Http/Middleware/AuthMiddleware`, `RoleMiddleware`, `CsrfMiddleware` | Ngay khi làm đăng nhập (module B) |
| Tạo sau | `app/Infrastructure/Persistence/Migrations`, `Seeders` | Trước khi làm module dữ liệu đầu tiên |
| Tạo sau | `app/Infrastructure/Auth/GoogleOAuthClient.php`, `config/google.php` | Khi triển khai đăng nhập Google |
| Tạo sau | `app/Infrastructure/Excel/*` | Khi làm xuất báo cáo |
| Tạo sau | `app/Infrastructure/Mail/*`, `app/Views/mail/*` | Khi bật email chào mừng / đặt lại mật khẩu |
| Tạo sau | `app/Infrastructure/Cache/*`, `app/Providers/*` | Optional – chỉ khi đo được vấn đề hiệu năng hoặc bootstrap quá dài |
| Tạo sau | `tests/*` | Song song khi chốt luật nghiệp vụ (admin cuối cùng, trùng lịch) |

---

## Phần 5 — 7 lỗi cấu trúc thường gặp ở đồ án và cách cấu trúc trên phòng tránh

| Lỗi thường gặp | Hậu quả | Cấu trúc trên phòng tránh thế nào |
|---|---|---|
| Nhét toàn bộ xử lý vào Controller (validate, SQL, gửi mail, render) | Controller dài hàng trăm dòng, không test được | Controller chỉ điều phối; validate ở `Http/Requests`, luật ở `Domain/Services`, SQL ở `Repositories` |
| Model vừa là Entity vừa là query builder | Entity dính PDO, đổi CSDL là vỡ nghiệp vụ | `Domain/Entities` thuần dữ liệu; `Contracts` khai báo truy vấn; `Pdo*Repository` hiện thực |
| View gọi thẳng PDO hoặc Service | Khó đổi giao diện, lộ lỗi SQL ra trang | View chỉ nhận biến đã chuẩn bị từ Controller, chỉ dùng helper định dạng |
| Dùng `require` trực tiếp file trong `app/` từ webroot | Lộ mã nguồn, phá single entry point | Chỉ `public/` nằm trong webroot, mọi request qua `public/index.php` |
| Trộn HTML và JSON trong cùng một controller | Route khó bảo vệ, phân quyền chắp vá | Tách `Controllers/Web`, `Controllers/Api`, `Controllers/Admin`; `JsonMiddleware` cho `/api` |
| Upload ảnh vào `public/uploads` và tin vào đuôi file | Upload shell, ghi đè file, XSS qua SVG | `storage/uploads` ngoài webroot, `ImageValidator` kiểm tra MIME thật + dung lượng, phục vụ qua `MediaController` |
| Tạo sẵn hàng loạt thư mục "cho đủ" (Events, Jobs, DTO, Cache…) | Người đọc không biết thư mục nào thật sự dùng | Mỗi thư mục gắn một chức năng trong danh sách; phần chưa cần ghi rõ "optional – chỉ tạo khi…" |
