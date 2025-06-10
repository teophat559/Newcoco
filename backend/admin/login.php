<?php
session_start();
require_once '../config.php';

// Kiểm tra đã đăng nhập chưa
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        try {
            // Kiểm tra email
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($password, $admin['password'])) {
                throw new Exception('Email hoặc mật khẩu không đúng');
            }

            // Lưu thông tin đăng nhập
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];

            // Ghi log
            $stmt = $pdo->prepare("
                INSERT INTO admin_logs (admin_id, action, ip_address, created_at)
                VALUES (?, 'login', ?, NOW())
            ");
            $stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR']]);

            header('Location: index.php');
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
    <title>Đăng nhập Admin - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: none;
            border-bottom: none;
            text-align: center;
            padding-top: 20px;
        }
        .card-header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
        }
        .btn-primary {
            border-radius: 5px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <img src="../images/logo.png" alt="<?php echo SITE_NAME; ?>">
                <h4 class="mb-0">Đăng nhập Admin</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>