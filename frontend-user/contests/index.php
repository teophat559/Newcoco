<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Danh sách cuộc thi';
require_once __DIR__ . '/../includes/header.php';

// Lấy danh sách cuộc thi đang diễn ra
$contests = get_active_contests();
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Cuộc thi đang diễn ra</h2>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (empty($contests)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Hiện tại không có cuộc thi nào đang diễn ra.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($contests as $contest): ?>
            <div class="col">
                <div class="card h-100 contest-card">
                    <?php if ($contest['banner']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/contests/' . $contest['banner']; ?>"
                             class="card-img-top contest-image" alt="<?php echo sanitize_output($contest['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title contest-title">
                            <?php echo sanitize_output($contest['title']); ?>
                        </h5>
                        <p class="card-text contest-description">
                            <?php echo sanitize_output(substr($contest['description'], 0, 150)) . '...'; ?>
                        </p>
                        <div class="contest-meta">
                            <span>
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo format_date($contest['end_date']); ?>
                            </span>
                            <span>
                                <i class="fas fa-users me-1"></i>
                                <?php echo format_number($contest['contestant_count']); ?> thí sinh
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $contest['id']; ?>"
                           class="btn btn-primary w-100">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>