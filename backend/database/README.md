# Database Setup - Online Contest System

Thư mục này chứa các file liên quan đến database của hệ thống tổ chức cuộc thi trực tuyến.

## Cấu trúc thư mục

```
database/
├── schema.sql          # Schema chính của database
├── seed.sql           # Dữ liệu mẫu ban đầu
├── migrations/        # Migration files (nếu có)
└── README.md         # File này
```

## Yêu cầu hệ thống

- PostgreSQL 12.0 hoặc cao hơn
- Có quyền tạo database và user

## Cài đặt Database

### 1. Tạo Database và User

```sql
-- Đăng nhập PostgreSQL với user có quyền admin
sudo -u postgres psql

-- Tạo database
CREATE DATABASE contest_system;

-- Tạo user
CREATE USER contest_user WITH PASSWORD 'contest_password';

-- Cấp quyền cho user
GRANT ALL PRIVILEGES ON DATABASE contest_system TO contest_user;
GRANT ALL ON SCHEMA public TO contest_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO contest_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO contest_user;

-- Thoát
\q
```

### 2. Import Schema

```bash
# Import schema chính
psql -h localhost -U contest_user -d contest_system -f schema.sql

# Import dữ liệu mẫu (tùy chọn)
psql -h localhost -U contest_user -d contest_system -f seed.sql
```

### 3. Kiểm tra kết quả

```bash
# Kết nối và kiểm tra tables
psql -h localhost -U contest_user -d contest_system

# Liệt kê tables
\dt

# Kiểm tra dữ liệu mẫu
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM contests;
SELECT COUNT(*) FROM contestants;
```

## Cấu trúc Database

### Bảng chính

1. **users** - Người dùng và admin
2. **contests** - Cuộc thi
3. **contestants** - Thí sinh tham gia
4. **votes** - Bình chọn
5. **settings** - Cài đặt hệ thống

### Bảng hỗ trợ

6. **uploaded_files** - File đã tải lên
7. **api_keys** - API keys
8. **user_sessions** - Sessions người dùng
9. **admin_logs** - Nhật ký admin
10. **request_logs** - Nhật ký requests
11. **error_logs** - Nhật ký lỗi

## Dữ liệu mẫu

File `seed.sql` bao gồm:

- Tài khoản admin mặc định:
  - Email: `admin@contest.com`
  - Password: `admin123`

- 4 user mẫu
- 3 cuộc thi mẫu với các trạng thái khác nhau
- Thí sinh và votes mẫu
- Cài đặt hệ thống cơ bản

## Environment Variables

Cấu hình kết nối database trong backend API:

```env
DB_HOST=localhost
DB_PORT=5432
DB_NAME=contest_system
DB_USER=contest_user
DB_PASSWORD=contest_password
```

## Backup và Restore

### Backup

```bash
# Backup toàn bộ database
pg_dump -h localhost -U contest_user contest_system > backup.sql

# Backup chỉ schema
pg_dump -h localhost -U contest_user --schema-only contest_system > schema_backup.sql

# Backup chỉ data
pg_dump -h localhost -U contest_user --data-only contest_system > data_backup.sql
```

### Restore

```bash
# Restore từ backup
psql -h localhost -U contest_user -d contest_system < backup.sql
```

## Maintenance

### Dọn dẹp dữ liệu

```sql
-- Xóa logs cũ (> 30 ngày)
DELETE FROM request_logs WHERE created_at < NOW() - INTERVAL '30 days';
DELETE FROM error_logs WHERE created_at < NOW() - INTERVAL '30 days';

-- Xóa sessions hết hạn
DELETE FROM user_sessions WHERE expires_at < NOW();

-- Vacuum để tối ưu performance
VACUUM ANALYZE;
```

### Reindex

```sql
-- Reindex tất cả tables
REINDEX DATABASE contest_system;
```

## Monitoring

### Kiểm tra kích thước database

```sql
SELECT pg_size_pretty(pg_database_size('contest_system')) as database_size;
```

### Kiểm tra kích thước tables

```sql
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

### Kiểm tra connections

```sql
SELECT count(*) as active_connections
FROM pg_stat_activity
WHERE datname = 'contest_system';
```

## Troubleshooting

### Lỗi thường gặp

1. **Permission denied**: Đảm bảo user có đủ quyền
2. **Connection refused**: Kiểm tra PostgreSQL service đang chạy
3. **Database does not exist**: Tạo database trước khi import

### Logs PostgreSQL

```bash
# Ubuntu/Debian
tail -f /var/log/postgresql/postgresql-*.log

# CentOS/RHEL
tail -f /var/lib/pgsql/data/pg_log/postgresql-*.log
```

## Performance Tuning

### Indexes quan trọng

Database đã được tạo với các indexes cần thiết. Kiểm tra:

```sql
-- Xem tất cả indexes
\di

-- Kiểm tra query performance
EXPLAIN ANALYZE SELECT * FROM contestants WHERE contest_id = 'uuid-here';
```

### Configuration PostgreSQL

Tham khảo file `/etc/postgresql/*/main/postgresql.conf`:

```
# Memory settings
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB

# Connection settings
max_connections = 100

# Logging
log_statement = 'all'  # cho development
log_min_duration_statement = 1000  # log slow queries
```

## Security

1. **Đổi password mặc định** sau khi setup
2. **Restrict database access** chỉ từ IP cần thiết
3. **Regular backup** và test restore
4. **Monitor logs** cho các truy cập bất thường

## Migration (Future)

Khi cần thay đổi schema, tạo migration files trong thư mục `migrations/`:

```
migrations/
├── 001_initial_schema.sql
├── 002_add_voting_features.sql
└── 003_add_analytics_tables.sql
```