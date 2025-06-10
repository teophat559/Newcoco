<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error = 'Vui lòng nhập email';
    } else {
        try {
            // Kiểm tra email tồn tại
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('Email không tồn tại trong hệ thống');
            }

            // Tạo token reset password
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Lưu token vào database
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user['id'], $token, $expires]);

            // Gửi email
            $reset_link = SITE_URL . '/reset-password.php?token=' . $token;
            $to = $email;
            $subject = 'Yêu cầu đặt lại mật khẩu - ' . SITE_NAME;
            $message = "
                <p>Xin chào {$user['name']},</p>
                <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
                <p>Vui lòng click vào link sau để đặt lại mật khẩu:</p>
                <p><a href='{$reset_link}'>{$reset_link}</a></p>
                <p>Link này sẽ hết hạn sau 1 giờ.</p>
                <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                <p>Trân trọng,<br>" . SITE_NAME . "</p>
            ";

            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . SITE_NAME . ' <' . SMTP_USER . '>'
            ];

            if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                $success = 'Vui lòng kiểm tra email để đặt lại mật khẩu';
            } else {
                throw new Exception('Không thể gửi email. Vui lòng thử lại sau');
            }

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
    <title>Quên mật khẩu - <?php echo SITE_NAME; ?></title>
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
                        <h5 class="card-title text-center mb-4">Quên mật khẩu</h5>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                                <div class="form-text">Nhập email đã đăng ký để nhận link đặt lại mật khẩu</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php">Quay lại đăng nhập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>