<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$contest_id = $_GET['id'] ?? 0;
$contest = get_contest($contest_id);

if (!$contest) {
    set_flash_message('danger', 'Cuộc thi không tồn tại!');
    redirect(SITE_URL . '/contests');
}

$page_title = $contest['title'];
require_once __DIR__ . '/../includes/header.php';

// Lấy danh sách thí sinh
$contestants = get_contest_contestants($contest_id);

// Kiểm tra xem user đã bình chọn chưa
$has_voted = is_logged_in() ? has_voted($_SESSION['user_id'], $contest_id) : false;
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/contests">Cuộc thi</a></li>
                <li class="breadcrumb-item active"><?php echo sanitize_output($contest['title']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-3"><?php echo sanitize_output($contest['title']); ?></h2>
        <?php if ($contest['banner']): ?>
            <img src="<?php echo SITE_URL . '/uploads/contests/' . $contest['banner']; ?>"
                 class="img-fluid rounded mb-3" alt="<?php echo sanitize_output($contest['title']); ?>">
        <?php endif; ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Mô tả</h5>
                <p class="card-text"><?php echo nl2br(sanitize_output($contest['description'])); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thông tin cuộc thi</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Bắt đầu: <?php echo format_date($contest['start_date']); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-calendar-check me-2"></i>
                        Kết thúc: <?php echo format_date($contest['end_date']); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-users me-2"></i>
                        Số thí sinh: <?php echo format_number($contest['contestant_count']); ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-vote-yea me-2"></i>
                        Tổng lượt bình chọn: <?php echo format_number($contest['vote_count']); ?>
                    </li>
                </ul>
                <?php if (is_logged_in() && !$has_voted && strtotime($contest['end_date']) > time()): ?>
                    <a href="<?php echo SITE_URL; ?>/contestants/register.php?contest_id=<?php echo $contest['id']; ?>"
                       class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>
                        Đăng ký tham gia
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="mb-4">Danh sách thí sinh</h3>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (empty($contestants)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Chưa có thí sinh nào tham gia.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($contestants as $contestant): ?>
            <div class="col">
                <div class="card h-100 contestant-card">
                    <?php if ($contestant['image']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/contestants/' . $contestant['image']; ?>"
                             class="contestant-image" alt="<?php echo sanitize_output($contestant['full_name']); ?>">
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <h5 class="contestant-name">
                            <?php echo sanitize_output($contestant['full_name']); ?>
                        </h5>
                        <p class="contestant-votes">
                            <?php echo format_number($contestant['vote_count']); ?> lượt bình chọn
                        </p>
                        <?php if (is_logged_in() && !$has_voted && strtotime($contest['end_date']) > time()): ?>
                            <button type="button" class="btn btn-primary vote-btn"
                                    onclick="handleVote(<?php echo $contest['id']; ?>, <?php echo $contestant['id']; ?>)"
                                    data-contestant-id="<?php echo $contestant['id']; ?>">
                                <i class="fas fa-vote-yea me-2"></i>
                                Bình chọn
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>