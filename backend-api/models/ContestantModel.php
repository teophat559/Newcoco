<?php
namespace BackendApi\Models;

class ContestantModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllContestants() {
        $query = "SELECT ct.*, c.title as contest_title,
                    COUNT(v.id) as vote_count
                 FROM contestants ct
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 GROUP BY ct.id
                 ORDER BY ct.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getContestant($id) {
        $query = "SELECT ct.*, c.title as contest_title,
                    COUNT(v.id) as vote_count
                 FROM contestants ct
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE ct.id = ?
                 GROUP BY ct.id";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO contestants (contest_id, name, description, image, status, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $this->db->query($query, [
            $data['contest_id'],
            $data['name'],
            $data['description'],
            $data['image'],
            $data['status']
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $query = "UPDATE contestants
                 SET contest_id = ?, name = ?, description = ?, status = ?, updated_at = NOW()";
        $params = [
            $data['contest_id'],
            $data['name'],
            $data['description'],
            $data['status']
        ];

        if (isset($data['image'])) {
            $query .= ", image = ?";
            $params[] = $data['image'];
        }

        $query .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->query($query, $params);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            // Delete votes
            $query = "DELETE FROM votes WHERE contestant_id = ?";
            $this->db->query($query, [$id]);

            // Delete contestant
            $query = "DELETE FROM contestants WHERE id = ?";
            $this->db->query($query, [$id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getVoteHistory($id) {
        $query = "SELECT v.*, u.username
                 FROM votes v
                 LEFT JOIN users u ON v.user_id = u.id
                 WHERE v.contestant_id = ?
                 ORDER BY v.created_at DESC";

        return $this->db->query($query, [$id])->fetchAll();
    }

    public function getContestantStats($id) {
        $query = "SELECT
                    COUNT(v.id) as total_votes,
                    COUNT(DISTINCT v.user_id) as unique_voters,
                    MIN(v.created_at) as first_vote,
                    MAX(v.created_at) as last_vote,
                    AVG(TIMESTAMPDIFF(HOUR, v.created_at, NOW())) as avg_vote_age
                 FROM contestants ct
                 LEFT JOIN votes v ON ct.id = v.contestant_id
                 WHERE ct.id = ?";

        return $this->db->query($query, [$id])->fetch();
    }
}