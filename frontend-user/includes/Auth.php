<?php
require_once __DIR__ . '/Database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = ? AND status = 'active'";
        $user = $this->db->query($query, [$username])->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Update last login
            $query = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $this->db->query($query, [$user['id']]);

            return true;
        }

        return false;
    }

    public function register($data) {
        try {
            $query = "INSERT INTO users (username, email, password, role, status, created_at)
                     VALUES (?, ?, ?, 'user', 'active', NOW())";

            $this->db->query($query, [
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT)
            ]);

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $query = "SELECT id, username, email, role, status, created_at, last_login
                 FROM users WHERE id = ?";

        return $this->db->query($query, [$_SESSION['user_id']])->fetch();
    }

    public function updateProfile($data) {
        if (!$this->isLoggedIn()) {
            throw new Exception("User not logged in");
        }

        $query = "UPDATE users SET email = ?";
        $params = [$data['email']];

        if (!empty($data['password'])) {
            $query .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];

        return $this->db->query($query, $params);
    }

    public function checkUsername($username) {
        $query = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $result = $this->db->query($query, [$username])->fetch();
        return $result['count'] > 0;
    }

    public function checkEmail($email) {
        $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $result = $this->db->query($query, [$email])->fetch();
        return $result['count'] > 0;
    }
}