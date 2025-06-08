<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(SITE_URL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $db = db_connect();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND status = "active"');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $db->prepare('INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $token, $expires]);

            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
        }

        set_flash_message('success', 'Đăng nhập thành công!');
        redirect(SITE_URL);
    } else {
        set_flash_message('danger', 'Email hoặc mật khẩu không đúng!');
    }
}

$page_title = 'Đăng nhập';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Đăng nhập</h4>
                <form method="POST" action="" onsubmit="return validateForm('login-form')">
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
                    <a href="<?php echo SITE_URL; ?>/auth/forgot-password.php">Quên mật khẩu?</a>
                    <p class="mt-3 mb-0">
                        Chưa có tài khoản?
                        <a href="<?php echo SITE_URL; ?>/auth/register.php">Đăng ký ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>