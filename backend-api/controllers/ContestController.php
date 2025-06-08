<?php
namespace BackendApi\Controllers;

use BackendApi\Models\Contest;
use BackendApi\Models\Contestant;
use BackendApi\Models\Vote;

class ContestController extends BaseController {
    private $contestModel;
    private $contestantModel;
    private $voteModel;

    public function __construct() {
        parent::__construct();
        $this->contestModel = new Contest();
        $this->contestantModel = new Contestant();
        $this->voteModel = new Vote();
    }

    public function index() {
        try {
            $contests = $this->contestModel->getAllContests();
            $this->jsonResponse(['contests' => $contests]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id) {
        try {
            $contest = $this->contestModel->getContest($id);
            if (!$contest) {
                $this->jsonResponse(['error' => 'Contest not found'], 404);
                return;
            }

            $contestants = $this->contestantModel->getContestantsByContest($id);
            $stats = $this->contestModel->getContestStats($id);
            $topContestants = $this->contestModel->getTopContestants($id, 5);

            $this->jsonResponse([
                'contest' => $contest,
                'contestants' => $contestants,
                'stats' => $stats,
                'top_contestants' => $topContestants
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function create() {
        try {
            $data = $this->getRequestData();

            // Validate required fields
            $requiredFields = ['title', 'description', 'start_date', 'end_date'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->jsonResponse(['error' => "Field {$field} is required"], 400);
                    return;
                }
            }

            // Validate dates
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);
            if ($startDate >= $endDate) {
                $this->jsonResponse(['error' => 'End date must be after start date'], 400);
                return;
            }

            // Create contest
            $contestId = $this->contestModel->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'] ?? 'draft',
                'rules' => $data['rules'] ?? null,
                'prizes' => $data['prizes'] ?? null,
                'created_by' => $_SESSION['user_id'] ?? null
            ]);

            $this->jsonResponse([
                'message' => 'Contest created successfully',
                'contest_id' => $contestId
            ], 201);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $data = $this->getRequestData();

            // Check if contest exists
            $contest = $this->contestModel->getContest($id);
            if (!$contest) {
                $this->jsonResponse(['error' => 'Contest not found'], 404);
                return;
            }

            // Validate dates if provided
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $startDate = strtotime($data['start_date']);
                $endDate = strtotime($data['end_date']);
                if ($startDate >= $endDate) {
                    $this->jsonResponse(['error' => 'End date must be after start date'], 400);
                    return;
                }
            }

            // Update contest
            $this->contestModel->update($id, [
                'title' => $data['title'] ?? $contest['title'],
                'description' => $data['description'] ?? $contest['description'],
                'start_date' => $data['start_date'] ?? $contest['start_date'],
                'end_date' => $data['end_date'] ?? $contest['end_date'],
                'status' => $data['status'] ?? $contest['status'],
                'rules' => $data['rules'] ?? $contest['rules'],
                'prizes' => $data['prizes'] ?? $contest['prizes']
            ]);

            $this->jsonResponse([
                'message' => 'Contest updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id) {
        try {
            // Check if contest exists
            $contest = $this->contestModel->getContest($id);
            if (!$contest) {
                $this->jsonResponse(['error' => 'Contest not found'], 404);
                return;
            }

            // Delete contest and related data
            $this->contestModel->delete($id);

            $this->jsonResponse([
                'message' => 'Contest deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getStats($id) {
        try {
            $stats = $this->contestModel->getContestStats($id);
            $this->jsonResponse(['stats' => $stats]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getTopContestants($id) {
        try {
            $limit = $_GET['limit'] ?? 5;
            $contestants = $this->contestModel->getTopContestants($id, $limit);
            $this->jsonResponse(['contestants' => $contestants]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
