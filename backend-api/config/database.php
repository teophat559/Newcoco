<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'contest_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Database connection options
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');
define('DB_PREFIX', '');

// Database connection pool
define('DB_MAX_CONNECTIONS', 10);
define('DB_IDLE_TIMEOUT', 60);
define('DB_CONNECTION_TIMEOUT', 5);

// Database query options
define('DB_QUERY_TIMEOUT', 30);
define('DB_MAX_RETRIES', 3);
define('DB_RETRY_DELAY', 1);

// Database backup options
define('DB_BACKUP_DIR', __DIR__ . '/../public/backups');
define('DB_BACKUP_RETENTION', 7); // days
define('DB_BACKUP_COMPRESS', true);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));