#!/bin/bash

# Palsa POS Production Deployment Script for cPanel
# This script automates the deployment process for cPanel hosting

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="Palsa POS"
DEPLOY_ENV="production"
BACKUP_DIR="storage/backups"
LOG_FILE="storage/logs/deploy_$(date +%Y%m%d_%H%M%S).log"

# Functions
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

info() {
    echo -e "${BLUE}ℹ️  $1${NC}" | tee -a "$LOG_FILE"
}

# Pre-deployment checks
pre_deployment_checks() {
    log "Starting pre-deployment checks..."

    # Check if .env file exists
    if [ ! -f ".env" ]; then
        error ".env file not found! Please create it from .env.example"
        exit 1
    fi

    # Check if required PHP extensions are available
    required_extensions=("pdo" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo")
    for ext in "${required_extensions[@]}"; do
        if ! php -m | grep -i "$ext" > /dev/null; then
            error "PHP extension '$ext' is not installed"
            exit 1
        fi
    done

    success "Pre-deployment checks passed"
}

# Backup current state
create_backup() {
    log "Creating pre-deployment backup..."

    # Create backup directory
    mkdir -p "$BACKUP_DIR"

    # Backup database if possible
    if command -v mysqldump &> /dev/null; then
        php artisan db:backup --type=deployment 2>/dev/null || warning "Database backup failed, continuing..."
    else
        warning "mysqldump not available, skipping database backup"
    fi

    # Backup important files
    backup_file="$BACKUP_DIR/pre_deploy_$(date +%Y%m%d_%H%M%S).tar.gz"
    tar -czf "$backup_file" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='.git' \
        --exclude='storage/backups' \
        . 2>/dev/null || warning "File backup failed"

    success "Backup created: $backup_file"
}

# Install/update dependencies
install_dependencies() {
    log "Installing/updating dependencies..."

    # Install PHP dependencies
    if [ -f "composer.json" ]; then
        if command -v composer &> /dev/null; then
            composer install --no-dev --optimize-autoloader
            success "Composer dependencies installed"
        else
            warning "Composer not found, skipping PHP dependencies"
        fi
    fi

    # Install Node dependencies and build assets
    if [ -f "package.json" ]; then
        if command -v npm &> /dev/null; then
            npm ci
            npm run build
            success "Node dependencies installed and assets built"
        else
            warning "npm not found, skipping Node dependencies"
        fi
    fi
}

# Environment setup
setup_environment() {
    log "Setting up environment..."

    # Generate application key if not set
    if ! grep -q "APP_KEY=base64:" .env; then
        php artisan key:generate
        success "Application key generated"
    fi

    # Set proper permissions
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache

    # Create symbolic link for storage
    php artisan storage:link 2>/dev/null || warning "Storage link may already exist"

    success "Environment setup completed"
}

# Database operations
setup_database() {
    log "Setting up database..."

    # Run migrations
    php artisan migrate --force
    success "Database migrations completed"

    # Seed database if in development/debug mode
    if grep -q "APP_DEBUG=true" .env; then
        php artisan db:seed --force 2>/dev/null || warning "Database seeding failed"
    fi
}

# Clear and optimize
optimize_application() {
    log "Optimizing application..."

    # Clear caches
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    # Cache configuration for production
    if [ "$DEPLOY_ENV" = "production" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        success "Application optimized for production"
    else
        success "Caches cleared"
    fi
}

# Health check
run_health_check() {
    log "Running health checks..."

    # Basic Laravel health check
    if php artisan --version > /dev/null 2>&1; then
        success "Laravel installation is healthy"
    else
        error "Laravel health check failed"
        exit 1
    fi

    # Check if health endpoint is accessible
    if curl -s -o /dev/null -w "%{http_code}" http://localhost/health | grep -q "200"; then
        success "Health endpoint is responding"
    else
        warning "Health endpoint not accessible (may be normal for CLI deployment)"
    fi
}

# Post-deployment tasks
post_deployment() {
    log "Running post-deployment tasks..."

    # Set maintenance mode off
    php artisan up

    # Run any pending jobs
    php artisan queue:work --once 2>/dev/null || true

    success "Post-deployment tasks completed"
}

# Main deployment function
deploy() {
    log "🚀 Starting $APP_NAME deployment to $DEPLOY_ENV environment"
    log "Deployment started at: $(date)"

    pre_deployment_checks
    create_backup
    install_dependencies
    setup_environment
    setup_database
    optimize_application
    run_health_check
    post_deployment

    success "🎉 Deployment completed successfully!"
    success "Application is now live at: $(grep APP_URL .env | cut -d'=' -f2)"

    # Display important information
    echo
    info "Important post-deployment steps:"
    echo "1. Update your domain DNS to point to this server"
    echo "2. Set up SSL certificate in cPanel"
    echo "3. Configure email settings if not done"
    echo "4. Set up automated backups"
    echo "5. Monitor application logs"
    echo
    info "Useful commands:"
    echo "• php artisan tinker          - Access Laravel shell"
    echo "• php artisan migrate:status  - Check migration status"
    echo "• php artisan queue:work      - Process queued jobs"
    echo "• tail -f storage/logs/laravel.log  - Monitor logs"
}

# Rollback function
rollback() {
    warning "Starting rollback procedure..."

    # Put application in maintenance mode
    php artisan down

    # Find latest backup
    latest_backup=$(find "$BACKUP_DIR" -name "pre_deploy_*.tar.gz" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)

    if [ -n "$latest_backup" ]; then
        info "Rolling back to: $latest_backup"

        # Extract backup (excluding certain directories)
        tar -xzf "$latest_backup" --exclude='vendor' --exclude='node_modules'

        # Restore database if backup exists
        db_backup=$(find "$BACKUP_DIR" -name "backup_deployment_*.sql*" -type f -printf '%T@ %p\n' 2>/dev/null | sort -n | tail -1 | cut -d' ' -f2-)
        if [ -n "$db_backup" ]; then
            info "Restoring database from: $db_backup"
            # Database restoration would need to be implemented based on your setup
            warning "Database restoration needs to be done manually"
        fi

        # Clear caches and re-optimize
        optimize_application

        # Bring application back up
        php artisan up

        success "Rollback completed"
    else
        error "No backup found for rollback"
        exit 1
    fi
}

# Main script logic
case "${1:-deploy}" in
    "deploy")
        deploy
        ;;
    "rollback")
        rollback
        ;;
    "backup")
        create_backup
        ;;
    "health")
        run_health_check
        ;;
    *)
        echo "Usage: $0 [deploy|rollback|backup|health]"
        echo "  deploy   - Full deployment process (default)"
        echo "  rollback - Rollback to previous version"
        echo "  backup   - Create backup only"
        echo "  health   - Run health checks only"
        exit 1
        ;;
esac
