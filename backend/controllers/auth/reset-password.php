<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

// Kiểm tra token
$stmt = $pdo->prepare("
    SELECT pr.*, u.email
    FROM password_resets pr
    JOIN users u ON pr.user_id = u.id
    WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    $error = 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu không khớp';
    } else {
        try {
            // Cập nhật mật khẩu
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);

            // Đánh dấu token đã sử dụng
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
            $stmt->execute([$reset['id']]);

            $success = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại';
        } catch (Exception $e) {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại sau';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Đặt lại mật khẩu</h5>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary">Đăng nhập</a>
                                </div>
                            </div>
                        <?php elseif ($reset): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($reset['email']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
                                </div>
                            </form>
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