<?php

namespace BackendApi\Utils;

class Cache {
    private static $instance = null;
    private $cache = null;
    private $prefix;
    private $ttl;

    private function __construct() {
        $this->prefix = CACHE_PREFIX;
        $this->ttl = CACHE_DEFAULT_TTL;
        $this->initCache();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initCache() {
        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
                $this->initMemcached();
                break;
            case CACHE_TYPE_REDIS:
                $this->initRedis();
                break;
            default:
                $this->initFileCache();
        }
    }

    private function initMemcached() {
        $this->cache = new \Memcached();
        $this->cache->addServer(MEMCACHED_HOST, MEMCACHED_PORT);
    }

    private function initRedis() {
        $this->cache = new \Redis();
        $this->cache->connect(REDIS_HOST, REDIS_PORT);
        if (REDIS_PASSWORD) {
            $this->cache->auth(REDIS_PASSWORD);
        }
        if (REDIS_DATABASE) {
            $this->cache->select(REDIS_DATABASE);
        }
    }

    private function initFileCache() {
        if (!is_dir(CACHE_DIR)) {
            mkdir(CACHE_DIR, 0777, true);
        }
    }

    public function get($key) {
        $key = $this->prefix . $key;

        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
            case CACHE_TYPE_REDIS:
                return $this->cache->get($key);
            default:
                $file = CACHE_DIR . md5($key) . '.cache';
                if (file_exists($file)) {
                    $data = unserialize(file_get_contents($file));
                    if ($data['expires'] > time()) {
                        return $data['value'];
                    }
                    unlink($file);
                }
                return null;
        }
    }

    public function set($key, $value, $ttl = null) {
        $key = $this->prefix . $key;
        $ttl = $ttl ?? $this->ttl;

        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
                return $this->cache->set($key, $value, $ttl);
            case CACHE_TYPE_REDIS:
                return $this->cache->setex($key, $ttl, $value);
            default:
                $file = CACHE_DIR . md5($key) . '.cache';
                $data = [
                    'value' => $value,
                    'expires' => time() + $ttl
                ];
                return file_put_contents($file, serialize($data));
        }
    }

    public function delete($key) {
        $key = $this->prefix . $key;

        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
            case CACHE_TYPE_REDIS:
                return $this->cache->delete($key);
            default:
                $file = CACHE_DIR . md5($key) . '.cache';
                if (file_exists($file)) {
                    return unlink($file);
                }
                return true;
        }
    }

    public function clear() {
        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
                return $this->cache->flush();
            case CACHE_TYPE_REDIS:
                return $this->cache->flushDB();
            default:
                $files = glob(CACHE_DIR . '*.cache');
                foreach ($files as $file) {
                    unlink($file);
                }
                return true;
        }
    }

    public function exists($key) {
        $key = $this->prefix . $key;

        switch (CACHE_TYPE) {
            case CACHE_TYPE_MEMCACHED:
                return $this->cache->get($key) !== false;
            case CACHE_TYPE_REDIS:
                return $this->cache->exists($key);
            default:
                $file = CACHE_DIR . md5($key) . '.cache';
                if (file_exists($file)) {
                    $data = unserialize(file_get_contents($file));
                    return $data['expires'] > time();
                }
                return false;
        }
    }
}
