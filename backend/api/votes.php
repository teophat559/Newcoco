<?php
require_once '../config/config.php';
require_once '../backend/includes/Auth.php';
require_once __DIR__ . '/vendor/autoload.php';

if (!class_exists('Redis')) {
    die('Redis extension is not installed or enabled.');
}

// Initialize Auth
$auth = new Auth($db);

// Submit vote
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->requireAuth();

    $data = json_decode(file_get_contents('php://input'), true);
    $contestId = $data['contest_id'] ?? null;
    $contestantId = $data['contestant_id'] ?? null;

    if (!$contestId || !$contestantId) {
        http_response_code(400);
        echo json_encode(['error' => 'Contest ID and Contestant ID are required']);
        exit();
    }

    try {
        // Check if contest is active
        $stmt = $db->prepare("
            SELECT status, end_date
            FROM contests
            WHERE id = ? AND status = 'active' AND end_date > GETDATE()
        ");
        $stmt->execute([$contestId]);
        $contest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contest) {
            http_response_code(400);
            echo json_encode(['error' => 'Contest is not active or has ended']);
            exit();
        }

        // Check if user has already voted
        $stmt = $db->prepare("
            SELECT id FROM votes
            WHERE contest_id = ? AND user_id = ?
        ");
        $stmt->execute([$contestId, $auth->getCurrentUser()['id']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'You have already voted in this contest']);
            exit();
        }

        // Submit vote
        $stmt = $db->prepare("
            INSERT INTO votes (contest_id, contestant_id, user_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$contestId, $contestantId, $auth->getCurrentUser()['id']]);

        echo json_encode(['message' => 'Vote submitted successfully']);
    } catch (PDOException $e) {
        error_log("Error submitting vote: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}

// Get user's votes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $auth->requireAuth();

    try {
        $stmt = $db->prepare("
            SELECT v.*, c.title as contest_title, ct.name as contestant_name
            FROM votes v
            JOIN contests c ON v.contest_id = c.id
            JOIN contestants ct ON v.contestant_id = ct.id
            WHERE v.user_id = ?
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$auth->getCurrentUser()['id']]);
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($votes);
    } catch (PDOException $e) {
        error_log("Error fetching votes: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}