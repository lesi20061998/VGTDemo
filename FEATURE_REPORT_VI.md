# Hệ thống CMS Đa Nền Tảng Laravel - Báo Cáo Tính Năng

## Mục Lục
1. [Tổng Quan Hệ Thống](#tổng-quan-hệ-thống)
2. [Kiến Trúc & Công Nghệ](#kiến-trúc--công-nghệ)
3. [Kiến Trúc Đa Nền Tảng](#kiến-trúc-đa-nền-tảng)
4. [Vai Trò & Quyền Hạn Người Dùng](#vai-trò--quyền-hạn-người-dùng)
5. [Tính Năng Cốt Lõi](#tính-năng-cốt-lõi)
6. [Quản Lý Nội Dung](#quản-lý-nội-dung)
7. [Tính Năng Thương Mại Điện Tử](#tính-năng-thương-mại-điện-tử)
8. [Quản Lý Dự Án](#quản-lý-dự-án)
9. [Các Endpoint API](#các-endpoint-api)
10. [Widget & Trình Xây Dựng Trang](#widget--trình-xây-dựng-trang)
11. [Quản Lý Phương Tiện](#quản-lý-phương-tiện)
12. [SEO & Marketing](#seo--marketing)
13. [Xuất & Triển Khai](#xuất--triển-khai)

## Tổng Quan Hệ Thống

Đây là một hệ thống CMS Laravel đa nền tảng tinh vi được thiết kế để quản lý nhiều website khách hàng từ một codebase duy nhất. Hệ thống cung cấp các tính năng toàn diện cho cả quản lý nội dung và hoạt động thương mại điện tử với sự tập trung mạnh mẽ vào kiến trúc đa nền tảng, cho phép mỗi dự án khách hàng có dữ liệu riêng biệt trong khi chạy trên một ứng dụng chia sẻ.

**Đặc điểm chính:**
- Kiến trúc đa nền tảng với phân tách cơ sở dữ liệu
- Định tuyến tên miền phụ dựa trên dự án (`/{projectCode}/*`)
- Hệ thống phân quyền mô-đun với kiểm soát truy cập theo vai trò
- Tính năng thương mại điện tử toàn diện
- Hệ thống quản lý nội dung chuyên nghiệp
- Khả năng xuất và triển khai tích hợp

## Kiến Trúc & Công Nghệ

### Công Nghệ Cốt Lõi
- **Framework:** Laravel 12.x
- **Ngôn ngữ:** PHP 8.2+
- **Cơ sở dữ liệu:** MySQL (với chuyển đổi cơ sở dữ liệu đa nền tảng)
- **Giao diện người dùng:** Mẫu Blade với các thành phần Livewire/Volt
- **Quản lý tài nguyên:** Vite

### Gói Bên Thứ Ba
- **Livewire/Volt:** Cho các thành phần giao diện phản ứng
- **Spatie Laravel Media Library:** Quản lý phương tiện nâng cao
- **Spatie Laravel Permission:** Quản lý vai trò và quyền hạn
- **Spatie Laravel Translatable:** Hỗ trợ đa ngôn ngữ
- **Kalnoy NestedSet:** Cấu trúc phân cấp cho danh mục
- **Unisharp Laravel File Manager:** Giao diện quản lý tệp

### Thành Phần Kiến Trúc Tùy Chỉnh
- Middleware định tuyến cơ sở dữ liệu đa nền tảng
- Mô hình có phạm vi dự án
- Hệ thống đăng ký widget
- Quản lý cấu hình động

## Kiến Trúc Đa Nền Tảng

### Phân Tách Cơ Sở Dữ Liệu
- Mỗi dự án có cơ sở dữ liệu riêng theo mẫu `project_{projectCode}`
- Chuyển đổi cơ sở dữ liệu thông qua middleware `SetProjectDatabase`
- Mô hình có phạm vi dự án sử dụng trait tùy chỉnh

### Cấu Trúc URL
- **Giao diện người dùng:** `/{projectCode}/*` (ví dụ: `myproject.com/products`)
- **Quản trị CMS:** `/{projectCode}/admin/*` (ví dụ: `myproject.com/admin/products`)
- **Tự động tạo dự án** khi truy cập URL dự án không tồn tại

### Quản Lý Nền Tảng
- Mô hình nền tảng với cách ly dự án
- Tự động tạo cơ sở dữ liệu nền tảng
- Cấu hình và thiết lập riêng theo nền tảng

## Vai Trò & Quyền Hạn Người Dùng

### Cấp Truy Cập
| Cấp | Tên | Mô tả |
|-------|------|-------------|
| 0 | SuperAdmin | Quyền truy cập hệ thống đầy đủ |
| 1 | Administrator | Quản trị viên hệ thống với quyền truy cập tất cả dự án |
| 2 | User | Quyền truy cập giới hạn dựa trên các dự án được gán |

### Loại Vai Trò
| Vai trò | Mô tả | Quyền truy cập |
|------|-------------|--------|
| `admin` | Quản trị viên hệ thống | Tất cả khu vực |
| `cms` | Người quản lý nội dung | Chỉ tính năng CMS |
| `employee` | Nhân viên | Bảng điều khiển nhân viên |

### Ma Trận Phân Quyền
- **Bảng điều khiển SuperAdmin:** Người dùng cấp 0-1
- **Bảng điều khiển CMS:** Người dùng cấp 0-1 + vai trò 'cms'
- **Bảng điều khiển Nhân viên:** Người dùng cấp 0-1 + vai trò 'employee'
- **Truy cập dự án cụ thể:** Dựa trên mảng `project_ids`

## Tính Năng Cốt Lõi

### Xác Thực & Phân Quyền
- Hệ thống xác thực đa cấp
- Kiểm soát truy cập dựa trên vai trò
- Gán người dùng dự án cụ thể
- Hỗ trợ xác thực API JWT

### Bảng Điều Khiển & Phân Tích
- Bảng điều khiển SuperAdmin với tổng quan dự án
- Phân tích cụ thể theo dự án
- Giám sát và nhật ký hệ thống
- Ghi nhật ký hoạt động cho tất cả hành động

### Cấu Hình Động
- Lưu trữ thiết lập cụ thể theo dự án
- Cấu hình điều khiển bởi cơ sở dữ liệu
- Tùy chọn tùy chỉnh giao diện
- Cập nhật cấu hình thời gian thực

## Quản Lý Nội Dung

### Loại Nội Dung
- **Bài viết/Blog:** Quản lý bài viết với trình soạn thảo nâng cao
- **Trang:** Quản lý trang tĩnh
- **Câu hỏi thường gặp:** Quản lý câu hỏi thường gặp
- **Quản lý Menu:** Menu điều hướng động

### Tính năng
- Tích hợp trình soạn thảo WYSIWYG
- Quản lý metadata SEO
- Lên lịch nội dung
- Kiểm soát phiên bản cho nội dung
- Hỗ trợ nội dung đa ngôn ngữ

### Quản Lý Phương Tiện
- Tải lên hình ảnh và tệp
- Quản lý thư viện ảnh
- Tổ chức tệp và thư mục
- Chuyển đổi phương tiện và hình thu nhỏ
- Giao diện kéo thả

## Tính Năng Thương Mại Điện Tử

### Quản Lý Sản Phẩm
- **Sản phẩm:** Danh mục sản phẩm đầy đủ với biến thể
- **Danh mục:** Cấu trúc danh mục phân cấp
- **Thương hiệu:** Hệ thống quản lý thương hiệu
- **Thuộc tính:** Thuộc tính sản phẩm tùy chỉnh với giá trị
- **Biến thể:** Biến thể sản phẩm và tùy chọn

### Tính năng Sản Phẩm
- Thông tin sản phẩm chi tiết
- Quản lý giá cả (giá thường/giá khuyến mãi)
- Quản lý hàng tồn kho và theo dõi kho
- Thư viện sản phẩm với nhiều hình ảnh
- Tối ưu hóa SEO cho sản phẩm
- Đánh giá và xếp hạng sản phẩm
- Huy hiệu và nhãn

### Quản Lý Đơn Hàng
- Hệ thống xử lý đơn hàng hoàn chỉnh
- Nhiều trạng thái đơn hàng
- Theo dõi trạng thái thanh toán
- Ghi chú và giao tiếp đơn hàng
- Báo cáo và phân tích đơn hàng
- Tạo hóa đơn

### Giỏ Hàng & Thanh Toán
- Chức năng thêm vào giỏ hàng
- Quản lý giỏ hàng (cập nhật/xóa)
- Quy trình thanh toán với xác thực
- Xác nhận thành công đơn hàng

## Quản Lý Dự Án

### Vòng Đời Dự Án
- **Tạo Dự Án:** Thiết lập dự án tự động
- **Quản Lý Hợp Đồng:** Theo dõi và phê duyệt hợp đồng
- **Gán Nhân Viên:** Gán thành viên nhóm cho dự án
- **Quản Lý Công Việc:** Công việc cụ thể theo dự án
- **Hệ Thống Phiếu:** Quản lý phiếu hỗ trợ

### Tính Năng Dự Án
- Theo dõi trạng thái dự án
- Quản lý ngân sách và thời hạn
- Kênh giao tiếp khách hàng
- Quản lý người dùng cụ thể theo dự án
- Bật/tắt tính năng theo dự án

### Điều Khiển SuperAdmin
- Tạo và cấu hình dự án
- Quản lý CMS từ xa
- Khả năng xuất website
- Quản lý đa nền tảng
- Đồng bộ hóa cơ sở dữ liệu

## Các Endpoint API

### API Công Khai
- **Đăng Ký Nhận Bản Tin:** `/api/newsletter/subscribe`
- **Đánh Giá:** `/api/reviews` (lưu đánh giá)
- **Gửi Biểu Mẫu:** `/api/form-submit`
- **API Địa Phương:** Tra cứu Tỉnh/Quận/Xã
- **Cầu Nối Dự Án:** `/api/bridge` (tích hợp bên ngoài)

### API Nội Bộ
- **Tạo Sơ Đồ Trang Web:** Các endpoint XML sitemap
- **Tải Lên Phương Tiện:** API tải tệp
- **Hiển Thị Widget:** API widget động

## Widget & Trình Xây Dựng Trang

### Widget Có Sẵn
#### Widget Hero/Marketing
- Section hero với nút kêu gọi hành động
- Hiển thị tính năng
- Bố cục lưới Bento
- Banner kêu gọi hành động
- Lời chứng thực
- Biểu mẫu đăng ký nhận bản tin

#### Widget Nội Dung
- Danh sách bài viết và trình chiếu
- Bài viết tin tức và nội dung nổi bật
- Bài viết liên quan

#### Widget Thương Mại Điện Tử
- Danh sách và lưới sản phẩm
- Hiển thị danh mục
- Sản phẩm nổi bật
- Danh mục sản phẩm

### Tính Năng Trình Xây Dựng Trang
- Đặt widget kéo thả
- Cài đặt widget có thể tùy chỉnh
- Hỗ trợ thiết kế đáp ứng
- Nhiều tùy chọn bố cục
- Xem trước thời gian thực

## Quản Lý Phương Tiện

### Tính năng
- **Tải Lên Tệp:** Tải tệp kéo thả
- **Xử Lý Ảnh:** Tự động tạo hình thu nhỏ
- **Tổ Chức Thư Mục:** Cấu trúc thư mục phân cấp
- **Quản Lý Tệp:** Di chuyển, xóa, tổ chức tệp
- **Thư Viện Phương Tiện:** Quản lý tài sản phương tiện tập trung

### Xử Lý Ảnh
- Nhiều chuyển đổi ảnh (hình thu nhỏ, xem trước, v.v.)
- Tối ưu hóa tự động
- Hỗ trợ ảnh đáp ứng
- Hỗ trợ watermark

## SEO & Marketing

### Tính Năng SEO
- **Tạo Sơ Đồ Trang Web:** XML sitemap động cho trang, sản phẩm, danh mục, thương hiệu
- **Quản Lý Meta:** Metadata SEO từng trang/sản phẩm
- **URL Chính Tả:** Xử lý chính xác URL chính tả
- **Robots.txt:** Quản lý robots.txt động
- **Đánh Dấu Schema:** Triển khai dữ liệu có cấu trúc

### Công Cụ Marketing
- **Tích Hợp Bản Tin:** Quản lý đăng ký nhận bản tin
- **Xử Lý Biểu Mẫu:** Gửi liên hệ và biểu mẫu
- **Hệ Thống Đánh Giá:** Đánh giá sản phẩm và trang web
- **Quản Lý Liên Hệ:** Theo dõi khách hàng tiềm năng và liên hệ
- **Hệ Thống Phản Hồi:** Thu thập phản hồi người dùng

### Tích Hợp Mạng Xã Hội
- Nút chia sẻ mạng xã hội
- Quản lý liên kết mạng xã hội
- Yếu tố bằng chứng xã hội

## Xuất & Triển Khai

### Tính Năng Xuất
- Xuất toàn bộ mã nguồn dự án
- Xuất cơ sở dữ liệu ở định dạng SQL và JSON
- Tạo tệp cấu hình
- Tạo script và tài liệu triển khai
- Xuất cấu hình bảo mật

### Quy Trình Xuất
- **Xuất từ bảng điều khiển SuperAdmin**
- **Nhiều tùy chọn xuất:**
  - Bao gồm/không bao gồm cơ sở dữ liệu
  - Bao gồm/không bao gồm tệp bảo mật
  - Bao gồm/không bao gồm phụ thuộc phát triển
- **Tự động tạo ZIP**
- **Tạo tài liệu cài đặt**

### Triển Khai
- **Yêu cầu máy chủ:** PHP 8.1+, MySQL 5.7+, Composer, Node.js
- **Script cài đặt:** Quy trình thiết lập tự động
- **Tối ưu hóa sản xuất:** Cache và tối ưu hóa hiệu suất
- **Cấu hình máy chủ web:** Hướng dẫn thiết lập Apache/Nginx

## Điểm Nổi Bật Kỹ Thuật

### Tính Năng Bảo Mật
- Kiểm soát truy cập theo vai trò đa cấp
- Cách ly dữ liệu theo cấp độ dự án
- Xác thực và làm sạch đầu vào
- Bảo vệ CSRF
- Quản lý phiên làm việc
- Bảo mật mật khẩu

### Tối Ưu Hóa Hiệu Suất
- Tối ưu hóa truy vấn cơ sở dữ liệu
- Cơ chế cache
- Tối ưu hóa tài nguyên
- Tải chậm cho widget
- Kết nối cơ sở dữ liệu được lập hàng đợi

### Tính Năng Mở Rộng
- Thiết kế kiến trúc đa nền tảng
- Phân tách cơ sở dữ liệu theo dự án
- Kiến trúc thành phần mô-đun
- Hoạt động theo hàng đợi
- Giới hạn tốc độ API

## Kết Luận

Hệ thống CMS Đa Nền Tảng Laravel này là giải pháp toàn diện kết hợp sự linh hoạt của hệ thống quản lý nội dung với sức mạnh của nền tảng thương mại điện tử, tất cả được xây dựng trên kiến trúc đa nền tảng vững chắc. Hệ thống cung cấp:

- **Giá trị Kinh doanh:** Khả năng quản lý nhiều website khách hàng một cách hiệu quả từ một codebase duy nhất
- **Xuất sắc về Kỹ thuật:** Kiến trúc Laravel hiện đại với các thực hành tốt nhất về bảo mật và hiệu suất
- **Khả năng Mở rộng:** Được thiết kế để mở rộng đến hàng trăm dự án khách hàng
- **Sự Linh hoạt:** Tùy chỉnh mở rộng và kiến trúc mô-đun
- **Hiệu suất:** Công cụ tích hợp cho quản lý nội dung, thương mại điện tử và quản lý dự án

Sức mạnh của hệ thống nằm ở kiến trúc đa nền tảng được suy nghĩ cẩn thận, tập hợp tính năng toàn diện và triển khai ở cấp độ chuyên nghiệp cân bằng giữa độ phức tạp và khả năng sử dụng.