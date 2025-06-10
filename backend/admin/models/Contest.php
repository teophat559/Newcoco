<?php
require_once __DIR__ . '/../config/config.php';

class Contest {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllContests() {
        $query = "SELECT c.*,
                        COUNT(DISTINCT ct.id) as total_contestants,
                        COUNT(DISTINCT v.id) as total_votes
                 FROM contests c
                 LEFT JOIN contestants ct ON c.id = ct.contest_id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 GROUP BY c.id
                 ORDER BY c.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getContest($id) {
        $query = "SELECT c.*,
                        COUNT(DISTINCT ct.id) as total_contestants,
                        COUNT(DISTINCT v.id) as total_votes
                 FROM contests c
                 LEFT JOIN contestants ct ON c.id = ct.contest_id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE c.id = ?
                 GROUP BY c.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO contests (title, description, start_date, end_date, status, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 'active'
        ];

        return $this->db->query($query, $params);
    }

    public function update($id, $data) {
        $query = "UPDATE contests
                 SET title = ?, description = ?, start_date = ?, end_date = ?, status = ?, updated_at = NOW()
                 WHERE id = ?";

        $params = [
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $id
        ];

        return $this->db->query($query, $params);
    }

    public function delete($id) {
        // First delete related records
        $this->db->query("DELETE FROM votes WHERE contestant_id IN (SELECT id FROM contestants WHERE contest_id = ?)", [$id]);
        $this->db->query("DELETE FROM contestants WHERE contest_id = ?", [$id]);

        // Then delete the contest
        return $this->db->query("DELETE FROM contests WHERE id = ?", [$id]);
    }

    public function getContestStats($id) {
        $query = "SELECT
                    COUNT(DISTINCT ct.id) as total_contestants,
                    COUNT(DISTINCT v.id) as total_votes,
                    COUNT(DISTINCT v.user_id) as total_voters,
                    AVG(v.created_at) as average_vote_time
                 FROM contests c
                 LEFT JOIN contestants ct ON c.id = ct.contest_id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE c.id = ?
                 GROUP BY c.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function getTopContestants($id, $limit = 10) {
        $query = "SELECT ct.*, COUNT(v.id) as vote_count
                 FROM contestants ct
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE ct.contest_id = ?
                 GROUP BY ct.id
                 ORDER BY vote_count DESC
                 LIMIT ?";

        return $this->db->query($query, [$id, $limit])->fetchAll();
    }
}