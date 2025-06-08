<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $notification;

    public function __construct() {
        $this->notification = new Notification();
    }

    public function index() {
        $notifications = $this->notification->getAllNotifications();
        require_once __DIR__ . '/../views/notifications.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'title' => $_POST['title'],
                    'message' => $_POST['message'],
                    'type' => $_POST['type'],
                    'recipients' => $_POST['recipients'] ?? [],
                    'status' => $_POST['status']
                ];

                $this->notification->create($data);
                header('Location: /admin/notifications.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/notifications.php';
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'title' => $_POST['title'],
                    'message' => $_POST['message'],
                    'type' => $_POST['type'],
                    'recipients' => $_POST['recipients'] ?? [],
                    'status' => $_POST['status']
                ];

                $this->notification->update($id, $data);
                header('Location: /admin/notifications.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/notifications.php';
            }
        }

        $notification = $this->notification->getNotification($id);
        require_once __DIR__ . '/../views/notifications.php';
    }

    public function delete($id) {
        try {
            $this->notification->delete($id);
            header('Location: /admin/notifications.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
            require_once __DIR__ . '/../views/notifications.php';
        }
    }

    public function recipients($id) {
        $recipients = $this->notification->getRecipients($id);
        require_once __DIR__ . '/../views/notification_recipients.php';
    }

    public function stats($id) {
        $stats = $this->notification->getNotificationStats($id);
        require_once __DIR__ . '/../views/notification_stats.php';
    }
}