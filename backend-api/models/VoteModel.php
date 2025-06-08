<?php
namespace BackendApi\Models;

class VoteModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllVotes() {
        $query = "SELECT v.*, u.username, ct.name as contestant_name, c.title as contest_title
                 FROM votes v
                 LEFT JOIN users u ON v.user_id = u.id
                 LEFT JOIN contestants ct ON v.contestant_id = ct.id
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 ORDER BY v.created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    public function getVote($id) {
        $query = "SELECT v.*, u.username, ct.name as contestant_name, c.title as contest_title
                 FROM votes v
                 LEFT JOIN users u ON v.user_id = u.id
                 LEFT JOIN contestants ct ON v.contestant_id = ct.id
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 WHERE v.id = ?";

        return $this->db->query($query, [$id])->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO votes (user_id, contestant_id, created_at)
                 VALUES (?, ?, NOW())";

        $this->db->query($query, [
            $data['user_id'],
            $data['contestant_id']
        ]);

        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $query = "DELETE FROM votes WHERE id = ?";
        return $this->db->query($query, [$id]);
    }

    public function getUserVotes($userId) {
        $query = "SELECT v.*, ct.name as contestant_name, c.title as contest_title
                 FROM votes v
                 LEFT JOIN contestants ct ON v.contestant_id = ct.id
                 LEFT JOIN contests c ON ct.contest_id = c.id
                 WHERE v.user_id = ?
                 ORDER BY v.created_at DESC";

        return $this->db->query($query, [$userId])->fetchAll();
    }

    public function getContestantVotes($contestantId) {
        $query = "SELECT v.*, u.username
                 FROM votes v
                 LEFT JOIN users u ON v.user_id = u.id
                 WHERE v.contestant_id = ?
                 ORDER BY v.created_at DESC";

        return $this->db->query($query, [$contestantId])->fetchAll();
    }

    public function hasVoted($userId, $contestantId) {
        $query = "SELECT COUNT(*) as count
                 FROM votes
                 WHERE user_id = ? AND contestant_id = ?";

        $result = $this->db->query($query, [$userId, $contestantId])->fetch();
        return $result['count'] > 0;
    }

    public function getVoteStats() {
        $query = "SELECT
                    COUNT(*) as total_votes,
                    COUNT(DISTINCT user_id) as unique_voters,
                    COUNT(DISTINCT contestant_id) as voted_contestants,
                    MIN(created_at) as first_vote,
                    MAX(created_at) as last_vote,
                    AVG(TIMESTAMPDIFF(HOUR, created_at, NOW())) as avg_vote_age
                 FROM votes";

        return $this->db->query($query)->fetch();
    }
}