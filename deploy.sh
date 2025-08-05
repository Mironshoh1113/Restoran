#!/bin/bash

# Laravel Auto Deployment Script
# This script will be executed on your server when Git webhook is triggered

# Set the project directory
PROJECT_DIR="/var/www/restaurant-order-system"
BACKUP_DIR="/var/www/backups"
LOG_FILE="/var/log/deploy.log"

# Create log entry
echo "$(date): Starting deployment..." >> $LOG_FILE

# Navigate to project directory
cd $PROJECT_DIR

# Create backup before deployment
if [ ! -d "$BACKUP_DIR" ]; then
    mkdir -p $BACKUP_DIR
fi

# Backup current version
echo "$(date): Creating backup..." >> $LOG_FILE
tar -czf "$BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz" --exclude='.git' --exclude='node_modules' --exclude='vendor' .

# Pull latest changes from Git
echo "$(date): Pulling latest changes from Git..." >> $LOG_FILE
git pull origin main >> $LOG_FILE 2>&1

if [ $? -eq 0 ]; then
    echo "$(date): Git pull successful" >> $LOG_FILE
    
    # Install/update Composer dependencies
    echo "$(date): Installing Composer dependencies..." >> $LOG_FILE
    composer install --no-dev --optimize-autoloader >> $LOG_FILE 2>&1
    
    # Install/update NPM dependencies
    echo "$(date): Installing NPM dependencies..." >> $LOG_FILE
    npm install >> $LOG_FILE 2>&1
    
    # Build assets
    echo "$(date): Building assets..." >> $LOG_FILE
    npm run build >> $LOG_FILE 2>&1
    
    # Run database migrations
    echo "$(date): Running database migrations..." >> $LOG_FILE
    php artisan migrate --force >> $LOG_FILE 2>&1
    
    # Clear and cache config
    echo "$(date): Clearing and caching configuration..." >> $LOG_FILE
    php artisan config:clear >> $LOG_FILE 2>&1
    php artisan config:cache >> $LOG_FILE 2>&1
    php artisan route:clear >> $LOG_FILE 2>&1
    php artisan route:cache >> $LOG_FILE 2>&1
    php artisan view:clear >> $LOG_FILE 2>&1
    php artisan view:cache >> $LOG_FILE 2>&1
    
    # Set proper permissions
    echo "$(date): Setting permissions..." >> $LOG_FILE
    chmod -R 755 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
    
    # Restart services if needed
    echo "$(date): Restarting services..." >> $LOG_FILE
    systemctl reload nginx >> $LOG_FILE 2>&1
    systemctl reload php8.1-fpm >> $LOG_FILE 2>&1
    
    echo "$(date): Deployment completed successfully!" >> $LOG_FILE
    
    # Send notification (optional)
    # curl -X POST -H 'Content-type: application/json' --data '{"text":"Deployment completed successfully!"}' YOUR_SLACK_WEBHOOK_URL
    
else
    echo "$(date): Git pull failed!" >> $LOG_FILE
    exit 1
fi 