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

// Xử lý thêm admin mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        // Kiểm tra email đã tồn tại
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            $error = 'Email đã tồn tại';
        } else {
            // Thêm admin mới
            $stmt = $pdo->prepare("
                INSERT INTO admins (name, email, password, status, created_at, updated_at)
                VALUES (?, ?, ?, 'active', NOW(), NOW())
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT)
            ]);
            $success = 'Thêm admin thành công';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý cập nhật trạng thái
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("
            UPDATE admins
            SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $success = 'Cập nhật trạng thái thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý xóa admin
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        // Không cho phép xóa chính mình
        if ($_GET['id'] == $_SESSION['admin_id']) {
            $error = 'Không thể xóa tài khoản của chính mình';
        } else {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $success = 'Xóa admin thành công';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy danh sách admin
try {
    $stmt = $pdo->query("
        SELECT a.*,
               (SELECT COUNT(*) FROM admin_logs WHERE admin_id = a.id) as login_count,
               (SELECT MAX(created_at) FROM admin_logs WHERE admin_id = a.id) as last_login
        FROM admins a
        ORDER BY a.created_at DESC
    ");
    $admins = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý admin - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="h2">Quản lý admin</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="bi bi-plus-lg"></i>
                        Thêm admin
                    </button>
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
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Trạng thái</th>
                                        <th>Số lần đăng nhập</th>
                                        <th>Đăng nhập cuối</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admins as $admin): ?>
                                        <tr>
                                            <td><?php echo $admin['id']; ?></td>
                                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                            <td>
                                                <?php if ($admin['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $admin['login_count']; ?></td>
                                            <td>
                                                <?php if ($admin['last_login']): ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($admin['last_login'])); ?>
                                                <?php else: ?>
                                                    Chưa đăng nhập
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="?action=toggle_status&id=<?php echo $admin['id']; ?>"
                                                       class="btn btn-sm btn-warning"
                                                       onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </a>
                                                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                                        <a href="?action=delete&id=<?php echo $admin['id']; ?>"
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirm('Bạn có chắc muốn xóa admin này?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
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

    <!-- Modal thêm admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm admin mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>