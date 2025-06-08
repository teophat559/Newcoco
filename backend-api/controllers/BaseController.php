<?php
namespace BackendApi\Controllers;

use BackendApi\Core\Database;

class BaseController {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    protected function validateRequest() {
        // Validate request method, headers, etc
        return true;
    }

    protected function requireAuth() {
        // Check if user is authenticated
        if (!$this->isLoggedIn()) {
            $this->sendError('Unauthorized', 401);
            exit;
        }
        return true;
    }

    protected function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    protected function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        return $data ?: $_POST;
    }

    protected function sendResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function sendError($message, $status = 400) {
        $this->sendResponse(['error' => $message], $status);
    }

    protected function jsonResponse($data = [], $status = 200) {
        return $this->sendResponse($data, $status);
    }

    protected function validateRequiredFields($data, $fields) {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $this->sendError("Field {$field} is required");
                return false;
            }
        }
        return true;
    }

    protected function validateDateRange($startDate, $endDate) {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        if ($start >= $end) {
            $this->sendError('End date must be after start date');
            return false;
        }
        return true;
    }

    protected function validateFileUpload($file, $allowedTypes, $maxSize) {
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->sendError('Invalid file parameters');
            return false;
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->sendError('File size exceeds limit');
                return false;
            case UPLOAD_ERR_PARTIAL:
                $this->sendError('File was only partially uploaded');
                return false;
            case UPLOAD_ERR_NO_FILE:
                $this->sendError('No file was uploaded');
                return false;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->sendError('Missing temporary folder');
                return false;
            case UPLOAD_ERR_CANT_WRITE:
                $this->sendError('Failed to write file to disk');
                return false;
            case UPLOAD_ERR_EXTENSION:
                $this->sendError('A PHP extension stopped the file upload');
                return false;
            default:
                $this->sendError('Unknown upload error');
                return false;
        }

        if ($file['size'] > $maxSize) {
            $this->sendError('File size exceeds limit');
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            $this->sendError('Invalid file type');
            return false;
        }

        return true;
    }
}