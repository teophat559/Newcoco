<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'newcoco_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO connection options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// Database tables
define('TABLES', [
    'users' => 'users',
    'admins' => 'admins',
    'contests' => 'contests',
    'contestants' => 'contestants',
    'votes' => 'votes',
    'settings' => 'settings',
    'logs' => 'logs',
    'notifications' => 'notifications'
]);

// Backup settings
define('BACKUP_DIR', __DIR__ . '/../backups/');
define('MAX_BACKUPS', 10);
define('BACKUP_INTERVAL', 86400); // 24 hours in seconds