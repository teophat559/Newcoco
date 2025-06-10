# Hướng dẫn triển khai website

## Yêu cầu hệ thống

- PHP 8.1 trở lên
- MySQL 8.0 trở lên
- Node.js 16 trở lên
- Composer
- NPM hoặc Yarn
- Redis (tùy chọn)

## Các bước triển khai

### 1. Chuẩn bị hosting

- Đăng ký domain và hosting
- Cấu hình DNS trỏ về hosting
- Cài đặt SSL certificate (Let's Encrypt)
- Tạo database và user

### 2. Upload code

```bash
# Clone repository
git clone <repository-url>
cd <project-directory>

# Cài đặt dependencies
composer install --no-dev
npm install
npm run build

# Upload lên hosting
# Sử dụng FTP hoặc Git
```

### 3. Cấu hình database

- Import file `database/schema.sql`
- Cập nhật thông tin database trong `backend/config/database.php`

### 4. Cấu hình web server

#### Apache (.htaccess)
- Đã được cấu hình sẵn trong `backend/public/.htaccess`
- Đảm bảo mod_rewrite đã được bật

#### Nginx (nginx.conf)
- Copy file `backend/public/nginx.conf` vào thư mục cấu hình Nginx
- Cập nhật `server_name` và đường dẫn

### 5. Cấu hình môi trường

- Copy `.env.example` thành `.env`
- Cập nhật các biến môi trường:
  + Database credentials
  + Redis credentials (nếu có)
  + SMTP settings
  + API keys
  + Telegram bot token

### 6. Phân quyền thư mục

```bash
chmod -R 755 backend/public
chmod -R 777 backend/public/uploads
chmod -R 777 backend/storage/logs
```

### 7. Kiểm tra

- Truy cập website qua domain
- Kiểm tra các chức năng:
  + Đăng nhập/đăng ký
  + Upload file
  + Gửi email
  + API endpoints
  + Webhook
  + Telegram notifications

### 8. Bảo mật

- Đảm bảo các file nhạy cảm không thể truy cập trực tiếp
- Cấu hình firewall
- Bật HTTPS
- Cập nhật các security headers

### 9. Monitoring

- Cấu hình error logging
- Thiết lập backup tự động
- Cấu hình monitoring service

## Troubleshooting

### Lỗi 500 Internal Server Error
- Kiểm tra error log
- Kiểm tra quyền thư mục
- Kiểm tra cấu hình PHP

### Lỗi Database Connection
- Kiểm tra credentials
- Kiểm tra firewall
- Kiểm tra database user permissions

### Lỗi Upload File
- Kiểm tra quyền thư mục uploads
- Kiểm tra cấu hình PHP upload limits
- Kiểm tra web server upload limits

## Liên hệ hỗ trợ

Nếu gặp vấn đề trong quá trình triển khai, vui lòng liên hệ:
- Email: support@example.com
- Telegram: @support_bot