<?php
namespace BackendApi\Controllers;

use BackendApi\Middleware\AuthMiddleware;
use BackendApi\Utils\ActivityLogger;
use BackendApi\Utils\Database;

class UserController {
    private $db;
    private $auth;
    private $activityLogger;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->auth = new AuthMiddleware($db);
        $this->activityLogger = new ActivityLogger($db);
    }

    public function index() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $userData = $stmt->fetch();

        if (!$userData) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        echo json_encode($userData);
    }

    public function update() {
        $user = $this->auth->handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request data']);
            return;
        }

        $allowedFields = ['name', 'email', 'phone', 'address'];
        $updates = [];
        $params = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid fields to update']);
            return;
        }

        $params[] = $user['id'];
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            $this->activityLogger->addActivityLog(
                $user['id'],
                'profile_update',
                'User updated their profile',
                $data
            );

            echo json_encode(['message' => 'Profile updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update profile']);
        }
    }

    public function changePassword() {
        $user = $this->auth->handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['current_password']) || !isset($data['new_password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $userData = $stmt->fetch();

        if (!password_verify($data['current_password'], $userData['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Current password is incorrect']);
            return;
        }

        $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $result = $stmt->execute([$newPasswordHash, $user['id']]);

        if ($result) {
            $this->activityLogger->addActivityLog(
                $user['id'],
                'password_change',
                'User changed their password'
            );

            echo json_encode(['message' => 'Password changed successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to change password']);
        }
    }

    public function getContests() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("
            SELECT c.*,
                   COUNT(DISTINCT s.id) as submission_count,
                   COUNT(DISTINCT v.id) as vote_count
            FROM contests c
            LEFT JOIN submissions s ON c.id = s.contest_id
            LEFT JOIN votes v ON c.id = v.contest_id
            WHERE c.user_id = ?
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $contests = $stmt->fetchAll();

        echo json_encode($contests);
    }

    public function getSubmissions() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("
            SELECT s.*, c.title as contest_title
            FROM submissions s
            JOIN contests c ON s.contest_id = c.id
            WHERE s.user_id = ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $submissions = $stmt->fetchAll();

        echo json_encode($submissions);
    }

    public function getVotes() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("
            SELECT v.*, c.title as contest_title, s.title as submission_title
            FROM votes v
            JOIN contests c ON v.contest_id = c.id
            JOIN submissions s ON v.submission_id = s.id
            WHERE v.user_id = ?
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $votes = $stmt->fetchAll();

        echo json_encode($votes);
    }

    public function getNotifications() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$user['id']]);
        $notifications = $stmt->fetchAll();

        echo json_encode($notifications);
    }

    public function markNotificationRead() {
        $user = $this->auth->handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['notification_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing notification ID']);
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        $result = $stmt->execute([$data['notification_id'], $user['id']]);

        if ($result) {
            $this->activityLogger->addActivityLog(
                $user['id'],
                'notification_read',
                'User marked notification as read',
                ['notification_id' => $data['notification_id']]
            );

            echo json_encode(['message' => 'Notification marked as read']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark notification as read']);
        }
    }

    public function markAllNotificationsRead() {
        $user = $this->auth->handle();

        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ? AND is_read = 0
        ");
        $result = $stmt->execute([$user['id']]);

        if ($result) {
            $this->activityLogger->addActivityLog(
                $user['id'],
                'notifications_read_all',
                'User marked all notifications as read'
            );

            echo json_encode(['message' => 'All notifications marked as read']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark notifications as read']);
        }
    }
}