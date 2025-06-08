CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text,
  `data_type` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_public` BOOLEAN DEFAULT FALSE,
  `default_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm các cài đặt mặc định cho Telegram
INSERT INTO `settings` (`key`, `value`, `data_type`, `description`, `category`, `is_public`, `default_value`) VALUES
('telegram_bot_token', '', 'string', 'Token của bot Telegram', 'telegram', FALSE, ''),
('telegram_chat_id', '', 'string', 'ID của chat/group để gửi thông báo', 'telegram', FALSE, ''),
('telegram_enabled', '0', 'boolean', 'Bật/tắt thông báo Telegram', 'telegram', FALSE, '0'),
('telegram_notify_login', '0', 'boolean', 'Thông báo đăng nhập thành công', 'telegram', FALSE, '0'),
('telegram_notify_failed_login', '0', 'boolean', 'Thông báo đăng nhập thất bại', 'telegram', FALSE, '0'),
('telegram_notify_password_change', '0', 'boolean', 'Thông báo thay đổi mật khẩu', 'telegram', FALSE, '0'),
('telegram_notify_role_change', '0', 'boolean', 'Thông báo thay đổi vai trò', 'telegram', FALSE, '0'),
('telegram_notify_user_approval', '0', 'boolean', 'Thông báo duyệt/từ chối người dùng', 'telegram', FALSE, '0');