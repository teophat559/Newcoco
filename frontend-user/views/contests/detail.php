<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Contest Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <?php if ($contest['image']): ?>
                <img src="<?php echo htmlspecialchars($contest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contest['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($contest['title']); ?></h1>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-<?php echo $contest['status'] === 'active' ? 'success' : ($contest['status'] === 'upcoming' ? 'warning' : 'secondary'); ?>">
                            <?php echo ucfirst($contest['status']); ?>
                        </span>
                        <small class="text-muted">
                            Bắt đầu: <?php echo date('d/m/Y', strtotime($contest['start_date'])); ?> |
                            Kết thúc: <?php echo date('d/m/Y', strtotime($contest['end_date'])); ?>
                        </small>
                    </div>
                    <div class="card-text">
                        <?php echo nl2br(htmlspecialchars($contest['description'])); ?>
                    </div>
                </div>
            </div>

            <!-- Contest Rules -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thể lệ cuộc thi</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($contest['rules'])); ?>
                </div>
            </div>

            <!-- Contest Prizes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Giải thưởng</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br(htmlspecialchars($contest['prizes'])); ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Contest Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng số thí sinh
                            <span class="badge bg-primary rounded-pill"><?php echo $contest['contestant_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tổng số lượt bình chọn
                            <span class="badge bg-primary rounded-pill"><?php echo $contest['vote_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Lượt xem
                            <span class="badge bg-primary rounded-pill"><?php echo $contest['view_count']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contest Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <?php if ($contest['status'] === 'active'): ?>
                        <?php if ($isRegistered): ?>
                            <a href="/contestants/register/<?php echo $contest['id']; ?>" class="btn btn-success w-100 mb-2">Đăng ký tham gia</a>
                        <?php endif; ?>
                        <a href="/contests/<?php echo $contest['id']; ?>/vote" class="btn btn-primary w-100">Bình chọn</a>
                    <?php elseif ($contest['status'] === 'upcoming'): ?>
                        <button class="btn btn-secondary w-100" disabled>Chưa bắt đầu</button>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>Đã kết thúc</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Share Contest -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Chia sẻ</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>"
                           class="btn btn-outline-primary" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($currentUrl); ?>&text=<?php echo urlencode($contest['title']); ?>"
                           class="btn btn-outline-info" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <button class="btn btn-outline-success" onclick="copyLink()">
                            <i class="fas fa-link"></i> Sao chép link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contestants -->
    <div class="row mt-4">
        <div class="col-12">
            <h2 class="mb-4">Top thí sinh</h2>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($topContestants as $contestant): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($contestant['image']): ?>
                        <img src="<?php echo htmlspecialchars($contestant['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contestant['name']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($contestant['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($contestant['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">
                                    <?php echo $contestant['vote_count']; ?> lượt bình chọn
                                </span>
                                <a href="/contestants/<?php echo $contestant['id']; ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const dummy = document.createElement('input');
    document.body.appendChild(dummy);
    dummy.value = window.location.href;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    alert('Đã sao chép link!');
}
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>