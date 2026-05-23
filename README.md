<p align="center">
  <img src="./scr/public/assets/images/logos/logo-dark.png" alt="URTISYNC Logo" width="220">
</p>


# Hệ thống tối ưu hóa quản lý thời gian cá nhân

## Giới thiệu đề tài

**Hệ thống tối ưu hóa quản lý thời gian cá nhân** là một ứng dụng web hỗ trợ người dùng lập kế hoạch, quản lý lịch trình, theo dõi nhật ký thời gian và nhận gợi ý tối ưu các khoảng thời gian trống trong ngày. Ứng dụng được xây dựng theo mô hình PHP MVC thuần, giao diện HTML/CSS/JavaScript, sử dụng MySQL/MariaDB để lưu trữ dữ liệu.

Hệ thống tập trung vào nhu cầu quản lý thời gian cá nhân trong học tập, công việc, sức khỏe và các hoạt động hằng ngày. Người dùng có thể tạo danh mục, hoạt động, lịch, nhắc nhở, ngày quan trọng, xem thời gian biểu theo ngày, xem lịch tháng, theo dõi báo cáo thời gian và sử dụng trợ lý thông minh để nhận phân tích/gợi ý.

## Thông tin sinh viên

- Họ tên: Nguyễn Hoàng Thảo
- Lớp: DX23TT10
- Tài khoản GitHub: thaonh090301-eng
- Email: thaonh090301@sv-onuni.edu.vn
- Số điện thoại: 0343431374

## Thông tin đồ án

- Tên đề tài: Xây dựng hệ thống tối ưu hóa quản lý thời gian cá nhân
- Loại đồ án: Thực tập đồ án chuyên ngành
- Repository: `thaonh090301-eng/cn-DX23TT10-nguyenhoangthao-urtisync`
- Môi trường chạy thử: Localhost với PHP built-in server, XAMPP, Laragon hoặc WAMP
- Cơ sở dữ liệu: MySQL/MariaDB

## Mục tiêu đề tài

- Xây dựng ứng dụng web hỗ trợ người dùng quản lý thời gian cá nhân một cách trực quan.
- Cho phép lập lịch, xem lịch theo ngày và theo tháng, quản lý hoạt động và danh mục.
- Hỗ trợ nhắc nhở cá nhân theo ngày, theo tuần hoặc theo khoảng thời gian.
- Cung cấp báo cáo nhật ký thời gian dạng hiển thị/read-only, có chức năng đặt lại dữ liệu nhật ký khi cần.
- Gợi ý các khoảng trống phù hợp để sắp xếp hoạt động mới.
- Cung cấp dashboard, trợ lý thông minh và giao diện song ngữ VI/EN.
- Duy trì giao diện sáng/tối, thân thiện và dễ sử dụng.

## Công nghệ sử dụng

- PHP 8.x
- MySQL hoặc MariaDB
- HTML5
- CSS3
- JavaScript thuần
- FullCalendar
- PDO
- PHP built-in server hoặc Apache qua XAMPP/Laragon/WAMP

## Chức năng chính

- Đăng nhập / đăng xuất
- Dashboard tổng quan
- Trang chủ với các bài viết thói quen mỗi ngày
- Quản lý danh mục
- Quản lý hoạt động
- Quản lý lịch
- Xem lịch theo ngày trên trang Lịch
- Thời gian biểu theo ngày
- Lịch tháng bằng FullCalendar
- Nhắc nhở hằng ngày / hằng tuần / theo khoảng thời gian
- Thông báo trình duyệt / Windows notification cho nhắc nhở
- Quản lý ngày quan trọng
- Nhật ký thời gian dạng báo cáo
- Đặt lại nhật ký thời gian của người dùng hiện tại
- Gợi ý tối ưu khoảng trống
- Trợ lý thông minh phân tích và gợi ý quản lý thời gian
- Giao diện sáng/tối
- Hỗ trợ chuyển đổi ngôn ngữ VI / EN
- Logo riêng cho giao diện chính và trang đăng nhập

## Cấu trúc thư mục

```txt
cn-DX23TT10-nguyenhoangthao-urtisync/
├── README.md
├── .env.example
├── docs/
├── progress-report/
├── setup/
│   └── database/
│       ├── schema.sql
│       └── seed.sql
├── scr/
│   ├── app/
│   │   ├── Controllers/
│   │   ├── Core/
│   │   ├── Repositories/
│   │   ├── Services/
│   │   └── Views/
│   ├── config/
│   ├── lang/
│   ├── public/
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   ├── images/
│   │   │   └── js/
│   │   └── index.php
│   └── routes.php
└── thesis/
```

