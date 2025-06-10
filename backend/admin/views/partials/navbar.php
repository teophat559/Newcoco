<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if ($unread_notifications > 0): ?>
                            <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                        <h6 class="dropdown-header">Thông báo</h6>
                        <?php if (empty($notifications)): ?>
                            <div class="dropdown-item text-center">Không có thông báo mới</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <a class="dropdown-item <?php echo $notification['is_read'] ? '' : 'bg-light'; ?>" href="<?php echo $notification['link']; ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas <?php echo $notification['icon']; ?> fa-fw"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="small text-muted"><?php echo format_datetime($notification['created_at']); ?></div>
                                            <div><?php echo $notification['message']; ?></div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?php echo APP_URL; ?>/admin/notifications.php">
                                Xem tất cả thông báo
                            </a>
                        <?php endif; ?>
                    </div>
                </li>

                <!-- Help -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/admin/help.php">
                        <i class="fas fa-question-circle"></i>
                    </a>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="<?php echo APP_URL; ?>/assets/img/avatars/<?php echo $_SESSION['admin_avatar'] ?? 'default.png'; ?>" alt="Avatar" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-item-text">
                                <strong><?php echo $_SESSION['admin_name']; ?></strong>
                                <div class="small text-muted"><?php echo $_SESSION['admin_email']; ?></div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/profile.php">Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/settings.php">Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/admin/logout.php">Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>