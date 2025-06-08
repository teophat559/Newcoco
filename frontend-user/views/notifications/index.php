<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Thông báo</h1>

            <!-- Notifications List -->
            <div class="card">
                <div class="list-group list-group-flush">
                    <?php if (empty($notifications)): ?>
                    <div class="list-group-item text-center py-5">
                        <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                        <p class="mb-0">Bạn chưa có thông báo nào</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item <?php echo $notification['read_at'] ? '' : 'bg-light'; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h5>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <?php if (!$notification['read_at']): ?>
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                        Đánh dấu đã đọc
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($notification['link']): ?>
                                    <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="btn btn-sm btn-primary">
                                        Xem chi tiết
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Trước</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Sau</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch('/ajax/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>