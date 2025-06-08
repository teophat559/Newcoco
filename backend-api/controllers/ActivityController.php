<?php
/**
 * Activity Controller
 */

namespace BackendApi\Controllers;

use BackendApi\Controllers\BaseController;
use BackendApi\Models\ActivityModel;
use Exception;

const DEFAULT_PAGE_SIZE = 10;

class ActivityController extends BaseController {
    private $activityModel;

    public function __construct() {
        parent::__construct();
        $this->activityModel = new ActivityModel();
    }

    /**
     * Get all activities
     */
    public function index() {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get query parameters
            $type = $_GET['type'] ?? null;
            $userId = $_GET['user_id'] ?? null;
            $contestId = $_GET['contest_id'] ?? null;
            $contestantId = $_GET['contestant_id'] ?? null;
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE);
            $offset = ($page - 1) * $limit;

            // Get activities based on filters
            if ($userId) {
                $activities = $this->activityModel->getUserActivities($userId, $limit, $offset);
            } elseif ($contestId) {
                $activities = $this->activityModel->getContestActivities($contestId, $limit, $offset);
            } elseif ($contestantId) {
                $activities = $this->activityModel->getContestantActivities($contestantId, $limit, $offset);
            } else {
                $activities = $this->activityModel->getAllActivities();
            }

            $this->sendResponse($activities);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get recent activities
     */
    public function recent() {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get query parameters
            $limit = (int)($_GET['limit'] ?? 10);

            // Get recent activities
            $activities = $this->activityModel->getRecentActivities($limit);

            $this->sendResponse($activities);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get contest activities
     */
    public function contest($contestId) {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get query parameters
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE);
            $offset = ($page - 1) * $limit;

            // Get contest activities
            $activities = $this->activityModel->getContestActivities($contestId, $limit, $offset);

            $this->sendResponse($activities);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get user activities
     */
    public function user($userId) {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get query parameters
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE);
            $offset = ($page - 1) * $limit;

            // Get user activities
            $activities = $this->activityModel->getUserActivities($userId, $limit, $offset);

            $this->sendResponse($activities);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get contestant activities
     */
    public function contestant($contestantId) {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get query parameters
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE);
            $offset = ($page - 1) * $limit;

            // Get contestant activities
            $activities = $this->activityModel->getContestantActivities($contestantId, $limit, $offset);

            $this->sendResponse($activities);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Log activity
     */
    public function log() {
        $this->validateRequest();
        $this->requireAuth();

        try {
            // Get request data
            $data = $this->getRequestData();

            if (empty($data['type']) || empty($data['message'])) {
                $this->sendError('Type and message are required');
            }

            // Log activity based on type
            $activity = null;
            switch ($data['type']) {
                case 'vote':
                    $activity = $this->activityModel->logVote(
                        $_SESSION['user_id'],
                        $data['contestant_id'],
                        $_SERVER['REMOTE_ADDR'] ?? null
                    );
                    break;
                case 'contest':
                    $activity = $this->activityModel->logContestAction(
                        $_SESSION['user_id'],
                        $data['contest_id'],
                        $data['action'] ?? 'update',
                        $data['details'] ?? null,
                        $_SERVER['REMOTE_ADDR'] ?? null
                    );
                    break;
                case 'contestant':
                    $activity = $this->activityModel->logContestantAction(
                        $_SESSION['user_id'],
                        $data['contestant_id'],
                        $data['action'] ?? 'update',
                        $data['details'] ?? null,
                        $_SERVER['REMOTE_ADDR'] ?? null
                    );
                    break;
                default:
                    $this->sendError('Invalid activity type');
            }

            $this->sendResponse($activity);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
}