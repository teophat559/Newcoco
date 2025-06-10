<?php
require_once __DIR__ . '/../config/config.php';

class Settings {
    private $db;
    private $cache = [];

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllSettings() {
        if (!empty($this->cache)) {
            return $this->cache;
        }

        $query = "SELECT * FROM settings";
        $settings = $this->db->query($query)->fetchAll();

        foreach ($settings as $setting) {
            $this->cache[$setting['key']] = $setting['value'];
        }

        return $this->cache;
    }

    public function getSetting($key, $default = null) {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $query = "SELECT value FROM settings WHERE `key` = ?";
        $result = $this->db->query($query, [$key])->fetch();

        if ($result) {
            $this->cache[$key] = $result['value'];
            return $result['value'];
        }

        return $default;
    }

    public function update($data) {
        $this->db->beginTransaction();

        try {
            foreach ($data as $key => $value) {
                $query = "INSERT INTO settings (`key`, value, updated_at)
                         VALUES (?, ?, NOW())
                         ON DUPLICATE KEY UPDATE
                         value = VALUES(value),
                         updated_at = VALUES(updated_at)";

                $this->db->query($query, [$key, $value]);
                $this->cache[$key] = $value;
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($key) {
        $query = "DELETE FROM settings WHERE `key` = ?";
        $result = $this->db->query($query, [$key]);

        if ($result) {
            unset($this->cache[$key]);
        }

        return $result;
    }

    public function getSettingsHistory($key) {
        $query = "SELECT * FROM settings_history
                 WHERE `key` = ?
                 ORDER BY updated_at DESC
                 LIMIT 10";

        return $this->db->query($query, [$key])->fetchAll();
    }

    public function getSettingsStats() {
        $query = "SELECT
                    COUNT(*) as total_settings,
                    COUNT(CASE WHEN updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as recently_updated,
                    MIN(updated_at) as first_update,
                    MAX(updated_at) as last_update
                 FROM settings";

        return $this->db->query($query)->fetch();
    }
}