<?php
if (!function_exists('env')) {
    function env($key, $default = null) {
        static $env = null;
        if ($env === null) {
            $envFile = __DIR__ . '/../.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($name, $value) = explode('=', $line, 2);
                        $env[trim($name)] = trim($value);
                    }
                }
            }
        }
        return $env[$key] ?? $default;
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '') {
        return __DIR__ . '/../resources/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('__')) {
    function __($key, $replace = [], $locale = null) {
        $locale = $locale ?: 'en';
        $langFile = __DIR__ . '/../resources/lang/' . $locale . '/messages.php';
        $messages = file_exists($langFile) ? include $langFile : [];
        $line = $messages[$key] ?? $key;
        foreach ($replace as $search => $value) {
            $line = str_replace(':' . $search, $value, $line);
        }
        return $line;
    }
}

if (!function_exists('app')) {
    function app() {
        global $app;
        return $app;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $config = null;
        if ($config === null) {
            $config = include __DIR__ . '/../config/app.php';
        }

        if (strpos($key, '.') === false) {
            return $config[$key] ?? $default;
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('route')) {
    function route($name, $parameters = []) {
        global $routes;
        if (isset($routes[$name])) {
            $route = $routes[$name];
            foreach ($parameters as $key => $value) {
                $route = str_replace('{' . $key . '}', $value, $route);
            }
            return $route;
        }
        return '#';
    }
}

if (!function_exists('auth')) {
    function auth() {
        static $auth = null;
        if ($auth === null) {
            $auth = new class {
                public function check() {
                    return isset($_SESSION['user_id']);
                }

                public function user() {
                    if ($this->check()) {
                        global $db;
                        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        return $stmt->fetch(PDO::FETCH_OBJ);
                    }
                    return null;
                }

                public function id() {
                    return $_SESSION['user_id'] ?? null;
                }

                public function guest() {
                    return !$this->check();
                }

                public function logout() {
                    unset($_SESSION['user_id']);
                    session_destroy();
                }
            };
        }
        return $auth;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        $token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}

if (!function_exists('session')) {
    function session($key = null, $value = null) {
        if ($key === null) {
            return $_SESSION;
        }
        if ($value === null) {
            return $_SESSION[$key] ?? null;
        }
        $_SESSION[$key] = $value;
        return $value;
    }
}