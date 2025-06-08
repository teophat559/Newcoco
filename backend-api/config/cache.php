<?php

// Cache configuration
define('CACHE_ENABLED', true);
define('CACHE_DIR', __DIR__ . '/../public/cache/');
define('CACHE_PREFIX', 'contest_');
define('CACHE_DEFAULT_TTL', 3600); // 1 hour

// Cache types
define('CACHE_TYPE_FILE', 'file');
define('CACHE_TYPE_MEMCACHED', 'memcached');
define('CACHE_TYPE_REDIS', 'redis');

// Current cache type
define('CACHE_TYPE', CACHE_TYPE_FILE);

// Memcached configuration
define('MEMCACHED_HOST', 'localhost');
define('MEMCACHED_PORT', 11211);
define('MEMCACHED_WEIGHT', 100);
define('MEMCACHED_PERSISTENT', true);
define('MEMCACHED_TIMEOUT', 1);
define('MEMCACHED_RETRY_INTERVAL', 15);
define('MEMCACHED_STATUS_TIMEOUT', 1);

// Redis configuration
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', null);
define('REDIS_DATABASE', 0);
define('REDIS_TIMEOUT', 0);
define('REDIS_READ_TIMEOUT', -1);
define('REDIS_PERSISTENT', false);
define('REDIS_PREFIX', 'contest:');