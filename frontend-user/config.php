<?php
// Application Configuration
define('APP_NAME', 'Contest Management System');
define('APP_URL', 'http://localhost:8000');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'contest_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_NAME', 'contest_session');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false);
define('SESSION_HTTP_ONLY', true);

// Security Configuration
define('HASH_COST', 12);
define('TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm']);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);

// Pagination Configuration
define('ITEMS_PER_PAGE', 10);
define('MAX_PAGES', 100);

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_LIFETIME', 3600); // 1 hour

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Time Zone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Character Set
ini_set('default_charset', 'UTF-8');

// Load required files
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params(
        SESSION_LIFETIME,
        SESSION_PATH,
        SESSION_DOMAIN,
        SESSION_SECURE,
        SESSION_HTTP_ONLY
    );
    session_start();
}

// Initialize core components
$db = new Database();
$auth = new Auth();