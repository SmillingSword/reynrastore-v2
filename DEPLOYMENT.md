# Reynra Store - Production Deployment Guide

## 🚀 Production Deployment

This guide covers the complete deployment process for Reynra Store to production environment.

## Prerequisites

### Server Requirements
- **OS**: Ubuntu 20.04 LTS or higher
- **PHP**: 8.2 or higher
- **Node.js**: 18.x or higher
- **Database**: MySQL 8.0 or PostgreSQL 13+
- **Web Server**: Nginx 1.18+
- **Memory**: Minimum 2GB RAM (4GB recommended)
- **Storage**: Minimum 20GB SSD

### Required Services
- **Redis**: For caching and sessions
- **Supervisor**: For queue management
- **Certbot**: For SSL certificates
- **Git**: For code deployment

## Installation Steps

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server redis-server supervisor git curl unzip

# Install PHP 8.2 and extensions
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-redis php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd php8.2-intl

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE reynrastore_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'reynrastore'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON reynrastore_production.* TO 'reynrastore'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Create project directory
sudo mkdir -p /var/www/reynrastore
sudo chown $USER:$USER /var/www/reynrastore

# Clone repository
cd /var/www
git clone https://github.com/your-username/reynrastore.git reynrastore
cd reynrastore

# Make deployment script executable
chmod +x deploy.sh

# Copy production environment file
cp .env.production .env

# Edit environment variables
nano .env
```

### 4. Environment Configuration

Update `.env` file with your production values:

```env
APP_NAME="Reynra Store"
APP_ENV=production
APP_KEY=base64:your-generated-app-key
APP_DEBUG=false
APP_URL=https://reynrastore.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reynrastore_production
DB_USERNAME=reynrastore
DB_PASSWORD=your_secure_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MIDTRANS_ENVIRONMENT=production
MIDTRANS_SERVER_KEY=your-production-server-key
MIDTRANS_CLIENT_KEY=your-production-client-key

DIGGIE_API_KEY=your-diggie-api-key
DIGGIE_USERNAME=your-diggie-username
```

### 5. Initial Deployment

```bash
# Run deployment script
./deploy.sh
```

### 6. Nginx Configuration

```bash
# Copy nginx configuration
sudo cp nginx.conf /etc/nginx/sites-available/reynrastore.com

# Enable site
sudo ln -s /etc/nginx/sites-available/reynrastore.com /etc/nginx/sites-enabled/

# Test nginx configuration
sudo nginx -t

# Restart nginx
sudo systemctl restart nginx
```

### 7. SSL Certificate

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d reynrastore.com -d www.reynrastore.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### 8. Queue Worker Setup

Create supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/reynrastore-worker.conf
```

```ini
[program:reynrastore-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/reynrastore/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/reynrastore/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reynrastore-worker:*
```

### 9. Cron Jobs

```bash
# Edit crontab
sudo crontab -e
```

Add these entries:

```cron
# Laravel Scheduler
* * * * * cd /var/www/reynrastore && php artisan schedule:run >> /dev/null 2>&1

# Diggie Price Updates (every 30 minutes)
*/30 * * * * cd /var/www/reynrastore && php artisan diggie:update-prices >> /dev/null 2>&1

# Diggie Order Status Check (every 5 minutes)
*/5 * * * * cd /var/www/reynrastore && php artisan diggie:check-status >> /dev/null 2>&1

# Clear expired cache (daily at 2 AM)
0 2 * * * cd /var/www/reynrastore && php artisan cache:clear >> /dev/null 2>&1

# Backup database (daily at 3 AM)
0 3 * * * mysqldump -u reynrastore -p'your_password' reynrastore_production > /var/backups/reynrastore/db_$(date +\%Y\%m\%d_\%H\%M\%S).sql
```

## Monitoring & Maintenance

### Health Checks

The application provides several health check endpoints:

- `GET /up` - Basic health check
- `GET /health` - Comprehensive health check
- `GET /health/database` - Database connectivity
- `GET /health/cache` - Cache functionality
- `GET /health/external-apis` - External API status
- `GET /health/metrics` - System metrics

### Log Monitoring

```bash
# Application logs
tail -f /var/www/reynrastore/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/reynrastore_access.log
tail -f /var/log/nginx/reynrastore_error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Performance Monitoring

```bash
# Check system resources
htop

# Check disk usage
df -h

# Check memory usage
free -h

# Check Redis status
redis-cli info

# Check MySQL status
sudo systemctl status mysql
```

## Security Considerations

### Firewall Setup

```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow ssh

# Allow HTTP and HTTPS
sudo ufw allow 'Nginx Full'

# Check status
sudo ufw status
```

### File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/reynrastore

# Set directory permissions
sudo find /var/www/reynrastore -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/reynrastore -type f -exec chmod 644 {} \;

# Set executable permissions
sudo chmod +x /var/www/reynrastore/artisan
sudo chmod +x /var/www/reynrastore/deploy.sh

# Set writable directories
sudo chmod -R 775 /var/www/reynrastore/storage
sudo chmod -R 775 /var/www/reynrastore/bootstrap/cache
```

### Database Security

```bash
# Secure MySQL
sudo mysql_secure_installation

# Create backup user with limited privileges
mysql -u root -p
```

```sql
CREATE USER 'backup'@'localhost' IDENTIFIED BY 'backup_password';
GRANT SELECT, LOCK TABLES ON reynrastore_production.* TO 'backup'@'localhost';
FLUSH PRIVILEGES;
```

## Backup Strategy

### Automated Backups

Create backup script:

```bash
sudo nano /usr/local/bin/reynrastore-backup.sh
```

```bash
#!/bin/bash

BACKUP_DIR="/var/backups/reynrastore"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="reynrastore_production"
DB_USER="backup"
DB_PASS="backup_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/app_$DATE.tar.gz -C /var/www reynrastore \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*'

# Remove backups older than 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/reynrastore-backup.sh

# Add to crontab
sudo crontab -e
```

```cron
# Daily backup at 3 AM
0 3 * * * /usr/local/bin/reynrastore-backup.sh >> /var/log/reynrastore-backup.log 2>&1
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/reynrastore/storage
   sudo chmod -R 775 /var/www/reynrastore/storage
   ```

2. **Queue Not Processing**
   ```bash
   sudo supervisorctl restart reynrastore-worker:*
   ```

3. **Cache Issues**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

4. **Database Connection Issues**
   ```bash
   # Check MySQL status
   sudo systemctl status mysql
   
   # Test connection
   mysql -u reynrastore -p reynrastore_production
   ```

### Performance Optimization

1. **Enable OPcache**
   ```bash
   sudo nano /etc/php/8.2/fpm/php.ini
   ```
   
   ```ini
   opcache.enable=1
   opcache.memory_consumption=256
   opcache.interned_strings_buffer=8
   opcache.max_accelerated_files=4000
   opcache.revalidate_freq=2
   opcache.fast_shutdown=1
   ```

2. **Optimize MySQL**
   ```bash
   sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
   ```
   
   ```ini
   innodb_buffer_pool_size = 1G
   innodb_log_file_size = 256M
   query_cache_type = 1
   query_cache_size = 64M
   ```

3. **Redis Optimization**
   ```bash
   sudo nano /etc/redis/redis.conf
   ```
   
   ```ini
   maxmemory 512mb
   maxmemory-policy allkeys-lru
   ```

## Support

For deployment issues or questions:
- Email: support@reynrastore.com
- Documentation: https://docs.reynrastore.com
- GitHub Issues: https://github.com/your-username/reynrastore/issues

---

**Last Updated**: January 2025
**Version**: 1.0.0
