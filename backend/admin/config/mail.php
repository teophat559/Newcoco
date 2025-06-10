<?php
// Mail configuration
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'your-email@gmail.com');
define('MAIL_FROM_NAME', 'Newcoco Admin');

// Mail templates
define('MAIL_TEMPLATES', [
    'welcome' => [
        'subject' => 'Chào mừng đến với Newcoco',
        'template' => 'welcome.php'
    ],
    'reset_password' => [
        'subject' => 'Đặt lại mật khẩu',
        'template' => 'reset-password.php'
    ],
    'notification' => [
        'subject' => 'Thông báo mới',
        'template' => 'notification.php'
    ],
    'contest_reminder' => [
        'subject' => 'Nhắc nhở cuộc thi',
        'template' => 'contest-reminder.php'
    ]
]);

// Mail queue settings
define('MAIL_QUEUE_ENABLED', true);
define('MAIL_QUEUE_DIR', __DIR__ . '/../queue/mail/');
define('MAIL_QUEUE_INTERVAL', 300); // 5 minutes
define('MAIL_QUEUE_MAX_ATTEMPTS', 3);

// Mail logging
define('MAIL_LOG_ENABLED', true);
define('MAIL_LOG_DIR', __DIR__ . '/../logs/mail/');
define('MAIL_LOG_LEVEL', 'info'); // debug, info, warning, error