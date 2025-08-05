# Laravel Auto Deployment Setup Guide

Bu qo'llanma sizning Laravel loyihangizni Git repositorydan avtomatik ravishda serverga deploy qilish uchun kerakli konfiguratsiyalarni o'rnatishga yordam beradi.

## 1. Serverda o'rnatish

### 1.1. Deployment scriptini o'rnatish

```bash
# Serverda loyiha papkasiga o'ting
cd /var/www/restaurant-order-system

# deploy.sh faylini executable qiling
chmod +x deploy.sh

# Webhook faylini public papkasiga ko'chiring
cp webhook.php public/
```

### 1.2. Kerakli papkalarni yarating

```bash
# Backup papkasini yarating
mkdir -p /var/www/backups

# Log papkasini yarating
touch /var/log/deploy.log
touch /var/log/webhook.log

# Log fayllariga yozish huquqini bering
chmod 666 /var/log/deploy.log
chmod 666 /var/log/webhook.log
```

### 1.3. Webhook secret key yarating

```bash
# Random secret key yarating
openssl rand -hex 32
```

Bu komandadan kelgan natijani saqlang va `webhook.php` faylidagi `YOUR_WEBHOOK_SECRET` o'rniga qo'yng.

## 2. Git Repository sozlamalari

### 2.1. GitHub/GitLab Webhook sozlash

1. Repositoryingizga o'ting
2. Settings > Webhooks bo'limiga o'ting
3. "Add webhook" tugmasini bosing
4. Quyidagi ma'lumotlarni kiriting:

**Payload URL:**
```
https://yourdomain.com/webhook.php
```

**Content type:** `application/json`

**Secret:** Yuqorida yaratgan secret key

**Events:** Push events (faqat push eventlarini tanlang)

**Branch filter:** `main` yoki `master`

### 2.2. Webhook secret key yangilash

`webhook.php` faylidagi 25-qatorda:
```php
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, 'YOUR_WEBHOOK_SECRET');
```

`YOUR_WEBHOOK_SECRET` o'rniga yuqorida yaratgan secret keyni qo'yng.

## 3. Server konfiguratsiyasi

### 3.1. Nginx konfiguratsiyasi

`/etc/nginx/sites-available/your-site` fayliga qo'shing:

```nginx
location /webhook.php {
    try_files $uri $uri/ =404;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

### 3.2. PHP sozlamalari

`/etc/php/8.1/fpm/php.ini` faylida:
```ini
allow_url_fopen = On
exec = On
```

### 3.3. User huquqlari

```bash
# www-data userga kerakli huquqlarni bering
sudo chown -R www-data:www-data /var/www/restaurant-order-system
sudo chmod -R 755 /var/www/restaurant-order-system
```

## 4. Test qilish

### 4.1. Webhook test

```bash
# Test payload yarating
curl -X POST https://yourdomain.com/webhook.php \
  -H "Content-Type: application/json" \
  -H "X-Hub-Signature-256: sha256=test" \
  -d '{"ref":"refs/heads/main"}'
```

### 4.2. Log fayllarini tekshirish

```bash
# Deployment log
tail -f /var/log/deploy.log

# Webhook log
tail -f /var/log/webhook.log
```

## 5. Xavfsizlik

### 5.1. Webhook secret key
- Kuchli secret key ishlating
- Keyni xavfsiz joyda saqlang
- Muntazam yangilang

### 5.2. IP cheklash (ixtiyoriy)
GitHub/GitLab IP manzillarini cheklash uchun Nginx konfiguratsiyasiga qo'shing:

```nginx
location /webhook.php {
    allow 192.30.252.0/22;
    allow 185.199.108.0/22;
    allow 140.82.112.0/20;
    deny all;
    # ... boshqa sozlamalar
}
```

## 6. Muammolarni hal qilish

### 6.1. Permission xatolari
```bash
sudo chown -R www-data:www-data /var/www/restaurant-order-system
sudo chmod -R 755 /var/www/restaurant-order-system
sudo chmod -R 777 storage bootstrap/cache
```

### 6.2. Composer xatolari
```bash
cd /var/www/restaurant-order-system
composer install --no-dev
```

### 6.3. NPM xatolari
```bash
cd /var/www/restaurant-order-system
npm install
npm run build
```

## 7. Monitoring

### 7.1. Log monitoring
```bash
# Real-time log monitoring
tail -f /var/log/deploy.log /var/log/webhook.log
```

### 7.2. Status checking
```bash
# Deployment status
systemctl status nginx
systemctl status php8.1-fpm
```

## 8. Backup va restore

### 8.1. Backup yaratish
```bash
cd /var/www/backups
ls -la
```

### 8.2. Restore qilish
```bash
cd /var/www/restaurant-order-system
tar -xzf /var/www/backups/backup-YYYYMMDD-HHMMSS.tar.gz
```

## 9. Foydali komandalar

```bash
# Deployment scriptini qo'lda ishga tushirish
bash /var/www/restaurant-order-system/deploy.sh

# Log fayllarini tozalash
> /var/log/deploy.log
> /var/log/webhook.log

# Permissions to'g'rilash
sudo chown -R www-data:www-data /var/www/restaurant-order-system
sudo chmod -R 755 /var/www/restaurant-order-system
```

## 10. Support

Agar muammolar bo'lsa:
1. Log fayllarini tekshiring
2. Permissions to'g'ri ekanligini tekshiring
3. Webhook secret key to'g'ri ekanligini tekshiring
4. Server xizmatlari ishlayotganini tekshiring 