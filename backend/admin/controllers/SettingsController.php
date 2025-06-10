<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Settings.php';

class SettingsController {
    private $settings;

    public function __construct() {
        $this->settings = new Settings();
    }

    public function index() {
        $settings = $this->settings->getAllSettings();
        require_once __DIR__ . '/../views/settings.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'site_name' => $_POST['site_name'],
                    'admin_email' => $_POST['admin_email'],
                    'items_per_page' => $_POST['items_per_page'],
                    'max_file_size' => $_POST['max_file_size'],
                    'allow_registration' => isset($_POST['allow_registration']) ? 1 : 0,
                    'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
                    'default_language' => $_POST['default_language'],
                    'timezone' => $_POST['timezone']
                ];

                $this->settings->update($data);
                header('Location: /admin/settings.php?success=1');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
                require_once __DIR__ . '/../views/settings.php';
            }
        }
    }

    public function history($key) {
        $history = $this->settings->getSettingsHistory($key);
        require_once __DIR__ . '/../views/settings_history.php';
    }

    public function stats() {
        $stats = $this->settings->getSettingsStats();
        require_once __DIR__ . '/../views/settings_stats.php';
    }
}