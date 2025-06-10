<?php
class Auth {
    private $db;
    private $token;

    public function __construct($db) {
        $this->db = $db;
        $this->token = $this->getBearerToken();
    }

    private function getBearerToken() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function validateToken() {
        if (!$this->token) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("SELECT user_id FROM user_tokens WHERE token = ? AND expires_at > GETDATE()");
            $stmt->execute([$this->token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Token validation error: " . $e->getMessage());
            return false;
        }
    }

    public function requireAuth() {
        if (!$this->validateToken()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }
}

// Khởi tạo và kiểm tra authentication
$auth = new Auth($db);
$auth->requireAuth();