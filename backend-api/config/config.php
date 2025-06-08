<?php

// Site configuration
define('SITE_NAME', 'Hệ thống bình chọn trực tuyến');
define('SITE_DESCRIPTION', 'Nền tảng tổ chức và quản lý các cuộc thi bình chọn trực tuyến');
define('SITE_EMAIL', 'contact@yourdomain.com');
define('SITE_PHONE', '0123 456 789');
define('SITE_ADDRESS', '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh');

// URL configuration
define('SITE_URL', 'https://yourdomain.com');
define('API_URL', SITE_URL . '/api');
define('ADMIN_URL', SITE_URL . '/admin');
define('USER_URL', SITE_URL . '/user');

// Path configuration
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('CACHE_PATH', PUBLIC_PATH . '/cache');
define('LOG_PATH', PUBLIC_PATH . '/logs');
define('BACKUP_PATH', PUBLIC_PATH . '/backups');
define('ASSET_PATH', PUBLIC_PATH . '/assets');
define('TEMP_PATH', PUBLIC_PATH . '/temp');

// File upload configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Security configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour
define('REMEMBER_LIFETIME', 2592000); // 30 days
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Create required directories
$directories = [
    UPLOAD_PATH,
    UPLOAD_PATH . '/avatars',
    UPLOAD_PATH . '/contests',
    UPLOAD_PATH . '/contestants',
    CACHE_PATH,
    LOG_PATH,
    BACKUP_PATH,
    ASSET_PATH,
    TEMP_PATH
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}