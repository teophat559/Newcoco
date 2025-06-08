<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Settings.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Validate required fields
$required_fields = ['site_name', 'admin_email', 'items_per_page', 'max_file_size'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit;
    }
}

// Validate email
if (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate numeric fields
if (!is_numeric($data['items_per_page']) || !is_numeric($data['max_file_size'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Items per page and max file size must be numeric']);
    exit;
}

try {
    $settings = new Settings();
    $result = $settings->update($data);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}