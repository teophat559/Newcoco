<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
require_login();

$user = get_current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($current_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
    } elseif (!password_verify($current_password, $user['password'])) {
        $errors[] = 'Mật khẩu hiện tại không đúng';
    }

    if (empty($new_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu mới';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
    }

    if (empty($confirm_password)) {
        $errors[] = 'Vui lòng xác nhận mật khẩu mới';
    } elseif ($new_password !== $confirm_password) {
        $errors[] = 'Mật khẩu xác nhận không khớp';
    }

    if (empty($errors)) {
        $db = db_connect();
        $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        if ($stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $user['id']])) {
            set_flash_message('success', 'Đổi mật khẩu thành công!');
            redirect(SITE_URL . '/users/profile.php');
        } else {
            set_flash_message('danger', 'Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    }

    if (!empty($errors)) {
        set_flash_message('danger', implode('<br>', $errors));
    }
}

$page_title = 'Đổi mật khẩu';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Đổi mật khẩu</h4>
                <form method="POST" action="" onsubmit="return validateForm('change-password-form')">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required
                               onkeyup="checkPasswordStrength(this.value)">
                        <div class="progress mt-2" style="height: 5px;">
                            <div id="password-strength-meter" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="password-feedback" class="form-text text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        <a href="<?php echo SITE_URL; ?>/users/profile.php" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>