<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
require_login();

$user = get_current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            set_flash_message('danger', 'Định dạng file không được hỗ trợ');
        } elseif ($file['size'] > MAX_FILE_SIZE) {
            set_flash_message('danger', 'Kích thước file quá lớn');
        } else {
            // Xóa avatar cũ nếu có
            if ($user['avatar']) {
                $old_avatar = UPLOAD_DIR . '/avatars/' . $user['avatar'];
                if (file_exists($old_avatar)) {
                    unlink($old_avatar);
                }
            }

            // Upload avatar mới
            $avatar = uniqid() . '.' . $ext;
            $upload_path = UPLOAD_DIR . '/avatars/' . $avatar;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $db = db_connect();
                $stmt = $db->prepare('UPDATE users SET avatar = ? WHERE id = ?');
                if ($stmt->execute([$avatar, $user['id']])) {
                    set_flash_message('success', 'Cập nhật avatar thành công!');
                    redirect(SITE_URL . '/users/profile.php');
                } else {
                    set_flash_message('danger', 'Có lỗi xảy ra, vui lòng thử lại sau!');
                }
            } else {
                set_flash_message('danger', 'Không thể upload file');
            }
        }
    } else {
        set_flash_message('danger', 'Vui lòng chọn file');
    }
}

$page_title = 'Cập nhật avatar';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Cập nhật avatar</h4>
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm('upload-avatar-form')">
                    <div class="text-center mb-4">
                        <img src="<?php echo $user['avatar'] ? SITE_URL . '/uploads/avatars/' . $user['avatar'] : 'https://via.placeholder.com/150'; ?>"
                             class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;"
                             alt="<?php echo sanitize_output($user['full_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Chọn ảnh</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required
                               onchange="previewImage(this)">
                        <div class="mt-2">
                            <img id="image-preview" src="" alt="" style="max-width: 200px; display: none;">
                        </div>
                        <small class="text-muted">
                            Định dạng: <?php echo implode(', ', ALLOWED_EXTENSIONS); ?><br>
                            Kích thước tối đa: <?php echo format_file_size(MAX_FILE_SIZE); ?>
                        </small>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="<?php echo SITE_URL; ?>/users/profile.php" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>