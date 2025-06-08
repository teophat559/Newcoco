<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(SITE_URL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($full_name)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }

    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Mật khẩu xác nhận không khớp';
    }

    if (empty($errors)) {
        $pdo = get_db_connection();

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email đã được sử dụng';
        } else {
            // Create user
            $stmt = $pdo->prepare('
                INSERT INTO users (full_name, email, password, role, status)
                VALUES (?, ?, ?, "user", "pending")
            ');
            $stmt->execute([
                $full_name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            set_flash_message('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
            redirect(SITE_URL . '/auth/login.php');
        }
    }

    if (!empty($errors)) {
        set_flash_message('danger', implode('<br>', $errors));
    }
}

$page_title = 'Đăng ký';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Đăng ký</h4>
                <form method="POST" action="" onsubmit="return validateForm('register-form')">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required
                               onkeyup="checkPasswordStrength(this.value)">
                        <div class="progress mt-2" style="height: 5px;">
                            <div id="password-strength-meter" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="password-feedback" class="form-text text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p class="mb-0">
                        Đã có tài khoản?
                        <a href="<?php echo SITE_URL; ?>/auth/login.php">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>