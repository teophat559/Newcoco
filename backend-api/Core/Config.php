<?php

namespace BackendApi\Core;

class Config {
    protected static $config = [];

    public static function load() {
        self::$config = require dirname(__DIR__) . '/resources/config/app.php';
    }

    public static function get($key, $default = null) {
        if (empty(self::$config)) {
            self::load();
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function set($key, $value) {
        if (empty(self::$config)) {
            self::load();
        }

        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment])) {
                    $config[$segment] = [];
                }
                $config = &$config[$segment];
            }
        }
    }
}