## Hướng dẫn cài đặt local

### Yêu cầu

- PHP 8.x
- MySQL hoặc MariaDB
- Trình duyệt web hiện đại
- XAMPP, Laragon, WAMP hoặc PHP CLI

### Các bước cài đặt

1. Clone repository:

```bash
git clone https://github.com/thaonh090301-eng/cn-DX23TT10-nguyenhoangthao-urtisync.git
cd cn-DX23TT10-nguyenhoangthao-urtisync
```

2. Tạo file `.env` từ file mẫu:

```bash
cp .env.example .env
```

Trên Windows PowerShell có thể dùng:

```powershell
Copy-Item .env.example .env
```

3. Cấu hình kết nối cơ sở dữ liệu trong `.env`.
4. Import database theo hướng dẫn bên dưới.
5. Chạy ứng dụng bằng PHP built-in server hoặc cấu hình virtual host trỏ vào `scr/public`.

## Cấu hình .env

Ví dụ cấu hình local:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=personal_time_optimizer
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

Lưu ý:

- Không commit file `.env`.
- Tên database mặc định là `personal_time_optimizer`.
- Nếu MySQL có mật khẩu, cần cập nhật `DB_PASSWORD`.

## Import database

### Cách 1: Import bằng phpMyAdmin

1. Mở phpMyAdmin.
2. Import file `setup/database/schema.sql`.
3. Import tiếp file `setup/database/seed.sql` nếu muốn có dữ liệu mẫu.

### Cách 2: Import bằng terminal

Nếu MySQL có mật khẩu:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS personal_time_optimizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p < setup/database/schema.sql
mysql -u root -p personal_time_optimizer < setup/database/seed.sql
```

Nếu MySQL không có mật khẩu:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS personal_time_optimizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root < setup/database/schema.sql
mysql -u root personal_time_optimizer < setup/database/seed.sql
```

Lưu ý: `schema.sql` tạo cấu trúc database và bảng; `seed.sql` chỉ cần import nếu muốn có dữ liệu mẫu.

## Chạy ứng dụng bằng PHP built-in server

Chạy từ thư mục gốc của project:

```bash
php -S localhost:8000 -t scr/public
```

Sau đó mở trình duyệt:

```txt
http://localhost:8000
```

Khi chưa đăng nhập, trang gốc `/` sẽ chuyển đến trang đăng nhập. Sau khi đăng nhập thành công, người dùng có thể truy cập các chức năng chính của hệ thống.

## Tài khoản demo

Dữ liệu demo nằm trong `setup/database/seed.sql`.

- Email: `demo@example.com`
- Mật khẩu: `password`
- Trang đăng nhập: `http://localhost:8000/login`

## Kiểm tra cú pháp PHP

Có thể kiểm tra từng file:

```bash
php -l scr/app/Controllers/HomeController.php
```

Hoặc kiểm tra toàn bộ file PHP trong thư mục `scr` trên Windows PowerShell:

```powershell
Get-ChildItem -Path scr -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Trên macOS/Linux:

```bash
find scr -name "*.php" -print0 | xargs -0 -n1 php -l
```

## Ghi chú phát triển

- Source code chính nằm trong `scr/`.
- Routes được khai báo trong `scr/routes.php`.
- Controllers nằm trong `scr/app/Controllers/`.
- Repositories nằm trong `scr/app/Repositories/`.
- Services nằm trong `scr/app/Services/`.
- Views nằm trong `scr/app/Views/`.
- File cấu hình ứng dụng nằm trong `scr/config/config.php`.
- File ngôn ngữ nằm trong `scr/lang/vi.php` và `scr/lang/en.php`.
- Public entry point là `scr/public/index.php`.
- CSS và JavaScript chính nằm trong `scr/public/assets/`.
- Database schema và dữ liệu mẫu nằm trong `setup/database/`.
- Kết nối database sử dụng PDO trong `scr/app/Core/Database.php`.
- Router đơn giản nằm trong `scr/app/Core/Router.php`.
- Dự án sử dụng PHP MVC thuần, không dùng Laravel, React, Vue hoặc Tailwind.
- Nhật ký thời gian hiện là trang báo cáo/read-only; người dùng có thể đặt lại toàn bộ nhật ký thời gian của tài khoản hiện tại khi cần.
