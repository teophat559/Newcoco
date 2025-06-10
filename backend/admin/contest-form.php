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
$contest = [
    'id' => '',
    'title' => '',
    'description' => '',
    'image' => '',
    'start_date' => '',
    'end_date' => '',
    'status' => 'pending'
];

// Lấy thông tin cuộc thi nếu đang sửa
if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contests WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $contest = $stmt->fetch();
        if (!$contest) {
            header('Location: contests.php');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contest['title'] = $_POST['title'] ?? '';
    $contest['description'] = $_POST['description'] ?? '';
    $contest['start_date'] = $_POST['start_date'] ?? '';
    $contest['end_date'] = $_POST['end_date'] ?? '';
    $contest['status'] = $_POST['status'] ?? 'pending';

    // Validate
    if (empty($contest['title'])) {
        $error = 'Vui lòng nhập tiêu đề cuộc thi';
    } elseif (empty($contest['description'])) {
        $error = 'Vui lòng nhập mô tả cuộc thi';
    } elseif (empty($contest['start_date'])) {
        $error = 'Vui lòng chọn ngày bắt đầu';
    } elseif (empty($contest['end_date'])) {
        $error = 'Vui lòng chọn ngày kết thúc';
    } elseif (strtotime($contest['end_date']) <= strtotime($contest['start_date'])) {
        $error = 'Ngày kết thúc phải sau ngày bắt đầu';
    } else {
        try {
            // Xử lý upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    throw new Exception('Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)');
                }

                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    throw new Exception('Kích thước file không được vượt quá 5MB');
                }

                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../uploads/contests/' . $new_filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception('Không thể upload file');
                }

                // Xóa ảnh cũ nếu đang sửa
                if (!empty($contest['image']) && file_exists('../' . $contest['image'])) {
                    unlink('../' . $contest['image']);
                }

                $contest['image'] = 'uploads/contests/' . $new_filename;
            }

            if (empty($contest['id'])) {
                // Thêm mới
                $stmt = $pdo->prepare("
                    INSERT INTO contests (title, description, image, start_date, end_date, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $contest['title'],
                    $contest['description'],
                    $contest['image'],
                    $contest['start_date'],
                    $contest['end_date'],
                    $contest['status']
                ]);
                $success = 'Thêm cuộc thi thành công';
            } else {
                // Cập nhật
                $stmt = $pdo->prepare("
                    UPDATE contests
                    SET title = ?, description = ?, image = ?, start_date = ?, end_date = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $contest['title'],
                    $contest['description'],
                    $contest['image'],
                    $contest['start_date'],
                    $contest['end_date'],
                    $contest['status'],
                    $contest['id']
                ]);
                $success = 'Cập nhật cuộc thi thành công';
            }

            // Chuyển hướng sau khi lưu thành công
            header('Location: contests.php');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo empty($contest['id']) ? 'Thêm' : 'Sửa'; ?> cuộc thi - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="h2"><?php echo empty($contest['id']) ? 'Thêm' : 'Sửa'; ?> cuộc thi</h1>
                    <a href="contests.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Quay lại
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
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề cuộc thi</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="<?php echo htmlspecialchars($contest['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($contest['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <?php if (!empty($contest['image'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars($contest['image']); ?>"
                                             alt="Current image" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Chấp nhận file jpg, jpeg, png, gif. Kích thước tối đa 5MB.</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                               value="<?php echo $contest['start_date']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                               value="<?php echo $contest['end_date']; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pending" <?php echo $contest['status'] === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                    <option value="active" <?php echo $contest['status'] === 'active' ? 'selected' : ''; ?>>Đang diễn ra</option>
                                    <option value="ended" <?php echo $contest['status'] === 'ended' ? 'selected' : ''; ?>>Đã kết thúc</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i>
                                    <?php echo empty($contest['id']) ? 'Thêm' : 'Cập nhật'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>