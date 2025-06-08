<?php
/**
 * Notification Controller
 */

namespace BackendApi\Controllers;

use BackendApi\Controllers\BaseController;
use BackendApi\Models\NotificationModel;
use Exception;

class NotificationController extends BaseController {
    private $notificationModel;

    public function __construct() {
        parent::__construct();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get all notifications
     */
    public function index() {
        try {
            // Validate request
            $this->validateRequest(['GET']);
            $this->requireAuth();

            // Get query parameters
            $type = $_GET['type'] ?? null;
            $isRead = $_GET['is_read'] ?? null;
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 20);

            // Get notifications
            $notifications = $this->notificationModel->getUserNotifications($_SESSION['user_id'], [
                'type' => $type,
                'is_read' => $isRead,
                'page' => $page,
                'limit' => $limit
            ]);

            $this->sendResponse([
                'notifications' => $notifications['data'],
                'total' => $notifications['total'],
                'page' => $page,
                'limit' => $limit
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get unread notifications
     */
    public function unread() {
        try {
            // Validate request
            $this->validateRequest(['GET']);
            $this->requireAuth();

            // Get unread notifications
            $notifications = $this->notificationModel->getUnreadNotifications($_SESSION['user_id']);

            $this->sendResponse([
                'notifications' => $notifications
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        try {
            // Validate request
            $this->validateRequest(['POST']);
            $this->requireAuth();

            // Get notification
            $notification = $this->notificationModel->getById($id);
            if (!$notification) {
                throw new Exception('Notification not found');
            }

            // Check if user owns notification
            if ($notification['user_id'] !== $_SESSION['user_id']) {
                throw new Exception('Access denied');
            }

            // Mark as read
            $updated = $notification->markAsRead();
            if (!$updated) {
                throw new Exception('Failed to mark notification as read');
            }

            $this->sendResponse([
                'message' => 'Notification marked as read',
                'notification' => $notification
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        try {
            // Validate request
            $this->validateRequest(['POST']);
            $this->requireAuth();

            // Mark all as read
            $updated = $this->notificationModel->markAllAsRead($_SESSION['user_id']);
            if (!$updated) {
                throw new Exception('Failed to mark notifications as read');
            }

            $this->sendResponse([
                'message' => 'All notifications marked as read'
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Delete notification
     */
    public function delete($id) {
        try {
            // Validate request
            $this->validateRequest(['DELETE']);
            $this->requireAuth();

            // Get notification
            $notification = $this->notificationModel->getById($id);
            if (!$notification) {
                throw new Exception('Notification not found');
            }

            // Check if user owns notification
            if ($notification['user_id'] !== $_SESSION['user_id']) {
                throw new Exception('Access denied');
            }

            // Delete notification
            $deleted = $this->notificationModel->delete($id);
            if (!$deleted) {
                throw new Exception('Failed to delete notification');
            }

            $this->sendResponse([
                'message' => 'Notification deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Delete all notifications
     */
    public function deleteAll() {
        try {
            // Validate request
            $this->validateRequest(['DELETE']);
            $this->requireAuth();

            // Delete all notifications
            $deleted = $this->notificationModel->deleteAll($_SESSION['user_id']);
            if (!$deleted) {
                throw new Exception('Failed to delete notifications');
            }

            $this->sendResponse([
                'message' => 'All notifications deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get notification count
     */
    public function count() {
        try {
            // Validate request
            $this->validateRequest(['GET']);
            $this->requireAuth();

            // Get notification count
            $count = $this->notificationModel->getUnreadCount($_SESSION['user_id']);

            $this->sendResponse([
                'count' => $count
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
}