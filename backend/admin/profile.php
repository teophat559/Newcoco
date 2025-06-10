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

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Kiểm tra mật khẩu hiện tại
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!password_verify($_POST['current_password'], $admin['password'])) {
            $error = 'Mật khẩu hiện tại không đúng';
        } else {
            // Cập nhật thông tin
            $stmt = $pdo->prepare("
                UPDATE admins
                SET name = ?,
                    email = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_SESSION['admin_id']
            ]);

            // Cập nhật mật khẩu nếu có
            if (!empty($_POST['new_password'])) {
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    $error = 'Mật khẩu mới không khớp';
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE admins
                        SET password = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        password_hash($_POST['new_password'], PASSWORD_DEFAULT),
                        $_SESSION['admin_id']
                    ]);
                }
            }

            if (empty($error)) {
                $_SESSION['admin_name'] = $_POST['name'];
                $success = 'Cập nhật thông tin thành công';
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy thông tin admin
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="h2">Thông tin cá nhân</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Cập nhật thông tin</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Họ tên</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i>
                                        Cập nhật
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thông tin tài khoản</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">ID</label>
                                    <p class="form-control-static"><?php echo $admin['id']; ?></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày tạo</label>
                                    <p class="form-control-static"><?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Cập nhật lần cuối</label>
                                    <p class="form-control-static"><?php echo date('d/m/Y H:i', strtotime($admin['updated_at'])); ?></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <p class="form-control-static">
                                        <?php if ($admin['status'] === 'active'): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Không hoạt động</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>