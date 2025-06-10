<?php
// Script tự động thêm dữ liệu mẫu
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DataSeeder {
    private $pdo;
    private $basePath;
    private $uploadDirs = [
        'uploads/contests',
        'uploads/contestants',
        'uploads/avatars',
        'uploads/banners'
    ];

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO(
                "mysql:host=$host;charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->pdo->exec("USE `$dbname`");
            $this->basePath = dirname(__DIR__);

            echo "✅ Kết nối database thành công!\n";
        } catch (PDOException $e) {
            die("❌ Lỗi kết nối database: " . $e->getMessage() . "\n");
        }
    }

    public function seed() {
        echo "\n🔄 Bắt đầu thêm dữ liệu mẫu...\n";

        // Tạo thư mục uploads
        $this->createUploadDirs();

        // Thêm dữ liệu mẫu
        $this->seedSettings();
        $this->seedUsers();
        $this->seedContests();
        $this->seedContestants();
        $this->seedVotes();
        $this->seedNotifications();

        echo "\n✨ Hoàn tất thêm dữ liệu mẫu!\n";
    }

    private function createUploadDirs() {
        echo "\n📁 Tạo thư mục uploads:\n";

        foreach ($this->uploadDirs as $dir) {
            $path = $this->basePath . '/' . $dir;
            if (!file_exists($path)) {
                if (mkdir($path, 0755, true)) {
                    echo "✅ Đã tạo thư mục: $dir\n";
                } else {
                    echo "❌ Không thể tạo thư mục: $dir\n";
                }
            } else {
                echo "ℹ️ Thư mục đã tồn tại: $dir\n";
            }
        }
    }

    private function seedSettings() {
        echo "\n📝 Thêm cài đặt mặc định:\n";

        $settings = [
            ['site_name', 'Hệ thống Bình chọn Online'],
            ['site_description', 'Nền tảng bình chọn trực tuyến'],
            ['maintenance_mode', '0'],
            ['allow_registration', '1'],
            ['max_votes_per_user', '3'],
            ['contest_duration_days', '30'],
            ['telegram_bot_token', ''],
            ['telegram_chat_id', ''],
            ['mail_from_address', 'noreply@example.com'],
            ['mail_from_name', 'Hệ thống Bình chọn']
        ];

        $stmt = $this->pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
        foreach ($settings as $setting) {
            try {
                $stmt->execute($setting);
                echo "✅ Đã thêm cài đặt: {$setting[0]}\n";
            } catch (PDOException $e) {
                echo "⚠️ Cài đặt {$setting[0]} đã tồn tại\n";
            }
        }
    }

    private function seedUsers() {
        echo "\n👥 Thêm người dùng mẫu:\n";

        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'username' => 'user1',
                'email' => 'user1@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user'
            ]
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        foreach ($users as $user) {
            try {
                $stmt->execute(array_values($user));
                echo "✅ Đã thêm người dùng: {$user['username']}\n";
            } catch (PDOException $e) {
                echo "⚠️ Người dùng {$user['username']} đã tồn tại\n";
            }
        }
    }

    private function seedContests() {
        echo "\n🏆 Thêm cuộc thi mẫu:\n";

        $contests = [
            [
                'title' => 'Cuộc thi Ảnh Đẹp 2024',
                'description' => 'Cuộc thi ảnh đẹp dành cho mọi lứa tuổi',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'status' => 'active'
            ],
            [
                'title' => 'Cuộc thi Tài năng Trẻ',
                'description' => 'Tìm kiếm tài năng trẻ trong lĩnh vực nghệ thuật',
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+45 days')),
                'status' => 'active'
            ]
        ];

        $stmt = $this->pdo->prepare("INSERT INTO contests (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        foreach ($contests as $contest) {
            try {
                $stmt->execute(array_values($contest));
                echo "✅ Đã thêm cuộc thi: {$contest['title']}\n";
            } catch (PDOException $e) {
                echo "⚠️ Cuộc thi {$contest['title']} đã tồn tại\n";
            }
        }
    }

    private function seedContestants() {
        echo "\n👤 Thêm thí sinh mẫu:\n";

        // Lấy danh sách cuộc thi
        $contests = $this->pdo->query("SELECT id FROM contests")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($contests as $contestId) {
            $contestants = [
                [
                    'contest_id' => $contestId,
                    'name' => 'Thí sinh 1',
                    'description' => 'Mô tả thí sinh 1',
                    'image' => 'contestant1.jpg',
                    'votes' => 0
                ],
                [
                    'contest_id' => $contestId,
                    'name' => 'Thí sinh 2',
                    'description' => 'Mô tả thí sinh 2',
                    'image' => 'contestant2.jpg',
                    'votes' => 0
                ]
            ];

            $stmt = $this->pdo->prepare("INSERT INTO contestants (contest_id, name, description, image, votes) VALUES (?, ?, ?, ?, ?)");
            foreach ($contestants as $contestant) {
                try {
                    $stmt->execute(array_values($contestant));
                    echo "✅ Đã thêm thí sinh: {$contestant['name']} (Cuộc thi ID: $contestId)\n";
                } catch (PDOException $e) {
                    echo "⚠️ Thí sinh {$contestant['name']} đã tồn tại\n";
                }
            }
        }
    }

    private function seedVotes() {
        echo "\n🗳️ Thêm lượt bình chọn mẫu:\n";

        // Lấy danh sách người dùng và thí sinh
        $users = $this->pdo->query("SELECT id FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_COLUMN);
        $contestants = $this->pdo->query("SELECT id, contest_id FROM contestants")->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("INSERT INTO votes (contest_id, contestant_id, user_id) VALUES (?, ?, ?)");

        foreach ($users as $userId) {
            foreach ($contestants as $contestant) {
                try {
                    $stmt->execute([$contestant['contest_id'], $contestant['id'], $userId]);
                    echo "✅ Đã thêm lượt bình chọn cho thí sinh ID: {$contestant['id']}\n";
                } catch (PDOException $e) {
                    echo "⚠️ Lượt bình chọn đã tồn tại\n";
                }
            }
        }
    }

    private function seedNotifications() {
        echo "\n🔔 Thêm thông báo mẫu:\n";

        $users = $this->pdo->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);

        $notifications = [
            'Chào mừng bạn đến với hệ thống!',
            'Cuộc thi mới đã được tạo',
            'Bạn có lượt bình chọn mới'
        ];

        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message, is_read) VALUES (?, ?, 0)");

        foreach ($users as $userId) {
            foreach ($notifications as $message) {
                try {
                    $stmt->execute([$userId, $message]);
                    echo "✅ Đã thêm thông báo cho người dùng ID: $userId\n";
                } catch (PDOException $e) {
                    echo "⚠️ Không thể thêm thông báo\n";
                }
            }
        }
    }
}

// Lấy thông tin từ file .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    $host = $env['DB_HOST'] ?? 'localhost';
    $dbname = $env['DB_NAME'] ?? 'contest_db';
    $username = $env['DB_USER'] ?? 'root';
    $password = $env['DB_PASS'] ?? '';
} else {
    // Thông tin mặc định
    $host = 'localhost';
    $dbname = 'contest_db';
    $username = 'root';
    $password = '';
}

// Chạy seed dữ liệu
$seeder = new DataSeeder($host, $dbname, $username, $password);
$seeder->seed();