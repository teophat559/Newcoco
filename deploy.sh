#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Function to print status
print_status() {
    echo -e "${GREEN}[✓] $1${NC}"
}

print_error() {
    echo -e "${RED}[✗] $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}[!] $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run as root"
    exit 1
fi

# Create necessary directories
print_status "Creating directory structure..."
mkdir -p backend/public/uploads
mkdir -p backend/storage/logs
mkdir -p backend/storage/cache

# Move files to correct locations
print_status "Moving files to correct locations..."

# Move API files
if [ -d "api" ]; then
    mv api/* backend/api/ 2>/dev/null
    rm -rf api
fi

# Move public files
if [ -d "public" ]; then
    mv public/* backend/public/ 2>/dev/null
    rm -rf public
fi

# Move uploads
if [ -d "uploads" ]; then
    mv uploads/* backend/public/uploads/ 2>/dev/null
    rm -rf uploads
fi

# Move PHP files
mv install.php backend/ 2>/dev/null
mv index.php backend/ 2>/dev/null
mv update.php backend/ 2>/dev/null

# Move configuration files
mv sitemap.xml backend/public/ 2>/dev/null
mv robots.txt backend/public/ 2>/dev/null
mv .htaccess backend/public/ 2>/dev/null

# Create environment file
print_status "Creating environment file..."
if [ ! -f "backend/config/.env" ]; then
    cp backend/config/.env.example backend/config/.env
    print_warning "Please update backend/config/.env with your configuration"
fi

# Set permissions
print_status "Setting permissions..."
chmod -R 755 backend/public
chmod -R 777 backend/public/uploads
chmod -R 777 backend/storage/logs
chmod -R 777 backend/storage/cache

# Install dependencies
print_status "Installing dependencies..."

# Install PHP dependencies
if command -v composer &> /dev/null; then
    cd backend
    composer install --no-dev --optimize-autoloader
    cd ..
else
    print_error "Composer not found. Please install Composer first."
    exit 1
fi

# Install Node.js dependencies
if command -v npm &> /dev/null; then
    cd frontend
    npm install
    npm run build
    cd ..
else
    print_error "npm not found. Please install Node.js first."
    exit 1
fi

# Configure web server
print_status "Configuring web server..."

# Check if Apache is installed
if command -v apache2 &> /dev/null || command -v httpd &> /dev/null; then
    # Enable required Apache modules
    a2enmod rewrite
    a2enmod headers

    # Restart Apache
    if command -v systemctl &> /dev/null; then
        systemctl restart apache2
    else
        service apache2 restart
    fi
fi

# Check if Nginx is installed
if command -v nginx &> /dev/null; then
    # Copy Nginx configuration
    cp backend/public/nginx.conf /etc/nginx/sites-available/your-domain.com
    ln -s /etc/nginx/sites-available/your-domain.com /etc/nginx/sites-enabled/

    # Test Nginx configuration
    nginx -t

    # Restart Nginx
    if command -v systemctl &> /dev/null; then
        systemctl restart nginx
    else
        service nginx restart
    fi
fi

# Configure SSL
print_status "Configuring SSL..."
if command -v certbot &> /dev/null; then
    certbot --nginx -d your-domain.com
else
    print_warning "Certbot not found. Please install Certbot for SSL configuration."
fi

# Configure database
print_status "Configuring database..."
if command -v mysql &> /dev/null; then
    # Import database schema
    mysql -u root -p < backend/database/schema.sql
else
    print_warning "MySQL not found. Please import database schema manually."
fi

# Configure Redis
print_status "Configuring Redis..."
if command -v redis-cli &> /dev/null; then
    # Test Redis connection
    redis-cli ping
else
    print_warning "Redis not found. Please install Redis for caching."
fi

# Configure cron jobs
print_status "Configuring cron jobs..."
(crontab -l 2>/dev/null; echo "0 0 * * * php /path/to/your/backend/artisan schedule:run") | crontab -

# Final checks
print_status "Performing final checks..."

# Check PHP version
php -v

# Check MySQL version
mysql --version

# Check Redis version
redis-cli --version

# Check Node.js version
node -v

# Check npm version
npm -v

print_status "Deployment completed successfully!"
print_warning "Please review the following:"
echo "1. Update backend/config/.env with your configuration"
echo "2. Configure your domain in web server configuration"
echo "3. Set up SSL certificate"
echo "4. Configure database credentials"
echo "5. Test all functionality"

exit 0