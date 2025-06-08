<?php

// Start session
session_start();

// Load configuration files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/cache.php';
require_once __DIR__ . '/config/logging.php';
require_once __DIR__ . '/config/locale.php';

// Load helpers
require_once __DIR__ . '/helpers/Helper.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'BackendApi\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators
    // and append .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize utilities
$cache = \BackendApi\Utils\Cache::getInstance();
$logger = \BackendApi\Utils\Logger::getInstance();
$locale = \BackendApi\Utils\Locale::getInstance();

// Set error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
    $logger->error($errstr, [
        'errno' => $errno,
        'file' => $errfile,
        'line' => $errline
    ]);
    return true;
});

// Set exception handler
set_exception_handler(function ($exception) use ($logger) {
    $logger->critical($exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});

// Initialize database connection
try {
    $db = new \BackendApi\Utils\Database(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    $logger->critical('Database connection failed: ' . $e->getMessage());
    die('Database connection failed');
}