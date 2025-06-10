<?php
session_start();
require_once 'config.php';

$contest_id = $_GET['id'] ?? 0;

// Lấy thông tin cuộc thi
$stmt = $pdo->prepare("SELECT * FROM contests WHERE id = ? AND status = 'active'");
$stmt->execute([$contest_id]);
$contest = $stmt->fetch();

if (!$contest) {
    header('Location: contests.php');
    exit;
}

// Lấy danh sách thí sinh
$stmt = $pdo->prepare("
    SELECT c.*, u.name as user_name, u.avatar
    FROM contestants c
    JOIN users u ON c.user_id = u.id
    WHERE c.contest_id = ?
    ORDER BY c.votes DESC
");
$stmt->execute([$contest_id]);
$contestants = $stmt->fetchAll();

// Kiểm tra xem người dùng đã đăng ký tham gia chưa
$is_registered = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM contestants WHERE contest_id = ? AND user_id = ?");
    $stmt->execute([$contest_id, $_SESSION['user_id']]);
    $is_registered = $stmt->fetch() ? true : false;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contest['title']); ?> - Newcoco</title>
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
            <div class="row">
                <div class="col-md-8">
                    <h1><?php echo htmlspecialchars($contest['title']); ?></h1>
                    <?php if ($contest['image']): ?>
                        <img src="<?php echo htmlspecialchars($contest['image']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($contest['title']); ?>">
                    <?php endif; ?>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Thông tin cuộc thi</h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($contest['description'])); ?></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Thời gian bắt đầu:</strong> <?php echo date('d/m/Y', strtotime($contest['start_date'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Thời gian kết thúc:</strong> <?php echo date('d/m/Y', strtotime($contest['end_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="mb-4">Danh sách thí sinh</h2>
                    <div class="row">
                        <?php foreach ($contestants as $contestant): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <?php if ($contestant['image']): ?>
                                        <img src="<?php echo htmlspecialchars($contestant['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contestant['title']); ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($contestant['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($contestant['description']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <?php if ($contestant['avatar']): ?>
                                                    <img src="<?php echo htmlspecialchars($contestant['avatar']); ?>" class="rounded-circle me-2" width="30" height="30" alt="<?php echo htmlspecialchars($contestant['user_name']); ?>">
                                                <?php endif; ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($contestant['user_name']); ?></small>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary"><?php echo $contestant['votes']; ?> lượt bình chọn</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tham gia cuộc thi</h5>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <p>Vui lòng <a href="login.php">đăng nhập</a> để tham gia cuộc thi.</p>
                            <?php elseif ($is_registered): ?>
                                <div class="alert alert-info">
                                    Bạn đã đăng ký tham gia cuộc thi này.
                                </div>
                            <?php else: ?>
                                <form action="register-contestant.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="contest_id" value="<?php echo $contest_id; ?>">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Hình ảnh</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Đăng ký tham gia</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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