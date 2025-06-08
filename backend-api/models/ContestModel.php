<?php
namespace App\Models;

class ContestModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllContests() {
        $query = "SELECT c.*,
                    COUNT(DISTINCT ct.id) as contestant_count,
                    COUNT(DISTINCT v.id) as vote_count
                 FROM contests c
                 LEFT JOIN contestants ct ON c.id = ct.contest_id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 GROUP BY c.id
                 ORDER BY c.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getContest($id) {
        $query = "SELECT c.*,
                    COUNT(DISTINCT ct.id) as contestant_count,
                    COUNT(DISTINCT v.id) as vote_count
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

        $this->db->query($query, [
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['status']
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $query = "UPDATE contests
                 SET title = ?, description = ?, start_date = ?, end_date = ?, status = ?, updated_at = NOW()
                 WHERE id = ?";

        return $this->db->query($query, [
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            // Delete related votes
            $query = "DELETE v FROM votes v
                     INNER JOIN contestants ct ON v.contestant_id = ct.id
                     WHERE ct.contest_id = ?";
            $this->db->query($query, [$id]);

            // Delete contestants
            $query = "DELETE FROM contestants WHERE contest_id = ?";
            $this->db->query($query, [$id]);

            // Delete contest
            $query = "DELETE FROM contests WHERE id = ?";
            $this->db->query($query, [$id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getContestStats($id) {
        $query = "SELECT
                    COUNT(DISTINCT ct.id) as total_contestants,
                    COUNT(DISTINCT v.id) as total_votes,
                    COUNT(DISTINCT v.user_id) as unique_voters,
                    MIN(v.created_at) as first_vote,
                    MAX(v.created_at) as last_vote
                 FROM contests c
                 LEFT JOIN contestants ct ON c.id = ct.contest_id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE c.id = ?";

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