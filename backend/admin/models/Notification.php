<?php
require_once __DIR__ . '/../config/config.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllNotifications() {
        $query = "SELECT n.*,
                        COUNT(DISTINCT nr.id) as total_recipients,
                        COUNT(DISTINCT CASE WHEN nr.read_at IS NOT NULL THEN nr.id END) as read_count
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 GROUP BY n.id
                 ORDER BY n.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getNotification($id) {
        $query = "SELECT n.*,
                        COUNT(DISTINCT nr.id) as total_recipients,
                        COUNT(DISTINCT CASE WHEN nr.read_at IS NOT NULL THEN nr.id END) as read_count
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 WHERE n.id = ?
                 GROUP BY n.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            // Insert notification
            $query = "INSERT INTO notifications (title, message, type, status, created_at)
                     VALUES (?, ?, ?, ?, NOW())";

            $params = [
                $data['title'],
                $data['message'],
                $data['type'],
                $data['status'] ?? 'active'
            ];

            $this->db->query($query, $params);
            $notification_id = $this->db->lastInsertId();

            // Add recipients
            if (!empty($data['recipients'])) {
                $query = "INSERT INTO notification_recipients (notification_id, user_id)
                         VALUES (?, ?)";

                foreach ($data['recipients'] as $user_id) {
                    $this->db->query($query, [$notification_id, $user_id]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
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

            $params = [
                $data['title'],
                $data['message'],
                $data['type'],
                $data['status'],
                $id
            ];

            $this->db->query($query, $params);

            // Update recipients if provided
            if (isset($data['recipients'])) {
                // Remove existing recipients
                $this->db->query("DELETE FROM notification_recipients WHERE notification_id = ?", [$id]);

                // Add new recipients
                $query = "INSERT INTO notification_recipients (notification_id, user_id)
                         VALUES (?, ?)";

                foreach ($data['recipients'] as $user_id) {
                    $this->db->query($query, [$id, $user_id]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        // First delete recipients
        $this->db->query("DELETE FROM notification_recipients WHERE notification_id = ?", [$id]);

        // Then delete the notification
        return $this->db->query("DELETE FROM notifications WHERE id = ?", [$id]);
    }

    public function getRecipients($id) {
        $query = "SELECT u.*, nr.read_at
                 FROM notification_recipients nr
                 JOIN users u ON nr.user_id = u.id
                 WHERE nr.notification_id = ?
                 ORDER BY u.username";

        return $this->db->query($query, [$id])->fetchAll();
    }

    public function getNotificationStats($id) {
        $query = "SELECT
                    COUNT(DISTINCT nr.id) as total_recipients,
                    COUNT(DISTINCT CASE WHEN nr.read_at IS NOT NULL THEN nr.id END) as read_count,
                    MIN(nr.read_at) as first_read,
                    MAX(nr.read_at) as last_read
                 FROM notifications n
                 LEFT JOIN notification_recipients nr ON n.id = nr.notification_id
                 WHERE n.id = ?
                 GROUP BY n.id";

        return $this->db->query($query, [$id])->fetch();
    }
}