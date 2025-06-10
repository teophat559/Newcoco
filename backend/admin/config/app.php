<?php
// Application settings
define('APP_NAME', 'Newcoco Admin');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/newcoco/admin');
define('APP_ROOT', dirname(__DIR__));

// Session settings
define('SESSION_NAME', 'newcoco_admin');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false);
define('SESSION_HTTPONLY', true);

// Security settings
define('HASH_COST', 12);
define('TOKEN_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Cache settings
define('CACHE_DIR', __DIR__ . '/../cache/');
define('CACHE_LIFETIME', 3600); // 1 hour

// Log settings
define('LOG_DIR', __DIR__ . '/../logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error
define('LOG_FORMAT', 'Y-m-d H:i:s');

// Pagination settings
define('ITEMS_PER_PAGE', 10);
define('PAGE_RANGE', 5);

// Date and time settings
define('TIMEZONE', 'Asia/Ho_Chi_Minh');
define('DATE_FORMAT', 'd/m/Y');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');