# Newcoco - Hệ thống Quản lý Cuộc thi Trực tuyến

Newcoco là một nền tảng quản lý cuộc thi trực tuyến, cho phép người dùng tạo và tham gia các cuộc thi, bình chọn và theo dõi kết quả.

## Tính năng chính

- Quản lý cuộc thi (tạo, chỉnh sửa, xóa)
- Đăng ký thí sinh và nộp bài dự thi
- Hệ thống bình chọn và đánh giá
- Thông báo và cập nhật trạng thái
- Quản lý người dùng và phân quyền
- Giao diện người dùng thân thiện

## Yêu cầu hệ thống

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- Node.js & NPM (cho frontend)

## Cài đặt

1. Clone repository:
```bash
git clone https://github.com/your-username/newcoco.git
cd newcoco
```

2. Cài đặt dependencies:
```bash
composer install
npm install
```

3. Cấu hình môi trường:
```bash
cp .env.example .env
# Chỉnh sửa file .env với thông tin cấu hình của bạn
```

4. Tạo database và chạy migrations:
```bash
php artisan migrate
```

5. Khởi động server:
```bash
php artisan serve
```

## Cấu trúc thư mục

```
newcoco/
├── backend-api/        # Backend API
├── frontend-user/      # Frontend cho người dùng
├── admin/             # Giao diện quản trị
├── public/            # Assets và entry point
├── resources/         # Views, translations, etc.
└── storage/           # Uploads, logs, cache
```

## Đóng góp

Mọi đóng góp đều được hoan nghênh! Vui lòng đọc [CONTRIBUTING.md](CONTRIBUTING.md) để biết thêm chi tiết.

## Giấy phép

Dự án này được cấp phép theo [MIT License](LICENSE).