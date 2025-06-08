<?php

namespace BackendApi\Models;

use BackendApi\Utils\Database;
use PDO;
use Exception;

class ActivityModel {
    private $db;
    private $table = 'activities';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllActivities() {
        $query = "SELECT a.*, u.username,
                    CASE
                        WHEN a.type = 'vote' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        WHEN a.type = 'contest' THEN (SELECT title FROM contests WHERE id = a.target_id)
                        WHEN a.type = 'contestant' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        ELSE NULL
                    END as target_name
                 FROM activities a
                 LEFT JOIN users u ON a.user_id = u.id
                 ORDER BY a.created_at DESC";

        return $this->db->fetchAll($query);
    }

    public function getActivity($id) {
        $query = "SELECT a.*, u.username,
                    CASE
                        WHEN a.type = 'vote' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        WHEN a.type = 'contest' THEN (SELECT title FROM contests WHERE id = a.target_id)
                        WHEN a.type = 'contestant' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        ELSE NULL
                    END as target_name
                 FROM activities a
                 LEFT JOIN users u ON a.user_id = u.id
                 WHERE a.id = ?";

        return $this->db->fetch($query, [$id]);
    }

    public function create($data) {
        $query = "INSERT INTO activities (user_id, type, action, target_id, details, ip_address, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";

        return $this->db->executeQuery($query, [
            $data['user_id'],
            $data['type'],
            $data['action'],
            $data['target_id'] ?? null,
            $data['details'] ?? null,
            $data['ip_address'] ?? null
        ]);
    }

    public function getUserActivities($userId, $limit = 10, $offset = 0) {
        $query = "SELECT a.*,
                    CASE
                        WHEN a.type = 'vote' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        WHEN a.type = 'contest' THEN (SELECT title FROM contests WHERE id = a.target_id)
                        WHEN a.type = 'contestant' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        ELSE NULL
                    END as target_name
                 FROM activities a
                 WHERE a.user_id = ?
                 ORDER BY a.created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->fetchAll($query, [$userId, $limit, $offset]);
    }

    public function getContestActivities($contestId, $limit = 10, $offset = 0) {
        $query = "SELECT a.*, u.username,
                    CASE
                        WHEN a.type = 'vote' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        WHEN a.type = 'contestant' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        ELSE NULL
                    END as target_name
                 FROM activities a
                 LEFT JOIN users u ON a.user_id = u.id
                 WHERE a.type IN ('vote', 'contestant')
                 AND a.target_id IN (SELECT id FROM contestants WHERE contest_id = ?)
                 ORDER BY a.created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->fetchAll($query, [$contestId, $limit, $offset]);
    }

    public function getContestantActivities($contestantId, $limit = 10, $offset = 0) {
        $query = "SELECT a.*, u.username
                 FROM activities a
                 LEFT JOIN users u ON a.user_id = u.id
                 WHERE a.type = 'vote' AND a.target_id = ?
                 ORDER BY a.created_at DESC
                 LIMIT ? OFFSET ?";

        return $this->db->fetchAll($query, [$contestantId, $limit, $offset]);
    }

    public function getActivityStats($userId = null) {
        $query = "SELECT
                    COUNT(*) as total_activities,
                    COUNT(CASE WHEN type = 'vote' THEN 1 END) as total_votes,
                    COUNT(CASE WHEN type = 'contest' THEN 1 END) as total_contests,
                    COUNT(CASE WHEN type = 'contestant' THEN 1 END) as total_contestants,
                    MIN(created_at) as first_activity,
                    MAX(created_at) as last_activity";

        if ($userId) {
            $query .= " FROM activities WHERE user_id = ?";
            return $this->db->fetch($query, [$userId]);
        } else {
            $query .= " FROM activities";
            return $this->db->fetch($query);
        }
    }

    public function getRecentActivities($limit = 10) {
        $query = "SELECT a.*, u.username,
                    CASE
                        WHEN a.type = 'vote' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        WHEN a.type = 'contest' THEN (SELECT title FROM contests WHERE id = a.target_id)
                        WHEN a.type = 'contestant' THEN (SELECT name FROM contestants WHERE id = a.target_id)
                        ELSE NULL
                    END as target_name
                 FROM activities a
                 LEFT JOIN users u ON a.user_id = u.id
                 ORDER BY a.created_at DESC
                 LIMIT ?";

        return $this->db->fetchAll($query, [$limit]);
    }

    public function deleteOldActivities($days = 30) {
        $query = "DELETE FROM activities WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        return $this->db->executeQuery($query, [$days]);
    }

    public function logVote($userId, $contestantId, $ipAddress = null) {
        return $this->create([
            'user_id' => $userId,
            'type' => 'vote',
            'action' => 'vote',
            'target_id' => $contestantId,
            'details' => json_encode(['action' => 'voted']),
            'ip_address' => $ipAddress
        ]);
    }

    public function logContestAction($userId, $contestId, $action, $details = null, $ipAddress = null) {
        return $this->create([
            'user_id' => $userId,
            'type' => 'contest',
            'action' => $action,
            'target_id' => $contestId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ipAddress
        ]);
    }

    public function logContestantAction($userId, $contestantId, $action, $details = null, $ipAddress = null) {
        return $this->create([
            'user_id' => $userId,
            'type' => 'contestant',
            'action' => $action,
            'target_id' => $contestantId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ipAddress
        ]);
    }
}