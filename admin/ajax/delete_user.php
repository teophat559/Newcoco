<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get user ID
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// Prevent deleting self
if ($user_id == $_SESSION['admin_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
    exit;
}

try {
    $user = new User();
    $result = $user->delete($user_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}