<?php
require_once '../config/config.php';

// Get all contests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->query("
            SELECT c.*, u.username as creator_name,
                   COUNT(DISTINCT v.id) as total_votes,
                   COUNT(DISTINCT ct.id) as total_contestants
            FROM contests c
            LEFT JOIN users u ON c.created_by = u.id
            LEFT JOIN votes v ON c.id = v.contest_id
            LEFT JOIN contestants ct ON c.id = ct.contest_id
            WHERE c.status = 'active'
            GROUP BY c.id, c.title, c.description, c.start_date, c.end_date,
                     c.status, c.created_at, c.updated_at, c.created_by, u.username
            ORDER BY c.created_at DESC
        ");
        $contests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($contests);
    } catch (PDOException $e) {
        error_log("Error fetching contests: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// Create new contest (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->requireAuth();

    // Check if user is admin
    if ($auth->user()['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        $stmt = $db->prepare("
            INSERT INTO contests (title, description, start_date, end_date, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $auth->user()['id']
        ]);

        $contestId = $db->lastInsertId();
        echo json_encode(['id' => $contestId, 'message' => 'Contest created successfully']);
    } catch (PDOException $e) {
        error_log("Error creating contest: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}