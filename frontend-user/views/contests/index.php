<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Cuộc thi</h1>

            <!-- Search and Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm cuộc thi..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Đang diễn ra</option>
                                <option value="upcoming" <?php echo ($_GET['status'] ?? '') === 'upcoming' ? 'selected' : ''; ?>>Sắp diễn ra</option>
                                <option value="ended" <?php echo ($_GET['status'] ?? '') === 'ended' ? 'selected' : ''; ?>>Đã kết thúc</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort">
                                <option value="newest" <?php echo ($_GET['sort'] ?? '') === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="popular" <?php echo ($_GET['sort'] ?? '') === 'popular' ? 'selected' : ''; ?>>Phổ biến nhất</option>
                                <option value="ending" <?php echo ($_GET['sort'] ?? '') === 'ending' ? 'selected' : ''; ?>>Sắp kết thúc</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contests Grid -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($contests as $contest): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($contest['image']): ?>
                        <img src="<?php echo htmlspecialchars($contest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contest['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($contest['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($contest['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?php echo $contest['status'] === 'active' ? 'success' : ($contest['status'] === 'upcoming' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst($contest['status']); ?>
                                </span>
                                <small class="text-muted">
                                    <?php echo $contest['contestant_count']; ?> thí sinh
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Kết thúc: <?php echo date('d/m/Y', strtotime($contest['end_date'])); ?>
                                </small>
                                <a href="/contests/<?php echo $contest['id']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>">Trước</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>">Sau</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>