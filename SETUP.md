# USG Accreditation System - Setup Guide

This guide provides step-by-step instructions for setting up the USG Accreditation Management System on your server.

## Prerequisites

Before you begin, ensure you have the following:

- Server access (SSH or direct access)
- PHP 8.0+ installed
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache or Nginx)
- Composer (for dependency management)
- Git (optional, for cloning)

## Step 1: Server Environment Setup

### Install PHP and Required Extensions

#### Ubuntu/Debian:
```bash
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-json php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-intl php8.1-fileinfo php8.1-cli
```

#### CentOS/RHEL:
```bash
sudo yum install php php-fpm php-mysqlnd php-json php-mbstring php-xml php-curl php-gd php-intl php-fileinfo php-cli
```

### Install Web Server

#### Apache:
```bash
# Ubuntu/Debian
sudo apt install apache2 libapache2-mod-php8.1

# Enable modules
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### Nginx:
```bash
# Ubuntu/Debian
sudo apt install nginx php8.1-fpm

# Start services
sudo systemctl start nginx
sudo systemctl start php8.1-fpm
sudo systemctl enable nginx
sudo systemctl enable php8.1-fpm
```

### Install Database

#### MySQL:
```bash
# Ubuntu/Debian
sudo apt install mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

#### MariaDB:
```bash
# Ubuntu/Debian
sudo apt install mariadb-server

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

## Step 2: Database Configuration

Execute these SQL commands in your MySQL/MariaDB client:

```sql
-- Create database
CREATE DATABASE usg_accreditation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user
CREATE USER 'usg_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON usg_accreditation.* TO 'usg_user'@'localhost';
FLUSH PRIVILEGES;

-- Verify creation
SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = 'usg_user';
```

## Step 3: Application Deployment

### Option A: Clone from Repository
```bash
# Navigate to web directory
cd /var/www/

# Clone the repository
git clone https://github.com/your-repo/usg-accreditation.git

# Rename if needed
mv usg-accreditation usg-accreditation
cd usg-accreditation
```

### Option B: Upload Files
```bash
# Create directory
sudo mkdir -p /var/www/usg-accreditation
cd /var/www/usg-accreditation

# Upload files using SCP, FTP, or file manager
# Extract if uploaded as zip
unzip usg-accreditation.zip
```

### Install Dependencies
```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# If Composer is not installed globally
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader
```

## Step 4: Environment Configuration

### Copy and Configure Environment File
```bash
# Copy environment template
cp env .env

# Edit the environment file
nano .env
```

### Configure .env Settings
```env
# Database Configuration
database.default.hostname = localhost
database.default.database = usg_accreditation
database.default.username = usg_user
database.default.password = your_secure_password
database.default.DBDriver = MySQLi

# Application Configuration
app.baseURL = 'http://your-domain.com'
app.indexPage = ''
app.appTimezone = 'Asia/Manila'
app.sessionDriver = 'file'
app.sessionSavePath = WRITEPATH . 'session'

# Security Configuration
security.tokenName = 'csrf_token'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'csrf_cookie'
security.expires = 7200
security.regenerate = true

# Session Configuration
session.driver = 'file'
session.cookieName = 'usg_session'
session.expiration = 7200
session.savePath = WRITEPATH . 'session'
session.matchIP = false
session.timeToUpdate = 300
session.regenerateDestroy = false

# Production Settings (uncomment for production)
# CI_ENVIRONMENT = production
```

## Step 5: Database Migration

### Run Database Migrations
```bash
# Navigate to application directory
cd /var/www/usg-accreditation

# Run migrations
php spark migrate

# Seed initial data
php spark db:seed --class "DatabaseSeeder"

# Verify migration
php spark db:status
```

### Manual Database Setup (Alternative)
If migrations fail, you can manually import the database:

```bash
# Import SQL file if available
mysql -u usg_user -p usg_accreditation < database.sql
```

## Step 6: File Permissions

### Set Proper Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/usg-accreditation

# Set directory permissions
sudo find /var/www/usg-accreditation -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/usg-accreditation -type f -exec chmod 644 {} \;

# Set writable permissions
sudo chmod -R 777 /var/www/usg-accreditation/writable

# Create upload directories
mkdir -p /var/www/usg-accreditation/writable/uploads/documents
mkdir -p /var/www/usg-accreditation/writable/uploads/financial
mkdir -p /var/www/usg-accreditation/writable/uploads/commitments
mkdir -p /var/www/usg-accreditation/writable/uploads/accomplishments

# Set upload permissions
sudo chmod -R 777 /var/www/usg-accreditation/writable/uploads
```

## Step 7: Web Server Configuration

