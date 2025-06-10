<?php
session_start();
require_once '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Xử lý xóa cuộc thi
if (isset($_POST['delete_contest'])) {
    $contest_id = $_POST['contest_id'] ?? 0;
    try {
        // Kiểm tra xem có thí sinh nào không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contestants WHERE contest_id = ?");
        $stmt->execute([$contest_id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Không thể xóa cuộc thi đã có thí sinh tham gia');
        }

        // Xóa cuộc thi
        $stmt = $pdo->prepare("DELETE FROM contests WHERE id = ?");
        $stmt->execute([$contest_id]);

        $success = 'Xóa cuộc thi thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy danh sách cuộc thi
try {
    $stmt = $pdo->query("
        SELECT c.*,
            COUNT(ct.id) as contestant_count,
            COUNT(DISTINCT v.id) as vote_count
        FROM contests c
        LEFT JOIN contestants ct ON c.id = ct.contest_id
        LEFT JOIN votes v ON ct.id = v.contestant_id
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $contests = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cuộc thi - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #f8f9fa;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../images/logo.png" alt="<?php echo SITE_NAME; ?>" height="30" class="d-inline-block align-text-top">
                Admin Panel
            </a>
            <div class="d-flex">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-white text-decoration-none" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">Thông tin cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-house-door"></i>
                                Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contests.php">
                                <i class="bi bi-trophy"></i>
                                Cuộc thi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="bi bi-people"></i>
                                Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contestants.php">
                                <i class="bi bi-person-badge"></i>
                                Thí sinh
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="votes.php">
                                <i class="bi bi-hand-thumbs-up"></i>
                                Bình chọn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="bi bi-gear"></i>
                                Cài đặt
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Quản lý cuộc thi</h1>
                    <a href="contest-form.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        Thêm cuộc thi
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hình ảnh</th>
                                        <th>Tiêu đề</th>
                                        <th>Mô tả</th>
                                        <th>Thí sinh</th>
                                        <th>Lượt bình chọn</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contests as $contest): ?>
                                        <tr>
                                            <td><?php echo $contest['id']; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($contest['image']); ?>"
                                                     alt="<?php echo htmlspecialchars($contest['title']); ?>"
                                                     class="img-thumbnail"
                                                     style="width: 100px;">
                                            </td>
                                            <td><?php echo htmlspecialchars($contest['title']); ?></td>
                                            <td><?php echo htmlspecialchars($contest['description']); ?></td>
                                            <td><?php echo number_format($contest['contestant_count']); ?></td>
                                            <td><?php echo number_format($contest['vote_count']); ?></td>
                                            <td>
                                                <?php if ($contest['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Đang diễn ra</span>
                                                <?php elseif ($contest['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">Chờ duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã kết thúc</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($contest['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="contest-form.php?id=<?php echo $contest['id']; ?>"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal<?php echo $contest['id']; ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal xác nhận xóa -->
                                                <div class="modal fade" id="deleteModal<?php echo $contest['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Xác nhận xóa</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn xóa cuộc thi "<?php echo htmlspecialchars($contest['title']); ?>"?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="contest_id" value="<?php echo $contest['id']; ?>">
                                                                    <button type="submit" name="delete_contest" class="btn btn-danger">Xóa</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>