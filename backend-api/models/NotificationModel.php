<?php

namespace BackendApi\Models;

use BackendApi\Utils\Database;
use PDO;
use Exception;

class NotificationModel {
    private $db;
    private $table = 'notifications';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllNotifications() {
        $query = "SELECT n.*,
                    COUNT(nr.id) as recipient_count,
                    GROUP_CONCAT(u.username) as recipients
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 LEFT JOIN users u ON nr.user_id = u.id
                 GROUP BY n.id
                 ORDER BY n.created_at DESC";

        return $this->db->fetchAll($query);
    }

    public function getNotification($id) {
        $query = "SELECT n.*,
                    COUNT(nr.id) as recipient_count,
                    GROUP_CONCAT(u.username) as recipients
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 LEFT JOIN users u ON nr.user_id = u.id
                 WHERE n.id = ?
                 GROUP BY n.id";

        return $this->db->fetch($query, [$id]);
    }

    public function getNotifications($userId, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM {$this->table}
                 WHERE user_id = :user_id
                 ORDER BY created_at DESC
                 LIMIT :limit OFFSET :offset";

        return $this->db->fetchAll($query, [
            ':user_id' => $userId,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
    }

    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table}
                 WHERE user_id = :user_id AND is_read = 0";

        $result = $this->db->fetch($query, [':user_id' => $userId]);
        return $result['count'] ?? 0;
    }

    public function markAsRead($id, $userId) {
        $query = "UPDATE {$this->table}
                 SET is_read = 1
                 WHERE id = :id AND user_id = :user_id";

        return $this->db->executeQuery($query, [
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }

    public function markAllAsRead($userId) {
        $query = "UPDATE {$this->table}
                 SET is_read = 1
                 WHERE user_id = :user_id AND is_read = 0";

        return $this->db->executeQuery($query, [':user_id' => $userId]);
    }

    public function delete($id, $userId) {
        $query = "DELETE FROM {$this->table}
                 WHERE id = :id AND user_id = :user_id";

        return $this->db->executeQuery($query, [
            ':id' => $id,
            ':user_id' => $userId
        ]);
    }

    public function deleteAll($userId) {
        $query = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        return $this->db->executeQuery($query, [':user_id' => $userId]);
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            // Create notification
            $query = "INSERT INTO notifications (title, message, type, status, created_at)
                     VALUES (?, ?, ?, ?, NOW())";

            $this->db->executeQuery($query, [
                $data['title'],
                $data['message'],
                $data['type'],
                $data['status'] ?? 'active'
            ]);

            $notificationId = $this->db->lastInsertId();

            // Add recipients
            if (!empty($data['recipients'])) {
                $query = "INSERT INTO notification_recipients (notification_id, user_id)
                         VALUES (?, ?)";

                foreach ($data['recipients'] as $userId) {
                    $this->db->executeQuery($query, [$notificationId, $userId]);
                }
            }

            $this->db->commit();
            return $notificationId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            // Update notification
            $query = "UPDATE notifications
                     SET title = ?, message = ?, type = ?, status = ?, updated_at = NOW()
                     WHERE id = ?";

            $this->db->executeQuery($query, [
                $data['title'],
                $data['message'],
                $data['type'],
                $data['status'],
                $id
            ]);

            // Update recipients if provided
            if (isset($data['recipients'])) {
                // Delete existing recipients
                $query = "DELETE FROM notification_recipients WHERE notification_id = ?";
                $this->db->executeQuery($query, [$id]);

                // Add new recipients
                $query = "INSERT INTO notification_recipients (notification_id, user_id)
                         VALUES (?, ?)";

                foreach ($data['recipients'] as $userId) {
                    $this->db->executeQuery($query, [$id, $userId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getRecipients($id) {
        $query = "SELECT u.*
                 FROM users u
                 JOIN notification_recipients nr ON u.id = nr.user_id
                 WHERE nr.notification_id = ?";

        return $this->db->fetchAll($query, [$id]);
    }

    public function getNotificationStats($id) {
        $query = "SELECT
                    COUNT(nr.id) as total_recipients,
                    COUNT(CASE WHEN nr.read_at IS NOT NULL THEN 1 END) as read_count,
                    MIN(nr.read_at) as first_read,
                    MAX(nr.read_at) as last_read
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 WHERE n.id = ?";

        return $this->db->fetch($query, [$id]);
    }

    public function getUserNotifications($userId) {
        $query = "SELECT n.*, nr.read_at
                 FROM notifications n
                 JOIN notification_recipients nr ON n.id = nr.notification_id
                 WHERE nr.user_id = ?
                 ORDER BY n.created_at DESC";

        return $this->db->fetchAll($query, [$userId]);
    }
}