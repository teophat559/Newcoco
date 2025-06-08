<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Thí sinh</h1>

            <!-- Search and Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm thí sinh..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="contest">
                                <option value="">Tất cả cuộc thi</option>
                                <?php foreach ($contests as $contest): ?>
                                <option value="<?php echo $contest['id']; ?>" <?php echo ($_GET['contest'] ?? '') == $contest['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($contest['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort">
                                <option value="votes" <?php echo ($_GET['sort'] ?? '') === 'votes' ? 'selected' : ''; ?>>Lượt bình chọn</option>
                                <option value="newest" <?php echo ($_GET['sort'] ?? '') === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="name" <?php echo ($_GET['sort'] ?? '') === 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contestants Grid -->
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($contestants as $contestant): ?>
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
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($contestant['contest_title']); ?>
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Đăng ký: <?php echo date('d/m/Y', strtotime($contestant['created_at'])); ?>
                                </small>
                                <a href="/contestants/<?php echo $contestant['id']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
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
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&contest=<?php echo urlencode($_GET['contest'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>">Trước</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&contest=<?php echo urlencode($_GET['contest'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&contest=<?php echo urlencode($_GET['contest'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? ''); ?>">Sau</a>
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