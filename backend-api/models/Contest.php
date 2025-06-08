<?php

namespace BackendApi\Models;

class Contest {
    private $db;

    public function __construct() {
        $this->db = \BackendApi\Core\Database::getInstance();
    }

    public function getAllContests() {
        $stmt = $this->db->prepare("SELECT * FROM contests ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getContest($id) {
        $stmt = $this->db->prepare("SELECT * FROM contests WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO contests (title, description, start_date, end_date, status, rules, prizes, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $data['rules'],
            $data['prizes'],
            $data['created_by']
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        $values[] = $id;
        $sql = "UPDATE contests SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM contests WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getContestStats($id) {
        $stats = [
            'total_contestants' => 0,
            'total_votes' => 0,
            'total_views' => 0
        ];

        // Get total contestants
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM contestants
            WHERE contest_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_contestants'] = $result['count'];

        // Get total votes
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM votes
            WHERE contest_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_votes'] = $result['count'];

        // Get total views
        $stmt = $this->db->prepare("
            SELECT SUM(views) as total
            FROM contestants
            WHERE contest_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_views'] = $result['total'] ?? 0;

        return $stats;
    }

    public function getTopContestants($id, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(v.id) as vote_count
            FROM contestants c
            LEFT JOIN votes v ON c.id = v.contestant_id
            WHERE c.contest_id = ?
            GROUP BY c.id
            ORDER BY vote_count DESC
            LIMIT ?
        ");
        $stmt->execute([$id, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}