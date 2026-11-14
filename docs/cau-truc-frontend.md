# Cấu trúc thư mục Frontend — bộ UI tĩnh (HTML + Bootstrap + JS thuần)

## Bối cảnh đã chốt

| Hạng mục | Giá trị áp dụng |
|---|---|
| Dự án | Hệ thống quản lý nhà xe & đặt xe trực tuyến |
| Vai trò frontend | Bộ template tĩnh độc lập, review/demo trước, sau đó ghép vào tầng View của backend PHP MVC |
| Công nghệ | HTML5, Bootstrap 5.x self-host (không CDN), CSS tự viết, JavaScript ES Module thuần |
| Không dùng | React/Vue/jQuery, bundler, build step, SPA |
| Trang | 9 trang khách + 19 trang quản trị |

## Nguyên tắc xuyên suốt

- Ba loại tài nguyên tách tuyệt đối: markup (`.html`) – style (`.css`) – behavior (`.js`). Không thẻ `style`, không thuộc tính `style=""`, không `<script>` nội tuyến, không `onclick`/`onchange` trong HTML.
- Hành vi gắn qua thuộc tính `data-*`: mỗi vùng có `data-component` mang tên component; file JS của component tự tìm phần tử theo thuộc tính đó, đọc tham số qua các `data-*` đi kèm (ví dụ `data-modal-target`, `data-endpoint`, `data-page-size`) rồi khởi tạo. Không có id/handler viết tay trong trang.
- Mỗi trang là một thư mục riêng có `index.html`, URL hiển thị không có `.html`.
- Không lặp markup: phần xuất hiện từ hai trang trở lên bắt buộc nằm trong `components/`.
- Đặt tên: thư mục và file kebab-case, không dấu, không khoảng trắng; class CSS tự viết theo BEM, tận dụng utility Bootstrap; file JS trùng tên component/trang.
- Mỗi trang chỉ nạp CSS/JS của component mà trang đó dùng, cộng file riêng của trang.

> Giới hạn cần nói rõ với hội đồng: ẩn đuôi `.html` chỉ làm đẹp URL, không che được HTML/CSS/JS khỏi DevTools. Mọi logic và dữ liệu nhạy cảm (giá, trạng thái đơn, phân quyền) phải xử lý ở backend; frontend chỉ hiển thị.

---

## Phần 1 — Cây thư mục đầy đủ

