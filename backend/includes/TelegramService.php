<?php

class TelegramService {
    private $botToken;
    private $chatId;
    private $soundTypes = [
        'user_login' => '🔔',
        'admin_login' => '📢',
        'new_visitor' => '👋',
        'user_register' => '🎉',
        'admin_action' => '⚡',
        'error_alert' => '🚨'
    ];

    private $messages = [
        'user_login' => [
            'vi' => 'Người dùng đã đăng nhập',
            'en' => 'User logged in'
        ],
        'admin_login' => [
            'vi' => 'Quản trị viên đã đăng nhập',
            'en' => 'Admin logged in'
        ],
        'new_visitor' => [
            'vi' => 'Khách truy cập mới',
            'en' => 'New visitor'
        ],
        'user_register' => [
            'vi' => 'Người dùng mới đăng ký',
            'en' => 'New user registered'
        ],
        'admin_action' => [
            'vi' => 'Hành động quản trị',
            'en' => 'Admin action'
        ],
        'error_alert' => [
            'vi' => 'Cảnh báo lỗi',
            'en' => 'Error alert'
        ]
    ];

    public function __construct($botToken, $chatId) {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    public function send($message, $type = 'user_login', $lang = 'vi') {
        if (!isset($this->soundTypes[$type])) {
            throw new Exception("Loại thông báo không hợp lệ: $type");
        }

        $emoji = $this->soundTypes[$type];
        $defaultMessage = $this->messages[$type][$lang] ?? $this->messages[$type]['en'];
        $formattedMessage = "$emoji " . ($message ?: $defaultMessage);

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $data = [
            'chat_id' => $this->chatId,
            'text' => $formattedMessage,
            'parse_mode' => 'HTML'
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Không thể gửi thông báo Telegram");
        }

        return json_decode($result, true);
    }

    public function getSoundType($type) {
        return $this->soundTypes[$type] ?? null;
    }

    public function getMessage($type, $lang = 'vi') {
        return $this->messages[$type][$lang] ?? $this->messages[$type]['en'];
    }
}