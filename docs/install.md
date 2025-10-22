# Smart Home System Installation Guide

## System Requirements

### Server Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (Apache)
- PHP Extensions:
  - PDO
  - PDO_MySQL
  - JSON
  - OpenSSL
  - mbstring

### Client Requirements
- Modern web browser with JavaScript enabled
- Internet connection for remote access

## Installation Steps

1. Database Setup
```sql
-- Create database
CREATE DATABASE smart_home;
USE smart_home;

-- Import base schema
mysql -u your_user -p smart_home < smart_home.sql

-- Import user tables
mysql -u your_user -p smart_home < users.sql

-- Import logging tables
mysql -u your_user -p smart_home < logs.sql
```

2. Configuration
```php
// Copy sample config
cp config.sample.php config.php

// Edit config.php with your settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_home');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('JWT_SECRET', 'generate_random_secret');
```

3. File Permissions
```bash
# Create and set permissions for backup directory
mkdir backups
chmod 755 backups

# Set permissions for log directory
chmod 755 logs
```

4. Web Server Configuration

Apache (.htaccess):
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

Nginx:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

5. Initial Setup
- Access the system at http://your-domain/
- Default admin credentials:
  - Username: admin
  - Password: admin123
- **IMPORTANT**: Change admin password immediately after first login

## Security Recommendations

1. File Permissions
```bash
chmod 644 config.php
chmod 644 *.sql
chmod 755 public/
chmod 755 api/
```

2. SSL/TLS Setup
- Install SSL certificate
- Force HTTPS in .htaccess:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

3. Firewall Configuration
- Allow ports 80/443 for web traffic
- Restrict MySQL port (3306) to localhost
- Configure fail2ban for SSH/HTTP

## Maintenance

### Regular Tasks
1. Database backup (automated daily)
2. Log rotation (automated weekly)
3. Check system logs for errors
4. Update PHP and MySQL regularly

### Backup Strategy
- Daily automated backups
- Keep last 30 days of backups
- Test restore procedure monthly

## Troubleshooting

### Common Issues

1. Database Connection Failed
- Check database credentials in config.php
- Verify MySQL service is running
- Check MySQL user permissions

2. API Errors
- Verify API key format
- Check server logs for detailed errors
- Confirm proper JSON request format

3. Permission Issues
- Check file/folder permissions
- Verify web server user has required access
- Check SELinux settings if applicable

### Support Contacts

For technical support:
- Email: support@smarthome.example.com
- Documentation: /docs/
- Issue tracker: github.com/your-repo/issues