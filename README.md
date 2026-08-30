# OSAS Accreditation Management System

A comprehensive web-based system for managing student organization accreditation, document submissions, financial reports, and compliance tracking. AI-assisted



## Installation Guide

### 1. Server Setup

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/osas/public
    
    <Directory /var/www/osas/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/osas-error.log
    CustomLog ${APACHE_LOG_DIR}/osas-access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/osas/public;
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

### 2. Application Setup

#### Clone/Download the Application
```bash
# Clone from repository
git clone https://github.com/DrewpySmith/OSAS-Accreditation-System.git
cd OSAS-Accreditation-System
```

#### Install Dependencies
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install JS dependencies
npm install

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
# Database Configuration (SQLite)
database.default.DBDriver = SQLite3
# Database path is set in app/Config/Database.php using WRITEPATH

# Application Configuration
app.baseURL = 'http://localhost:8080'
app.indexPage = ''
app.appTimezone = 'Asia/Manila'

# Security Configuration
security.tokenName = 'csrf_osas_token'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'osas_csrf_cookie'
security.expires = 7200
security.regenerate = true

# Session Configuration
session.driver = 'file'
session.cookieName = 'osas_session'
session.expiration = 7200
session.savePath = WRITEPATH . 'session'
session.matchIP = true
session.timeToUpdate = 300
session.regenerateDestroy = false
```

#### Database Migration
```bash
# Run database migrations (creates SQLite database at writable/database/usg_accreditation.db)
php spark migrate

# Seed admin user (username: admin, password: password)
php spark db:seed --class "AdminUser"
```

#### Build Frontend
```bash
npm run build
```

### 3. File Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data /path/to/osas
sudo chmod -R 755 /path/to/osas
sudo chmod -R 777 /path/to/osas/writable

# Ensure upload directories exist
mkdir -p writable/uploads/documents
mkdir -p writable/uploads/financial
mkdir -p writable/uploads/commitments
mkdir -p writable/uploads/accomplishment
mkdir -p writable/uploads/passbooks
chmod -R 777 writable/uploads
```

### 4. PHP Configuration (Windows/XAMPP)
Edit `php.ini` and ensure these extensions are enabled:
```ini
extension=sqlite3
extension=pdo_sqlite
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
file_uploads = On
```

## Initial Setup

### 1. Access the Application
Open your browser and navigate to `http://localhost:8080`

### 2. Login
Default admin credentials: username `admin`, password `password`

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
- Application Letter
- Officer List
- Constitution & By-Laws
- Org Structure
- Other Custom Documents

### Academic Years
Configure academic years in the format `YYYY-YYYY` (e.g., `2024-2025`)

## Security Considerations

### Production Environment
1. **Environment File**: Ensure `.env` is not publicly accessible
2. **Debug Mode**: Set `CI_ENVIRONMENT = production` in `.env`
3. **File Permissions**: Restrict write permissions to necessary directories only
4. **SSL/TLS**: Enable HTTPS in production and set `Cookie::$secure = true`
5. **Regular Updates**: Keep CodeIgniter and dependencies updated
6. **Rate Limiting**: Throttle filter protects login/reset/registration (5 attempts/15min)
7. **CSRF Regeneration**: Token regenerates on every request (Security::$regenerate = true)
8. **Session IP Matching**: Sessions are bound to IP address (Session::$matchIP = true)

### Backup Strategy
```bash
# SQLite database backup
cp writable/database/usg_accreditation.db backup_$(date +%Y%m%d).db

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz writable/uploads/
```

### Security Audit
The system has been audited for CRITICAL and HIGH vulnerabilities:
- ✅ Rate limiting on auth endpoints (ThrottleFilter)
- ✅ CSRF token regeneration enabled
- ✅ Session IP matching enabled
- ✅ Password minimum length increased to 8
- ✅ File upload MIME type and size validation
- ✅ Adviser signature endpoint hardened (base64 validation, image verification)
- ✅ Delete routes changed to POST
- ✅ Field whitelist on updateChecklist

## Troubleshooting

### Common Issues

#### 1. White Screen / 500 Error
- Check PHP error logs: `tail -f /var/log/apache2/error.log`
- Verify file permissions
- Check `.env` configuration

#### 2. Database Connection Failed
- Verify SQLite extension is enabled in `php.ini`
- Check `writable/database/` directory exists and is writable
- Run `php spark migrate` to create tables

#### 3. File Upload Issues
- Check upload directory permissions
- Verify PHP upload settings
- Check file size and type limits

#### 4. CSRF Token Errors
- The CSRF token regenerates on every request
- AJAX responses include the new token in `data.csrf`
- Update the meta tag: `document.querySelector('meta[name="X-CSRF-TOKEN"]').content = data.csrf`

#### 5. Secure Cookie Error
- If you see "Attempted to send a secure cookie over a non-secure connection"
- Set `Cookie::$secure = false` in `app/Config/Cookie.php` for local dev
- Set to `true` on production HTTPS

### Error Log Locations
- **Apache**: `/var/log/apache2/error.log`
- **Nginx**: `/var/log/nginx/error.log`
- **PHP**: `/var/log/php8.0-fpm.log`
- **Application**: `writable/logs/log-*.php`

## Maintenance

### Regular Tasks
1. **Database Optimization**: Run `php spark db:optimize` or `VACUUM;` on SQLite
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

## License

This software is licensed under the MIT License. See `LICENSE.md` for details.

## Credits

- Developed by Aejer Theranz D. Balayon and Joshua Gelbolingo

## Members

- Balayon, Aejer Theranz D.
- Gelbolingo, Joshua Rey M.
- Alcarde, Jershon Cris U.
- Felongco, Ken Chester I.
- Camat, Christine Mae M.
- Camat, Krizel Joy M.
- Caducoy, Sheena Mae B.
- Natividad, Jhomel G.
- Malakad, Jann Lemor M.

Framework: CodeIgniter 4
Frontend: Tailwind CSS v4 + shadcn/ui (React components for admin org list)
Chart Library: Chart.js
PDF Generation: Dompdf

---

## Quick Start Checklist

- [ ] Server requirements met (PHP 8+, SQLite3)
- [ ] SQLite extension enabled in php.ini
- [ ] Application files downloaded
- [ ] Dependencies installed (composer install, npm install)
- [ ] Environment configured (.env)
- [ ] Database migrated (php spark migrate)
- [ ] Admin user seeded (php spark db:seed --class AdminUser)
- [ ] Frontend built (npm run build)
- [ ] File permissions set
- [ ] Admin account working
- [ ] Organizations registered
- [ ] System tested

For additional assistance, please contact the technical support team.
