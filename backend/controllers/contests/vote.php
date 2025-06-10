<?php
session_start();
require_once 'config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để bình chọn']);
    exit;
}

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$contestant_id = $_POST['contestant_id'] ?? 0;

// Kiểm tra dữ liệu
if (empty($contestant_id)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    // Kiểm tra xem đã bình chọn chưa
    $stmt = $pdo->prepare("
        SELECT id FROM votes
        WHERE contestant_id = ? AND user_id = ? AND DATE(created_at) = CURDATE()
    ");
    $stmt->execute([$contestant_id, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        throw new Exception('Bạn đã bình chọn cho thí sinh này hôm nay');
    }

    // Thêm lượt bình chọn
    $stmt = $pdo->prepare("
        INSERT INTO votes (contestant_id, user_id, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$contestant_id, $_SESSION['user_id']]);

    // Cập nhật số lượt bình chọn
    $stmt = $pdo->prepare("
        UPDATE contestants
        SET vote_count = vote_count + 1
        WHERE id = ?
    ");
    $stmt->execute([$contestant_id]);

    // Lấy số lượt bình chọn mới
    $stmt = $pdo->prepare("SELECT vote_count FROM contestants WHERE id = ?");
    $stmt->execute([$contestant_id]);
    $vote_count = $stmt->fetchColumn();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Bình chọn thành công',
        'vote_count' => $vote_count
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}