<?php
session_start();
require_once 'config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra dữ liệu
    if (empty($name) || empty($email)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        try {
            // Kiểm tra email đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                throw new Exception('Email đã được sử dụng');
            }

            // Cập nhật thông tin cơ bản
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);

            // Cập nhật mật khẩu nếu có
            if (!empty($current_password)) {
                if (empty($new_password) || empty($confirm_password)) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin mật khẩu');
                }

                if ($new_password !== $confirm_password) {
                    throw new Exception('Mật khẩu mới không khớp');
                }

                // Kiểm tra mật khẩu hiện tại
                if (!password_verify($current_password, $user['password'])) {
                    throw new Exception('Mật khẩu hiện tại không đúng');
                }

                // Cập nhật mật khẩu
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $user_id]);
            }

            $success = 'Cập nhật thông tin thành công';

            // Cập nhật lại thông tin người dùng
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Lấy danh sách cuộc thi đã tham gia
$stmt = $pdo->prepare("
    SELECT c.*, co.title as contest_title, co.image as contest_image
    FROM contestants c
    JOIN contests co ON c.contest_id = co.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$contests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin cá nhân</h5>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <hr>
                            <h6>Đổi mật khẩu</h6>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control" name="current_password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" name="new_password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" name="confirm_password">
                            </div>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cuộc thi đã tham gia</h5>
                        <?php if (empty($contests)): ?>
                            <p class="text-muted">Bạn chưa tham gia cuộc thi nào</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($contests as $contest): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <img src="<?php echo htmlspecialchars($contest['contest_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($contest['contest_title']); ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($contest['contest_title']); ?></h6>
                                                <p class="card-text"><?php echo htmlspecialchars($contest['title']); ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        Lượt bình chọn: <?php echo number_format($contest['vote_count']); ?>
                                                    </small>
                                                </p>
                                                <a href="contest-details.php?id=<?php echo $contest['contest_id']; ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>