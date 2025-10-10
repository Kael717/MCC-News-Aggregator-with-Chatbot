#!/bin/bash

# MCC-NAC Production Deployment Fix Script
# Run this script on your production server to fix the deployment issues

echo "=========================================="
echo "MCC-NAC Production Deployment Fix"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found!"
    echo "Please run this script from your Laravel project root directory."
    echo "Current directory: $(pwd)"
    exit 1
fi

echo "✅ Found composer.json - we're in the right directory"
echo "Current directory: $(pwd)"
echo ""

# Check critical files
echo "Checking critical files..."
missing_files=()

if [ ! -f "routes/api.php" ]; then
    echo "❌ routes/api.php is missing (this is causing your current error)"
    missing_files+=("routes/api.php")
fi

if [ ! -f "routes/web.php" ]; then
    echo "❌ routes/web.php is missing"
    missing_files+=("routes/web.php")
fi

if [ ! -f "bootstrap/app.php" ]; then
    echo "❌ bootstrap/app.php is missing"
    missing_files+=("bootstrap/app.php")
fi

if [ ! -f "vendor/autoload.php" ]; then
    echo "❌ vendor/autoload.php is missing"
    missing_files+=("vendor/autoload.php")
fi

if [ ${#missing_files[@]} -eq 0 ]; then
    echo "✅ All critical files are present"
else
    echo ""
    echo "❌ Missing critical files detected:"
    for file in "${missing_files[@]}"; do
        echo "   - $file"
    done
    echo ""
    echo "You need to upload the complete Laravel application structure to your server."
    echo "Please upload all missing files and directories, then run this script again."
    exit 1
fi

echo ""

# Install Composer dependencies
echo "Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
    if [ $? -eq 0 ]; then
        echo "✅ Composer dependencies installed successfully"
    else
        echo "❌ Failed to install Composer dependencies"
        exit 1
    fi
else
    echo "❌ Composer not found. Please install Composer first."
    exit 1
fi

echo ""

# Set proper permissions
echo "Setting proper permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo "✅ Permissions set successfully"

# Create storage symbolic link
echo "Creating storage symbolic link..."
if command -v php &> /dev/null; then
    php artisan storage:link
    if [ $? -eq 0 ]; then
        echo "✅ Storage symbolic link created"
    else
        echo "⚠️  Storage link creation failed (may already exist)"
    fi
else
    echo "❌ PHP not found. Please install PHP first."
    exit 1
fi

echo ""

# Clear caches
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Caches cleared successfully"

echo ""

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Production optimization complete"

echo ""

# Test the application
echo "Testing application bootstrap..."
php -r "
try {
    require 'vendor/autoload.php';
    \$app = require 'bootstrap/app.php';
    echo '✅ Application bootstrap successful\n';
} catch (Exception \$e) {
    echo '❌ Application bootstrap failed: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

echo ""

echo "=========================================="
echo "✅ Deployment fix complete!"
echo "=========================================="
echo ""
echo "Your MCC-NAC application should now be working properly."
echo ""
echo "Next steps:"
echo "1. Visit your website: https://mcc-nac.com"
echo "2. Test the API endpoints"
echo "3. Verify all functionality is working"
echo ""
echo "If you still encounter issues:"
echo "1. Check your web server configuration"
echo "2. Verify your .env file is properly configured"
echo "3. Check file permissions on storage/ and bootstrap/cache/"
echo ""
echo "For support, check the PRODUCTION_DEPLOYMENT_FIX.md file for detailed instructions."
