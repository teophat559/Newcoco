<?php

namespace BackendApi\Core;

class Cache {
    protected static $instance = null;
    protected $path;
    protected $lifetime;

    public function __construct() {
        $config = Config::get('services.cache');
        $this->path = $config['path'];
        $this->lifetime = $config['lifetime'] * 60;

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key, $default = null) {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function set($key, $value, $ttl = null) {
        $file = $this->getFilePath($key);
        $ttl = $ttl ?? $this->lifetime;

        $data = [
            'value' => $value,
            'expires' => $ttl === 0 ? 0 : time() + $ttl
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function delete($key) {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    public function clear() {
        $files = glob($this->path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    protected function getFilePath($key) {
        return $this->path . '/' . md5($key);
    }
}