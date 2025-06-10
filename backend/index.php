<?php
require_once 'config.php';
require_once 'includes/header.php';

// Kiểm tra trạng thái bảo trì
if (file_exists('maintenance.php') && !isset($_SESSION['admin'])) {
    include 'maintenance.php';
    exit;
}

// Lấy danh sách cuộc thi đang diễn ra
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(v.id) as vote_count
    FROM contests c
    LEFT JOIN votes v ON c.id = v.contest_id
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt->execute();
$contests = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center mb-4">Cuộc Thi Sắc Đẹp</h1>

            <?php if (empty($contests)): ?>
                <div class="alert alert-info">
                    Hiện tại không có cuộc thi nào đang diễn ra.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($contests as $contest): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($contest['image']): ?>
                                    <img src="<?php echo UPLOAD_PATH . $contest['image']; ?>"
                                         class="card-img-top"
                                         alt="<?php echo htmlspecialchars($contest['title']); ?>">
                                <?php endif; ?>

                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($contest['title']); ?></h5>
                                    <p class="card-text">
                                        <?php echo htmlspecialchars(substr($contest['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            <?php echo number_format($contest['vote_count']); ?> lượt bình chọn
                                        </span>
                                        <a href="contest-details.php?id=<?php echo $contest['id']; ?>"
                                           class="btn btn-primary">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>

                                <div class="card-footer text-muted">
                                    Kết thúc: <?php echo date('d/m/Y', strtotime($contest['end_date'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>