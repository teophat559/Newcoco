<?php
session_start();
require_once 'config.php';

// Lấy danh sách cuộc thi
$stmt = $pdo->query("SELECT * FROM contests WHERE status = 'active' ORDER BY created_at DESC");
$contests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuộc thi - Newcoco</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Newcoco</div>
            <button class="menu-toggle">Menu</button>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="contests.php" class="active">Cuộc thi</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Tài khoản</a></li>
                    <li><a href="logout.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="login.php">Đăng nhập</a></li>
                    <li><a href="register.php">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Danh sách cuộc thi</h1>

            <div class="row">
                <?php if (empty($contests)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Hiện không có cuộc thi nào đang diễn ra.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($contests as $contest): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($contest['image']): ?>
                                    <img src="<?php echo htmlspecialchars($contest['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contest['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($contest['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($contest['description']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Thời gian: <?php echo date('d/m/Y', strtotime($contest['start_date'])); ?> -
                                            <?php echo date('d/m/Y', strtotime($contest['end_date'])); ?>
                                        </small>
                                        <a href="contest-details.php?id=<?php echo $contest['id']; ?>" class="btn btn-primary">Chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Newcoco. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>