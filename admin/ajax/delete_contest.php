<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Contest.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get contest ID
$contest_id = $_GET['id'] ?? null;

if (!$contest_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Contest ID is required']);
    exit;
}

try {
    $contest = new Contest();
    $result = $contest->delete($contest_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Contest deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete contest']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}