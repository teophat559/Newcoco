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

// Xử lý cập nhật cài đặt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Cập nhật thông tin cơ bản
        $stmt = $pdo->prepare("
            UPDATE settings
            SET site_name = ?,
                site_description = ?,
                contact_email = ?,
                contact_phone = ?,
                facebook_url = ?,
                twitter_url = ?,
                instagram_url = ?
            WHERE id = 1
        ");
        $stmt->execute([
            $_POST['site_name'],
            $_POST['site_description'],
            $_POST['contact_email'],
            $_POST['contact_phone'],
            $_POST['facebook_url'],
            $_POST['twitter_url'],
            $_POST['instagram_url']
        ]);

        // Cập nhật cài đặt bình chọn
        $stmt = $pdo->prepare("
            UPDATE settings
            SET vote_limit = ?,
                vote_interval = ?,
                require_login = ?
            WHERE id = 1
        ");
        $stmt->execute([
            $_POST['vote_limit'],
            $_POST['vote_interval'],
            isset($_POST['require_login']) ? 1 : 0
        ]);

        // Cập nhật cài đặt email
        $stmt = $pdo->prepare("
            UPDATE settings
            SET smtp_host = ?,
                smtp_port = ?,
                smtp_user = ?,
                smtp_pass = ?,
                smtp_encryption = ?
            WHERE id = 1
        ");
        $stmt->execute([
            $_POST['smtp_host'],
            $_POST['smtp_port'],
            $_POST['smtp_user'],
            $_POST['smtp_pass'],
            $_POST['smtp_encryption']
        ]);

        $success = 'Cập nhật cài đặt thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy thông tin cài đặt
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt hệ thống - <?php echo SITE_NAME; ?></title>
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
                            <a class="nav-link active" href="settings.php">
                                <i class="bi bi-gear"></i>
                                Cài đặt
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Cài đặt hệ thống</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Tên website</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name"
                                               value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="site_description" class="form-label">Mô tả website</label>
                                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Email liên hệ</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                                               value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                               value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="facebook_url" class="form-label">Facebook URL</label>
                                        <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                               value="<?php echo htmlspecialchars($settings['facebook_url']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="twitter_url" class="form-label">Twitter URL</label>
                                        <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                               value="<?php echo htmlspecialchars($settings['twitter_url']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="instagram_url" class="form-label">Instagram URL</label>
                                        <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                               value="<?php echo htmlspecialchars($settings['instagram_url']); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Cài đặt bình chọn</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="vote_limit" class="form-label">Giới hạn lượt bình chọn</label>
                                        <input type="number" class="form-control" id="vote_limit" name="vote_limit"
                                               value="<?php echo $settings['vote_limit']; ?>" min="0" required>
                                        <div class="form-text">Số lượt bình chọn tối đa cho mỗi người dùng (0 = không giới hạn)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="vote_interval" class="form-label">Thời gian chờ giữa các lượt bình chọn (giây)</label>
                                        <input type="number" class="form-control" id="vote_interval" name="vote_interval"
                                               value="<?php echo $settings['vote_interval']; ?>" min="0" required>
                                        <div class="form-text">Thời gian tối thiểu giữa hai lượt bình chọn (0 = không giới hạn)</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="require_login" name="require_login"
                                                   <?php echo $settings['require_login'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="require_login">Yêu cầu đăng nhập để bình chọn</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Cài đặt email</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                               value="<?php echo htmlspecialchars($settings['smtp_host']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="smtp_port" class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                                               value="<?php echo $settings['smtp_port']; ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="smtp_user" class="form-label">SMTP Username</label>
                                        <input type="text" class="form-control" id="smtp_user" name="smtp_user"
                                               value="<?php echo htmlspecialchars($settings['smtp_user']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="smtp_pass" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass"
                                               value="<?php echo htmlspecialchars($settings['smtp_pass']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="smtp_encryption" class="form-label">SMTP Encryption</label>
                                        <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                            <option value="tls" <?php echo $settings['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            <option value="ssl" <?php echo $settings['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                            <option value="" <?php echo empty($settings['smtp_encryption']) ? 'selected' : ''; ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>
                            Lưu cài đặt
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>