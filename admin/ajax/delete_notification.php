<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Notification.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get notification ID
$notification_id = $_GET['id'] ?? null;

if (!$notification_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit;
}

try {
    $notification = new Notification();
    $result = $notification->delete($notification_id);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Notification deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete notification']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}