<?php
namespace BackendApi\Controllers;

use BackendApi\Models\ContestantModel;
use BackendApi\Models\VoteModel;
use BackendApi\Controllers\BaseController;

class ContestantController extends BaseController {
    private $contestantModel;
    private $voteModel;

    public function __construct() {
        parent::__construct();
        $this->contestantModel = new ContestantModel($this->db);
        $this->voteModel = new VoteModel($this->db);
    }

    public function index() {
        try {
            $contestants = $this->contestantModel->getAllContestants();
            $this->jsonResponse(['success' => true, 'data' => $contestants]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $contestant = $this->contestantModel->getContestant($id);
            if (!$contestant) {
                $this->jsonResponse(['success' => false, 'message' => 'Contestant not found'], 404);
                return;
            }

            $voteHistory = $this->contestantModel->getVoteHistory($id);
            $stats = $this->contestantModel->getContestantStats($id);

            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'contestant' => $contestant,
                    'vote_history' => $voteHistory,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create() {
        try {
            $data = $this->getRequestData();

            // Validate required fields
            $requiredFields = ['contest_id', 'name', 'description'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->jsonResponse(['success' => false, 'message' => "Field {$field} is required"], 400);
                    return;
                }
            }

            // Handle file upload
            if (isset($_FILES['image'])) {
                $uploadResult = $this->handleFileUpload($_FILES['image'], 'contestants');
                if (!$uploadResult['success']) {
                    $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']], 400);
                    return;
                }
                $data['image'] = $uploadResult['path'];
            }

            $contestantId = $this->contestantModel->create($data);
            $contestant = $this->contestantModel->getContestant($contestantId);

            $this->jsonResponse(['success' => true, 'data' => $contestant], 201);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $data = $this->getRequestData();

            // Check if contestant exists
            $contestant = $this->contestantModel->getContestant($id);
            if (!$contestant) {
                $this->jsonResponse(['success' => false, 'message' => 'Contestant not found'], 404);
                return;
            }

            // Handle file upload
            if (isset($_FILES['image'])) {
                $uploadResult = $this->handleFileUpload($_FILES['image'], 'contestants');
                if (!$uploadResult['success']) {
                    $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']], 400);
                    return;
                }
                $data['image'] = $uploadResult['path'];

                // Delete old image
                if (!empty($contestant['image'])) {
                    $this->deleteFile($contestant['image']);
                }
            }

            $this->contestantModel->update($id, $data);
            $updatedContestant = $this->contestantModel->getContestant($id);

            $this->jsonResponse(['success' => true, 'data' => $updatedContestant]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id) {
        try {
            // Check if contestant exists
            $contestant = $this->contestantModel->getContestant($id);
            if (!$contestant) {
                $this->jsonResponse(['success' => false, 'message' => 'Contestant not found'], 404);
                return;
            }

            // Delete image
            if (!empty($contestant['image'])) {
                $this->deleteFile($contestant['image']);
            }

            $this->contestantModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => 'Contestant deleted successfully']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function vote($id) {
        try {
            // Check if contestant exists
            $contestant = $this->contestantModel->getContestant($id);
            if (!$contestant) {
                $this->jsonResponse(['success' => false, 'message' => 'Contestant not found'], 404);
                return;
            }

            // Check if user is logged in
            if (!$this->isLoggedIn()) {
                $this->jsonResponse(['success' => false, 'message' => 'User must be logged in to vote'], 401);
                return;
            }

            // Check if user has already voted
            if ($this->voteModel->hasVoted($this->getCurrentUserId(), $id)) {
                $this->jsonResponse(['success' => false, 'message' => 'User has already voted for this contestant'], 400);
                return;
            }

            $voteData = [
                'user_id' => $this->getCurrentUserId(),
                'contestant_id' => $id
            ];

            $this->voteModel->create($voteData);
            $this->jsonResponse(['success' => true, 'message' => 'Vote recorded successfully']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function handleFileUpload($file, $directory) {
        $uploadDir = __DIR__ . '/../../public/uploads/' . $directory . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => true,
                'path' => '/uploads/' . $directory . '/' . $fileName
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to upload file'
        ];
    }

    private function deleteFile($path) {
        $filePath = __DIR__ . '/../../public' . $path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}