<?php
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/functions.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit;
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get admin info
$admin = isset($_SESSION['admin_id']) ? get_admin_info($_SESSION['admin_id']) : null;

// Get notifications
$notifications = isset($_SESSION['admin_id']) ? get_admin_notifications($_SESSION['admin_id']) : [];

// Get unread notifications count
$unread_notifications = isset($_SESSION['admin_id']) ? count_unread_notifications($_SESSION['admin_id']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo get_page_title($current_page); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/admin.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo.png" alt="Logo" height="30">
                <?php echo APP_NAME; ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="index.php">
                                <i class="bi bi-house"></i> Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'contests' ? 'active' : ''; ?>" href="contests.php">
                                <i class="bi bi-trophy"></i> Cuộc thi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'contestants' ? 'active' : ''; ?>" href="contestants.php">
                                <i class="bi bi-people"></i> Thí sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'votes' ? 'active' : ''; ?>" href="votes.php">
                                <i class="bi bi-hand-thumbs-up"></i> Bình chọn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" href="users.php">
                                <i class="bi bi-person"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'admins' ? 'active' : ''; ?>" href="admins.php">
                                <i class="bi bi-shield-lock"></i> Quản trị viên
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" href="settings.php">
                                <i class="bi bi-gear"></i> Cài đặt
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <?php if ($unread_notifications > 0): ?>
                                    <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <?php if (empty($notifications)): ?>
                                    <div class="dropdown-item text-center">Không có thông báo mới</div>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <a class="dropdown-item <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>" href="<?php echo $notification['link']; ?>">
                                            <?php echo $notification['message']; ?>
                                            <small class="d-block text-muted"><?php echo format_datetime($notification['created_at']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?php echo $admin['username']; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person"></i> Hồ sơ
                                </a>
                                <a class="dropdown-item" href="help.php">
                                    <i class="bi bi-question-circle"></i> Trợ giúp
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="container-fluid py-4">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['flash_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>