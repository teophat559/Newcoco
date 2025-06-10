<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Admin Panel</title>
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-header">
                <h1>Admin Panel</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="/admin/dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="/admin/contests.php"><i class="fas fa-trophy"></i> Contests</a></li>
                <li><a href="/admin/contestants.php"><i class="fas fa-users"></i> Contestants</a></li>
                <li><a href="/admin/users.php"><i class="fas fa-user"></i> Users</a></li>
                <li><a href="/admin/notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="/admin/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        <main class="admin-content">