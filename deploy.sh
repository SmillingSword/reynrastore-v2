#!/bin/bash

# Reynra Store Deployment Script
# This script automates the deployment process for production

set -e

echo "🚀 Starting Reynra Store deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/reynrastore"
BACKUP_DIR="/var/backups/reynrastore"
PHP_VERSION="8.2"

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   log_error "This script should not be run as root for security reasons"
   exit 1
fi

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    log_error "Project directory $PROJECT_DIR does not exist"
    exit 1
fi

cd $PROJECT_DIR

# 1. Backup current deployment
log_info "Creating backup..."
BACKUP_NAME="reynrastore_backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR
tar -czf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    .

log_info "Backup created: $BACKUP_DIR/$BACKUP_NAME.tar.gz"

# 2. Pull latest changes
log_info "Pulling latest changes from repository..."
git fetch origin
git reset --hard origin/main

# 3. Install/Update Composer dependencies
log_info "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Install/Update NPM dependencies
log_info "Installing NPM dependencies..."
npm ci --production

# 5. Build frontend assets
log_info "Building frontend assets..."
npm run build

# 6. Copy production environment file
if [ -f ".env.production" ]; then
    log_info "Copying production environment configuration..."
    cp .env.production .env
else
    log_warning ".env.production not found, using existing .env"
fi

# 7. Generate application key if needed
if ! grep -q "APP_KEY=base64:" .env; then
    log_info "Generating application key..."
    php artisan key:generate --force
fi

# 8. Run database migrations
log_info "Running database migrations..."
php artisan migrate --force

# 9. Clear and cache configurations
log_info "Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan event:cache

# 10. Clear application cache
log_info "Clearing application cache..."
php artisan cache:clear
php artisan queue:restart

# 11. Set proper permissions
log_info "Setting file permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 12. Restart services
log_info "Restarting services..."
sudo systemctl reload nginx
sudo systemctl restart php${PHP_VERSION}-fpm

# 13. Run health check
log_info "Running health check..."
if curl -f -s http://localhost/up > /dev/null; then
    log_info "✅ Health check passed"
else
    log_error "❌ Health check failed"
    exit 1
fi

# 14. Clean up old backups (keep last 5)
log_info "Cleaning up old backups..."
cd $BACKUP_DIR
ls -t reynrastore_backup_*.tar.gz | tail -n +6 | xargs -r rm

log_info "🎉 Deployment completed successfully!"
log_info "Website is now live at: https://reynrastore.com"

# Optional: Send notification
if command -v curl &> /dev/null && [ ! -z "$SLACK_WEBHOOK_URL" ]; then
    curl -X POST -H 'Content-type: application/json' \
        --data '{"text":"🚀 Reynra Store has been successfully deployed to production!"}' \
        $SLACK_WEBHOOK_URL
fi
