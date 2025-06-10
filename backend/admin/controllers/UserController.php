<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function index() {
        $users = $this->user->getAllUsers();
        require_once __DIR__ . '/../views/users.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'role' => $_POST['role'],
                    'status' => $_POST['status']
                ];

                $this->user->create($data);
                header('Location: /admin/users.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/users.php';
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'role' => $_POST['role'],
                    'status' => $_POST['status']
                ];

                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->user->update($id, $data);
                header('Location: /admin/users.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/users.php';
            }
        }

        $user = $this->user->getUser($id);
        require_once __DIR__ . '/../views/users.php';
    }

    public function delete($id) {
        try {
            $this->user->delete($id);
            header('Location: /admin/users.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
            require_once __DIR__ . '/../views/users.php';
        }
    }

    public function activity($id) {
        $activity = $this->user->getUserActivity($id);
        require_once __DIR__ . '/../views/user_activity.php';
    }

    public function stats($id) {
        $stats = $this->user->getUserStats($id);
        require_once __DIR__ . '/../views/user_stats.php';
    }
}