<?php
// Script tự động import database
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DatabaseImporter {
    private $pdo;
    private $sqlFiles = [
        'schema.sql',
        'settings.sql',
        'notifications.sql',
        'seed.sql'
    ];
    private $startTime;
    private $importStats = [];

    public function __construct($host, $dbname, $username, $password) {
        $this->startTime = microtime(true);
        try {
            $this->pdo = new PDO(
                "mysql:host=$host;charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Tạo database nếu chưa tồn tại
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("USE `$dbname`");

            echo "✅ Kết nối database thành công!\n";
        } catch (PDOException $e) {
            die("❌ Lỗi kết nối database: " . $e->getMessage() . "\n");
        }
    }

    public function import() {
        echo "\n🔄 Bắt đầu import database...\n";

        foreach ($this->sqlFiles as $file) {
            $filePath = __DIR__ . '/' . $file;
            if (file_exists($filePath)) {
                $fileStartTime = microtime(true);
                echo "\n📦 Đang import file: $file\n";

                try {
                    $sql = file_get_contents($filePath);

                    // Tách các câu lệnh SQL
                    $queries = array_filter(
                        array_map('trim',
                            explode(';', $sql)
                        )
                    );

                    $totalQueries = count($queries);
                    $currentQuery = 0;
                    $successQueries = 0;

                    foreach ($queries as $query) {
                        if (!empty($query)) {
                            $currentQuery++;
                            $this->pdo->exec($query);
                            $successQueries++;
                            echo "  ⏳ Đang thực thi câu lệnh $currentQuery/$totalQueries...\r";
                        }
                    }

                    $fileTime = round(microtime(true) - $fileStartTime, 2);
                    $this->importStats[$file] = [
                        'total' => $totalQueries,
                        'success' => $successQueries,
                        'time' => $fileTime
                    ];

                    echo "\n✅ Import file $file thành công!\n";
                    echo "  - Tổng số câu lệnh: $totalQueries\n";
                    echo "  - Thành công: $successQueries\n";
                    echo "  - Thời gian: {$fileTime}s\n";
                } catch (PDOException $e) {
                    echo "\n❌ Lỗi khi import file $file:\n";
                    echo "  - Lỗi: " . $e->getMessage() . "\n";
                    echo "  - Mã lỗi: " . $e->getCode() . "\n";
                    echo "  - Dòng lỗi: " . $e->getLine() . "\n";
                    echo "  - Câu lệnh gây lỗi: " . substr($query, 0, 100) . "...\n";
                }
            } else {
                echo "⚠️ Không tìm thấy file: $file\n";
            }
        }

        $totalTime = round(microtime(true) - $this->startTime, 2);
        echo "\n✨ Import database hoàn tất trong {$totalTime}s!\n";

        // Hiển thị thống kê import
        $this->showImportStats();

        // Kiểm tra các bảng và dữ liệu
        $this->checkTables();
        $this->validateData();
        $this->checkDataIntegrity();
    }

    private function showImportStats() {
        echo "\n📊 Thống kê import:\n";
        echo str_repeat('-', 50) . "\n";
        echo sprintf("%-20s %-10s %-10s %-10s\n", "File", "Tổng", "Thành công", "Thời gian");
        echo str_repeat('-', 50) . "\n";

        foreach ($this->importStats as $file => $stats) {
            echo sprintf("%-20s %-10d %-10d %-10.2fs\n",
                basename($file),
                $stats['total'],
                $stats['success'],
                $stats['time']
            );
        }
        echo str_repeat('-', 50) . "\n";
    }

    private function checkTables() {
        echo "\n🔍 Kiểm tra cấu trúc bảng:\n";

        $expectedTables = [
            'users' => ['id', 'username', 'email', 'password', 'role', 'created_at'],
            'contests' => ['id', 'title', 'description', 'start_date', 'end_date', 'status'],
            'contestants' => ['id', 'contest_id', 'name', 'description', 'image', 'votes'],
            'votes' => ['id', 'contest_id', 'contestant_id', 'user_id', 'created_at'],
            'settings' => ['id', 'key', 'value', 'updated_at'],
            'notifications' => ['id', 'user_id', 'message', 'is_read', 'created_at'],
            'activity_logs' => ['id', 'user_id', 'action', 'details', 'created_at']
        ];

        foreach ($expectedTables as $table => $expectedColumns) {
            try {
                $stmt = $this->pdo->query("SHOW COLUMNS FROM `$table`");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $missingColumns = array_diff($expectedColumns, $columns);
                $extraColumns = array_diff($columns, $expectedColumns);

                if (empty($missingColumns) && empty($extraColumns)) {
                    echo "✅ Bảng '$table' có cấu trúc đúng (" . count($columns) . " cột)\n";
                } else {
                    echo "⚠️ Bảng '$table' có vấn đề:\n";
                    if (!empty($missingColumns)) {
                        echo "  - Thiếu cột: " . implode(', ', $missingColumns) . "\n";
                    }
                    if (!empty($extraColumns)) {
                        echo "  - Thừa cột: " . implode(', ', $extraColumns) . "\n";
                    }
                }
            } catch (PDOException $e) {
                echo "❌ Bảng '$table' chưa được tạo\n";
            }
        }
    }

    private function validateData() {
        echo "\n🔍 Kiểm tra dữ liệu:\n";

        $tables = [
            'settings' => '📊 Cài đặt',
            'users' => '👥 Người dùng',
            'contests' => '🏆 Cuộc thi',
            'contestants' => '👤 Thí sinh',
            'votes' => '🗳️ Lượt bình chọn',
            'notifications' => '🔔 Thông báo',
            'activity_logs' => '📝 Nhật ký hoạt động'
        ];

        foreach ($tables as $table => $label) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) FROM $table");
                $count = $stmt->fetchColumn();
                echo "$label: $count bản ghi\n";
            } catch (PDOException $e) {
                echo "$label: ❌ Lỗi truy vấn\n";
            }
        }
    }

    private function checkDataIntegrity() {
        echo "\n🔍 Kiểm tra tính toàn vẹn dữ liệu:\n";

        // Kiểm tra khóa ngoại
        $checks = [
            'contestants' => [
                'contest_id' => 'contests',
                'message' => 'Thí sinh không liên kết với cuộc thi'
            ],
            'votes' => [
                'contest_id' => 'contests',
                'contestant_id' => 'contestants',
                'user_id' => 'users',
                'message' => 'Lượt bình chọn không hợp lệ'
            ],
            'notifications' => [
                'user_id' => 'users',
                'message' => 'Thông báo không liên kết với người dùng'
            ],
            'activity_logs' => [
                'user_id' => 'users',
                'message' => 'Nhật ký không liên kết với người dùng'
            ]
        ];

        foreach ($checks as $table => $relations) {
            $message = $relations['message'];
            unset($relations['message']);

            foreach ($relations as $column => $refTable) {
                $sql = "SELECT COUNT(*) FROM $table t
                        LEFT JOIN $refTable r ON t.$column = r.id
                        WHERE r.id IS NULL";
                try {
                    $stmt = $this->pdo->query($sql);
                    $invalidCount = $stmt->fetchColumn();
                    if ($invalidCount > 0) {
                        echo "⚠️ $message ($invalidCount bản ghi)\n";
                    } else {
                        echo "✅ $message: OK\n";
                    }
                } catch (PDOException $e) {
                    echo "❌ Lỗi kiểm tra $message: " . $e->getMessage() . "\n";
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

// Chạy import
$importer = new DatabaseImporter($host, $dbname, $username, $password);
$importer->import();