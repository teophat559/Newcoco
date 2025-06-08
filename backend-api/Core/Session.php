<?php

namespace BackendApi\Core;

class Session {
    protected static $instance = null;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            $config = Config::get('services.session');

            session_set_cookie_params([
                'lifetime' => $config['lifetime'] * 60,
                'path' => $config['path'],
                'domain' => $config['domain'],
                'secure' => $config['secure'],
                'httponly' => $config['http_only'],
                'samesite' => $config['same_site']
            ]);

            session_start();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        unset($_SESSION[$key]);
    }

    public function clear() {
        session_unset();
    }

    public function destroy() {
        session_destroy();
    }

    public function regenerate() {
        session_regenerate_id(true);
    }
}