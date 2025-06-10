@echo off
setlocal enabledelayedexpansion

echo ===================================
echo    Deployment Script Started
echo ===================================

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [-] Please run as administrator
    exit /b 1
)

:: Create necessary directories
echo [+] Creating directory structure...
echo    Creating backend\public\uploads...
if not exist "backend\public\uploads" mkdir "backend\public\uploads"
echo    Creating backend\storage\logs...
if not exist "backend\storage\logs" mkdir "backend\storage\logs"
echo    Creating backend\storage\cache...
if not exist "backend\storage\cache" mkdir "backend\storage\cache"

:: Move files to correct locations
echo [+] Moving files to correct locations...

:: Move API files
if exist "api" (
    echo    Moving API files...
    xcopy /E /I /Y "api\*" "backend\api\"
    rmdir /S /Q "api"
)

:: Move public files
if exist "public" (
    echo    Moving public files...
    xcopy /E /I /Y "public\*" "backend\public\"
    rmdir /S /Q "public"
)

:: Move uploads
if exist "uploads" (
    echo    Moving uploads...
    xcopy /E /I /Y "uploads\*" "backend\public\uploads\"
    rmdir /S /Q "uploads"
)

:: Move PHP files
if exist "install.php" (
    echo    Moving install.php...
    move "install.php" "backend\"
)
if exist "index.php" (
    echo    Moving index.php...
    move "index.php" "backend\"
)
if exist "update.php" (
    echo    Moving update.php...
    move "update.php" "backend\"
)

:: Move configuration files
if exist "sitemap.xml" (
    echo    Moving sitemap.xml...
    move "sitemap.xml" "backend\public\"
)
if exist "robots.txt" (
    echo    Moving robots.txt...
    move "robots.txt" "backend\public\"
)
if exist ".htaccess" (
    echo    Moving .htaccess...
    move ".htaccess" "backend\public\"
)

:: Create environment file
echo [+] Creating environment file...
if not exist "backend\config\.env" (
    echo    Copying .env.example to .env...
    copy "backend\config\.env.example" "backend\config\.env"
    echo [!] Please update backend\config\.env with your configuration
)

:: Set permissions (Windows equivalent)
echo [+] Setting permissions...
echo    Setting permissions for backend\public...
icacls "backend\public" /grant "IUSR:(OI)(CI)F" /T
echo    Setting permissions for backend\public\uploads...
icacls "backend\public\uploads" /grant "IUSR:(OI)(CI)F" /T
echo    Setting permissions for backend\storage\logs...
icacls "backend\storage\logs" /grant "IUSR:(OI)(CI)F" /T
echo    Setting permissions for backend\storage\cache...
icacls "backend\storage\cache" /grant "IUSR:(OI)(CI)F" /T

:: Install dependencies
echo [+] Installing dependencies...

:: Install PHP dependencies
where composer >nul 2>&1
if %errorLevel% equ 0 (
    echo    Installing PHP dependencies...
    cd backend
    composer install --no-dev --optimize-autoloader
    cd ..
) else (
    echo [-] Composer not found. Please install Composer first.
    exit /b 1
)

:: Install Node.js dependencies
where npm >nul 2>&1
if %errorLevel% equ 0 (
    echo    Installing Node.js dependencies...
    cd frontend
    call npm install
    call npm run build
    cd ..
) else (
    echo [-] npm not found. Please install Node.js first.
    exit /b 1
)

:: Configure IIS (if installed)
echo [+] Configuring IIS...
where iisreset >nul 2>&1
if %errorLevel% equ 0 (
    echo    Creating application pool...
    %windir%\system32\inetsrv\appcmd add apppool /name:"YourAppPool" /managedRuntimeVersion:"v4.0" /managedPipelineMode:"Integrated"

    echo    Creating website...
    %windir%\system32\inetsrv\appcmd add site /name:"YourSite" /physicalPath:"%cd%\backend\public" /bindings:"http/*:80:your-domain.com"

    echo    Setting application pool...
    %windir%\system32\inetsrv\appcmd set site "YourSite" /applicationDefaults.applicationPool:"YourAppPool"

    echo    Enabling required features...
    %windir%\system32\inetsrv\appcmd set config "YourSite" /section:system.webServer/rewrite /enabled:"True"
    %windir%\system32\inetsrv\appcmd set config "YourSite" /section:system.webServer/security/requestFiltering /allowDoubleEscaping:"True"

    echo    Restarting IIS...
    iisreset
)

:: Configure SSL
echo [+] Configuring SSL...
where certbot >nul 2>&1
if %errorLevel% equ 0 (
    echo    Installing SSL certificate...
    certbot --nginx -d your-domain.com
) else (
    echo [!] Certbot not found. Please install Certbot for SSL configuration.
)

:: Configure database
echo [+] Configuring database...
where mysql >nul 2>&1
if %errorLevel% equ 0 (
    echo    Importing database schema...
    mysql -u root -p < backend\database\schema.sql
) else (
    echo [!] MySQL not found. Please import database schema manually.
)

:: Configure Redis
echo [+] Configuring Redis...
where redis-cli >nul 2>&1
if %errorLevel% equ 0 (
    echo    Testing Redis connection...
    redis-cli ping
) else (
    echo [!] Redis not found. Please install Redis for caching.
)

:: Configure scheduled tasks
echo [+] Configuring scheduled tasks...
echo    Creating maintenance task...
schtasks /create /tn "YourAppMaintenance" /tr "php %cd%\backend\artisan schedule:run" /sc daily /st 00:00

:: Final checks
echo [+] Performing final checks...

:: Check PHP version
echo    Checking PHP version...
php -v

:: Check MySQL version
echo    Checking MySQL version...
mysql --version

:: Check Redis version
echo    Checking Redis version...
redis-cli --version

:: Check Node.js version
echo    Checking Node.js version...
node -v

:: Check npm version
echo    Checking npm version...
npm -v

echo ===================================
echo    Deployment Completed
echo ===================================

echo [+] Deployment completed successfully!
echo [!] Please review the following:
echo 1. Update backend\config\.env with your configuration
echo 2. Configure your domain in IIS
echo 3. Set up SSL certificate
echo 4. Configure database credentials
echo 5. Test all functionality

echo ===================================
echo    Press any key to exit...
echo ===================================
pause >nul

exit /b 0