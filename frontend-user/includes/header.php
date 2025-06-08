<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Notification.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get unread notifications count
$notification = new Notification($db);
$unread_count = $notification->get_unread_count(get_current_user_id());
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Meta tags -->
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : APP_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? $page_keywords : APP_KEYWORDS; ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title : APP_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : APP_DESCRIPTION; ?>">
    <meta property="og:image" content="<?php echo isset($page_image) ? $page_image : APP_LOGO; ?>">
    <meta property="og:url" content="<?php echo current_url(); ?>">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <img src="assets/images/logo.png" alt="<?php echo APP_NAME; ?>">
                    </a>
                </div>

                <nav class="nav">
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a href="contests.php" class="nav-link">Cuộc thi</a>
                        </li>
                        <?php if (is_logged_in()): ?>
                            <li class="nav-item">
                                <a href="dashboard.php" class="nav-link">Bảng điều khiển</a>
                            </li>
                            <li class="nav-item">
                                <a href="profile.php" class="nav-link">Hồ sơ</a>
                            </li>
                            <li class="nav-item">
                                <a href="notifications.php" class="nav-link notification-bell">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($unread_count > 0): ?>
                                        <span class="badge"><?php echo $unread_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link">Đăng xuất</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a href="register.php" class="nav-link">Đăng ký</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>">
                <?php
                echo $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
            </div>
        <?php endif; ?>