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

// Xử lý xóa người dùng
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'] ?? 0;
    try {
        // Kiểm tra xem có thí sinh nào không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contestants WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Không thể xóa người dùng đã tham gia cuộc thi');
        }

        // Xóa người dùng
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $success = 'Xóa người dùng thành công';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Xử lý khóa/mở khóa người dùng
if (isset($_POST['toggle_status'])) {
    $user_id = $_POST['user_id'] ?? 0;
    $new_status = $_POST['new_status'] ?? '';

    if (!in_array($new_status, ['active', 'inactive'])) {
        $error = 'Trạng thái không hợp lệ';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            $success = 'Cập nhật trạng thái thành công';
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Lấy danh sách người dùng
try {
    $stmt = $pdo->query("
        SELECT u.*,
            COUNT(DISTINCT c.id) as contest_count,
            COUNT(DISTINCT v.id) as vote_count
        FROM users u
        LEFT JOIN contestants c ON u.id = c.user_id
        LEFT JOIN votes v ON u.id = v.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - <?php echo SITE_NAME; ?></title>
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
                            <a class="nav-link active" href="users.php">
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
                    <h1 class="h2">Quản lý người dùng</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" style="width: 100%; overflow-x: auto;">
                            <table class="table table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Avatar</th>
                                        <th>Email</th>
                                        <th>Số cuộc thi</th>
                                        <th>Lượt bình chọn</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td>
                                                <img src="<?php echo $user['avatar'] ? '../uploads/avatars/' . $user['avatar'] : 'https://via.placeholder.com/40'; ?>" alt="Avatar" width="40" height="40" class="rounded-circle border">
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#avatarModal<?php echo $user['id']; ?>">
                                                    <i class="bi bi-image"></i>
                                                </button>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo number_format($user['contest_count']); ?></td>
                                            <td><?php echo number_format($user['vote_count']); ?></td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Bị khóa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <input type="hidden" name="new_status" value="inactive">
                                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-lock"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <input type="hidden" name="new_status" value="active">
                                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-success">
                                                                <i class="bi bi-unlock"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal<?php echo $user['id']; ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal xác nhận xóa -->
                                                <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Xác nhận xóa</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn xóa người dùng "<?php echo htmlspecialchars($user['name']); ?>"?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <button type="submit" name="delete_user" class="btn btn-danger">Xóa</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Modal đổi avatar -->
                                        <div class="modal fade" id="avatarModal<?php echo $user['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" enctype="multipart/form-data" class="avatar-upload-form" data-user-id="<?php echo $user['id']; ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Đổi avatar cho <?php echo htmlspecialchars($user['name']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <div class="mb-3 text-center">
                                                                <img src="<?php echo $user['avatar'] ? '../uploads/avatars/' . $user['avatar'] : 'https://via.placeholder.com/100'; ?>" alt="Avatar" width="100" height="100" class="rounded-circle border mb-2 avatar-preview" id="avatar-preview-<?php echo $user['id']; ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="avatar<?php echo $user['id']; ?>" class="form-label">Chọn ảnh mới</label>
                                                                <input type="file" class="form-control avatar-input" id="avatar<?php echo $user['id']; ?>" name="avatar" accept="image/*" required onchange="previewAvatar(event, <?php echo $user['id']; ?>)">
                                                            </div>
                                                            <div class="alert alert-danger d-none" id="avatar-error-<?php echo $user['id']; ?>"></div>
                                                            <div class="alert alert-success d-none" id="avatar-success-<?php echo $user['id']; ?>"></div>
                                                            <div class="text-center d-none" id="avatar-loading-<?php echo $user['id']; ?>">
                                                                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" name="change_avatar" class="btn btn-primary">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
    <script>
    function previewAvatar(event, userId) {
        const input = event.target;
        const preview = document.getElementById('avatar-preview-' + userId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    // Hiển thị loading, alert khi submit
    const forms = document.querySelectorAll('.avatar-upload-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const userId = form.getAttribute('data-user-id');
            const errorDiv = document.getElementById('avatar-error-' + userId);
            const successDiv = document.getElementById('avatar-success-' + userId);
            const loadingDiv = document.getElementById('avatar-loading-' + userId);
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            loadingDiv.classList.remove('d-none');
        });
    });
    </script>
    <!-- PHP xử lý đổi avatar với bảo mật, kiểm tra định dạng, kích thước, chỉ cho admin -->
    <?php if (isset($_POST['change_avatar']) && isset($_POST['user_id'])) {
        if (!isset($_SESSION['admin_id'])) {
            $error = 'Bạn không có quyền thực hiện thao tác này.';
        } else {
            $user_id = (int)$_POST['user_id'];
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                $max_size = 2 * 1024 * 1024; // 2MB
                if (!in_array($ext, $allowed)) {
                    $error = 'Chỉ chấp nhận file ảnh jpg, jpeg, png, gif, webp!';
                } elseif ($file['size'] > $max_size) {
                    $error = 'Dung lượng file tối đa 2MB!';
                } elseif (!getimagesize($file['tmp_name'])) {
                    $error = 'File không phải là ảnh hợp lệ!';
                } else {
                    $avatar = uniqid() . '.' . $ext;
                    $upload_path = dirname(__DIR__) . '/uploads/avatars/' . $avatar;
                    if (!is_dir(dirname($upload_path))) mkdir(dirname($upload_path), 0777, true);
                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        // Xóa avatar cũ nếu có
                        $stmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ?');
                        $stmt->execute([$user_id]);
                        $old = $stmt->fetchColumn();
                        if ($old && file_exists(dirname(__DIR__) . '/uploads/avatars/' . $old)) {
                            @unlink(dirname(__DIR__) . '/uploads/avatars/' . $old);
                        }
                        // Cập nhật DB
                        $stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
                        $stmt->execute([$avatar, $user_id]);
                        echo '<script>setTimeout(function(){location.href="users.php?success=1"}, 1200);</script>';
                        echo '<script>document.getElementById("avatar-success-' . $user_id . '").classList.remove("d-none");document.getElementById("avatar-success-' . $user_id . '").innerText="Cập nhật avatar thành công!";document.getElementById("avatar-loading-' . $user_id . '").classList.add("d-none");setTimeout(()=>{var modal = bootstrap.Modal.getInstance(document.getElementById("avatarModal' . $user_id . '"));if(modal)modal.hide();}, 900);</script>';
                        exit;
                    } else {
                        $error = 'Không thể upload file ảnh.';
                    }
                }
            } else {
                $error = 'Vui lòng chọn file ảnh.';
            }
        }
        if (!empty($error)) {
            $user_id = (int)$_POST['user_id'];
            echo '<script>document.getElementById("avatar-error-' . $user_id . '").classList.remove("d-none");document.getElementById("avatar-error-' . $user_id . '").innerText="' . addslashes($error) . '";document.getElementById("avatar-loading-' . $user_id . '").classList.add("d-none");</script>';
        }
    }
    ?>
</body>
</html>