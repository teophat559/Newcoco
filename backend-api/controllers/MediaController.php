<?php
/**
 * Media Controller
 */

namespace BackendApi\Controllers;

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/MediaModel.php';

use BackendApi\Models\MediaModel;

class MediaController extends BaseController {
    private $mediaModel;

    public function __construct() {
        parent::__construct();
        $this->mediaModel = new MediaModel($this->db);
    }

    /**
     * Upload image
     */
    public function upload() {
        $this->validateRequest();
        $this->requireAuth();

        try {
            if (!isset($_FILES['file'])) {
                $this->jsonResponse(['error' => 'No file uploaded'], 400);
                return;
            }

            $file = $_FILES['file'];
            $errors = $this->mediaModel->validateFile($file);
            if (!empty($errors)) {
                $this->jsonResponse(['error' => implode(', ', $errors)], 400);
                return;
            }

            $data = [
                'user_id' => $_SESSION['user_id'],
                'type' => $_POST['type'] ?? 'image'
            ];

            $mediaId = $this->mediaModel->create($data, $file);
            $media = $this->mediaModel->getMedia($mediaId);

            $this->jsonResponse([
                'message' => 'File uploaded successfully',
                'media' => $media
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete image
     */
    public function delete($id) {
        $this->validateRequest();
        $this->requireAuth();

        try {
            $media = $this->mediaModel->getMedia($id);
            if (!$media) {
                $this->jsonResponse(['error' => 'Media not found'], 404);
                return;
            }

            if (!$this->isAdmin() && $media['user_id'] != $_SESSION['user_id']) {
                $this->jsonResponse(['error' => 'Unauthorized'], 403);
                return;
            }

            $this->mediaModel->delete($id);
            $this->jsonResponse(['message' => 'Media deleted successfully']);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user images
     */
    public function getUserMedia() {
        $this->validateRequest();
        $this->requireAuth();

        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;

            $media = $this->mediaModel->getUserMedia($_SESSION['user_id'], $limit, $offset);
            $stats = $this->mediaModel->getMediaStats($_SESSION['user_id']);

            $this->jsonResponse([
                'media' => $media,
                'stats' => $stats,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $stats['total_media']
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get image by ID
     */
    public function getMedia($id) {
        $this->validateRequest();

        try {
            $media = $this->mediaModel->getMedia($id);
            if (!$media) {
                $this->jsonResponse(['error' => 'Media not found'], 404);
                return;
            }

            $this->jsonResponse(['media' => $media]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update image
     */
    public function update($id) {
        $this->validateRequest();
        $this->requireAuth();

        try {
            $media = $this->mediaModel->getMedia($id);
            if (!$media) {
                $this->jsonResponse(['error' => 'Media not found'], 404);
                return;
            }

            if (!$this->isAdmin() && $media['user_id'] != $_SESSION['user_id']) {
                $this->jsonResponse(['error' => 'Unauthorized'], 403);
                return;
            }

            $data = $this->getRequestData();
            $this->mediaModel->update($id, $data);
            $updatedMedia = $this->mediaModel->getMedia($id);

            $this->jsonResponse([
                'message' => 'Media updated successfully',
                'media' => $updatedMedia
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get image URL
     */
    public function getImageUrl($id) {
        $this->validateRequest();

        try {
            $media = $this->mediaModel->getMedia($id);
            if (!$media) {
                $this->jsonResponse(['error' => 'Image not found'], 404);
                return;
            }

            $this->jsonResponse(['url' => $media['url']]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getContestMedia($contestId) {
        $this->validateRequest();

        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;

            $media = $this->mediaModel->getContestMedia($contestId, $limit, $offset);
            $stats = $this->mediaModel->getMediaStats();

            $this->jsonResponse([
                'media' => $media,
                'stats' => $stats,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $stats['total_media']
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}