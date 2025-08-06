# Telegram Webhook Setup Guide

Bu qo'llanma Telegram bot webhook tizimini to'g'ri sozlash va ishlatish uchun kerakli ma'lumotlarni o'z ichiga oladi.

## 1. Webhook URL Formatlari

Sistemada ikkita webhook implementatsiyasi mavjud:

### 1.1. Direct PHP Webhook
- **URL Format**: `/telegram-webhook/{token}`
- **Fayl**: `public/telegram-webhook.php`
- **Misol**: `https://yourdomain.com/telegram-webhook/1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`

### 1.2. Laravel Route Webhook
- **URL Format**: `/telegram/webhook/{token}`
- **Controller**: `TelegramController`
- **Misol**: `https://yourdomain.com/telegram/webhook/1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`

## 2. Admin Panelida Webhook Sozlash

### 2.1. Bot Token Olish
1. Telegram da @BotFather ga o'ting
2. `/newbot` buyrug'ini yuboring
3. Bot nomini kiriting
4. Bot username ni kiriting (oxiri 'bot' bilan tugashi kerak)
5. Bot token ni saqlang

### 2.2. Admin Panelida Sozlash
1. Admin paneliga o'ting
2. "Bots" bo'limiga o'ting
3. Restoran tanlang
4. Bot token ni kiriting
5. Webhook URL avtomatik yaratiladi
6. "Saqlash" tugmasini bosing

## 3. Webhook URL Manzilini Telegram ga O'rnatish

### 3.1. Avtomatik O'rnatish
Admin panelida "Saqlash" tugmasini bosganingizda webhook avtomatik o'rnatiladi.

### 3.2. Qo'lda O'rnatish
```bash
# Telegram API orqali webhook o'rnatish
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://yourdomain.com/telegram-webhook/YOUR_BOT_TOKEN"
  }'
```

### 3.3. Webhook Holatini Tekshirish
```bash
# Webhook holatini ko'rish
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"
```

## 4. Bot Funksiyalari

### 4.1. Asosiy Buyruqlar
- `/start` - Botni ishga tushirish
- `📊 Buyurtmalarim` - Buyurtmalarni ko'rish
- `ℹ Yordam` - Yordam olish

### 4.2. Callback Querylar
- `refresh_orders` - Buyurtmalarni yangilash
- `contact_admin` - Admin bilan bog'lanish

## 5. Xavfsizlik

### 5.1. Token Xavfsizligi
- Bot token ni hech kimga bermang
- Token ni environment variable da saqlang
- Muntazam yangilang

### 5.2. Webhook Xavfsizligi
- HTTPS ishlating
- IP cheklash qo'shing (ixtiyoriy)
- Rate limiting qo'shing

## 6. Log va Monitoring

### 6.1. Log Fayllari
```bash
# Laravel log fayllari
tail -f storage/logs/laravel.log

# Webhook log
tail -f storage/logs/telegram-webhook.log
```

### 6.2. Monitoring
```bash
# Webhook holatini tekshirish
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"

# Bot holatini tekshirish
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getMe"
```

## 7. Muammolarni Hal Qilish

### 7.1. Webhook O'rnatilmaydi
```bash
# 1. Bot token to'g'ri ekanligini tekshiring
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getMe"

# 2. URL to'g'ri ekanligini tekshiring
curl -I "https://yourdomain.com/telegram-webhook/YOUR_BOT_TOKEN"

# 3. SSL sertifikat to'g'ri ekanligini tekshiring
openssl s_client -connect yourdomain.com:443
```

### 7.2. Xabarlar Kelmaydi
```bash
# 1. Webhook holatini tekshiring
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"

# 2. Log fayllarini tekshiring
tail -f storage/logs/laravel.log

# 3. Database ulanishini tekshiring
php artisan tinker
>>> App\Models\Restaurant::where('bot_token', 'YOUR_BOT_TOKEN')->first();
```

### 7.3. Bot Javob Bermaydi
```bash
# 1. Bot token to'g'ri ekanligini tekshiring
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getMe"

# 2. Test xabar yuboring
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/sendMessage" \
  -H "Content-Type: application/json" \
  -d '{
    "chat_id": "YOUR_CHAT_ID",
    "text": "Test xabar"
  }'
```

## 8. Test Qilish

### 8.1. Webhook Test
```bash
# Test payload yarating
curl -X POST "https://yourdomain.com/telegram-webhook/YOUR_BOT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "update_id": 123456789,
    "message": {
      "message_id": 1,
      "from": {
        "id": 123456789,
        "first_name": "Test",
        "username": "testuser"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1234567890,
      "text": "/start"
    }
  }'
```

### 8.2. Bot Test
```bash
# Bot test xabarini yuborish
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/sendMessage" \
  -H "Content-Type: application/json" \
  -d '{
    "chat_id": "YOUR_CHAT_ID",
    "text": "Test xabar",
    "parse_mode": "HTML"
  }'
```

## 9. Production Sozlamalari

### 9.1. Environment Variables
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/telegram-webhook
TELEGRAM_DEBUG=false
```

### 9.2. Nginx Konfiguratsiyasi
```nginx
location /telegram-webhook {
    try_files $uri $uri/ =404;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    
    # Rate limiting
    limit_req zone=telegram burst=10 nodelay;
}

# Rate limiting zone
limit_req_zone $binary_remote_addr zone=telegram:10m rate=10r/s;
```

### 9.3. SSL Sertifikat
```bash
# Let's Encrypt orqali SSL o'rnatish
sudo certbot --nginx -d yourdomain.com
```

## 10. Foydali Komandalar

### 10.1. Webhook Boshqaruvi
```bash
# Webhook o'rnatish
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://yourdomain.com/telegram-webhook/YOUR_BOT_TOKEN"}'

# Webhook o'chirish
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/deleteWebhook"

# Webhook holatini ko'rish
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getWebhookInfo"
```

### 10.2. Bot Boshqaruvi
```bash
# Bot ma'lumotlarini olish
curl "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/getMe"

# Bot buyruqlarini o'rnatish
curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setMyCommands" \
  -H "Content-Type: application/json" \
  -d '{
    "commands": [
      {"command": "start", "description": "Botni ishga tushirish"},
      {"command": "help", "description": "Yordam"}
    ]
  }'
```

## 11. Support

Agar muammolar bo'lsa:
1. Log fayllarini tekshiring
2. Bot token to'g'ri ekanligini tekshiring
3. Webhook URL to'g'ri ekanligini tekshiring
4. SSL sertifikat to'g'ri ekanligini tekshiring
5. Database ulanishini tekshiring 