<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    set_flash_message('warning', 'Vui lòng đăng nhập để xem trang này');
    redirect(SITE_URL . '/auth/login.php');
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy thống kê
$stats = [
    'total_contests' => get_user_total_contests($user_id),
    'active_contests' => get_user_active_contests($user_id),
    'total_participations' => get_user_total_participations($user_id),
    'total_wins' => get_user_total_wins($user_id)
];

// Lấy cuộc thi đang tham gia
$active_participations = get_user_active_participations($user_id, 5);

// Lấy thông báo mới
$notifications = get_user_notifications($user_id, 5);

$page_title = 'Tổng quan';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Tổng quan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Thống kê -->
    <div class="col-md-3">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Tổng số cuộc thi</h6>
                        <h2 class="mb-0"><?php echo $stats['total_contests']; ?></h2>
                    </div>
                    <i class="fas fa-trophy fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Cuộc thi đang diễn ra</h6>
                        <h2 class="mb-0"><?php echo $stats['active_contests']; ?></h2>
                    </div>
                    <i class="fas fa-play-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Lần tham gia</h6>
                        <h2 class="mb-0"><?php echo $stats['total_participations']; ?></h2>
                    </div>
                    <i class="fas fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Giải thưởng</h6>
                        <h2 class="mb-0"><?php echo $stats['total_wins']; ?></h2>
                    </div>
                    <i class="fas fa-medal fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cuộc thi đang tham gia -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Cuộc thi đang tham gia</h5>
            </div>
            <div class="card-body">
                <?php if (empty($active_participations)): ?>
                    <p class="text-muted mb-0">Bạn chưa tham gia cuộc thi nào</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Cuộc thi</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_participations as $participation): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $participation['contest_id']; ?>">
                                                <?php echo sanitize_output($participation['title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo format_date($participation['start_date']); ?></td>
                                        <td><?php echo format_date($participation['end_date']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo get_contest_status_badge($participation['status']); ?>">
                                                <?php echo get_contest_status_text($participation['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $participation['contest_id']; ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Thông báo mới -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông báo mới</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <p class="text-muted mb-0">Không có thông báo mới</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $notification): ?>
                            <a href="<?php echo SITE_URL; ?>/notifications/view.php?id=<?php echo $notification['id']; ?>"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo sanitize_output($notification['title']); ?></h6>
                                    <small><?php echo format_date($notification['created_at']); ?></small>
                                </div>
                                <p class="mb-1"><?php echo sanitize_output($notification['message']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>