```text
DuAn_QuanLyNhaXe/
├── frontend/                                     # bộ UI tĩnh độc lập, chạy được offline
│   ├── index.html                                # chỉ chuyển hướng về /home/; không chứa nội dung nghiệp vụ
│   ├── .htaccess                                 # DirectoryIndex + rewrite ẩn .html (mô tả ở Phần 4)
│   ├── layouts/                                  # khung trang dùng lại, chứa placeholder có tên
│   │   ├── base.html                             # khung khách: vùng head-meta, site-header, content, site-footer
│   │   ├── auth.html                             # khung đăng nhập: vùng thương hiệu, vùng form, vùng thông báo
│   │   └── dashboard.html                        # khung quản trị: vùng sidebar, topbar, content, toast
│   ├── components/                               # mỗi component là bộ 3 file cùng tên: .html | .css | .js
│   │   ├── site-header/                          # logo, menu chính, nút đăng nhập; dùng mọi trang khách
│   │   │   ├── site-header.html
│   │   │   ├── site-header.css
│   │   │   └── site-header.js                    # đánh dấu menu đang active theo URL
│   │   ├── mobile-menu/                          # menu trượt cho màn hình nhỏ + nút hamburger
│   │   ├── site-footer/                          # cột liên kết, hotline, mạng xã hội
│   │   ├── floating-contact/                     # nút nổi gọi điện, Zalo, Facebook
│   │   ├── breadcrumb/                           # đường dẫn phân cấp
│   │   ├── page-hero/                            # banner tiêu đề trang (ảnh nền + tiêu đề + mô tả)
│   │   ├── car-card/                             # thẻ xe: ảnh, loại, giá km/ngày, trạng thái, nút chi tiết
│   │   ├── car-detail-modal/                     # modal chi tiết xe: giá, mô tả, tài xế, bảo hiểm, đánh giá
│   │   ├── service-card/                         # thẻ dịch vụ: ảnh, tên, mô tả ngắn, giá
│   │   ├── post-card/                            # thẻ bài viết: ảnh, tiêu đề, ngày, danh mục
│   │   ├── booking-form/                         # form đặt xe dùng chung trang chủ + trang đặt xe
│   │   ├── contact-info/                         # khối hotline, Zalo, email, địa chỉ, giờ làm việc, bản đồ
│   │   ├── empty-state/                          # trạng thái "chưa có dữ liệu" cho danh sách
│   │   ├── admin-sidebar/                        # menu dọc quản trị, nhóm theo module
│   │   ├── admin-topbar/                         # breadcrumb quản trị, thông báo, menu tài khoản
│   │   ├── stat-card/                            # ô số liệu dashboard (nhãn, số, xu hướng)
│   │   ├── data-table/                           # bảng dữ liệu + vùng đầu bảng + trạng thái rỗng
│   │   ├── table-toolbar/                        # ô tìm kiếm, bộ lọc nhanh, nút thêm mới / xuất Excel
│   │   ├── pagination/                           # phân trang danh sách
│   │   ├── filter-bar/                           # lọc theo khoảng ngày, trạng thái, loại xe
│   │   ├── form-field/                           # nhãn + ô nhập + dòng lỗi + gợi ý, dùng cho mọi form
│   │   ├── image-uploader/                       # chọn ảnh, xem trước, hiển thị giới hạn 5MB
│   │   ├── modal/                                # modal nền dùng chung (kích thước, header, footer)
│   │   ├── confirm-dialog/                       # hộp xác nhận xoá / đổi trạng thái
│   │   ├── toast/                                # thông báo nổi góc màn hình
│   │   ├── alert/                                # khối thông báo trong trang (thành công, lỗi, cảnh báo)
│   │   └── chart-card/                           # optional – chỉ tạo khi làm biểu đồ thống kê
│   ├── pages/                                    # mỗi trang một thư mục, file mặc định là index.html
│   │   ├── home/index.html                       # trang chủ: hero, dịch vụ, loại xe, xe nổi bật, bài mới
│   │   ├── services/index.html                   # danh sách dịch vụ + lọc loại xe + danh sách xe sẵn sàng
│   │   ├── services/detail/index.html            # chi tiết dịch vụ
│   │   ├── booking/index.html                    # form đặt xe, chọn xe theo loại
│   │   ├── booking/success/index.html            # xác nhận đặt thành công + mã đơn
│   │   ├── posts/index.html                      # 6 bài mới nhất
│   │   ├── posts/detail/index.html               # chi tiết bài viết
│   │   ├── about/index.html                      # giới thiệu nhà xe
│   │   ├── contact/index.html                    # liên hệ: thông tin, giờ làm việc, bản đồ
│   │   ├── auth/login/index.html                 # đăng nhập quản trị
│   │   └── admin/
│   │       ├── dashboard/index.html              # tổng đơn, chờ xác nhận, số xe, dịch vụ, liên hệ mới
│   │       ├── bookings/index.html               # danh sách đơn + lọc trạng thái + xoá
│   │       ├── bookings/detail/index.html        # chi tiết đơn + cập nhật trạng thái
│   │       ├── statistics/index.html             # thống kê theo ngày/tháng/năm + xuất Excel
│   │       ├── cars/index.html                   # danh sách xe + trạng thái sẵn sàng
│   │       ├── cars/form/index.html              # thêm/sửa xe: giá km, giá ngày, tài xế, bảo hiểm, ảnh
│   │       ├── car-types/index.html              # quản lý loại xe 4/7/16/29/35–45 chỗ
│   │       ├── services/index.html               # danh sách dịch vụ
│   │       ├── services/form/index.html          # thêm/sửa dịch vụ
│   │       ├── posts/index.html                  # danh sách bài viết
│   │       ├── posts/form/index.html             # thêm/sửa bài viết
│   │       ├── contacts/index.html               # danh sách liên hệ
│   │       ├── contacts/detail/index.html        # chi tiết liên hệ + đánh dấu đã trả lời
│   │       ├── users/index.html                  # danh sách tài khoản + vai trò + khoá/mở khoá
│   │       ├── users/form/index.html             # thêm/sửa tài khoản
│   │       ├── reviews/index.html                # duyệt/ẩn đánh giá xe
│   │       ├── settings/index.html               # sửa hotline, Zalo, Facebook + xem trước
│   │       └── profile/index.html                # hồ sơ + đổi mật khẩu
│   └── assets/
│       ├── css/                                  # chia tầng theo thứ tự phụ thuộc
│       │   ├── base/                             # nền tảng, không phụ thuộc tầng nào
│       │   │   ├── variables.css                 # biến màu, khoảng cách, font, breakpoint
│       │   │   ├── reset.css                     # chuẩn hoá mặc định trình duyệt
│       │   │   ├── typography.css                # thang chữ, tiêu đề, đoạn văn
│       │   │   └── utilities.css                 # vài utility bổ sung ngoài Bootstrap
│       │   ├── layout/                           # khung trang
│       │   │   ├── base.css                      # bố cục chung, vùng nội dung, footer sticky
│       │   │   ├── auth.css
│       │   │   └── dashboard.css                 # sidebar + topbar + vùng nội dung quản trị
│       │   ├── components/                       # một file CSS cho mỗi component cùng tên
│       │   │   ├── site-header.css
│       │   │   ├── mobile-menu.css
│       │   │   ├── site-footer.css
│       │   │   ├── floating-contact.css
│       │   │   ├── breadcrumb.css
│       │   │   ├── page-hero.css
│       │   │   ├── car-card.css
│       │   │   ├── car-detail-modal.css
│       │   │   ├── service-card.css
│       │   │   ├── post-card.css
│       │   │   ├── booking-form.css
│       │   │   ├── contact-info.css
│       │   │   ├── empty-state.css
│       │   │   ├── admin-sidebar.css
│       │   │   ├── admin-topbar.css
│       │   │   ├── stat-card.css
│       │   │   ├── data-table.css
│       │   │   ├── table-toolbar.css
│       │   │   ├── pagination.css
│       │   │   ├── filter-bar.css
│       │   │   ├── form-field.css
│       │   │   ├── image-uploader.css
│       │   │   ├── modal.css
│       │   │   ├── confirm-dialog.css
│       │   │   ├── toast.css
│       │   │   ├── alert.css
│       │   │   └── chart-card.css
│       │   └── pages/                            # chỉ quy tắc riêng của từng trang
│       │       ├── home.css
│       │       ├── services.css
│       │       ├── booking.css
│       │       ├── posts.css
│       │       ├── about.css
│       │       ├── contact.css
│       │       ├── auth.css
│       │       ├── dashboard.css
│       │       ├── bookings.css
│       │       ├── statistics.css
│       │       ├── cars.css
│       │       ├── car-types.css
│       │       ├── posts-admin.css
│       │       ├── contacts-admin.css
│       │       ├── users.css
│       │       ├── settings.css
│       │       └── reviews.css
│       ├── js/                                   # chia tầng: vendor → core → components → pages
│       │   ├── vendor/                           # nạp thư viện, không sửa nội dung
│       │   │   ├── bootstrap-init.js             # kích hoạt tooltip, dropdown, modal của Bootstrap
│       │   │   └── chart-init.js                 # optional – chỉ khi dùng chart-card
│       │   ├── core/                             # tiện ích dùng chung, không phụ thuộc component
│       │   │   ├── http.js                       # gọi fetch, gắn CSRF, xử lý 401/422/500
│       │   │   ├── format.js                     # tiền VNĐ, ngày, số điện thoại
│       │   │   ├── validate.js                   # luật validate phía client, hiển thị lỗi field
│       │   │   ├── dom.js                        # truy vấn theo data-*, tạo phần tử, debounce
│       │   │   ├── modal.js                      # mở/đóng modal, focus trap, điều khiển qua data-*
│       │   │   ├── toast.js                      # hàng đợi thông báo nổi
│       │   │   ├── session.js                    # đọc thông tin người đăng nhập cho demo tĩnh
│       │   │   └── component-loader.js           # nạp component .html khi chạy demo tĩnh bằng fetch
│       │   ├── components/                       # mỗi component một file, trùng tên với component
│       │   │   ├── site-header.js
│       │   │   ├── mobile-menu.js
│       │   │   ├── floating-contact.js
│       │   │   ├── car-card.js
│       │   │   ├── car-detail-modal.js
│       │   │   ├── booking-form.js               # validate + gửi đơn, hiện mã đơn
│       │   │   ├── data-table.js                 # sắp xếp, chọn dòng, trạng thái rỗng
│       │   │   ├── table-toolbar.js              # debounce tìm kiếm, đồng bộ bộ lọc lên URL
│       │   │   ├── pagination.js
│       │   │   ├── filter-bar.js
│       │   │   ├── form-field.js
│       │   │   ├── image-uploader.js             # kiểm tra loại file + 5MB, xem trước
│       │   │   ├── confirm-dialog.js
│       │   │   ├── toast.js
│       │   │   ├── alert.js
│       │   │   ├── admin-sidebar.js              # thu gọn, đánh dấu mục active
│       │   │   ├── admin-topbar.js
│       │   │   ├── stat-card.js                  # optional – đếm số khi tải trang
│       │   │   └── chart-card.js                 # optional
│       │   └── pages/                            # chỉ hành vi đặc thù của trang
│       │       ├── home.js                       # scroll animation, tải xe nổi bật
│       │       ├── services.js                   # lọc loại xe, mở modal chi tiết
│       │       ├── booking.js                    # chọn xe theo loại, gửi đơn
│       │       ├── posts.js
│       │       ├── contact.js
│       │       ├── auth.js                       # hiện lỗi đăng nhập, chặn submit rỗng
│       │       ├── dashboard.js                  # tải số liệu tổng quan
│       │       ├── bookings.js                   # đổi trạng thái, xoá đơn
│       │       ├── statistics.js                 # đổi kỳ, xuất Excel
│       │       ├── cars.js                       # đổi trạng thái sẵn sàng, xoá
│       │       ├── car-types.js
│       │       ├── car-form.js                   # validate giá, upload ảnh
│       │       ├── posts-admin.js
│       │       ├── contacts-admin.js
│       │       ├── users.js
│       │       ├── settings.js                   # xem trước thay đổi
│       │       └── reviews.js
│       ├── vendor/                               # thư viện self-host, không chỉnh sửa
│       │   ├── bootstrap/                        # bootstrap.min.css, bootstrap.bundle.min.js
│       │   └── bootstrap-icons/                  # CSS icon + file font đi kèm
│       ├── fonts/                                # font chữ self-host (woff2)
│       └── img/                                  # ảnh theo module, không đặt lẫn
│           ├── common/                           # logo, favicon, ảnh chia sẻ mặc định
│           ├── home/                             # banner, ảnh hero
│           ├── cars/                             # ảnh xe
│           ├── services/                         # ảnh dịch vụ
│           └── posts/                            # ảnh bài viết
└── docs/                                         # tài liệu kiến trúc
```

