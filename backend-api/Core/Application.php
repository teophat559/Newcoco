<?php

namespace BackendApi\Core;

class Application {
    protected static $instance = null;
    protected $config = [];

    public function __construct() {
        $this->config = require dirname(__DIR__) . '/resources/config/app.php';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }
        return $this->config[$key] ?? null;
    }
}