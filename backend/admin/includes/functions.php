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
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Could not connect to the database. Please try again later.");
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
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

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

    if (DEBUG) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    return true;
}

set_error_handler('handle_error');

// Admin functions
function get_admin_info($admin_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM " . TABLES['admins'] . " WHERE id = ?");
    $stmt->execute([$admin_id]);
    return $stmt->fetch();
}

function get_admin_notifications($admin_id, $limit = 5) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT * FROM " . TABLES['notifications'] . "
        WHERE admin_id = ?
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$admin_id, $limit]);
    return $stmt->fetchAll();
}

function count_unread_notifications($admin_id) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM " . TABLES['notifications'] . "
        WHERE admin_id = ? AND is_read = 0
    ");
    $stmt->execute([$admin_id]);
    return $stmt->fetchColumn();
}

// Page functions
function get_page_title($page) {
    $titles = [
        'index' => 'Trang chủ',
        'login' => 'Đăng nhập',
        'contests' => 'Quản lý cuộc thi',
        'contest-form' => 'Thêm/Sửa cuộc thi',
        'contestants' => 'Quản lý thí sinh',
        'users' => 'Quản lý người dùng',
        'votes' => 'Quản lý bình chọn',
        'admins' => 'Quản lý quản trị viên',
        'settings' => 'Cài đặt hệ thống',
        'profile' => 'Hồ sơ cá nhân',
        'help' => 'Trợ giúp',
        'logs' => 'Nhật ký hệ thống',
        'backup' => 'Sao lưu dữ liệu',
        'statistics' => 'Thống kê'
    ];

    return $titles[$page] ?? 'Trang không xác định';
}

// Format functions
function format_datetime($datetime) {
    return date(DATETIME_FORMAT, strtotime($datetime));
}

function format_time($time) {
    return date(TIME_FORMAT, strtotime($time));
}

function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

// Security functions
function generate_token() {
    return bin2hex(random_bytes(32));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

    return $data;
}

// Log functions
function log_activity($admin_id, $action, $details = '') {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        INSERT INTO " . TABLES['logs'] . " (admin_id, action, details, ip_address)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $admin_id,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR']
    ]);
}

// Cache functions
function get_cache($key) {
    $cache_file = CACHE_DIR . md5($key) . '.cache';

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < CACHE_LIFETIME)) {
        return unserialize(file_get_contents($cache_file));
    }

    return false;
}

function set_cache($key, $value) {
    $cache_file = CACHE_DIR . md5($key) . '.cache';
    file_put_contents($cache_file, serialize($value));
}

function clear_cache($key = null) {
    if ($key === null) {
        array_map('unlink', glob(CACHE_DIR . '*.cache'));
    } else {
        $cache_file = CACHE_DIR . md5($key) . '.cache';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }
}

// Mail functions
function send_mail($to, $subject, $template, $data = []) {
    require_once __DIR__ . '/../config/mail.php';

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>'
    ];

    ob_start();
    extract($data);
    include __DIR__ . '/../views/mail/' . $template;
    $message = ob_get_clean();

    if (MAIL_QUEUE_ENABLED) {
        $queue_file = MAIL_QUEUE_DIR . uniqid() . '.mail';
        file_put_contents($queue_file, serialize([
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'headers' => $headers
        ]));
    } else {
        mail($to, $subject, $message, implode("\r\n", $headers));
    }

    if (MAIL_LOG_ENABLED) {
        $log_file = MAIL_LOG_DIR . date('Y-m-d') . '.log';
        $log_message = sprintf(
            "[%s] To: %s, Subject: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject
        );
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
}

// Backup functions
function create_backup() {
    $backup_file = BACKUP_DIR . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = sprintf(
        'mysqldump -h %s -u %s -p%s %s > %s',
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        $backup_file
    );

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception('Backup failed');
    }

    // Clean old backups
    $backups = glob(BACKUP_DIR . 'backup_*.sql');
    if (count($backups) > MAX_BACKUPS) {
        usort($backups, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        $old_backups = array_slice($backups, 0, count($backups) - MAX_BACKUPS);
        foreach ($old_backups as $backup) {
            unlink($backup);
        }
    }

    return $backup_file;
}

// Statistics functions
function get_contest_statistics($contest_id = null) {
    $pdo = get_db_connection();

    if ($contest_id === null) {
        $stmt = $pdo->query("
            SELECT
                COUNT(*) as total_contests,
                SUM(participants) as total_participants,
                SUM(votes) as total_votes
            FROM " . TABLES['contests']
        );
    } else {
        $stmt = $pdo->prepare("
            SELECT
                c.*,
                COUNT(DISTINCT ct.id) as total_contestants,
                COUNT(DISTINCT v.id) as total_votes
            FROM " . TABLES['contests'] . " c
            LEFT JOIN " . TABLES['contestants'] . " ct ON c.id = ct.contest_id
            LEFT JOIN " . TABLES['votes'] . " v ON ct.id = v.contestant_id
            WHERE c.id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$contest_id]);
    }

    return $stmt->fetch();
}

function get_user_statistics() {
    $pdo = get_db_connection();
    $stmt = $pdo->query("
        SELECT
            COUNT(*) as total_users,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
            COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_users,
            COUNT(CASE WHEN status = 'banned' THEN 1 END) as banned_users
        FROM " . TABLES['users']
    );
    return $stmt->fetch();
}

function get_vote_statistics($period = 'today') {
    $pdo = get_db_connection();

    $date_condition = '';
    switch ($period) {
        case 'today':
            $date_condition = 'DATE(created_at) = CURDATE()';
            break;
        case 'week':
            $date_condition = 'YEARWEEK(created_at) = YEARWEEK(CURDATE())';
            break;
        case 'month':
            $date_condition = 'YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())';
            break;
        case 'year':
            $date_condition = 'YEAR(created_at) = YEAR(CURDATE())';
            break;
    }

    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_votes,
            COUNT(DISTINCT user_id) as unique_voters,
            COUNT(DISTINCT contestant_id) as voted_contestants
        FROM " . TABLES['votes'] . "
        WHERE " . $date_condition
    );
    $stmt->execute();

    return $stmt->fetch();
}

// Dashboard Statistics Functions
function get_total_users() {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}

function get_active_contests() {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM contests WHERE status = 'active'");
    return $stmt->fetchColumn();
}

function get_total_votes() {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM votes");
    return $stmt->fetchColumn();
}

function get_new_users_today() {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    return $stmt->fetchColumn();
}

function get_recent_activities($limit = 5) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT a.*, u.name as user_name
        FROM activities a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function get_recent_notifications($limit = 5) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("
        SELECT *
        FROM notifications
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}