**Cơ chế nhúng component khi ghép vào backend:** `layouts/*.html` là khuôn, mỗi component được chèn vào đúng vùng placeholder đã đặt tên (vùng header, vùng sidebar, vùng nội dung, vùng thông báo). Hai cách dùng, chỉ chọn một cho mỗi môi trường:

1. **Demo tĩnh:** `component-loader.js` đọc thuộc tính `data-component` trên vùng chứa, tải file `.html` cùng tên trong `components/`, chèn vào vị trí, rồi nạp file `.js` của component đó.
2. **Ghép vào PHP View:** backend thay mỗi vùng chứa bằng lệnh include file `components/<ten>/<ten>.html`; CSS/JS chuyển sang `public/assets/` và nạp theo danh sách ở Phần 4. Không chép markup vào view.

---

## Phần 2 — Trách nhiệm và nơi sử dụng

| Đường dẫn | Trách nhiệm | Được dùng ở đâu / nạp khi nào |
|---|---|---|
| `index.html` | Chuyển hướng về `/home/` | Chỉ khi mở thư mục gốc; không phải một trang nghiệp vụ |
| `.htaccess` | DirectoryIndex `index.html` + rewrite ẩn đuôi | Cấu hình Apache; mọi request |
| `layouts/base.html` | Khung trang khách: head chung, header, content, footer | 9 trang khách |
| `layouts/auth.html` | Khung đăng nhập tối giản, không header/footer | `/auth/login/` |
| `layouts/dashboard.html` | Khung quản trị: sidebar, topbar, content, vùng toast | 19 trang `/admin/*` |
| `components/site-header/*` | Logo, menu, trạng thái đăng nhập | Mọi trang khách; nạp qua layout `base` |
| `components/mobile-menu/*` | Menu trượt cho màn hình nhỏ | Mọi trang khách dưới breakpoint md |
| `components/site-footer/*` | Liên kết chân trang + hotline | Mọi trang khách |
| `components/floating-contact/*` | Nút nổi gọi điện / Zalo / Facebook | Trang chủ, dịch vụ, đặt xe, bài viết, giới thiệu, liên hệ |
| `components/breadcrumb/*` | Đường dẫn phân cấp | Trang con: chi tiết dịch vụ, chi tiết bài viết, toàn bộ admin |
| `components/page-hero/*` | Banner tiêu đề trang | Dịch vụ, bài viết, giới thiệu, liên hệ, đặt xe |
| `components/car-card/*` | Thẻ xe trong lưới | Trang chủ, dịch vụ, kết quả chọn xe |
| `components/car-detail-modal/*` | Modal chi tiết xe (giá, tài xế, bảo hiểm, đánh giá) | Trang chủ, dịch vụ, đặt xe |
| `components/service-card/*` | Thẻ dịch vụ | Trang chủ, danh sách dịch vụ |
| `components/post-card/*` | Thẻ bài viết | Trang chủ, danh sách bài viết |
| `components/booking-form/*` | Form đặt xe + chọn xe theo loại | Trang chủ (bản rút gọn) và `/booking/` |
| `components/contact-info/*` | Hotline, Zalo, email, địa chỉ, giờ làm việc, bản đồ | Trang chủ, liên hệ, footer |
| `components/empty-state/*` | Trạng thái danh sách rỗng | Mọi danh sách có phân trang |
| `components/admin-sidebar/*` | Menu quản trị theo module + thu gọn | Mọi trang admin |
| `components/admin-topbar/*` | Breadcrumb quản trị, thông báo, menu tài khoản | Mọi trang admin |
| `components/stat-card/*` | Ô số liệu tổng quan | Dashboard, thống kê |
| `components/data-table/*` | Bảng dữ liệu + header + trạng thái rỗng | 11 trang danh sách admin |
| `components/table-toolbar/*` | Tìm kiếm, nút thêm mới, xuất Excel | Các trang danh sách admin |
| `components/pagination/*` | Phân trang | Các trang danh sách admin |
| `components/filter-bar/*` | Lọc ngày, trạng thái, loại xe | Đơn đặt, thống kê, xe, liên hệ |
| `components/form-field/*` | Một field chuẩn: nhãn, input, lỗi | Mọi form (đặt xe, đăng nhập, CRUD) |
| `components/image-uploader/*` | Chọn ảnh, xem trước, kiểm tra 5MB | Form xe, dịch vụ, bài viết, hồ sơ |
| `components/modal/*` | Modal nền dùng chung | Chi tiết xe, xác nhận, cài đặt xem trước |
| `components/confirm-dialog/*` | Hộp xác nhận hành động nguy hiểm | Xoá đơn, xoá xe, khoá tài khoản |
| `components/toast/*` | Thông báo nổi sau thao tác | Mọi trang có thao tác ghi |
| `components/alert/*` | Thông báo trong trang | Đăng nhập lỗi, form lỗi, cảnh báo dữ liệu |
| `components/chart-card/*` | Khung biểu đồ | Optional – chỉ khi làm biểu đồ thống kê |
| `pages/<module>/<page>/index.html` | Markup riêng của một trang, không chứa CSS/JS | Mở trực tiếp theo URL không đuôi |
| `assets/css/base/*` | Biến màu, reset, typography, utility | Nạp đầu tiên ở mọi trang |
| `assets/css/layout/*` | Bố cục khung | Mỗi khung nạp file tương ứng |
| `assets/css/components/*` | Style riêng của component | Chỉ trang dùng component đó |
| `assets/css/pages/*` | Style đặc thù trang | Chỉ trang tương ứng |
| `assets/js/vendor/*` | Kích hoạt thư viện | Sau JS vendor, trước core |
| `assets/js/core/*` | Tiện ích dùng chung | Ngay sau vendor ở mọi trang |
| `assets/js/components/*` | Hành vi component | Chỉ trang dùng component đó |
| `assets/js/pages/*` | Hành vi trang | Cuối cùng, sau component |
| `assets/vendor/bootstrap/*` | Bootstrap 5 self-host | Nạp đầu chuỗi CSS/JS |
| `assets/vendor/bootstrap-icons/*` | Bộ icon self-host | Ngay sau Bootstrap CSS |
| `assets/fonts/*` | Font chữ self-host | Khai báo trong `base/typography.css` |
| `assets/img/<module>/*` | Ảnh theo module | Chỉ trang thuộc module đó |

