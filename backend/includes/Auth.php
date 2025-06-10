<?php

class Auth {
    private $db;
    private $user = null;

    public function __construct($db) {
        $this->db = $db;
        $this->init();
    }

    private function init() {
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $this->user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $this->user = $user;
            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        $this->user = null;
        session_destroy();
    }

    public function isLoggedIn() {
        return $this->user !== null;
    }

    public function getCurrentUser() {
        return $this->user;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    public function user() {
        return $this->getCurrentUser();
    }
}