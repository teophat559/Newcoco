<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
require_login();

$user = get_current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($full_name)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }

    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    }

    // Nếu có thay đổi mật khẩu
    if (!empty($current_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Mật khẩu hiện tại không đúng';
        } elseif (empty($new_password)) {
            $errors[] = 'Vui lòng nhập mật khẩu mới';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }
    }

    if (empty($errors)) {
        $db = db_connect();

        // Cập nhật thông tin
        $sql = 'UPDATE users SET full_name = ?, phone = ?';
        $params = [$full_name, $phone];

        // Nếu có thay đổi mật khẩu
        if (!empty($new_password)) {
            $sql .= ', password = ?';
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $user['id'];

        $stmt = $db->prepare($sql);
        if ($stmt->execute($params)) {
            set_flash_message('success', 'Cập nhật thông tin thành công!');
            redirect(SITE_URL . '/users/profile.php');
        } else {
            set_flash_message('danger', 'Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    }

    if (!empty($errors)) {
        set_flash_message('danger', implode('<br>', $errors));
    }
}

$page_title = 'Thông tin cá nhân';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-header">
                    <img src="<?php echo $user['avatar'] ? SITE_URL . '/uploads/avatars/' . $user['avatar'] : 'https://via.placeholder.com/150'; ?>"
                         class="profile-image" alt="<?php echo sanitize_output($user['full_name']); ?>">
                    <h4 class="profile-name"><?php echo sanitize_output($user['full_name']); ?></h4>
                    <p class="profile-email"><?php echo sanitize_output($user['email']); ?></p>
                </div>
                <div class="mt-3">
                    <p class="mb-1">
                        <i class="fas fa-phone me-2"></i>
                        <?php echo sanitize_output($user['phone'] ?? 'Chưa cập nhật'); ?>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Tham gia: <?php echo format_date($user['created_at']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Cập nhật thông tin</h4>
                <form method="POST" action="" onsubmit="return validateForm('profile-form')">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?php echo sanitize_output($user['full_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?php echo sanitize_output($user['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email"
                               value="<?php echo sanitize_output($user['email']); ?>" disabled>
                        <small class="text-muted">Email không thể thay đổi</small>
                    </div>
                    <hr>
                    <h5 class="mb-3">Đổi mật khẩu</h5>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                               onkeyup="checkPasswordStrength(this.value)">
                        <div class="progress mt-2" style="height: 5px;">
                            <div id="password-strength-meter" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="password-feedback" class="form-text text-muted"></small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>