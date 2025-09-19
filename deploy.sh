#!/bin/bash

# Palsa POS Production Deployment Script
# Usage: ./deploy.sh [environment]

set -e

ENVIRONMENT=${1:-production}
APP_DIR="/var/www/palsa-pos"
BACKUP_DIR="/var/backups/palsa-pos"
DATE=$(date +%Y%m%d_%H%M%S)

echo "🚀 Starting Palsa POS deployment for $ENVIRONMENT environment..."

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Function to handle errors
handle_error() {
    log "❌ Error occurred during deployment. Rolling back..."
    # Add rollback logic here
    exit 1
}

trap handle_error ERR

# 1. Pre-deployment checks
log "🔍 Running pre-deployment checks..."

# Check if required environment variables are set
if [ "$ENVIRONMENT" = "production" ]; then
    if [ -z "$DB_PASSWORD" ] || [ -z "$APP_KEY" ]; then
        log "❌ Required environment variables not set"
        exit 1
    fi
fi

# Check disk space
AVAILABLE_SPACE=$(df / | awk 'NR==2 {print $4}')
if [ $AVAILABLE_SPACE -lt 1000000 ]; then # Less than 1GB
    log "❌ Insufficient disk space"
    exit 1
fi

# 2. Backup current application
log "💾 Creating backup..."
if [ -d "$APP_DIR" ]; then
    tar -czf "$BACKUP_DIR/app_backup_$DATE.tar.gz" -C "$APP_DIR" .
    log "✅ Application backup created: app_backup_$DATE.tar.gz"
fi

# 3. Database backup
log "💾 Creating database backup..."
if [ "$ENVIRONMENT" = "production" ]; then
    docker-compose -f docker-compose.production.yml exec -T db mysqldump \
        -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > "$BACKUP_DIR/db_backup_$DATE.sql"
    log "✅ Database backup created: db_backup_$DATE.sql"
fi

# 4. Pull latest code
log "📥 Pulling latest code..."
git pull origin main

# 5. Install/Update dependencies
log "📦 Installing dependencies..."
docker-compose -f docker-compose.production.yml exec -T app composer install --no-dev --optimize-autoloader

# 6. Run database migrations
log "🗄️ Running database migrations..."
docker-compose -f docker-compose.production.yml exec -T app php artisan migrate --force

# 7. Clear and cache configurations
log "🧹 Clearing and caching configurations..."
docker-compose -f docker-compose.production.yml exec -T app php artisan config:clear
docker-compose -f docker-compose.production.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan view:cache

# 8. Optimize application
log "⚡ Optimizing application..."
docker-compose -f docker-compose.production.yml exec -T app php artisan optimize

# 9. Restart services
log "🔄 Restarting services..."
docker-compose -f docker-compose.production.yml restart app queue

# 10. Health check
log "🏥 Running health checks..."
sleep 10

# Check if application is responding
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/health || echo "000")
if [ "$HTTP_STATUS" != "200" ]; then
    log "❌ Health check failed. HTTP status: $HTTP_STATUS"
    exit 1
fi

# Check database connection
DB_CHECK=$(docker-compose -f docker-compose.production.yml exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep "OK" || echo "FAIL")
if [ "$DB_CHECK" != "OK" ]; then
    log "❌ Database connection check failed"
    exit 1
fi

# 11. Run tests (if available)
if [ -f "phpunit.xml" ]; then
    log "🧪 Running tests..."
    docker-compose -f docker-compose.production.yml exec -T app php artisan test --parallel
fi

# 12. Cleanup old backups (keep last 7 days)
log "🧹 Cleaning up old backups..."
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete

# 13. Send deployment notification (optional)
if [ ! -z "$SLACK_WEBHOOK_URL" ]; then
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"✅ Palsa POS deployed successfully to $ENVIRONMENT\"}" \
        $SLACK_WEBHOOK_URL
fi

log "🎉 Deployment completed successfully!"
log "📊 Deployment summary:"
log "   - Environment: $ENVIRONMENT"
log "   - Backup created: app_backup_$DATE.tar.gz"
log "   - Database backup: db_backup_$DATE.sql"
log "   - Health check: ✅ Passed"
log "   - Deployment time: $(date)"

echo ""
echo "🔗 Application URLs:"
echo "   - Main site: https://your-domain.com"
echo "   - Admin panel: https://your-domain.com/admin"
echo "   - Health check: https://your-domain.com/health"