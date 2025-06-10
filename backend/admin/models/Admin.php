<?php

namespace Admin\Models;

use Admin\Includes\Database;
use PDO;
use PDOException;
use Exception;

class Admin {
    private $db;
    private $pdo;
    private $id;
    private $name;
    private $email;
    private $profile_image;
    private $role;
    private $last_login;
    private $created_at;
    private $updated_at;

    public function __construct() {
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                return $admin;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Login failed: " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get admin: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                if ($key !== 'id' && $key !== 'password') {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (isset($data['password'])) {
                $fields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql = "UPDATE admins SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Failed to update admin: " . $e->getMessage());
        }
    }

    public function create($data) {
        try {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $fields = array_keys($data);
            $placeholders = array_map(function($field) { return ":$field"; }, $fields);

            $sql = "INSERT INTO admins (" . implode(', ', $fields) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            throw new Exception("Failed to create admin: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM admins WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to delete admin: " . $e->getMessage());
        }
    }

    public function getAll($limit = 10, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM admins LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to get admins: " . $e->getMessage());
        }
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

    public function getRole() {
        return $this->role;
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function updateProfile($data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE admins
                SET name = :name, email = :email, profile_image = :profile_image, updated_at = NOW()
                WHERE id = :id
            ");
            return $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':profile_image' => $data['profile_image'],
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to update profile: " . $e->getMessage());
        }
    }

    public function updatePassword($new_password) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                UPDATE admins
                SET password = :password, updated_at = NOW()
                WHERE id = :id
            ");
            return $stmt->execute([
                ':password' => $hashed_password,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to update password: " . $e->getMessage());
        }
    }

    public function updateLastLogin() {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE admins
                SET last_login = NOW()
                WHERE id = :id
            ");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to update last login: " . $e->getMessage());
        }
    }

    public static function authenticate($email, $password) {
        try {
            $db = new Database();
            $pdo = $db->getConnection();

            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $admin_obj = new self();
                $admin_obj->id = $admin['id'];
                $admin_obj->name = $admin['name'];
                $admin_obj->email = $admin['email'];
                $admin_obj->profile_image = $admin['profile_image'];
                $admin_obj->role = $admin['role'];
                $admin_obj->last_login = $admin['last_login'];
                $admin_obj->created_at = $admin['created_at'];
                $admin_obj->updated_at = $admin['updated_at'];

                $admin_obj->updateLastLogin();
                return $admin_obj;
            }

            return false;
        } catch (PDOException $e) {
            throw new Exception("Authentication failed: " . $e->getMessage());
        }
    }

    public static function count() {
        try {
            $db = new Database();
            $pdo = $db->getConnection();

            $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Failed to count admins: " . $e->getMessage());
        }
    }
}