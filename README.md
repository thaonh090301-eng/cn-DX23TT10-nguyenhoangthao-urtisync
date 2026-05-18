# Hệ thống tối ưu hóa quản lý thời gian cá nhân

## 1. Thông tin sinh viên

- Họ tên: Nguyễn Hoàng Thảo
- Lớp: DX23TT10
- Tài khoản GitHub: thaonh090301-eng
- Email: thaonh090301@sv-onuni.edu.vn
- Số điện thoại: 0343431374

## 2. Thông tin đồ án

- Tên đề tài: Xây dựng hệ thống tối ưu hóa quản lý thời gian cá nhân
- Loại đồ án: Thực tập đồ án chuyên ngành
- Công nghệ sử dụng: PHP, MySQL, HTML, CSS, JavaScript, FullCalendar
- Môi trường chạy thử: Localhost với XAMPP/Laragon/WAMP

## 3. Mục tiêu đề tài

Xây dựng một website hỗ trợ người dùng quản lý, lập lịch, theo dõi và tối ưu hóa thời gian cá nhân. Hệ thống cho phép quản lý các hoạt động hằng ngày, lập kế hoạch theo ngày/tuần/tháng, ghi nhận thời gian thực tế và gợi ý khoảng thời gian trống phù hợp cho từng hoạt động.

## 4. Chức năng dự kiến

- Quản lý danh mục hoạt động
- Quản lý hoạt động cá nhân
- Lập lịch thời gian theo ngày, tuần, tháng
- Theo dõi thời gian thực tế
- Tìm khoảng thời gian trống phù hợp
- Cảnh báo khi sử dụng thời gian chưa hợp lý
- Thống kê thời gian theo nhóm hoạt động

## 5. Cấu trúc thư mục

```txt
cn-DX23TT10-nguyenhoangthao-thaonh090301-eng/
├── README.md
├── .gitignore
├── setup/
├── scr/
├── progress-report/
├── thesis/
│   ├── doc/
│   ├── pdf/
│   ├── html/
│   ├── abs/
│   └── refs/
├── soft/
└── docker/
```

## 6. Huong dan cai dat local

### Yeu cau

- PHP 8.x
- MySQL hoac MariaDB
- XAMPP, WAMP hoac Laragon
- Trinh duyet web

### Cau hinh co so du lieu

1. Tao file `.env` tu file mau `.env.example`.
2. Cap nhat cac gia tri `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`.
3. Mo phpMyAdmin hoac MySQL client.
4. Import `setup/database/schema.sql`.
5. Import `setup/database/seed.sql` neu can du lieu demo.

Tai khoan demo trong seed:

- Email: `demo@example.com`
- Mat khau: `password`

### Chay ung dung

Neu dung PHP built-in server:

```bash
cd scr/public
php -S localhost:8000
```

Sau do mo:

```txt
http://localhost:8000
```

Neu dung XAMPP, WAMP hoac Laragon, dat thu muc project trong web root va cau hinh virtual host tro vao:

```txt
scr/public
```

### Ghi chu phat trien

- Source code chinh nam trong `scr/`.
- File database nam trong `setup/database/`.
- Khong commit file `.env`.
- Ket noi database su dung PDO trong `scr/app/Core/Database.php`.
- Router don gian nam trong `scr/app/Core/Router.php`.
