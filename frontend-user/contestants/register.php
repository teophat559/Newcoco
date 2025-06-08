<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra đăng nhập
require_login();

$contest_id = $_GET['contest_id'] ?? 0;
$contest = get_contest($contest_id);

if (!$contest) {
    set_flash_message('danger', 'Cuộc thi không tồn tại!');
    redirect(SITE_URL . '/contests');
}

// Kiểm tra thời gian đăng ký
if (strtotime($contest['end_date']) <= time()) {
    set_flash_message('danger', 'Cuộc thi đã kết thúc!');
    redirect(SITE_URL . '/contests/view.php?id=' . $contest_id);
}

// Kiểm tra đã đăng ký chưa
$db = db_connect();
$stmt = $db->prepare('SELECT id FROM contestants WHERE user_id = ? AND contest_id = ?');
$stmt->execute([$_SESSION['user_id'], $contest_id]);
if ($stmt->fetch()) {
    set_flash_message('danger', 'Bạn đã đăng ký tham gia cuộc thi này!');
    redirect(SITE_URL . '/contests/view.php?id=' . $contest_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $description = $_POST['description'] ?? '';

    $errors = [];

    if (empty($full_name)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }

    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    }

    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    if (empty($description)) {
        $errors[] = 'Vui lòng nhập mô tả';
    }

    // Xử lý upload ảnh
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {
            $image = handle_file_upload($_FILES['image'], ['jpg', 'jpeg', 'png', 'gif'], 2 * 1024 * 1024);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        // Tạo thí sinh
        $stmt = $db->prepare('
            INSERT INTO contestants (user_id, contest_id, full_name, phone, email, description, image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, "pending")
        ');
        $stmt->execute([
            $_SESSION['user_id'],
            $contest_id,
            $full_name,
            $phone,
            $email,
            $description,
            $image
        ]);

        set_flash_message('success', 'Đăng ký thành công! Vui lòng chờ phê duyệt.');
        redirect(SITE_URL . '/contests/view.php?id=' . $contest_id);
    }

    if (!empty($errors)) {
        set_flash_message('danger', implode('<br>', $errors));
    }
}

$page_title = 'Đăng ký tham gia - ' . $contest['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/contests">Cuộc thi</a></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>/contests/view.php?id=<?php echo $contest['id']; ?>">
                        <?php echo sanitize_output($contest['title']); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">Đăng ký tham gia</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Đăng ký tham gia</h4>
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm('register-form')">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*"
                               onchange="previewImage(this)">
                        <div class="mt-2">
                            <img id="image-preview" src="" alt="" style="max-width: 200px; display: none;">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>