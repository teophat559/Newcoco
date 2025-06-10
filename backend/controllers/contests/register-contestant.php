<?php
session_start();
require_once 'config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contests.php');
    exit;
}

$contest_id = $_POST['contest_id'] ?? 0;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

// Kiểm tra dữ liệu
if (empty($contest_id) || empty($title) || empty($description)) {
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
    header('Location: contest-details.php?id=' . $contest_id);
    exit;
}

// Kiểm tra file upload
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Vui lòng chọn hình ảnh';
    header('Location: contest-details.php?id=' . $contest_id);
    exit;
}

// Kiểm tra loại file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($_FILES['image']['type'], $allowed_types)) {
    $_SESSION['error'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)';
    header('Location: contest-details.php?id=' . $contest_id);
    exit;
}

// Kiểm tra kích thước file
if ($_FILES['image']['size'] > MAX_FILE_SIZE) {
    $_SESSION['error'] = 'Kích thước file không được vượt quá 5MB';
    header('Location: contest-details.php?id=' . $contest_id);
    exit;
}

try {
    // Tạo tên file
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = UPLOAD_PATH . '/contestants/' . $filename;

    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists(UPLOAD_PATH . '/contestants')) {
        mkdir(UPLOAD_PATH . '/contestants', 0777, true);
    }

    // Upload file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        throw new Exception('Không thể upload file');
    }

    // Kiểm tra xem đã đăng ký chưa
    $stmt = $pdo->prepare("SELECT id FROM contestants WHERE contest_id = ? AND user_id = ?");
    $stmt->execute([$contest_id, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        throw new Exception('Bạn đã đăng ký tham gia cuộc thi này');
    }

    // Thêm thí sinh
    $stmt = $pdo->prepare("
        INSERT INTO contestants (contest_id, user_id, title, description, image, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $contest_id,
        $_SESSION['user_id'],
        $title,
        $description,
        'uploads/contestants/' . $filename
    ]);

    $_SESSION['success'] = 'Đăng ký tham gia thành công';
    header('Location: contest-details.php?id=' . $contest_id);
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: contest-details.php?id=' . $contest_id);
    exit;
}