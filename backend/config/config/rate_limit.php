<?php
class RateLimit {
    private $redis;
    private $maxRequests = 100; // Số request tối đa
    private $timeWindow = 3600; // Thời gian (giây)

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function check($ip) {
        $key = "rate_limit:$ip";
        $current = $this->redis->get($key);

        if (!$current) {
            $this->redis->setex($key, $this->timeWindow, 1);
            return true;
        }

        if ($current >= $this->maxRequests) {
            return false;
        }

        $this->redis->incr($key);
        return true;
    }
}

// Kiểm tra rate limit
$rateLimit = new RateLimit();
$ip = $_SERVER['REMOTE_ADDR'];

if (!$rateLimit->check($ip)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests']);
    exit();
}