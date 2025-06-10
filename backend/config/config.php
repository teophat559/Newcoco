<?php
// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
} else {
    die("❌ Không tìm thấy file .env\n");
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? false);
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Database configuration
define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_NAME', $env['DB_NAME'] ?? 'beauty_contest');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');

// Application configuration
define('APP_NAME', $env['APP_NAME'] ?? 'Beauty Contest');
define('APP_ENV', $env['APP_ENV'] ?? 'production');
define('APP_DEBUG', $env['APP_DEBUG'] ?? false);
define('APP_URL', $env['APP_URL'] ?? 'http://localhost');
define('APP_KEY', $_ENV['APP_KEY'] ?? '');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);
session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Content Security Policy
$csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:; " .
       "img-src 'self' data: https:; " .
       "font-src 'self' data: https:;";
header("Content-Security-Policy: " . $csp);

// Time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Could not connect to the database. Please try again later.");
}

// Telegram configuration
define('TELEGRAM_BOT_TOKEN', $env['TELEGRAM_BOT_TOKEN'] ?? '');
define('TELEGRAM_CHAT_ID', $env['TELEGRAM_CHAT_ID'] ?? '');

// File upload configuration
define('UPLOAD_MAX_SIZE', $env['UPLOAD_MAX_SIZE'] ?? 5242880); // 5MB
define('ALLOWED_FILE_TYPES', explode(',', $env['ALLOWED_FILE_TYPES'] ?? 'jpg,jpeg,png,gif'));
define('UPLOAD_PATH', __DIR__ . '/' . ($env['UPLOAD_PATH'] ?? 'uploads/'));

// Create required directories if they don't exist
$directories = ['uploads', 'logs'];
foreach ($directories as $dir) {
    if (!file_exists(__DIR__ . '/' . $dir)) {
        mkdir(__DIR__ . '/' . $dir, 0777, true);
    }
}

// JWT configuration
define('JWT_SECRET', $env['JWT_SECRET'] ?? '');
define('JWT_EXPIRATION', $env['JWT_EXPIRATION'] ?? 3600);

// Mail configuration
define('MAIL_MAILER', $env['MAIL_MAILER'] ?? 'smtp');
define('MAIL_HOST', $env['MAIL_HOST'] ?? '');
define('MAIL_PORT', $env['MAIL_PORT'] ?? 587);
define('MAIL_USERNAME', $env['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $env['MAIL_PASSWORD'] ?? '');
define('MAIL_ENCRYPTION', $env['MAIL_ENCRYPTION'] ?? 'tls');
define('MAIL_FROM_ADDRESS', $env['MAIL_FROM_ADDRESS'] ?? '');
define('MAIL_FROM_NAME', $env['MAIL_FROM_NAME'] ?? APP_NAME);

// Cấu hình đường dẫn
define('BASE_URL', 'http://localhost/newcoco');
define('ADMIN_URL', BASE_URL . '/admin');

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
define('HASH_COST', $env['PASSWORD_HASH_COST'] ?? 12); // Độ phức tạp của password hash
define('SESSION_LIFETIME', 86400); // 24 giờ
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 phút

// Cấu hình upload
define('MAX_FILE_SIZE', $env['MAX_FILE_SIZE'] ?? 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Cấu hình cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 giờ

// Cấu hình debug
define('DEBUG_MODE', $env['DEBUG_MODE'] ?? true);
define('ERROR_REPORTING', E_ALL);
define('DISPLAY_ERRORS', true);

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