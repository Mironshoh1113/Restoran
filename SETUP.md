# O'rnatish talimatlari

## 1. Muhit sozlamalari

### PHP talablari
- PHP 8.2+ o'rnatilgan bo'lishi kerak
- mbstring extension o'rnatilgan bo'lishi kerak

### MySQL o'rnatish
```bash
# MySQL server o'rnatish (Windows uchun)
# https://dev.mysql.com/downloads/installer/ dan yuklab oling

# Yoki XAMPP/WAMP o'rnatish
# https://www.apachefriends.org/download.html
```

## 2. Loyihani o'rnatish

### Dependensiyalarni o'rnatish
```bash
composer install
npm install
```

### Muhit sozlamalari
```bash
cp .env.example .env
php artisan key:generate
```

### Ma'lumotlar bazasini sozlash
`.env` faylida quyidagi sozlamalarni yangilang:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_order_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Ma'lumotlar bazasini yaratish
MySQL da yangi ma'lumotlar bazasini yarating:
```sql
CREATE DATABASE restaurant_order_system;
```

### Migratsiyalarni ishga tushirish
```bash
php artisan migrate
```

### Test ma'lumotlarini qo'shish
```bash
php artisan db:seed
```

### Frontend assetlarni build qilish
```bash
npm run build
```

## 3. Telegram bot sozlash

### Bot yaratish
1. Telegram da @BotFather ga murojaat qiling
2. `/newbot` buyrug'ini yuboring
3. Bot nomi va username ni kiriting
4. Bot token ni saqlang

### Webhook sozlash
`.env` faylida bot sozlamalarini qo'shing:
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_BOT_USERNAME=your_bot_username
TELEGRAM_WEBHOOK_URL=https://your-domain.com/telegram/webhook
```

### Webhook URL ni sozlash
```bash
# Local development uchun ngrok ishlatish
ngrok http 8000

# Webhook URL ni sozlash
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
     -H "Content-Type: application/json" \
     -d '{"url": "https://your-ngrok-url.ngrok.io/telegram/webhook"}'
```

## 4. Test foydalanuvchilari

Seeder orqali quyidagi test foydalanuvchilari yaratiladi:

### Super Admin
- Email: admin@example.com
- Password: password

### Restaurant Manager
- Email: manager@example.com
- Password: password

### Courier
- Email: courier@example.com
- Password: password

## 5. Loyihani ishga tushirish

### Development server
```bash
php artisan serve
```

### Production uchun
```bash
# Nginx/Apache sozlash
# SSL sertifikat o'rnatish
# Queue worker ishga tushirish
php artisan queue:work
```

## 6. Muammolar va yechimlar

### mb_split funksiyasi yo'q
```bash
# PHP mbstring extension ni o'rnatish
# Windows: php.ini faylida extension=mbstring ni yoqish
# Linux: sudo apt-get install php-mbstring
```

### Migratsiya xatolari
```bash
php artisan migrate:fresh --seed
```

### Composer xatolari
```bash
composer dump-autoload
composer clear-cache
```

## 7. Xavfsizlik sozlamalari

### Production uchun
- `.env` faylida `APP_DEBUG=false` qiling
- Kuchli parollar ishlating
- SSL sertifikat o'rnating
- Firewall sozlang

### Telegram bot xavfsizligi
- Bot token ni maxfiy saqlang
- Webhook URL ni HTTPS da ishlating
- Rate limiting sozlang

## 8. Monitoring va loglar

### Log fayllarini kuzatish
```bash
tail -f storage/logs/laravel.log
```

### Queue monitoring
```bash
php artisan queue:work --verbose
```

## 9. Backup va restore

### Ma'lumotlar bazasini backup qilish
```bash
mysqldump -u root -p restaurant_order_system > backup.sql
```

### Backup dan restore qilish
```bash
mysql -u root -p restaurant_order_system < backup.sql
``` 