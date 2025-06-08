<?php
require_once 'config.php';
require_once 'includes/Activity.php';
require_once 'includes/Notification.php';

use FrontendUser\Includes\Activity;
use FrontendUser\Includes\Notification;

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_current_user_id();
$activity = new Activity($db, $user_id);
$notification = new Notification($db, $user_id);

// Get recent activities
$activities = $activity->get_user_activities($user_id, 5);

// Get unread notifications
$notifications = $notification->get_user_notifications($user_id, 5);
$unread_count = $notification->get_unread_count($user_id);

// Get active contests
$active_contests = $db->query("
    SELECT c.*, COUNT(ct.id) as contestant_count
    FROM contests c
    LEFT JOIN contestants ct ON c.id = ct.contest_id
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY c.end_date ASC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="dashboard-header">
            <h1>Bảng điều khiển</h1>
            <div class="notification-bell">
                <a href="notifications.php" class="notification-link">
                    <i class="fas fa-bell"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Active Contests -->
            <div class="dashboard-card">
                <h2>Cuộc thi đang diễn ra</h2>
                <?php if (empty($active_contests)): ?>
                    <p class="no-data">Không có cuộc thi nào đang diễn ra.</p>
                <?php else: ?>
                    <div class="contest-list">
                        <?php foreach ($active_contests as $contest): ?>
                            <div class="contest-item">
                                <h3><?php echo htmlspecialchars($contest['title']); ?></h3>
                                <p><?php echo htmlspecialchars($contest['description']); ?></p>
                                <div class="contest-meta">
                                    <span>Thí sinh: <?php echo $contest['contestant_count']; ?></span>
                                    <span>Kết thúc: <?php echo format_date($contest['end_date'], 'd/m/Y'); ?></span>
                                </div>
                                <a href="contests/view.php?id=<?php echo $contest['id']; ?>" class="btn">Xem chi tiết</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activities -->
            <div class="dashboard-card">
                <h2>Hoạt động gần đây</h2>
                <?php if (empty($activities)): ?>
                    <p class="no-data">Chưa có hoạt động nào.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($activities as $item): ?>
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars($item['description']); ?></div>
                                    <div class="activity-time">
                                        <?php echo format_date($item['created_at'], 'd/m/Y H:i'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Notifications -->
            <div class="dashboard-card">
                <h2>Thông báo gần đây</h2>
                <?php if (empty($notifications)): ?>
                    <p class="no-data">Không có thông báo nào.</p>
                <?php else: ?>
                    <div class="notification-list">
                        <?php foreach ($notifications as $item): ?>
                            <div class="notification-item <?php echo $item['is_read'] ? '' : 'unread'; ?>">
                                <div class="notification-content">
                                    <div class="notification-title"><?php echo htmlspecialchars($item['message']); ?></div>
                                    <div class="notification-time">
                                        <?php echo format_date($item['created_at'], 'd/m/Y H:i'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>