### Apache Configuration
Create virtual host file:
```bash
sudo nano /etc/apache2/sites-available/usg-accreditation.conf
```

Add this configuration:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/usg-accreditation/public
    
    <Directory /var/www/usg-accreditation/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/usg-accreditation-error.log
    CustomLog ${APACHE_LOG_DIR}/usg-accreditation-access.log combined
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite usg-accreditation.conf
sudo systemctl reload apache2
```

### Nginx Configuration
Create server block file:
```bash
sudo nano /etc/nginx/sites-available/usg-accreditation
```

Add this configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/usg-accreditation/public;
    index index.php index.html;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Handle static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Deny access to .env and other sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(composer\.json|composer\.lock|\.env)$ {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/usg-accreditation /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Step 8: PHP Configuration

### Edit PHP Configuration
Find your `php.ini` file location:
```bash
php --ini
```

Edit the configuration:
```bash
sudo nano /etc/php/8.1/fpm/php.ini
# or for Apache
sudo nano /etc/php/8.1/apache2/php.ini
```

Update these settings:
```ini
; File Upload Settings
upload_max_filesize = 50M
post_max_size = 50M
max_file_uploads = 20

; Memory and Execution
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

; Error Reporting (production)
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; Session Settings
session.gc_maxlifetime = 7200
session.cookie_httponly = On
session.cookie_secure = Off
session.use_strict_mode = On
```

Restart PHP service:
```bash
# For PHP-FPM
sudo systemctl restart php8.1-fpm

# For Apache
sudo systemctl restart apache2
```

## Step 9: SSL Certificate (Recommended)

### Let's Encrypt Certificate
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Obtain certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Set up auto-renewal
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

### Manual SSL Configuration
If you have your own SSL certificate, update your Apache/Nginx configuration to use HTTPS.

## Step 10: Final Setup and Testing

### Test Application
1. Open browser and navigate to `http://your-domain.com`
2. Verify the application loads correctly
3. Create admin account when prompted
4. Test file upload functionality
5. Verify database operations

### Create Admin Account
1. Access the application
2. Follow the initial setup wizard
3. Create first administrator account
4. Configure basic settings

### Verify Permissions
```bash
# Test writable permissions
sudo -u www-data touch /var/www/usg-accreditation/writable/test.txt
sudo rm /var/www/usg-accreditation/writable/test.txt

# Check log files
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

## Step 11: Production Hardening

### Security Settings
1. Update `.env` for production:
```env
CI_ENVIRONMENT = production
```

2. Secure sensitive files:
```bash
# Protect .env file
sudo chmod 600 .env
sudo chown www-data:www-data .env

# Remove development files
sudo rm -rf composer.json composer.lock
```

3. Configure firewall:
```bash
# UFW (Ubuntu)
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Backup Setup
Create backup script:
```bash
sudo nano /usr/local/bin/usg-backup.sh
```

Add this content:
```bash
#!/bin/bash

# Configuration
DB_NAME="usg_accreditation"
DB_USER="usg_user"
DB_PASS="your_password"
APP_DIR="/var/www/usg-accreditation"
BACKUP_DIR="/var/backups/usg-accreditation"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz -C $APP_DIR writable/uploads

# Remove old backups (keep 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

Make it executable and set up cron:
```bash
sudo chmod +x /usr/local/bin/usg-backup.sh

# Add to crontab for daily backup at 2 AM
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/usg-backup.sh
```

## Troubleshooting

### Common Issues and Solutions

#### 1. 500 Internal Server Error
```bash
# Check logs
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/nginx/error.log

# Check .env configuration
cat .env

# Verify file permissions
ls -la /var/www/usg-accreditation/writable
```

#### 2. Database Connection Failed
```bash
# Test database connection
mysql -u usg_user -p usg_accreditation

# Check database service
sudo systemctl status mysql
# or
sudo systemctl status mariadb
```

#### 3. File Upload Issues
```bash
# Check upload directory permissions
ls -la writable/uploads/

# Check PHP upload settings
php -i | grep upload
```

#### 4. Session Issues
```bash
# Clear session files
sudo rm -rf writable/session/*

# Check session directory permissions
ls -la writable/session/
```

### Performance Optimization

1. Enable OPcache:
```bash
sudo nano /etc/php/8.1/mods-available/opcache.ini
```

2. Configure database caching in your application

3. Enable gzip compression in web server

4. Use CDN for static assets

## Support

If you encounter issues during setup:

1. Check the error logs
2. Verify all prerequisites are met
3. Ensure file permissions are correct
4. Test database connectivity
5. Contact technical support if needed

---

**Setup Complete!** Your USG Accreditation Management System should now be running. Access it through your web browser and complete the initial configuration.
