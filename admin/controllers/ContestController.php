<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Contest.php';

class ContestController {
    private $contest;

    public function __construct() {
        $this->contest = new Contest();
    }

    public function index() {
        $contests = $this->contest->getAllContests();
        require_once __DIR__ . '/../views/contests.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'status' => $_POST['status']
                ];

                $this->contest->create($data);
                header('Location: /admin/contests.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/contests.php';
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'start_date' => $_POST['start_date'],
                    'end_date' => $_POST['end_date'],
                    'status' => $_POST['status']
                ];

                $this->contest->update($id, $data);
                header('Location: /admin/contests.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/contests.php';
            }
        }

        $contest = $this->contest->getContest($id);
        require_once __DIR__ . '/../views/contests.php';
    }

    public function delete($id) {
        try {
            $this->contest->delete($id);
            header('Location: /admin/contests.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
            require_once __DIR__ . '/../views/contests.php';
        }
    }

    public function stats($id) {
        $stats = $this->contest->getContestStats($id);
        $topContestants = $this->contest->getTopContestants($id);
        require_once __DIR__ . '/../views/contest_stats.php';
    }
}