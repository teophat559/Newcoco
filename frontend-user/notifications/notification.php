<?php
if (!isset($user_id)) {
    return;
}

$notifications = get_user_notifications($user_id);
$unread_count = count_unread_notifications($user_id);
?>

<div class="notification-dropdown">
    <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>
    <div class="dropdown-menu dropdown-menu-end notification-menu">
        <div class="notification-header">
            <h6 class="mb-0">Thông báo</h6>
            <?php if ($unread_count > 0): ?>
                <button class="btn btn-link btn-sm mark-all-read">Đánh dấu đã đọc</button>
            <?php endif; ?>
        </div>
        <div class="notification-body">
            <?php if (empty($notifications)): ?>
                <div class="text-center py-3">
                    <i class="fas fa-bell-slash text-muted mb-2"></i>
                    <p class="text-muted mb-0">Không có thông báo</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>"
                         data-id="<?php echo $notification['id']; ?>">
                        <div class="notification-icon bg-<?php echo $notification['type']; ?>">
                            <i class="fas fa-<?php echo get_notification_icon($notification['type']); ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></div>
                            <div class="notification-time">
                                <?php echo time_elapsed_string($notification['created_at']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.notification-dropdown {
    position: relative;
}

.notification-menu {
    width: 320px;
    padding: 0;
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.notification-header {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-body {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 1rem;
    display: flex;
    gap: 1rem;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
    cursor: pointer;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item.unread {
    background: #f0f7ff;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.notification-time {
    font-size: 0.8rem;
    color: #adb5bd;
}

.bg-success {
    background: linear-gradient(45deg, #28a745, #218838);
}

.bg-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
}

.bg-info {
    background: linear-gradient(45deg, #17a2b8, #138496);
}

.bg-warning {
    background: linear-gradient(45deg, #ffc107, #d39e00);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý click vào notification
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.id;
            markNotificationRead(id);
            this.classList.remove('unread');
            updateUnreadCount();
        });
    });

    // Xử lý đánh dấu tất cả đã đọc
    const markAllReadBtn = document.querySelector('.mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                const id = item.dataset.id;
                markNotificationRead(id);
                item.classList.remove('unread');
            });
            updateUnreadCount();
        });
    }
});

function markNotificationRead(id) {
    fetch('<?php echo SITE_URL; ?>/user/notifications/mark-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    });
}

function updateUnreadCount() {
    const badge = document.querySelector('.badge');
    if (badge) {
        const count = parseInt(badge.textContent) - 1;
        if (count <= 0) {
            badge.remove();
        } else {
            badge.textContent = count;
        }
    }
}
</script>