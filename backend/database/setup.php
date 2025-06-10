<?php
// Script tự động thiết lập database
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DatabaseSetup {
    private $pdo;
    private $startTime;
    private $setupStats = [];

    public function __construct($host, $username, $password) {
        $this->startTime = microtime(true);
        try {
            $this->pdo = new PDO(
                "sqlsrv:Server=$host",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Kết nối SQL Server thành công!\n";
        } catch (PDOException $e) {
            die("❌ Lỗi kết nối SQL Server: " . $e->getMessage() . "\n");
        }
    }

    public function setup() {
        echo "\n🔄 Bắt đầu thiết lập database...\n";

        // Đọc file schema
        $schemaFile = __DIR__ . '/schema.sql';
        if (!file_exists($schemaFile)) {
            die("❌ Không tìm thấy file schema.sql\n");
        }

        $sql = file_get_contents($schemaFile);

        // Tách các batch SQL
        $batches = array_filter(
            array_map('trim',
                explode('GO', $sql)
            )
        );

        $totalBatches = count($batches);
        $currentBatch = 0;
        $successBatches = 0;

        foreach ($batches as $batch) {
            if (!empty($batch)) {
                $currentBatch++;
                try {
                    $this->pdo->exec($batch);
                    $successBatches++;
                    echo "  ⏳ Đang thực thi batch $currentBatch/$totalBatches...\r";
                } catch (PDOException $e) {
                    echo "\n❌ Lỗi khi thực thi batch $currentBatch:\n";
                    echo "  - Lỗi: " . $e->getMessage() . "\n";
                    echo "  - Mã lỗi: " . $e->getCode() . "\n";
                    echo "  - Batch gây lỗi: " . substr($batch, 0, 100) . "...\n";
                }
            }
        }

        $setupTime = round(microtime(true) - $this->startTime, 2);
        echo "\n\n✨ Thiết lập database hoàn tất trong {$setupTime}s!\n";

        // Kiểm tra kết quả
        $this->verifySetup();
    }

    private function verifySetup() {
        echo "\n🔍 Kiểm tra kết quả thiết lập:\n";

        // Kiểm tra database
        $this->checkDatabase();

        // Kiểm tra các bảng
        $this->checkTables();

        // Kiểm tra các ràng buộc
        $this->checkConstraints();

        // Kiểm tra các index
        $this->checkIndexes();
    }

    private function checkDatabase() {
        echo "\n📊 Kiểm tra database:\n";

        try {
            $stmt = $this->pdo->query("SELECT name FROM sys.databases WHERE name = 'contest_db'");
            if ($stmt->fetch()) {
                echo "✅ Database 'contest_db' đã được tạo\n";
            } else {
                echo "❌ Database 'contest_db' chưa được tạo\n";
            }
        } catch (PDOException $e) {
            echo "❌ Lỗi khi kiểm tra database: " . $e->getMessage() . "\n";
        }
    }

    private function checkTables() {
        echo "\n📋 Kiểm tra các bảng:\n";

        $expectedTables = [
            'users',
            'contests',
            'contestants',
            'votes',
            'settings',
            'notifications',
            'activity_logs'
        ];

        try {
            $this->pdo->exec("USE contest_db");
            $stmt = $this->pdo->query("SELECT name FROM sys.tables");
            $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($expectedTables as $table) {
                if (in_array($table, $existingTables)) {
                    // Kiểm tra số lượng cột
                    $stmt = $this->pdo->query("SELECT COUNT(*) FROM sys.columns WHERE object_id = OBJECT_ID('$table')");
                    $columnCount = $stmt->fetchColumn();
                    echo "✅ Bảng '$table' đã được tạo ($columnCount cột)\n";
                } else {
                    echo "❌ Bảng '$table' chưa được tạo\n";
                }
            }
        } catch (PDOException $e) {
            echo "❌ Lỗi khi kiểm tra bảng: " . $e->getMessage() . "\n";
        }
    }

    private function checkConstraints() {
        echo "\n🔒 Kiểm tra các ràng buộc:\n";

        $expectedConstraints = [
            'FK_Contests_Users',
            'FK_Contestants_Contests',
            'FK_Contestants_Users',
            'FK_Votes_Contests',
            'FK_Votes_Contestants',
            'FK_Votes_Users',
            'FK_Notifications_Users',
            'FK_ActivityLogs_Users'
        ];

        try {
            $stmt = $this->pdo->query("
                SELECT name
                FROM sys.foreign_keys
                WHERE parent_object_id IN (SELECT object_id FROM sys.tables)
            ");
            $existingConstraints = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($expectedConstraints as $constraint) {
                if (in_array($constraint, $existingConstraints)) {
                    echo "✅ Ràng buộc '$constraint' đã được tạo\n";
                } else {
                    echo "❌ Ràng buộc '$constraint' chưa được tạo\n";
                }
            }
        } catch (PDOException $e) {
            echo "❌ Lỗi khi kiểm tra ràng buộc: " . $e->getMessage() . "\n";
        }
    }

    private function checkIndexes() {
        echo "\n📈 Kiểm tra các index:\n";

        $expectedIndexes = [
            'IX_Users_Username',
            'IX_Users_Email',
            'IX_Contests_Status',
            'IX_Contestants_ContestId',
            'IX_Votes_ContestId',
            'IX_Votes_ContestantId',
            'IX_Votes_UserId',
            'IX_Notifications_UserId',
            'IX_ActivityLogs_UserId'
        ];

        try {
            $stmt = $this->pdo->query("
                SELECT i.name
                FROM sys.indexes i
                JOIN sys.tables t ON i.object_id = t.object_id
                WHERE i.is_primary_key = 0
            ");
            $existingIndexes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($expectedIndexes as $index) {
                if (in_array($index, $existingIndexes)) {
                    echo "✅ Index '$index' đã được tạo\n";
                } else {
                    echo "❌ Index '$index' chưa được tạo\n";
                }
            }
        } catch (PDOException $e) {
            echo "❌ Lỗi khi kiểm tra index: " . $e->getMessage() . "\n";
        }
    }
}

// Lấy thông tin từ file .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    $host = $env['DB_HOST'] ?? 'localhost';
    $username = $env['DB_USER'] ?? 'sa';
    $password = $env['DB_PASS'] ?? '';
} else {
    // Thông tin mặc định
    $host = 'localhost';
    $username = 'sa';
    $password = '';
}

// Chạy setup
$setup = new DatabaseSetup($host, $username, $password);
$setup->setup();