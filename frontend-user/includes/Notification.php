<?php
namespace FrontendUser\Includes;

class Notification {
    private $db;
    private $userId;

    public function __construct($db, $userId = null) {
        $this->db = $db;
        $this->userId = $userId;
    }

    public function get_unread_count($userId = null) {
        $userId = $userId ?? $this->userId;
        if (!$userId) return 0;

        $stmt = $this->db->query('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0', [$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function get_user_notifications($userId = null, $limit = 10) {
        $userId = $userId ?? $this->userId;
        if (!$userId) return [];

        $stmt = $this->db->query('
            SELECT * FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ', [$userId, $limit]);

        return $stmt->fetchAll();
    }

    public function get_notifications($userId = null, $limit = 10) {
        return $this->get_user_notifications($userId, $limit);
    }

    public function mark_as_read($notificationId) {
        return $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = ?', [$notificationId]);
    }

    public function mark_all_as_read($userId = null) {
        $userId = $userId ?? $this->userId;
        if (!$userId) return false;

        return $this->db->query('UPDATE notifications SET is_read = 1 WHERE user_id = ?', [$userId]);
    }

    public function create($userId, $type, $message, $link = null) {
        return $this->db->query('
            INSERT INTO notifications (user_id, type, message, link, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ', [$userId, $type, $message, $link]);
    }
}