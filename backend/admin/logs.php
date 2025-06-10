<?php
session_start();
require_once '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE a.name LIKE ? OR a.email LIKE ? OR l.action LIKE ? OR l.ip_address LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

// Lấy tổng số bản ghi
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM admin_logs l
        JOIN admins a ON l.admin_id = a.id
        $where
    ");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $total_pages = ceil($total / $limit);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Lấy danh sách log
try {
    $stmt = $pdo->prepare("
        SELECT l.*, a.name as admin_name, a.email as admin_email
        FROM admin_logs l
        JOIN admins a ON l.admin_id = a.id
        $where
        ORDER BY l.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử hoạt động - <?php echo SITE_NAME; ?></title>
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
                            <a class="nav-link" href="contests.php">
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
                    <h1 class="h2">Lịch sử hoạt động</h1>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search"
                                           value="<?php echo htmlspecialchars($search); ?>"
                                           placeholder="Tìm kiếm theo tên, email, hành động...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                        Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Admin</th>
                                        <th>Hành động</th>
                                        <th>IP</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log['id']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($log['admin_name']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($log['admin_email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>