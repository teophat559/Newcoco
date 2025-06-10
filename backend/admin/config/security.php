<?php
// Security configuration
define('SECURITY_KEY', 'your-secret-key-here');
define('SECURITY_SALT', 'your-salt-here');

// Password policy
define('MIN_PASSWORD_LENGTH', 8);
define('REQUIRE_SPECIAL_CHARS', true);
define('REQUIRE_NUMBERS', true);
define('REQUIRE_UPPERCASE', true);
define('REQUIRE_LOWERCASE', true);

// CSRF protection
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('CSRF_TOKEN_LIFETIME', 7200); // 2 hours

// XSS protection
define('XSS_CLEAN', true);
define('XSS_ALLOWED_TAGS', '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img>');

// Content Security Policy
define('CSP_ENABLED', true);
define('CSP_REPORT_ONLY', false);
define('CSP_DEFAULT_SRC', "'self'");
define('CSP_SCRIPT_SRC', "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net");
define('CSP_STYLE_SRC', "'self' 'unsafe-inline' https://cdn.jsdelivr.net");
define('CSP_IMG_SRC', "'self' data: https:");
define('CSP_FONT_SRC', "'self' https://cdn.jsdelivr.net");
define('CSP_CONNECT_SRC', "'self'");

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_WINDOW', 3600); // 1 hour
define('RATE_LIMIT_MAX_REQUESTS', 1000);

// IP blocking
define('IP_BLOCK_ENABLED', true);
define('IP_BLOCK_ATTEMPTS', 10);
define('IP_BLOCK_DURATION', 3600); // 1 hour

// Session security
define('SESSION_REGENERATE', true);
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutes
define('SESSION_FINGERPRINT', true);

// File upload security
define('SCAN_UPLOADS', true);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);