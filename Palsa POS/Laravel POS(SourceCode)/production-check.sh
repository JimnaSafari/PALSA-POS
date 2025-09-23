#!/bin/bash

# Palsa POS Production Readiness Check
# This script verifies that the application is properly configured for production

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Counters
PASSED=0
FAILED=0
WARNINGS=0

# Functions
log() {
    echo -e "${BLUE}[$(date +'%H:%M:%S')] $1${NC}"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
    ((PASSED++))
}

error() {
    echo -e "${RED}❌ $1${NC}"
    ((FAILED++))
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
    ((WARNINGS++))
}

info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if .env file exists
check_env_file() {
    log "Checking .env file..."
    if [ ! -f ".env" ]; then
        error ".env file not found"
        return 1
    fi

    # Check for required production settings
    if grep -q "APP_ENV=production" .env; then
        success "APP_ENV set to production"
    else
        error "APP_ENV not set to production"
    fi

    if grep -q "APP_DEBUG=false" .env; then
        success "APP_DEBUG disabled for production"
    else
        warning "APP_DEBUG not disabled (should be false in production)"
    fi

    if grep -q "APP_URL=https://" .env; then
        success "APP_URL configured with HTTPS"
    else
        warning "APP_URL not using HTTPS"
    fi

    # Check for placeholder values
    if grep -q "your_cpanel_database_name" .env; then
        warning "Database name contains placeholder value"
    fi

    if grep -q "your_production_consumer_key" .env; then
        warning "M-Pesa credentials contain placeholder values"
    fi
}

# Check file permissions
check_permissions() {
    log "Checking file permissions..."

    # Check storage directory
    if [ -d "storage" ]; then
        storage_perms=$(stat -c "%a" storage 2>/dev/null || echo "unknown")
        if [ "$storage_perms" = "755" ] || [ "$storage_perms" = "775" ]; then
            success "Storage directory permissions correct ($storage_perms)"
        else
            warning "Storage directory permissions: $storage_perms (should be 755 or 775)"
        fi
    else
        error "Storage directory not found"
    fi

    # Check bootstrap/cache directory
    if [ -d "bootstrap/cache" ]; then
        cache_perms=$(stat -c "%a" bootstrap/cache 2>/dev/null || echo "unknown")
        if [ "$cache_perms" = "755" ] || [ "$cache_perms" = "775" ]; then
            success "Bootstrap cache permissions correct ($cache_perms)"
        else
            warning "Bootstrap cache permissions: $cache_perms (should be 755 or 775)"
        fi
    else
        error "Bootstrap cache directory not found"
    fi
}

# Check PHP requirements
check_php() {
    log "Checking PHP requirements..."

    if ! command -v php &> /dev/null; then
        error "PHP not found"
        return 1
    fi

    php_version=$(php -r "echo PHP_VERSION;")
    info "PHP Version: $php_version"

    # Check required extensions
    required_extensions=("pdo" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo")
    for ext in "${required_extensions[@]}"; do
        if php -m | grep -i "$ext" > /dev/null; then
            success "PHP extension '$ext' available"
        else
            error "PHP extension '$ext' not found"
        fi
    done
}

# Check Laravel installation
check_laravel() {
    log "Checking Laravel installation..."

    if [ ! -f "artisan" ]; then
        error "Laravel artisan file not found"
        return 1
    fi

    if php artisan --version &> /dev/null; then
        laravel_version=$(php artisan --version)
        success "Laravel installed: $laravel_version"
    else
        error "Laravel artisan command failed"
        return 1
    fi

    # Check if application key is set
    if php artisan tinker --execute="echo config('app.key') ? 'set' : 'not set';" | grep -q "set"; then
        success "Application key is set"
    else
        error "Application key not set"
    fi
}

# Check database configuration
check_database() {
    log "Checking database configuration..."

    if ! grep -q "DB_CONNECTION=mysql" .env; then
        warning "Database connection not set to mysql"
    fi

    # Try database connection (this might fail in some environments)
    if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'connected'; } catch(Exception \$e) { echo 'failed'; }" 2>/dev/null | grep -q "connected"; then
        success "Database connection successful"
    else
        warning "Database connection could not be verified (may be normal during setup)"
    fi
}

# Check security features
check_security() {
    log "Checking security features..."

    # Check if SecurityHeaders middleware exists
    if [ -f "app/Http/Middleware/SecurityHeaders.php" ]; then
        success "SecurityHeaders middleware exists"
    else
        error "SecurityHeaders middleware not found"
    fi

    # Check if security middleware is registered
    if grep -q "SecurityHeaders::class" bootstrap/app.php; then
        success "SecurityHeaders middleware registered"
    else
        error "SecurityHeaders middleware not registered"
    fi

    # Check for security headers in routes
    if grep -q "health" routes/web.php; then
        success "Health check route exists"
    else
        warning "Health check route not found"
    fi
}

# Check backup system
check_backup() {
    log "Checking backup system..."

    if [ -f "app/Console/Commands/BackupDatabase.php" ]; then
        success "Database backup command exists"
    else
        error "Database backup command not found"
    fi

    # Check backup directory
    if [ -d "storage/backups" ]; then
        success "Backup directory exists"
    else
        info "Backup directory will be created during first backup"
    fi
}

# Check deployment script
check_deployment() {
    log "Checking deployment script..."

    if [ -f "deploy.sh" ]; then
        success "Deployment script exists"
        if [ -x "deploy.sh" ]; then
            success "Deployment script is executable"
        else
            warning "Deployment script is not executable (run: chmod +x deploy.sh)"
        fi
    else
        error "Deployment script not found"
    fi
}

# Check production optimizations
check_optimization() {
    log "Checking production optimizations..."

    # Check if routes are cached (optional - may not be cached yet)
    if [ -f "bootstrap/cache/routes-v7.php" ]; then
        success "Routes are cached for production"
    else
        info "Routes not cached yet (will be cached during deployment)"
    fi

    # Check if config is cached
    if [ -f "bootstrap/cache/config-v7.php" ]; then
        success "Configuration is cached for production"
    else
        info "Configuration not cached yet (will be cached during deployment)"
    fi
}

# Generate report
generate_report() {
    echo
    echo "========================================"
    echo "🧪 PRODUCTION READINESS REPORT"
    echo "========================================"
    echo
    echo "✅ Passed: $PASSED"
    echo "❌ Failed: $FAILED"
    echo "⚠️  Warnings: $WARNINGS"
    echo

    total=$((PASSED + FAILED + WARNINGS))
    if [ $total -gt 0 ]; then
        success_rate=$((PASSED * 100 / total))
        echo "📊 Success Rate: ${success_rate}%"
        echo
    fi

    if [ $FAILED -eq 0 ]; then
        echo "🎉 STATUS: PRODUCTION READY"
        echo
        echo "Your Palsa POS application is ready for production deployment!"
        echo "Run './deploy.sh' to deploy to your cPanel hosting."
    else
        echo "⚠️  STATUS: REQUIRES ATTENTION"
        echo
        echo "Please fix the failed checks before deploying to production."
        echo "Run this script again after making corrections."
    fi

    echo
    echo "========================================"
}

# Main execution
main() {
    echo "🚀 Palsa POS Production Readiness Check"
    echo "========================================"
    echo

    check_env_file
    check_permissions
    check_php
    check_laravel
    check_database
    check_security
    check_backup
    check_deployment
    check_optimization

    generate_report
}

# Run main function
main "$@"
