<?php

namespace BackendApi\Utils;

class ActivityLogger {
    private $db;
    private $logger;

    public function __construct($db) {
        $this->db = $db;
        $this->logger = Logger::getInstance();
    }

    public function addActivityLog($userId, $type, $description, $data = []) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, type, description, data, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            $result = $stmt->execute([
                $userId,
                $type,
                $description,
                json_encode($data)
            ]);

            if ($result) {
                $this->logger->info('Activity logged', [
                    'user_id' => $userId,
                    'type' => $type,
                    'description' => $description,
                    'data' => $data
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Failed to log activity', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'type' => $type
            ]);
            throw $e;
        }
    }
}