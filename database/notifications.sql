CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') NOT NULL DEFAULT 'info',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO notifications (user_id, title, message, type, is_read)
VALUES
(1, 'Chào mừng bạn!', 'Chào mừng bạn đến với hệ thống cuộc thi trực tuyến.', 'success', 0),
(2, 'Thông báo mới', 'Bạn có một thông báo mới.', 'info', 0);

DELIMITER //
CREATE EVENT IF NOT EXISTS cleanup_old_notifications
ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM notifications
  WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND is_read = 1;
//
DELIMITER ;