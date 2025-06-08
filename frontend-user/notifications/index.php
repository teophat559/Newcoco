<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    set_flash_message('warning', 'Vui lòng đăng nhập để xem thông báo');
    redirect(SITE_URL . '/auth/login.php');
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Lấy danh sách thông báo
$notifications = get_user_notifications($_SESSION['user_id'], $per_page, $offset);
$total_notifications = get_user_total_notifications($_SESSION['user_id']);
$total_pages = ceil($total_notifications / $per_page);

$page_title = 'Thông báo';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Thông báo</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Danh sách thông báo</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <p class="text-muted mb-0">Không có thông báo nào</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $notification): ?>
                            <a href="<?php echo SITE_URL; ?>/notifications/view.php?id=<?php echo $notification['id']; ?>"
                               class="list-group-item list-group-item-action <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo sanitize_output($notification['title']); ?></h6>
                                    <small><?php echo format_date($notification['created_at']); ?></small>
                                </div>
                                <p class="mb-1"><?php echo sanitize_output($notification['message']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>