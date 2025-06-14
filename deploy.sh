#!/bin/bash

# EMDR Library Deployment Script
# This script pulls the latest code from GitHub, builds the application,
# and sets up proper permissions for production deployment

set -e  # Exit on any error

# Configuration
REPO_URL="https://github.com/titovm/emdr-library.git"  # Update with your actual repo URL
BRANCH="main"  # or "master" depending on your default branch
WEB_USER="www-data"
WEB_GROUP="www-data"
APP_DIR="/var/www/emdr-library"  # Update with your actual deployment path

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if running as root or with sudo
if [[ $EUID -ne 0 ]]; then
    error "This script must be run as root or with sudo"
    exit 1
fi

# Check if git is installed
if ! command -v git &> /dev/null; then
    error "Git is not installed. Please install git first."
    exit 1
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    error "Composer is not installed. Please install composer first."
    exit 1
fi

# Check if node is installed
if ! command -v node &> /dev/null; then
    error "Node.js is not installed. Please install Node.js first."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    error "npm is not installed. Please install npm first."
    exit 1
fi

log "Starting deployment process..."

# Create app directory if it doesn't exist
if [ ! -d "$APP_DIR" ]; then
    log "Creating application directory: $APP_DIR"
    mkdir -p "$APP_DIR"
fi

# Navigate to app directory
cd "$APP_DIR"

# Check if it's a git repository
if [ ! -d ".git" ]; then
    log "Cloning repository from $REPO_URL"
    git clone "$REPO_URL" .
else
    log "Repository exists, pulling latest changes"
    
    # Stash any local changes
    git stash
    
    # Fetch latest changes
    git fetch origin
    
    # Reset to latest commit on the specified branch
    git reset --hard "origin/$BRANCH"
    
    # Clean untracked files
    git clean -fd
fi

# Check if .env file exists, if not copy from .env.example
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        log "Creating .env file from .env.example"
        cp .env.example .env
        warning "Please update .env file with your production settings"
    else
        error ".env.example file not found. Please create .env file manually."
        exit 1
    fi
fi

# Install/Update Composer dependencies
log "Installing/Updating Composer dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not set
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:" .env; then
    log "Generating application key"
    php artisan key:generate --force
fi

# Install/Update NPM dependencies
log "Installing/Updating NPM dependencies"
npm ci --production=false

# Build frontend assets
log "Building frontend assets"
npm run build

# Clear and cache configuration
log "Optimizing Laravel application"
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Run database migrations
# log "Running database migrations"
# php artisan migrate --force

# Clear application cache
# log "Clearing application cache"
# php artisan cache:clear

# Create symbolic link for storage (if not exists)
if [ ! -L "public/storage" ]; then
    log "Creating storage symbolic link"
    php artisan storage:link
fi

# Set proper ownership
log "Setting proper file ownership to $WEB_USER:$WEB_GROUP"
chown -R "$WEB_USER:$WEB_GROUP" "$APP_DIR"

# Set proper permissions
log "Setting proper file permissions"
find "$APP_DIR" -type f -exec chmod 644 {} \;
find "$APP_DIR" -type d -exec chmod 755 {} \;

# Set special permissions for Laravel directories
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

# Make artisan executable
chmod +x "$APP_DIR/artisan"

# Restart web server (uncomment the one you're using)
log "Restarting web server"
systemctl reload caddy    # For Caddy

# Restart PHP-FPM (uncomment if using PHP-FPM)
# systemctl restart php8.1-fpm  # Update version as needed
# systemctl restart php-fpm     # For Red Hat/CentOS

# Queue restart (if using queues)
log "Restarting queue workers"
php artisan queue:restart

# Display deployment summary
echo ""
echo "======================================"
echo "       DEPLOYMENT SUMMARY"
echo "======================================"
echo "Repository: $REPO_URL"
echo "Branch: $BRANCH"
echo "Deploy Path: $APP_DIR"
echo "Web User: $WEB_USER:$WEB_GROUP"
echo "Deployment Time: $(date)"
echo "======================================"

success "Deployment process completed!"

# Optional: Send notification (uncomment and configure as needed)
# curl -X POST -H 'Content-type: application/json' \
#     --data '{"text":"EMDR Library deployed successfully!"}' \
#     YOUR_SLACK_WEBHOOK_URL

log "Remember to:"
echo "  1. Update your .env file with production settings"
echo "  2. Configure your web server (Apache/Nginx) if not already done"
echo "  3. Set up SSL certificate"
echo "  4. Configure backup schedule"
echo "  5. Set up monitoring"
