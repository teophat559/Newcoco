<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    set_flash_message('warning', 'Vui lòng đăng nhập để xem danh sách thí sinh');
    redirect(SITE_URL . '/auth/login.php');
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Lọc theo cuộc thi
$contest_id = isset($_GET['contest_id']) ? (int)$_GET['contest_id'] : 0;
$contest = $contest_id ? get_contest_by_id($contest_id) : null;

// Lấy danh sách thí sinh
$contestants = get_contestants($contest_id, $per_page, $offset);
$total_contestants = get_total_contestants($contest_id);
$total_pages = ceil($total_contestants / $per_page);

$page_title = 'Danh sách thí sinh';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/contests">Cuộc thi</a></li>
                <?php if ($contest): ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $contest['id']; ?>">
                            <?php echo sanitize_output($contest['title']); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active">Thí sinh</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <?php echo $contest ? 'Thí sinh tham gia ' . sanitize_output($contest['title']) : 'Danh sách thí sinh'; ?>
                    </h5>
                    <?php if ($contest): ?>
                        <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $contest['id']; ?>"
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại cuộc thi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($contestants)): ?>
                    <p class="text-muted mb-0">Không có thí sinh nào</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Thí sinh</th>
                                    <?php if (!$contest): ?>
                                        <th>Cuộc thi</th>
                                    <?php endif; ?>
                                    <th>Ngày tham gia</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contestants as $index => $contestant): ?>
                                    <tr>
                                        <td><?php echo $offset + $index + 1; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $contestant['avatar'] ? SITE_URL . '/uploads/avatars/' . $contestant['avatar'] : 'https://via.placeholder.com/32'; ?>"
                                                     class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                <div>
                                                    <a href="<?php echo SITE_URL; ?>/contestants/profile.php?id=<?php echo $contestant['user_id']; ?>">
                                                        <?php echo sanitize_output($contestant['full_name']); ?>
                                                    </a>
                                                    <div class="small text-muted">
                                                        <?php echo sanitize_output($contestant['email']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <?php if (!$contest): ?>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $contestant['contest_id']; ?>">
                                                    <?php echo sanitize_output($contestant['contest_title']); ?>
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                        <td><?php echo format_date($contestant['joined_at']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo get_contestant_status_badge($contestant['status']); ?>">
                                                <?php echo get_contestant_status_text($contestant['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>/contestants/profile.php?id=<?php echo $contestant['user_id']; ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $contest_id ? '&contest_id=' . $contest_id : ''; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $contest_id ? '&contest_id=' . $contest_id : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $contest_id ? '&contest_id=' . $contest_id : ''; ?>">
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