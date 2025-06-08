<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Lấy và validate dữ liệu
$contest_id = filter_input(INPUT_POST, 'contest_id', FILTER_VALIDATE_INT);
$contestant_id = filter_input(INPUT_POST, 'contestant_id', FILTER_VALIDATE_INT);

if (!$contest_id || !$contestant_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // Kiểm tra xem cuộc thi có tồn tại và đang diễn ra không
    $contest = get_contest($contest_id);
    if (!$contest || $contest['status'] !== 'active') {
        http_response_code(400);
        echo json_encode(['error' => 'Contest not found or not active']);
        exit;
    }

    // Kiểm tra xem thí sinh có tồn tại trong cuộc thi không
    $contestant = get_contestant($contestant_id);
    if (!$contestant || $contestant['contest_id'] !== $contest_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Contestant not found']);
        exit;
    }

    // Kiểm tra xem người dùng đã bình chọn cho thí sinh này chưa
    if (has_voted($_SESSION['user_id'], $contestant_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'You have already voted for this contestant']);
        exit;
    }

    // Thực hiện bình chọn
    $db = get_db_connection();
    $stmt = $db->prepare("INSERT INTO votes (user_id, contestant_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $contestant_id]);
    $vote_id = $db->lastInsertId();

    if (!$vote_id) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to record vote']);
        exit;
    }

    // Cập nhật số lượt bình chọn của thí sinh
    $stmt = $db->prepare("UPDATE contestants SET vote_count = vote_count + 1 WHERE id = ?");
    $stmt->execute([$contestant_id]);

    // Trả về kết quả thành công
    echo json_encode([
        'success' => true,
        'message' => 'Vote recorded successfully',
        'vote_id' => $vote_id
    ]);

} catch (Exception $e) {
    error_log('Vote error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
