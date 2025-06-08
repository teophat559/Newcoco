<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Contestant.php';

class ContestantController {
    private $contestant;

    public function __construct() {
        $this->contestant = new Contestant();
    }

    public function index() {
        $contestants = $this->contestant->getAllContestants();
        require_once __DIR__ . '/../views/contestants.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'contest_id' => $_POST['contest_id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'image' => $_FILES['image'],
                    'status' => $_POST['status']
                ];

                $this->contestant->create($data);
                header('Location: /admin/contestants.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/contestants.php';
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'contest_id' => $_POST['contest_id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'status' => $_POST['status']
                ];

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $data['image'] = $_FILES['image'];
                }

                $this->contestant->update($id, $data);
                header('Location: /admin/contestants.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/contestants.php';
            }
        }

        $contestant = $this->contestant->getContestant($id);
        require_once __DIR__ . '/../views/contestants.php';
    }

    public function delete($id) {
        try {
            $this->contestant->delete($id);
            header('Location: /admin/contestants.php?success=1');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
            require_once __DIR__ . '/../views/contestants.php';
        }
    }

    public function stats($id) {
        $stats = $this->contestant->getContestantStats($id);
        $voteHistory = $this->contestant->getVoteHistory($id);
        require_once __DIR__ . '/../views/contestant_stats.php';
    }
}