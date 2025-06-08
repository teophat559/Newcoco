-- Seed Data for Online Contest System (MySQL/MariaDB)

-- Insert default system settings
INSERT INTO settings (key, value, data_type, description, category, is_public, default_value) VALUES
('site_name', 'Online Contest System', 'string', 'Tên website', 'general', TRUE, 'Online Contest System'),
('site_description', 'Hệ thống tổ chức cuộc thi trực tuyến', 'string', 'Mô tả website', 'general', TRUE, 'Hệ thống tổ chức cuộc thi trực tuyến'),
('site_email', 'admin@contest.com', 'string', 'Email liên hệ', 'general', TRUE, 'admin@contest.com'),
('site_phone', '0123456789', 'string', 'Số điện thoại liên hệ', 'general', TRUE, '0123456789'),
('maintenance_mode', 'false', 'boolean', 'Chế độ bảo trì', 'general', FALSE, 'false'),
('registration_open', 'true', 'boolean', 'Cho phép đăng ký người dùng mới', 'general', TRUE, 'true'),
('max_file_size', '5242880', 'number', 'Kích thước file tối đa (bytes)', 'upload', FALSE, '5242880'),
('allowed_image_types', '["jpg","jpeg","png","gif","webp"]', 'string', 'Định dạng ảnh được phép', 'upload', FALSE, '["jpg","jpeg","png","gif","webp"]'),
('voting_rate_limit', '10', 'number', 'Giới hạn vote mỗi ngày', 'voting', TRUE, '10'),
('contest_approval_required', 'true', 'boolean', 'Yêu cầu duyệt cuộc thi', 'contest', FALSE, 'true'),
('smtp_host', '', 'string', 'SMTP Host', 'email', FALSE, ''),
('smtp_port', '587', 'number', 'SMTP Port', 'email', FALSE, '587'),
('smtp_user', '', 'string', 'SMTP User', 'email', FALSE, ''),
('smtp_password', '', 'string', 'SMTP Password', 'email', FALSE, ''),
('social_facebook', '', 'string', 'Facebook URL', 'social', TRUE, ''),
('social_youtube', '', 'string', 'YouTube URL', 'social', TRUE, ''),
('social_instagram', '', 'string', 'Instagram URL', 'social', TRUE, ''),
('api_rate_limit_per_minute', '100', 'number', 'Giới hạn API mỗi phút', 'api', FALSE, '100'),
('api_rate_limit_per_hour', '1000', 'number', 'Giới hạn API mỗi giờ', 'api', FALSE, '1000');

-- Insert default admin user (admin123)
INSERT INTO users (name, email, password_hash, role, is_active, email_verified) VALUES
('Administrator', 'admin@contest.com', '$2b$10$DxPh5DLtOQFEMJGon/X10e0uRMNxybt48yV6pOa1q3G8a7n.qcA.q', 'admin', TRUE, TRUE);

-- Insert sample regular users
INSERT INTO users (name, email, password_hash, role, is_active, email_verified) VALUES
('Nguyễn Văn An', 'user1@example.com', '$2b$10$DxPh5DLtOQFEMJGon/X10e0uRMNxybt48yV6pOa1q3G8a7n.qcA.q', 'user', TRUE, TRUE),
('Trần Thị Bình', 'user2@example.com', '$2b$10$DxPh5DLtOQFEMJGon/X10e0uRMNxybt48yV6pOa1q3G8a7n.qcA.q', 'user', TRUE, TRUE),
('Lê Hoàng Cường', 'user3@example.com', '$2b$10$DxPh5DLtOQFEMJGon/X10e0uRMNxybt48yV6pOa1q3G8a7n.qcA.q', 'user', TRUE, TRUE),
('Phạm Thị Dung', 'user4@example.com', '$2b$10$DxPh5DLtOQFEMJGon/X10e0uRMNxybt48yV6pOa1q3G8a7n.qcA.q', 'user', TRUE, TRUE);

-- Insert sample contests
INSERT INTO contests (title, description, rules, category, status, start_date, end_date, voting_start_date, voting_end_date, created_by) VALUES
(
    'Cuộc Thi Sắc Đẹp 2025',
    'Cuộc thi tìm kiếm gương mặt đại diện cho thế hệ trẻ năm 2024.',
    'Thể lệ: từ 18-30 tuổi, 1 lần đăng ký, ảnh rõ, trang phục lịch sự.',
    'beauty',
    'active',
    NOW() - INTERVAL 10 DAY,
    NOW() + INTERVAL 20 DAY,
    NOW() - INTERVAL 5 DAY,
    NOW() + INTERVAL 15 DAY,
    1
),
(
    'Cuộc Thi Tài Năng Âm Nhạc',
    'Tìm kiếm những tài năng âm nhạc trẻ.',
    'Thể lệ: 16-25 tuổi, video 2-5 phút, rõ nét, nội dung phù hợp.',
    'talent',
    'voting',
    NOW() - INTERVAL 15 DAY,
    NOW() + INTERVAL 10 DAY,
    NOW() - INTERVAL 3 DAY,
    NOW() + INTERVAL 7 DAY,
    1
),
(
    'Cuộc Thi Ảnh Đẹp Thiên Nhiên',
    'Dành cho những người yêu thiên nhiên và nhiếp ảnh.',
    'Ảnh thiên nhiên VN, chụp trong 2 năm gần đây, không chỉnh sửa quá mức.',
    'photo',
    'upcoming',
    NOW() + INTERVAL 5 DAY,
    NOW() + INTERVAL 35 DAY,
    NOW() + INTERVAL 25 DAY,
    NOW() + INTERVAL 40 DAY,
    1
);

-- Insert sample contestants
-- Giả định ID users và contests là: admin:1, user1:2, user2:3, user3:4, user4:5, contests: 1,2,3

INSERT INTO contestants (user_id, contest_id, name, description, additional_info, status) VALUES
(2, 1, 'Nguyễn Văn An', 'Sinh viên năm cuối ngành Kinh tế.', '{"age":22,"location":"Hà Nội"}', 'approved'),
(3, 1, 'Trần Thị Bình', 'Yêu thích nghệ thuật và thời trang.', '{"age":24,"location":"TP.HCM"}', 'approved'),
(4, 2, 'Lê Hoàng Cường', 'Chơi guitar từ năm 12 tuổi.', '{"age":20,"location":"Đà Nẵng"}', 'approved'),
(5, 2, 'Phạm Thị Dung', 'Thích hát dân ca và ballad.', '{"age":19,"location":"Cần Thơ"}', 'pending');

-- Insert sample votes
INSERT INTO votes (user_id, contestant_id, contest_id, vote_type) VALUES
(4, 1, 1, 'heart'),
(5, 1, 1, 'like'),
(4, 2, 1, 'heart'),
(2, 3, 2, 'star'),
(3, 3, 2, 'heart');

-- Sample API key (SHA256 hash of 'cms_test123456789abcdef')
INSERT INTO api_keys (name, key_hash, permissions, created_by) VALUES
(
    'Test API Key',
    SHA2('cms_test123456789abcdef', 256),
    '["contests:read","contestants:read","votes:read"]',
    1
);
