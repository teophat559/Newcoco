<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } else {
        // TODO: Thêm logic đăng ký
        $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Newcoco</title>
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
                <li><a href="login.php">Đăng nhập</a></li>
                <li><a href="register.php" class="active">Đăng ký</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-center">Đăng ký tài khoản</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>

                            <form method="POST" action="register.php" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Tôi đồng ý với <a href="terms.php">điều khoản sử dụng</a>
                                    </label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                                </div>
                            </form>

                            <div class="text-center mt-3">
                                <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
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