<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Contestant.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get contestant ID
$contestant_id = $_GET['id'] ?? null;

if (!$contestant_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Contestant ID is required']);
    exit;
}

try {
    $contestant = new Contestant();
    $result = $contestant->delete($contestant_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Contestant deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete contestant']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}