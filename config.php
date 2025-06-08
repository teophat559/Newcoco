<?php
// Cấu hình cơ sở dữ liệu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'newcoco');

// Cấu hình đường dẫn
define('BASE_URL', 'http://localhost/newcoco');
define('ADMIN_URL', BASE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/uploads');

// Cấu hình email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');

// Cấu hình hệ thống
define('SITE_NAME', 'Newcoco');
define('SITE_DESCRIPTION', 'Hệ thống quản lý cuộc thi trực tuyến');
define('ADMIN_EMAIL', 'admin@newcoco.com');

// Cấu hình bảo mật
define('HASH_COST', 12); // Độ phức tạp của password hash
define('SESSION_LIFETIME', 86400); // 24 giờ
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 phút

// Cấu hình upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Cấu hình cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 giờ

// Cấu hình debug
define('DEBUG_MODE', true);
define('ERROR_REPORTING', E_ALL);
define('DISPLAY_ERRORS', true);

// Cấu hình timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Hàm helper để load cấu hình
function get_config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Hàm helper để kiểm tra môi trường
function is_production() {
    return !DEBUG_MODE;
}

// Hàm helper để log lỗi
function log_error($message, $context = []) {
    if (DEBUG_MODE) {
        error_log(date('Y-m-d H:i:s') . ' - ' . $message . ' - ' . json_encode($context));
    }
}