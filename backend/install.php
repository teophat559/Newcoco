<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Installer {
    private $startTime;
    private $steps = [];
    private $currentStep = 0;

    public function __construct() {
        $this->startTime = microtime(true);
        $this->steps = [
            'Kiểm tra yêu cầu hệ thống',
            'Tạo cấu trúc thư mục',
            'Cấu hình database',
            'Cài đặt dữ liệu mẫu',
            'Cấu hình bảo mật',
            'Hoàn tất cài đặt'
        ];
    }

    public function run() {
        echo "🚀 Bắt đầu quá trình cài đặt...\n\n";

        foreach ($this->steps as $index => $step) {
            $this->currentStep = $index + 1;
            echo "📌 Bước {$this->currentStep}: {$step}\n";

            $method = 'step' . ($index + 1);
            if (method_exists($this, $method)) {
                $this->$method();
            }

            echo "✅ Hoàn thành bước {$this->currentStep}\n\n";
        }

        $totalTime = round(microtime(true) - $this->startTime, 2);
        echo "✨ Cài đặt hoàn tất trong {$totalTime}s!\n";
    }

    private function step1() {
        echo "  🔍 Kiểm tra yêu cầu hệ thống...\n";

        // Kiểm tra PHP version
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            die("❌ Yêu cầu PHP 8.1.0 trở lên\n");
        }
        echo "  ✅ PHP version: " . PHP_VERSION . "\n";

        // Kiểm tra các extension cần thiết
        $requiredExtensions = ['pdo', 'pdo_sqlsrv', 'gd', 'mbstring', 'json'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                die("❌ Thiếu extension: {$ext}\n");
            }
            echo "  ✅ Extension {$ext} đã được cài đặt\n";
        }

        // Kiểm tra quyền ghi
        $writablePaths = [
            'uploads',
            'uploads/contests',
            'uploads/contestants',
            'uploads/avatars',
            'uploads/banners',
            'logs'
        ];

        foreach ($writablePaths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            if (!is_writable($path)) {
                die("❌ Không có quyền ghi vào thư mục: {$path}\n");
            }
            echo "  ✅ Thư mục {$path} có quyền ghi\n";
        }
    }

    private function step2() {
        echo "  📁 Tạo cấu trúc thư mục...\n";

        $directories = [
            'uploads/contests',
            'uploads/contestants',
            'uploads/avatars',
            'uploads/banners',
            'logs',
            'cache'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
                echo "  ✅ Đã tạo thư mục: {$dir}\n";
            }
        }

        // Tạo file .htaccess để bảo vệ
        $htaccess = <<<EOT
Options -Indexes
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
EOT;
        file_put_contents('.htaccess', $htaccess);
        echo "  ✅ Đã tạo file .htaccess\n";
    }

    private function step3() {
        echo "  💾 Cấu hình database...\n";

        // Kiểm tra file .env
        if (!file_exists('.env')) {
            die("❌ Không tìm thấy file .env\n");
        }

        // Chạy script setup database
        require_once 'database/setup.php';
        echo "  ✅ Đã cấu hình database\n";
    }

    private function step4() {
        echo "  📦 Cài đặt dữ liệu mẫu...\n";

        // Chạy script seed data
        require_once 'database/seed_data.php';
        echo "  ✅ Đã cài đặt dữ liệu mẫu\n";
    }

    private function step5() {
        echo "  🔒 Cấu hình bảo mật...\n";

        // Tạo khóa bảo mật
        $env = parse_ini_file('.env');
        if (empty($env['JWT_SECRET'])) {
            $jwtSecret = bin2hex(random_bytes(32));
            file_put_contents('.env', "\nJWT_SECRET={$jwtSecret}", FILE_APPEND);
            echo "  ✅ Đã tạo JWT secret\n";
        }

        // Cấu hình session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_only_cookies', 1);
        echo "  ✅ Đã cấu hình session\n";

        // Tạo file robots.txt
        $robots = <<<EOT
User-agent: *
Disallow: /admin/
Disallow: /backend-api/
Disallow: /includes/
Disallow: /database/
Disallow: /logs/
Disallow: /uploads/
EOT;
        file_put_contents('robots.txt', $robots);
        echo "  ✅ Đã tạo file robots.txt\n";
    }

    private function step6() {
        echo "  🎉 Hoàn tất cài đặt...\n";

        // Tạo file cài đặt hoàn tất
        file_put_contents('install.lock', date('Y-m-d H:i:s'));
        echo "  ✅ Đã tạo file install.lock\n";

        // Hiển thị thông tin đăng nhập
        echo "\n📝 Thông tin đăng nhập:\n";
        echo "  👤 Admin:\n";
        echo "    - Email: admin@example.com\n";
        echo "    - Password: admin123\n";
        echo "  👤 User:\n";
        echo "    - Email: user@example.com\n";
        echo "    - Password: user123\n";
    }
}

// Chạy installer
$installer = new Installer();
$installer->run();