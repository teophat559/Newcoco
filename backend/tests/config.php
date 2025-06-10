<?php
// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'contest_db',
    'username' => 'sa',
    'password' => 'YourStrong@Passw0rd'
];

try {
    $db = new PDO(
        "sqlsrv:Server={$dbConfig['host']};Database={$dbConfig['dbname']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Test configuration
define('TEST_USERNAME', 'user1');
define('TEST_PASSWORD', 'password');
define('TEST_ADMIN_USERNAME', 'admin');
define('TEST_ADMIN_PASSWORD', 'password');
define('API_URL', 'http://localhost/api');