---

## Phần 3 — Bảng ánh xạ trang

| Tên trang | URL không đuôi | Layout | Component sử dụng | CSS/JS nạp kèm |
|---|---|---|---|---|
| Trang chủ | `/home/` | base | site-header, mobile-menu, page-hero, service-card, car-card, car-detail-modal, post-card, booking-form, contact-info, floating-contact, site-footer | base/*, layout/base, components: site-header, mobile-menu, page-hero, service-card, car-card, car-detail-modal, post-card, booking-form, contact-info, floating-contact, site-footer, pages/home — JS: vendor, core/*, các component trên, pages/home |
| Danh sách dịch vụ | `/services/` | base | site-header, mobile-menu, page-hero, breadcrumb, service-card, filter-bar, car-card, car-detail-modal, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/services; JS core + components + pages/services |
| Chi tiết dịch vụ | `/services/detail/` | base | site-header, mobile-menu, breadcrumb, page-hero, car-card, car-detail-modal, booking-form, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/services; JS core + components + pages/services |
| Đặt xe | `/booking/` | base | site-header, mobile-menu, breadcrumb, car-card, car-detail-modal, booking-form, contact-info, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/booking; JS core + components + pages/booking |
| Đặt thành công | `/booking/success/` | base | site-header, mobile-menu, alert, contact-info, floating-contact, site-footer | base/*, layout/base, components: alert, contact-info, floating-contact, site-footer, pages/booking; JS core + components/alert + pages/booking |
| Danh sách bài viết | `/posts/` | base | site-header, mobile-menu, page-hero, breadcrumb, post-card, pagination, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/posts; JS core + components + pages/posts |
| Chi tiết bài viết | `/posts/detail/` | base | site-header, mobile-menu, breadcrumb, post-card (bài liên quan), floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/posts; JS core + components/post-card + pages/posts |
| Giới thiệu | `/about/` | base | site-header, mobile-menu, page-hero, stat-card, contact-info, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/about; JS core + components + pages/about |
| Liên hệ | `/contact/` | base | site-header, mobile-menu, page-hero, breadcrumb, contact-info, floating-contact, site-footer | base/*, layout/base, components tương ứng, pages/contact; JS core + components + pages/contact |
| Đăng nhập | `/auth/login/` | auth | form-field, alert | base/*, layout/auth, components: form-field, alert, pages/auth; JS core/validate + core/http + pages/auth |
| Dashboard | `/admin/dashboard/` | dashboard | admin-sidebar, admin-topbar, stat-card, data-table, empty-state, toast | base/*, layout/dashboard, components tương ứng, pages/dashboard; JS vendor + core + components + pages/dashboard |
| Quản lý đặt lịch | `/admin/bookings/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, filter-bar, table-toolbar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/bookings; JS core + components + pages/bookings |
| Chi tiết đơn | `/admin/bookings/detail/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/bookings; JS core + components + pages/bookings |
| Thống kê đơn | `/admin/statistics/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, filter-bar, stat-card, data-table, chart-card, table-toolbar, toast | base/*, layout/dashboard, components tương ứng, pages/statistics; JS vendor (chart) + core + components + pages/statistics |
| Quản lý xe | `/admin/cars/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, table-toolbar, filter-bar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/cars; JS core + components + pages/cars |
| Thêm/sửa xe | `/admin/cars/form/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, image-uploader, alert, toast | base/*, layout/dashboard, components tương ứng, pages/cars; JS core/validate + components + pages/car-form |
| Loại xe | `/admin/car-types/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, table-toolbar, data-table, modal, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/car-types; JS core + components + pages/car-types |
| Quản lý dịch vụ | `/admin/services/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, table-toolbar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/services; JS core + components + pages/services |
| Thêm/sửa dịch vụ | `/admin/services/form/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, image-uploader, toast | base/*, layout/dashboard, components tương ứng, pages/services; JS core/validate + pages/services |
| Quản lý bài viết | `/admin/posts/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, table-toolbar, filter-bar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/posts-admin; JS core + components + pages/posts-admin |
| Thêm/sửa bài viết | `/admin/posts/form/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, image-uploader, toast | base/*, layout/dashboard, components tương ứng, pages/posts-admin; JS core/validate + pages/posts-admin |
| Quản lý liên hệ | `/admin/contacts/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, filter-bar, table-toolbar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/contacts-admin; JS core + components + pages/contacts-admin |
| Chi tiết liên hệ | `/admin/contacts/detail/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, toast | base/*, layout/dashboard, components tương ứng, pages/contacts-admin; JS core + pages/contacts-admin |
| Quản lý tài khoản | `/admin/users/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, table-toolbar, filter-bar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/users; JS core + components + pages/users |
| Thêm/sửa tài khoản | `/admin/users/form/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, alert, toast | base/*, layout/dashboard, components tương ứng, pages/users; JS core/validate + pages/users |
| Đánh giá xe | `/admin/reviews/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, filter-bar, data-table, pagination, confirm-dialog, toast | base/*, layout/dashboard, components tương ứng, pages/reviews; JS core + components + pages/reviews |
| Cài đặt liên hệ | `/admin/settings/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, alert, toast | base/*, layout/dashboard, components tương ứng, pages/settings; JS core/validate + pages/settings |
| Hồ sơ / đổi mật khẩu | `/admin/profile/` | dashboard | admin-sidebar, admin-topbar, breadcrumb, form-field, alert, toast | base/*, layout/dashboard, components tương ứng, pages/settings; JS core/validate + pages/settings |

> Ghi chú: trang `/admin/*/form/` dùng chung một thư mục cho thêm mới và chỉnh sửa; trạng thái được xác định bằng tham số truy vấn trên URL demo, dữ liệu thật do backend truyền vào khi ghép View.

---

## Phần 4 — Thứ tự nạp bắt buộc

**CSS (theo đúng thứ tự, không đảo):**

1. `assets/vendor/bootstrap/bootstrap.min.css` — cung cấp grid, utility, component gốc.
2. `assets/vendor/bootstrap-icons/bootstrap-icons.css` — icon dùng trong nút và menu.
3. `assets/css/base/variables.css` — khai báo biến màu/khoảng cách mà các tầng sau dùng lại.
4. `assets/css/base/reset.css` — chuẩn hoá mặc định, chạy sau biến để dùng được biến.
5. `assets/css/base/typography.css` — thang chữ, cần biến và reset trước đó.
6. `assets/css/base/utilities.css` — utility bổ sung, nạp cuối tầng base.
7. `assets/css/layout/<khung>.css` — bố cục khung, cần biến + typography.
8. `assets/css/components/<a>.css` … theo thứ tự xuất hiện trên trang — style component cần biết biến và bố cục khung.
9. `assets/css/pages/<trang>.css` — nạp cuối để ghi đè tinh chỉnh riêng của trang.

**JavaScript (thuộc tính `defer` cho toàn bộ, module cho `core` và `pages`):**

1. `assets/vendor/bootstrap/bootstrap.bundle.min.js` (vendor) — Bootstrap phải có trước khi khởi tạo tooltip/dropdown.
2. `assets/js/vendor/bootstrap-init.js` — kích hoạt hành vi Bootstrap cần thiết.
3. `assets/js/core/dom.js` → `format.js` → `validate.js` → `http.js` → `modal.js` → `toast.js` — thứ tự nội bộ: `dom` là nền, `http` dùng `dom`, `modal`/`toast` dùng `dom`.
4. `assets/js/components/<component>.js` — chỉ các component có mặt trên trang; mỗi file tự đăng ký và khởi tạo theo `data-component`.
5. `assets/js/pages/<trang>.js` — chạy cuối, được phép dùng toàn bộ core và component.
6. `assets/js/core/component-loader.js` — **chỉ ở môi trường demo tĩnh**, nạp trước khi các component khởi tạo (chèn giữa bước 2 và bước 4); khi ghép vào PHP View thì bỏ hoàn toàn.

Lý do: CSS sau ghi đè CSS trước, nên nền tảng phải đứng trước và style trang đứng cuối; JS chạy theo thứ tự nạp nên thư viện phải có trước tiện ích, tiện ích trước component, component trước logic trang.

---

## Phần 5 — Checklist thêm một trang mới

1. **Tạo thư mục trang:** `pages/<module>/<page>/` với duy nhất một `index.html`; chọn layout `base`, `auth` hoặc `dashboard` theo khu vực.
2. **Xác định vùng:** điền nội dung vào các placeholder của layout (vùng tiêu đề trang, vùng nội dung, vùng thông báo); không chép header/sidebar/footer vào trang.
3. **Tái dùng component:** rà danh sách component trước khi viết markup mới; nếu một khối lặp lại ở trang thứ hai thì chuyển khối đó thành component mới trong `components/<ten>/` gồm đủ ba file.
4. **Gắn hành vi bằng `data-*`:** đặt `data-component` và các `data-*` tham số trên phần tử; không viết `onclick`, không viết script trong trang.
5. **Thêm style/behavior riêng:** chỉ khi thật sự đặc thù mới tạo `assets/css/pages/<trang>.css` và `assets/js/pages/<trang>.js`; style component mới đặt trong `assets/css/components/`, không nhét vào file trang.
6. **Cập nhật danh sách nạp:** bổ sung file CSS/JS của trang và của component mới vào danh sách nạp của trang theo thứ tự ở Phần 4, đồng thời thêm dòng vào bảng ánh xạ Phần 3 để người sau tra được.

---

## Phần 6 — 5 lỗi cấu trúc frontend thường gặp và cách phòng tránh

| Lỗi thường gặp | Hậu quả | Cấu trúc trên phòng tránh thế nào |
|---|---|---|
| Copy navbar/sidebar/footer vào từng trang | Sửa một chỗ phải sửa 20 file, dễ lệch giao diện | Mọi khối lặp nằm trong `components/`, nhúng qua layout hoặc loader; trang chỉ còn nội dung riêng |
| Dùng `style=""` và thẻ `<style>` trong trang | Không tái sử dụng được, xung đột thứ tự ưu tiên, khó review | Không có style nội tuyến; style chia tầng base → layout → components → pages |
| Nạp toàn bộ JS/CSS ở mọi trang | Tải nặng, hành vi trang này chạy nhầm ở trang khác | Mỗi trang chỉ nạp component nó dùng + file riêng của trang; bảng ánh xạ Phần 3 là nguồn tra cứu |
| Ảnh đặt lung tung trong thư mục trang | Khó tìm, trùng tên, xoá nhầm | Ảnh theo module trong `assets/img/<module>/`, ảnh dùng chung trong `assets/img/common/` |
| URL lộ `.html` và đường dẫn nội bộ kiểu `pages/admin/cars/index.html` | Xấu, lộ cấu trúc thư mục, đổi cấu trúc là chết link | Mỗi trang có `index.html` + DirectoryIndex, URL `/admin/cars/`; rewrite khai báo ở `.htaccess`, link nội bộ luôn dùng URL không đuôi |
