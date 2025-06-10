<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllUsers() {
        $query = "SELECT u.*,
                        COUNT(DISTINCT v.id) as total_votes,
                        COUNT(DISTINCT ct.id) as total_contestants
                 FROM users u
                 LEFT JOIN votes v ON u.id = v.user_id
                 LEFT JOIN contestants ct ON u.id = ct.user_id
                 GROUP BY u.id
                 ORDER BY u.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getUser($id) {
        $query = "SELECT u.*,
                        COUNT(DISTINCT v.id) as total_votes,
                        COUNT(DISTINCT ct.id) as total_contestants
                 FROM users u
                 LEFT JOIN votes v ON u.id = v.user_id
                 LEFT JOIN contestants ct ON u.id = ct.user_id
                 WHERE u.id = ?
                 GROUP BY u.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO users (username, email, password, role, status, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'user',
            $data['status'] ?? 'active'
        ];

        return $this->db->query($query, $params);
    }

    public function update($id, $data) {
        $query = "UPDATE users
                 SET username = ?, email = ?, role = ?, status = ?, updated_at = NOW()";

        $params = [
            $data['username'],
            $data['email'],
            $data['role'],
            $data['status']
        ];

        // Update password if provided
        if (!empty($data['password'])) {
            $query .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->query($query, $params);
    }

    public function delete($id) {
        // First delete related records
        $this->db->query("DELETE FROM votes WHERE user_id = ?", [$id]);
        $this->db->query("DELETE FROM contestants WHERE user_id = ?", [$id]);
        $this->db->query("DELETE FROM notifications WHERE user_id = ?", [$id]);

        // Then delete the user
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function getUserActivity($id) {
        $query = "SELECT
                    'vote' as type,
                    v.created_at,
                    c.title as contest_title,
                    ct.name as contestant_name
                 FROM votes v
                 JOIN contestants ct ON v.contestant_id = ct.id
                 JOIN contests c ON ct.contest_id = c.id
                 WHERE v.user_id = ?
                 UNION ALL
                 SELECT
                    'contestant' as type,
                    ct.created_at,
                    c.title as contest_title,
                    ct.name as contestant_name
                 FROM contestants ct
                 JOIN contests c ON ct.contest_id = c.id
                 WHERE ct.user_id = ?
                 ORDER BY created_at DESC";

        return $this->db->query($query, [$id, $id])->fetchAll();
    }

    public function getUserStats($id) {
        $query = "SELECT
                    COUNT(DISTINCT v.id) as total_votes,
                    COUNT(DISTINCT ct.id) as total_contestants,
                    COUNT(DISTINCT n.id) as total_notifications,
                    MIN(v.created_at) as first_activity,
                    MAX(v.created_at) as last_activity
                 FROM users u
                 LEFT JOIN votes v ON u.id = v.user_id
                 LEFT JOIN contestants ct ON u.id = ct.user_id
                 LEFT JOIN notifications n ON u.id = n.user_id
                 WHERE u.id = ?
                 GROUP BY u.id";

        return $this->db->query($query, [$id])->fetch();
    }
}