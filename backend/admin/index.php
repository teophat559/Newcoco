<?php
require_once 'includes/init.php';
require_once 'includes/header.php';
require_once 'includes/quick-actions.php';
require_once 'includes/quick-action-modals.php';
?>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Tổng quan</h1>
            </div>

            <!-- Quick Actions Section -->
            <div class="quick-actions-section mb-4">
                <h3 class="h4 mb-3">Thao tác nhanh</h3>
                <?php render_quick_actions(); ?>
            </div>

            <!-- Statistics Section -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Tổng người dùng</h5>
                            <h2 class="mb-0"><?php echo number_format(get_total_users()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Cuộc thi đang diễn ra</h5>
                            <h2 class="mb-0"><?php echo number_format(get_active_contests()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Tổng lượt bình chọn</h5>
                            <h2 class="mb-0"><?php echo number_format(get_total_votes()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Người dùng mới hôm nay</h5>
                            <h2 class="mb-0"><?php echo number_format(get_new_users_today()); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Hoạt động gần đây</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach (get_recent_activities(5) as $activity): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['description']); ?></h6>
                                        <small class="text-muted"><?php echo format_date($activity['created_at']); ?></small>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars($activity['user_name']); ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Thông báo mới</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach (get_recent_notifications(5) as $notification): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                        <small class="text-muted"><?php echo format_date($notification['created_at']); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<!-- Include Quick Actions JavaScript -->
<script src="assets/js/quick-actions.js"></script>

<?php require_once 'includes/footer.php'; ?>