<?php

namespace BackendApi\Models;

class Vote {
    private $db;

    public function __construct() {
        $this->db = \BackendApi\Core\Database::getInstance();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO votes (contest_id, contestant_id, user_id, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['contest_id'],
            $data['contestant_id'],
            $data['user_id'],
            $data['comment'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM votes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getUserVotes($userId, $contestId = null) {
        $sql = "
            SELECT v.*, c.title as contestant_title, u.name as user_name
            FROM votes v
            JOIN contestants c ON v.contestant_id = c.id
            JOIN users u ON v.user_id = u.id
            WHERE v.user_id = ?
        ";
        $params = [$userId];

        if ($contestId) {
            $sql .= " AND v.contest_id = ?";
            $params[] = $contestId;
        }

        $sql .= " ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getContestantVotes($contestantId) {
        $stmt = $this->db->prepare("
            SELECT v.*, u.name as user_name
            FROM votes v
            JOIN users u ON v.user_id = u.id
            WHERE v.contestant_id = ?
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$contestantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function hasUserVoted($userId, $contestantId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM votes
            WHERE user_id = ? AND contestant_id = ?
        ");
        $stmt->execute([$userId, $contestantId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getVoteStats($contestId) {
        $stats = [
            'total_votes' => 0,
            'unique_voters' => 0,
            'votes_by_day' => []
        ];

        // Get total votes
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM votes
            WHERE contest_id = ?
        ");
        $stmt->execute([$contestId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['total_votes'] = $result['count'];

        // Get unique voters
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT user_id) as count
            FROM votes
            WHERE contest_id = ?
        ");
        $stmt->execute([$contestId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stats['unique_voters'] = $result['count'];

        // Get votes by day
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM votes
            WHERE contest_id = ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $stmt->execute([$contestId]);
        $stats['votes_by_day'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $stats;
    }
}