<?php
namespace App\Models;

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        $query = "SELECT u.*,
                    COUNT(DISTINCT v.id) as vote_count,
                    COUNT(DISTINCT ct.id) as contestant_count
                 FROM users u
                 LEFT JOIN votes v ON u.id = v.user_id
                 LEFT JOIN contestants ct ON u.id = ct.user_id
                 GROUP BY u.id
                 ORDER BY u.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getUser($id) {
        $query = "SELECT u.*,
                    COUNT(DISTINCT v.id) as vote_count,
                    COUNT(DISTINCT ct.id) as contestant_count
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

        $this->db->query($query, [
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'user',
            $data['status'] ?? 'active'
        ]);

        return $this->db->lastInsertId();
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

        if (isset($data['password'])) {
            $query .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->query($query, $params);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            // Delete votes
            $query = "DELETE FROM votes WHERE user_id = ?";
            $this->db->query($query, [$id]);

            // Delete contestants
            $query = "DELETE FROM contestants WHERE user_id = ?";
            $this->db->query($query, [$id]);

            // Delete notifications
            $query = "DELETE FROM notifications WHERE user_id = ?";
            $this->db->query($query, [$id]);

            // Delete user
            $query = "DELETE FROM users WHERE id = ?";
            $this->db->query($query, [$id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getUserActivity($id) {
        $query = "SELECT 'vote' as type, v.created_at, ct.name as contestant_name, c.title as contest_title
                 FROM votes v
                 LEFT JOIN contestants ct ON v.contestant_id = ct.id
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 WHERE v.user_id = ?
                 UNION ALL
                 SELECT 'contestant' as type, ct.created_at, ct.name as contestant_name, c.title as contest_title
                 FROM contestants ct
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 WHERE ct.user_id = ?
                 ORDER BY created_at DESC";

        return $this->db->query($query, [$id, $id])->fetchAll();
    }

    public function getUserStats($id) {
        $query = "SELECT
                    COUNT(DISTINCT v.id) as total_votes,
                    COUNT(DISTINCT ct.id) as total_contestants,
                    COUNT(DISTINCT n.id) as total_notifications,
                    MIN(v.created_at) as first_vote,
                    MAX(v.created_at) as last_vote
                 FROM users u
                 LEFT JOIN votes v ON u.id = v.user_id
                 LEFT JOIN contestants ct ON u.id = ct.user_id
                 LEFT JOIN notifications n ON u.id = n.user_id
                 WHERE u.id = ?";

        return $this->db->query($query, [$id])->fetch();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        return $this->db->query($query, [$email])->fetch();
    }

    public function findByUsername($username) {
        $query = "SELECT * FROM users WHERE username = ?";
        return $this->db->query($query, [$username])->fetch();
    }

    public function updatePassword($id, $password) {
        $query = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($query, [
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function updateLastLogin($id) {
        $query = "UPDATE users SET last_login = NOW() WHERE id = ?";
        return $this->db->query($query, [$id]);
    }
}