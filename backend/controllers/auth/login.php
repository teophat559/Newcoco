<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        // TODO: Thêm logic đăng nhập
        $_SESSION['user_id'] = 1; // Tạm thời
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Newcoco</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Newcoco</div>
            <button class="menu-toggle">Menu</button>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="contests.php">Cuộc thi</a></li>
                <li><a href="login.php" class="active">Đăng nhập</a></li>
                <li><a href="register.php">Đăng ký</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-center">Đăng nhập</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="login.php" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                </div>
                            </form>

                            <div class="text-center mt-3">
                                <a href="forgot-password.php">Quên mật khẩu?</a>
                                <p class="mt-2">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Newcoco. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>