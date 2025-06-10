<?php
require_once __DIR__ . '/../config/config.php';

class Contestant {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllContestants() {
        $query = "SELECT ct.*, c.title as contest_title, COUNT(v.id) as total_votes
                 FROM contestants ct
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 GROUP BY ct.id
                 ORDER BY ct.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getContestant($id) {
        $query = "SELECT ct.*, c.title as contest_title, COUNT(v.id) as total_votes
                 FROM contestants ct
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE ct.id = ?
                 GROUP BY ct.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO contestants (contest_id, name, description, image_url, status, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $params = [
            $data['contest_id'],
            $data['name'],
            $data['description'],
            $data['image_url'],
            $data['status'] ?? 'active'
        ];

        return $this->db->query($query, $params);
    }

    public function update($id, $data) {
        $query = "UPDATE contestants
                 SET contest_id = ?, name = ?, description = ?, image_url = ?, status = ?, updated_at = NOW()
                 WHERE id = ?";

        $params = [
            $data['contest_id'],
            $data['name'],
            $data['description'],
            $data['image_url'],
            $data['status'],
            $id
        ];

        return $this->db->query($query, $params);
    }

    public function delete($id) {
        // First delete related votes
        $this->db->query("DELETE FROM votes WHERE contestant_id = ?", [$id]);

        // Then delete the contestant
        return $this->db->query("DELETE FROM contestants WHERE id = ?", [$id]);
    }

    public function getVoteHistory($id) {
        $query = "SELECT v.*, u.username
                 FROM votes v
                 JOIN users u ON v.user_id = u.id
                 WHERE v.contestant_id = ?
                 ORDER BY v.created_at DESC";

        return $this->db->query($query, [$id])->fetchAll();
    }

    public function getContestantStats($id) {
        $query = "SELECT
                    COUNT(v.id) as total_votes,
                    COUNT(DISTINCT v.user_id) as unique_voters,
                    MIN(v.created_at) as first_vote,
                    MAX(v.created_at) as last_vote
                 FROM contestants ct
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE ct.id = ?
                 GROUP BY ct.id";

        return $this->db->query($query, [$id])->fetch();
    }
}