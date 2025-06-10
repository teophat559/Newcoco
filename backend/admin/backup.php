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

// Xử lý tạo backup
if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    try {
        // Tạo thư mục backup nếu chưa tồn tại
        $backup_dir = __DIR__ . '/../backups';
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }

        // Tạo tên file backup
        $backup_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';

        // Lấy danh sách bảng
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        // Tạo nội dung file backup
        $output = "-- Backup created on " . date('Y-m-d H:i:s') . "\n\n";
        foreach ($tables as $table) {
            // Lấy cấu trúc bảng
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $output .= "\n\n" . $row[1] . ";\n\n";

            // Lấy dữ liệu bảng
            $stmt = $pdo->query("SELECT * FROM `$table`");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $output .= "INSERT INTO `$table` VALUES (";
                for ($i = 0; $i < count($row); $i++) {
                    $row[$i] = addslashes($row[$i]);
                    $row[$i] = str_replace("\n", "\\n", $row[$i]);
                    if (isset($row[$i])) {
                        $output .= '"' . $row[$i] . '"';
                    } else {
                        $output .= '""';
                    }
                    if ($i < (count($row) - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ");\n";
            }
        }

        // Lưu file backup
        file_put_contents($backup_file, $output);

        // Ghi log
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, ip_address, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['admin_id'],
            'Tạo backup: ' . basename($backup_file),
            $_SERVER['REMOTE_ADDR']
        ]);

        $success = 'Tạo backup thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý khôi phục backup
if (isset($_GET['action']) && $_GET['action'] === 'restore' && isset($_GET['file'])) {
    try {
        $backup_file = __DIR__ . '/../backups/' . basename($_GET['file']);
        if (!file_exists($backup_file)) {
            throw new Exception('File backup không tồn tại');
        }

        // Đọc nội dung file backup
        $sql = file_get_contents($backup_file);

        // Thực thi các câu lệnh SQL
        $pdo->exec($sql);

        // Ghi log
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, ip_address, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['admin_id'],
            'Khôi phục backup: ' . basename($backup_file),
            $_SERVER['REMOTE_ADDR']
        ]);

        $success = 'Khôi phục backup thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý xóa backup
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['file'])) {
    try {
        $backup_file = __DIR__ . '/../backups/' . basename($_GET['file']);
        if (!file_exists($backup_file)) {
            throw new Exception('File backup không tồn tại');
        }

        // Xóa file
        unlink($backup_file);

        // Ghi log
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, ip_address, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['admin_id'],
            'Xóa backup: ' . basename($backup_file),
            $_SERVER['REMOTE_ADDR']
        ]);

        $success = 'Xóa backup thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Lấy danh sách file backup
$backups = [];
$backup_dir = __DIR__ . '/../backups';
if (file_exists($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $backups[] = [
                'name' => $file,
                'size' => filesize($backup_dir . '/' . $file),
                'date' => filemtime($backup_dir . '/' . $file)
            ];
        }
    }
    // Sắp xếp theo thời gian tạo mới nhất
    usort($backups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sao lưu & Khôi phục - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="h2">Sao lưu & Khôi phục</h1>
                    <a href="?action=backup" class="btn btn-primary" onclick="return confirm('Bạn có chắc muốn tạo backup?')">
                        <i class="bi bi-download"></i>
                        Tạo backup
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
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên file</th>
                                        <th>Kích thước</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($backups)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có file backup nào</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($backups as $backup): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($backup['name']); ?></td>
                                                <td><?php echo number_format($backup['size'] / 1024, 2) . ' KB'; ?></td>
                                                <td><?php echo date('d/m/Y H:i:s', $backup['date']); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="../backups/<?php echo urlencode($backup['name']); ?>"
                                                           class="btn btn-sm btn-primary"
                                                           download>
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        <a href="?action=restore&file=<?php echo urlencode($backup['name']); ?>"
                                                           class="btn btn-sm btn-warning"
                                                           onclick="return confirm('Bạn có chắc muốn khôi phục backup này? Dữ liệu hiện tại sẽ bị ghi đè.')">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </a>
                                                        <a href="?action=delete&file=<?php echo urlencode($backup['name']); ?>"
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirm('Bạn có chắc muốn xóa backup này?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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