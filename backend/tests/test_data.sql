-- Tạo user test
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('user1', 'user1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('user2', 'user2@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Tạo contests test
INSERT INTO contests (title, description, start_date, end_date, status, created_by) VALUES
('Test Contest 1', 'Description for test contest 1', GETDATE(), DATEADD(day, 7, GETDATE()), 'active', 1),
('Test Contest 2', 'Description for test contest 2', GETDATE(), DATEADD(day, 7, GETDATE()), 'active', 1),
('Ended Contest', 'Description for ended contest', DATEADD(day, -14, GETDATE()), DATEADD(day, -7, GETDATE()), 'ended', 1);

-- Tạo contestants test
INSERT INTO contestants (contest_id, name, description, status) VALUES
(1, 'Contestant 1', 'Description for contestant 1', 'active'),
(1, 'Contestant 2', 'Description for contestant 2', 'active'),
(2, 'Contestant 3', 'Description for contestant 3', 'active'),
(2, 'Contestant 4', 'Description for contestant 4', 'active');