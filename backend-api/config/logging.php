<?php

// Logging configuration
define('LOG_ENABLED', true);
define('LOG_DIR', __DIR__ . '/../public/logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error, critical

// Log file names
define('LOG_ERROR_FILE', 'error.log');
define('LOG_ACCESS_FILE', 'access.log');
define('LOG_DEBUG_FILE', 'debug.log');

// Log format
define('LOG_FORMAT', '[%datetime%] %level%: %message% %context% %extra%\n');

// Log rotation
define('LOG_MAX_FILES', 5);
define('LOG_MAX_SIZE', 10485760); // 10MB

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_DIR . LOG_ERROR_FILE);