# USG Accreditation Management System

A comprehensive web-based system for managing university student government (USG) organization accreditation, document submissions, financial reports, and compliance tracking.

## Features

### Core Functionality
- **Organization Management**: Register and manage student organizations
- **Document Submission**: Upload and track accreditation documents
- **Financial Reporting**: Submit and review financial reports with expenditure tracking
- **Calendar Activities**: Plan and track organizational activities
- **Commitment Forms**: Manage organizational commitment forms
- **Accomplishment Reports**: Track and review organizational achievements

### Admin Features
- **Dashboard**: Overview of system statistics and recent activities
- **Document Review**: Review, approve, or reject submitted documents
- **Statistics & Analytics**: Comprehensive reporting with visual charts
- **User Management**: Manage admin and organization user accounts
- **Audit Trail**: Track all system activities and changes

### Organization Features
- **Document Portal**: Submit required accreditation documents
- **Financial Management**: Track collections, expenses, and remaining funds
- **Activity Planning**: Calendar-based activity management
- **Status Tracking**: Real-time accreditation status updates
- **Communication**: Comment system for document feedback

## System Requirements

### Server Requirements
- **PHP**: 8.0 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Memory**: Minimum 512MB RAM (1GB+ recommended)
- **Storage**: Minimum 2GB free space

### PHP Extensions Required
- php-cli
- php-fpm
- php-mysql
- php-json
- php-mbstring
- php-xml
- php-curl
- php-gd
- php-intl
- php-fileinfo

### Browser Requirements
- Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- JavaScript enabled
- Cookies enabled

## Installation Guide

### 1. Server Setup

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/usg-accreditation/public
    
    <Directory /var/www/usg-accreditation/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/usg-accreditation-error.log
    CustomLog ${APACHE_LOG_DIR}/usg-accreditation-access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/usg-accreditation/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 2. Database Setup

```sql
-- Create database
CREATE DATABASE usg_accreditation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional but recommended)
CREATE USER 'usg_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON usg_accreditation.* TO 'usg_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Application Setup

#### Clone/Download the Application
```bash
# Option 1: Clone from repository
git clone https://github.com/your-repo/usg-accreditation.git
cd usg-accreditation

# Option 2: Download and extract
wget https://github.com/your-repo/usg-accreditation/archive/main.zip
unzip main.zip
mv usg-accreditation-main usg-accreditation
cd usg-accreditation
```

#### Install Dependencies
```bash
# If using Composer
composer install --no-dev --optimize-autoloader

# Set proper permissions
chmod -R 755 .
chmod -R 777 writable
```

#### Environment Configuration
```bash
# Copy environment template
cp env .env

# Edit environment file
nano .env
```

Configure your `.env` file:
```env
# Database Configuration
database.default.hostname = localhost
database.default.database = usg_accreditation
database.default.username = usg_user
database.default.password = secure_password
database.default.DBDriver = MySQLi

# Application Configuration
app.baseURL = 'http://your-domain.com'
app.indexPage = ''
app.appTimezone = 'Asia/Manila'

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
```

#### Database Migration
```bash
# Run database migrations
php spark migrate

# Seed initial data
php spark db:seed --class "DatabaseSeeder"
```

### 4. File Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data /path/to/usg-accreditation
sudo chmod -R 755 /path/to/usg-accreditation
sudo chmod -R 777 /path/to/usg-accreditation/writable

# Ensure upload directory exists
mkdir -p writable/uploads/documents
mkdir -p writable/uploads/financial
mkdir -p writable/uploads/commitments
chmod -R 777 writable/uploads
```

### 5. Web Server Configuration

#### Enable Apache Modules
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### Configure PHP Settings
Edit your `php.ini` file:
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
file_uploads = On
```

## Initial Setup

### 1. Access the Application
Open your browser and navigate to `http://your-domain.com`

### 2. Create Admin Account
The system will prompt you to create the first administrator account.

### 3. Configure System Settings
- Set up academic years
- Configure document types
- Set up notification preferences
- Configure email settings (optional)

### 4. Add Organizations
- Register student organizations
- Assign organization representatives
- Set up organization profiles

## Configuration

### Document Types
The system supports the following document types:
- Financial Reports
- Program Expenditure
- Commitment Forms
- Accomplishment Reports
- Calendar Activities
- Other Custom Documents

### Academic Years
Configure academic years in the format `YYYY-YYYY` (e.g., `2024-2025`)

### File Upload Settings
Configure file upload limits and allowed types in `app/Config/Mimes.php`

## Security Considerations

### Production Environment
1. **Environment File**: Ensure `.env` is not publicly accessible
2. **Debug Mode**: Set `CI_ENVIRONMENT = production` in `.env`
3. **Database Security**: Use strong database passwords
4. **File Permissions**: Restrict write permissions to necessary directories only
5. **SSL/TLS**: Enable HTTPS in production
6. **Regular Updates**: Keep CodeIgniter and dependencies updated

### Backup Strategy
```bash
# Database backup
mysqldump -u usg_user -p usg_accreditation > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz writable/uploads/
```

## Troubleshooting

### Common Issues

#### 1. White Screen / 500 Error
- Check PHP error logs: `tail -f /var/log/apache2/error.log`
- Verify file permissions
- Check `.env` configuration

#### 2. Database Connection Failed
- Verify database credentials in `.env`
- Check database server status
- Ensure database user has proper permissions

#### 3. File Upload Issues
- Check upload directory permissions
- Verify PHP upload settings
- Check file size limits

#### 4. Session Issues
- Clear session files: `rm -rf writable/session/*`
- Check session configuration in `.env`

### Error Log Locations
- **Apache**: `/var/log/apache2/error.log`
- **Nginx**: `/var/log/nginx/error.log`
- **PHP**: `/var/log/php8.0-fpm.log`
- **Application**: `writable/logs/log-*.php`

## Maintenance

### Regular Tasks
1. **Database Optimization**: Run monthly
2. **Log Cleanup**: Remove old logs weekly
3. **File Cleanup**: Remove orphaned uploads monthly
4. **Backup Verification**: Test backups weekly

### Performance Optimization
1. Enable PHP OPcache
2. Configure database caching
3. Use CDN for static assets
4. Enable gzip compression

## Support

### Documentation
- User Manual: `/docs/user-manual.pdf`
- Admin Guide: `/docs/admin-guide.pdf`
- API Documentation: `/docs/api/`

### Technical Support
- Email: support@your-domain.com
- Phone: +63 XXX XXX XXXX
- Help Desk: https://help.your-domain.com

## License

This software is licensed under the MIT License. See `LICENSE.md` for details.

## Credits

Developed by [Your Organization Name]
Framework: CodeIgniter 4
UI Framework: Bootstrap 5
Chart Library: Chart.js

---

## Quick Start Checklist

- [ ] Server requirements met
- [ ] Database created
- [ ] Application files uploaded
- [ ] Environment configured
- [ ] Database migrated
- [ ] File permissions set
- [ ] Web server configured
- [ ] SSL certificate installed
- [ ] Admin account created
- [ ] Organizations registered
- [ ] System tested

For additional assistance, please contact the technical support team.
