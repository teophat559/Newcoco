<?php
// Security functions
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// File handling functions
function upload_file($file, $directory = UPLOAD_DIR) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid file parameters');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('File size exceeds limit');
        case UPLOAD_ERR_PARTIAL:
            throw new RuntimeException('File was only partially uploaded');
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file was uploaded');
        case UPLOAD_ERR_NO_TMP_DIR:
            throw new RuntimeException('Missing temporary folder');
        case UPLOAD_ERR_CANT_WRITE:
            throw new RuntimeException('Failed to write file to disk');
        case UPLOAD_ERR_EXTENSION:
            throw new RuntimeException('A PHP extension stopped the file upload');
        default:
            throw new RuntimeException('Unknown upload error');
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        throw new RuntimeException('File size exceeds limit');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        throw new RuntimeException('Invalid file format');
    }

    $filename = sprintf('%s.%s', sha1_file($file['tmp_name']), $ext);
    $filepath = $directory . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new RuntimeException('Failed to move uploaded file');
    }

    return $filename;
}

// Validation functions
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10,11}$/', $phone);
}

function validate_password($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password);
}

// Formatting functions
function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function format_number($number) {
    return number_format($number, 0, ',', '.');
}

function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Database helper functions
function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4", DB_HOST, DB_NAME);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}

// Session handling functions
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => false, // Set to true in production
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// Error handling functions
function handle_error($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];

    error_log(json_encode($error));

    if (defined('DEBUG') && DEBUG) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    return true;
}

set_error_handler('handle_error');