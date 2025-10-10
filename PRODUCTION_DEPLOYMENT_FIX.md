# Production Deployment Fix for mcc-nac.com

## Problem Analysis
The error `Failed to open stream: No such file or directory` for `/home/u802714156/domains/mcc-nac.com/public_html/bootstrap/../routes/api.php` indicates that the production server is missing critical Laravel files.

## Root Cause
The production deployment is incomplete. The server is trying to access:
- `routes/api.php` (missing)
- Other Laravel framework files may also be missing

## Solution Steps

### 1. Verify Complete File Structure
Ensure these files exist on your production server at `/home/u802714156/domains/mcc-nac.com/public_html/`:

```
public_html/
├── routes/
│   ├── api.php          ← MISSING (causing the error)
│   ├── web.php          ← MISSING
│   └── console.php      ← MISSING
├── bootstrap/
│   └── app.php          ← MISSING
├── config/
│   └── app.php          ← MISSING
├── app/
│   └── Http/
│       └── Controllers/ ← MISSING
├── vendor/              ← MISSING (Composer dependencies)
├── storage/             ← MISSING
├── database/            ← MISSING
└── public/
    └── index.php        ← MISSING
```

### 2. Upload Missing Files
Upload these critical files to your production server:

#### Essential Route Files:
- `routes/api.php` - Contains API route definitions
- `routes/web.php` - Contains web route definitions  
- `routes/console.php` - Contains console command definitions

#### Bootstrap Files:
- `bootstrap/app.php` - Laravel application bootstrap configuration

#### Configuration Files:
- `config/app.php` - Application configuration
- `config/database.php` - Database configuration
- All other files in `config/` directory

#### Application Files:
- Entire `app/` directory with all controllers and models
- `vendor/` directory (run `composer install` on production)
- `storage/` directory with proper permissions
- `database/` directory with migrations

### 3. Production Server Commands
Run these commands on your production server:

```bash
# Navigate to your Laravel project directory
cd /home/u802714156/domains/mcc-nac.com/public_html

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Create storage symbolic link
php artisan storage:link

# Clear and optimize caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Environment Configuration
Ensure your production `.env` file contains:

```env
APP_NAME="MCC-NAC"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://mcc-nac.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Add other required environment variables
```

### 5. Web Server Configuration
Ensure your web server (Apache/Nginx) is properly configured:

#### For Apache (.htaccess in public/):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### For Nginx:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Quick Fix Script
Create and run this script on your production server:

```bash
#!/bin/bash
echo "Fixing MCC-NAC Production Deployment..."

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json not found. Are you in the Laravel project root?"
    exit 1
fi

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Set permissions
echo "Setting proper permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Create storage link
echo "Creating storage symbolic link..."
php artisan storage:link

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment fix complete!"
```

### 7. Verification
After implementing the fix, verify the deployment:

1. Visit `https://mcc-nac.com` - should load without errors
2. Check API endpoints work properly
3. Verify all routes are accessible
4. Test file uploads and storage functionality

## Expected Result
After implementing these fixes:
- ✅ No more "Failed to open stream" errors
- ✅ All routes accessible (web, API, console)
- ✅ Application loads properly
- ✅ All Laravel features functional

## Prevention
To prevent this issue in the future:
1. Always deploy the complete Laravel application structure
2. Run `composer install` on production after deployment
3. Set proper file permissions
4. Clear and cache configuration files
5. Test the deployment before going live
