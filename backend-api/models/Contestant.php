<?php

namespace BackendApi\Models;

class Contestant {
    private $db;

    public function __construct() {
        $this->db = \BackendApi\Core\Database::getInstance();
    }

    public function getContestantsByContest($contestId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name, u.email as user_email
            FROM contestants c
            JOIN users u ON c.user_id = u.id
            WHERE c.contest_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$contestId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getContestant($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name, u.email as user_email
            FROM contestants c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO contestants (contest_id, user_id, title, description, media_url, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['contest_id'],
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['media_url'],
            $data['status'] ?? 'pending'
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
        $sql = "UPDATE contestants SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM contestants WHERE id = ?");
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }

    public function incrementViews($id) {
        $stmt = $this->db->prepare("
            UPDATE contestants
            SET views = views + 1
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function getContestantStats($id) {
        $stats = [
            'total_votes' => 0,
            'total_views' => 0,
            'rank' => 0
        ];

        // Get total votes
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM votes
            WHERE contestant_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_votes'] = $result['count'];

        // Get total views
        $stmt = $this->db->prepare("
            SELECT views
            FROM contestants
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_views'] = $result['views'] ?? 0;

        // Get rank
        $stmt = $this->db->prepare("
            SELECT COUNT(*) + 1 as rank
            FROM contestants c
            LEFT JOIN votes v ON c.id = v.contestant_id
            WHERE c.contest_id = (
                SELECT contest_id FROM contestants WHERE id = ?
            )
            GROUP BY c.id
            HAVING COUNT(v.id) > (
                SELECT COUNT(*)
                FROM votes
                WHERE contestant_id = ?
            )
        ");
        $stmt->execute([$id, $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['rank'] = $result['rank'] ?? 1;

        return $stats;
    }
}