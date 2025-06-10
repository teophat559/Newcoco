<?php
// Include main configuration
require_once __DIR__ . '/../../backend-api/config/config.php';

// Define environment
define('ENVIRONMENT', 'development'); // Change to 'production' in production environment

// Admin specific configurations
define('ADMIN_UPLOAD_PATH', PUBLIC_PATH . '/admin/uploads');
define('ADMIN_TEMP_PATH', PUBLIC_PATH . '/admin/temp');
define('ADMIN_ITEMS_PER_PAGE', 20);
define('ADMIN_MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ADMIN_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'voting_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('SITE_NAME', 'Voting System Admin');
define('SITE_URL', 'http://localhost/voting-system/admin');
define('ADMIN_EMAIL', 'admin@votingsystem.com');

// Security configuration
define('HASH_COST', 12);
define('SESSION_LIFETIME', 7200); // 2 hours
define('CSRF_TOKEN_NAME', 'csrf_token');

// File upload configuration
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination configuration
define('ITEMS_PER_PAGE', 10);

// Create required directories
$directories = [
    ADMIN_UPLOAD_PATH,
    ADMIN_TEMP_PATH
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', PUBLIC_PATH . '/logs/admin_error.log');

// Set custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($error, 3, PUBLIC_PATH . '/logs/admin_error.log');

    if (error_reporting() & $errno) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    return true;
});

// Set custom exception handler
set_exception_handler(function($exception) {
    $error = date('Y-m-d H:i:s') . " - Exception: " . $exception->getMessage() .
             " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($error, 3, PUBLIC_PATH . '/logs/admin_error.log');

    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        echo "<h1>Error</h1>";
        echo "<p>" . $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        header("Location: /admin/500.php");
        exit;
    }
});

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production