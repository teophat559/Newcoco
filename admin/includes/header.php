<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Admin.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin = new Admin($admin_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="/admin/assets/img/favicon.ico" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="/admin/assets/js/admin.js" defer></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="/admin/assets/img/logo.png" alt="Logo" class="logo">
            <h3><?php echo SITE_NAME; ?></h3>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/contests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contests.php' ? 'active' : ''; ?>">
                        <i class="fas fa-trophy"></i>
                        <span>Contests</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/contestants.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contestants.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>Contestants</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/votes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'votes.php' ? 'active' : ''; ?>">
                        <i class="fas fa-vote-yea"></i>
                        <span>Votes</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/notifications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="nav-left">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="nav-right">
                <div class="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>

                <div class="admin-profile">
                    <img src="<?php echo $admin->getProfileImage(); ?>" alt="Admin">
                    <div class="dropdown">
                        <button class="dropdown-toggle">
                            <?php echo $admin->getName(); ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/admin/profile.php">
                                <i class="fas fa-user"></i>
                                Profile
                            </a>
                            <a href="/admin/settings.php">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/admin/logout.php" class="text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['flash_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>