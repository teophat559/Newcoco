<?php
// Script tự động cập nhật website
error_reporting(E_ALL);
ini_set('display_errors', 1);

class WebsiteUpdater {
    private $baseDir;
    private $requiredDirs = ['uploads', 'logs', 'vendor'];
    private $requiredFiles = [
        '.htaccess',
        'config.php',
        'robots.txt',
        'sitemap.xml'
    ];

    public function __construct() {
        $this->baseDir = __DIR__;
    }

    public function update() {
        echo "Bắt đầu cập nhật website...\n";

        // Tạo thư mục cần thiết
        $this->createDirectories();

        // Cập nhật các file cấu hình
        $this->updateConfigFiles();

        // Cài đặt dependencies
        $this->installDependencies();

        // Cập nhật phân quyền
        $this->updatePermissions();

        echo "Cập nhật hoàn tất!\n";
    }

    private function createDirectories() {
        echo "Tạo thư mục cần thiết...\n";
        foreach ($this->requiredDirs as $dir) {
            $path = $this->baseDir . '/' . $dir;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
                echo "Đã tạo thư mục: $dir\n";
            }
        }
    }

    private function updateConfigFiles() {
        echo "Cập nhật file cấu hình...\n";

        // Cập nhật .htaccess
        $htaccess = <<<EOT
# Enable rewrite engine
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove trailing slashes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)/$ /$1 [L,R=301]

# Handle front controller
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    Header set Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:; img-src 'self' data: https:; font-src 'self' data: https:;"
</IfModule>

# Disable directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "\.(env|config|json|lock|sql|log)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Set caching headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType text/html "access plus 1 day"
    ExpiresByType application/xhtml+xml "access plus 1 day"
</IfModule>

# PHP settings
<IfModule mod_php8.c>
    php_value upload_max_filesize 10M
    php_value post_max_size 10M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
    php_value display_errors Off
    php_value log_errors On
    php_value error_log logs/php_errors.log
</IfModule>
EOT;
        file_put_contents($this->baseDir . '/.htaccess', $htaccess);
        echo "Đã cập nhật .htaccess\n";

        // Cập nhật robots.txt
        $robots = <<<EOT
User-agent: *
Allow: /
Allow: /contests.php
Allow: /contest-details.php
Allow: /register.php
Allow: /login.php
Allow: /profile.php

Disallow: /admin/
Disallow: /includes/
Disallow: /logs/
Disallow: /vendor/
Disallow: /uploads/
Disallow: /*.php$
Disallow: /*.json$
Disallow: /*.env$
Disallow: /*.log$
Disallow: /*.sql$

Sitemap: https://your-domain.com/sitemap.xml
EOT;
        file_put_contents($this->baseDir . '/robots.txt', $robots);
        echo "Đã cập nhật robots.txt\n";

        // Cập nhật sitemap.xml
        $sitemap = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://your-domain.com/</loc>
        <lastmod>2024-03-20</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://your-domain.com/contests.php</loc>
        <lastmod>2024-03-20</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>https://your-domain.com/register.php</loc>
        <lastmod>2024-03-20</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://your-domain.com/login.php</loc>
        <lastmod>2024-03-20</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
</urlset>
EOT;
        file_put_contents($this->baseDir . '/sitemap.xml', $sitemap);
        echo "Đã cập nhật sitemap.xml\n";
    }

    private function installDependencies() {
        echo "Cài đặt dependencies...\n";
        if (file_exists($this->baseDir . '/composer.json')) {
            exec('composer install --no-dev --optimize-autoloader', $output, $returnVar);
            if ($returnVar === 0) {
                echo "Đã cài đặt dependencies thành công\n";
            } else {
                echo "Lỗi khi cài đặt dependencies\n";
            }
        }
    }

    private function updatePermissions() {
        echo "Cập nhật phân quyền...\n";

        // Cập nhật quyền cho thư mục
        foreach ($this->requiredDirs as $dir) {
            $path = $this->baseDir . '/' . $dir;
            chmod($path, 0777);
            echo "Đã cập nhật quyền thư mục: $dir\n";
        }

        // Cập nhật quyền cho file
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDir)
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                chmod($file->getPathname(), 0644);
            }
        }
        echo "Đã cập nhật quyền cho tất cả file\n";
    }
}

// Chạy cập nhật
$updater = new WebsiteUpdater();
$updater->update();