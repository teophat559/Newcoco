<?php

class Admin {
    private $id;
    private $name;
    private $email;
    private $profile_image;
    private $db;

    public function __construct($id = null) {
        global $db;
        $this->db = $db;
        if ($id !== null) {
            $this->loadAdmin($id);
        }
    }

    private function loadAdmin($id) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $this->id = $admin['id'];
            $this->name = $admin['name'];
            $this->email = $admin['email'];
            $this->profile_image = $admin['profile_image'];
        } else {
            throw new Exception('Admin not found');
        }
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $this->id = $admin['id'];
            $this->name = $admin['name'];
            $this->email = $admin['email'];
            $this->profile_image = $admin['profile_image'];

            $_SESSION['admin_id'] = $this->id;
            $_SESSION['admin_name'] = $this->name;
            return true;
        }

        return false;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getProfileImage() {
        return $this->profile_image ?: '/admin/assets/img/default-avatar.png';
    }

    public function updateProfile($data) {
        $allowedFields = ['name', 'email', 'profile_image'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = ?";
                $params[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $this->id;
        $sql = "UPDATE admins SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function changePassword($currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$this->id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($currentPassword, $admin['password'])) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE admins SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $this->id]);
    }

    public function getNotifications($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE admin_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$this->id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadNotificationCount() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM notifications
            WHERE admin_id = ? AND is_read = 0
        ");
        $stmt->execute([$this->id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function markNotificationsAsRead() {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE admin_id = ? AND is_read = 0
        ");
        return $stmt->execute([$this->id]);
    }
}