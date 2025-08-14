# 10MB Upload Limit Setup Guide

## Overview
This guide explains how to configure your server to allow 10MB file uploads for the restaurant management system.

## 🔧 PHP Configuration

### Method 1: .htaccess File (Apache)
The `.htaccess` file in your `public/` directory already contains the necessary configuration:

```apache
# PHP Upload Limits - 10MB
<IfModule mod_php.c>
    php_value upload_max_filesize 10M
    php_value post_max_size 10M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
</IfModule>
```

### Method 2: php.ini File
A `php.ini` file has been created in your project root with these settings:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

### Method 3: Server php.ini (Recommended)
For production servers, modify the main `php.ini` file:

```bash
# Find php.ini location
php --ini

# Edit the file
sudo nano /etc/php/8.1/apache2/php.ini
# or
sudo nano /etc/php/8.1/fpm/php.ini
```

Add/modify these lines:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

## 🚀 Server-Specific Configuration

### Apache Server
1. **Enable mod_php**: Ensure PHP module is loaded
2. **Restart Apache**: `sudo systemctl restart apache2`
3. **Check configuration**: `php -i | grep upload_max_filesize`

### Nginx + PHP-FPM
1. **Edit php.ini**: Modify PHP-FPM configuration
2. **Restart PHP-FPM**: `sudo systemctl restart php8.1-fpm`
3. **Restart Nginx**: `sudo systemctl restart nginx`

### Shared Hosting
1. **Contact hosting provider** to increase limits
2. **Use .htaccess method** if allowed
3. **Check hosting control panel** for PHP settings

## 📱 Laravel Configuration

### Validation Rules
The controller now includes proper validation:

```php
$request->validate([
    'logo' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB = 10240 KB
]);
```

### File Storage
Files are stored in organized directories:
- Logos: `storage/app/public/restaurants/logos/`
- Bot Images: `storage/app/public/restaurants/bot-images/`

## 🔍 Testing Upload Limits

### Test File Upload
1. Create a test image larger than 5MB but smaller than 10MB
2. Try uploading through the admin panel
3. Check for validation errors

### Check Current Limits
Add this route to test current PHP settings:

```php
Route::get('/test-upload-limits', function() {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
    ]);
});
```

## ⚠️ Common Issues & Solutions

### Issue: "File too large" Error
**Solution**: Check if `post_max_size` is larger than `upload_max_filesize`

### Issue: "Memory limit exceeded"
**Solution**: Increase `memory_limit` to at least 256M

### Issue: "Request timeout"
**Solution**: Increase `max_execution_time` and `max_input_time`

### Issue: .htaccess not working
**Solution**: 
1. Ensure Apache has `AllowOverride All`
2. Check if mod_php is enabled
3. Verify file permissions

## 🛡️ Security Considerations

### File Type Validation
- Only allow image files: `jpeg`, `png`, `jpg`, `gif`
- Validate file content, not just extension
- Scan for malware if possible

### File Size Limits
- 10MB is reasonable for high-quality images
- Consider image compression for better performance
- Monitor storage usage

### Access Control
- Restrict uploads to authenticated users
- Validate file ownership
- Use secure file storage paths

## 📊 Performance Optimization

### Image Processing
Consider implementing:
- Automatic image resizing
- WebP format conversion
- Lazy loading for large images
- CDN integration

### Storage Optimization
- Regular cleanup of unused files
- Image compression
- Backup strategies

## 🔧 Troubleshooting

### Check PHP Configuration
```bash
# Check current settings
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"

# Check via web
<?php phpinfo(); ?>
```

### Check Server Logs
```bash
# Apache error logs
sudo tail -f /var/log/apache2/error.log

# PHP error logs
sudo tail -f /var/log/php8.1-fpm.log
```

### Test Upload Functionality
1. **Small file** (< 1MB): Should work
2. **Medium file** (1-5MB): Should work
3. **Large file** (5-10MB): Should work
4. **Very large file** (> 10MB): Should fail with validation error

## 📋 Configuration Checklist

- [ ] PHP `upload_max_filesize` set to 10M
- [ ] PHP `post_max_size` set to 10M
- [ ] PHP `memory_limit` set to 256M
- [ ] PHP `max_execution_time` set to 300
- [ ] PHP `max_input_time` set to 300
- [ ] Laravel validation rules updated
- [ ] .htaccess file configured (Apache)
- [ ] Server restarted after changes
- [ ] Upload functionality tested
- [ ] Error handling implemented

## 🚀 Deployment Notes

### Development Environment
- Use `.htaccess` method for quick testing
- Monitor error logs for issues
- Test with various file sizes

### Production Environment
- Modify server `php.ini` directly
- Use proper file permissions
- Implement monitoring and alerts
- Regular backup of uploaded files

### Staging Environment
- Test all configurations before production
- Verify with real-world file sizes
- Performance testing with large uploads

---

**Last Updated**: {{ date('Y-m-d H:i:s') }}
**Version**: 1.0.0
**Status**: Ready for Implementation 