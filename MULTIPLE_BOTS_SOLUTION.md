# Multiple Bots Solution

## Muammo
Telegram botlarida 1 ta akkauntga ko'p botlar qo'shilganda faqat 1 tasi ishlaydi, qolganlari ishlamaydi.

## Yechim
Sistema to'liq qayta ishlab chiqildi va ko'p botlarni qo'llab-quvvatlash uchun yangi funksiyalar qo'shildi.

## Yangi Funksiyalar

### 1. Ko'p Botlarni Boshqarish
- **Bulk operatsiyalar**: Bir vaqtda ko'p botlarni boshqarish
- **Statistikalar**: Barcha botlar bo'yicha statistikalar
- **Test qilish**: Bir vaqtda ko'p botlarni test qilish
- **Webhook o'rnatish**: Ko'p botlar uchun webhook o'rnatish

### 2. Foydalanuvchilarni Boshqarish
- **Barcha foydalanuvchilar**: Barcha botlardagi foydalanuvchilarni ko'rish
- **Filtrlash**: Restoran, qidirish va holat bo'yicha filtrlash
- **Bulk xabar yuborish**: Ko'p foydalanuvchilarga xabar yuborish
- **Individual xabar yuborish**: Har bir foydalanuvchiga alohida xabar

### 3. Yangi API Endpointlar

#### Bot Statistikalarini Olish
```
GET /admin/bots/stats/all
```

#### Ko'p Botlarni Test Qilish
```
POST /admin/bots/test-multiple
{
    "restaurant_ids": [1, 2, 3]
}
```

#### Ko'p Botlarga Xabar Yuborish
```
POST /admin/bots/send-multiple
{
    "restaurant_ids": [1, 2, 3],
    "message": "Xabar matni",
    "user_ids": [123, 456] // ixtiyoriy
}
```

#### Barcha Foydalanuvchilarni Olish
```
GET /admin/bots/users/all?restaurant_id=1&search=ism&status=active
```

#### Ko'p Webhook O'rnatish
```
POST /admin/bots/set-webhooks-multiple
{
    "restaurant_ids": [1, 2, 3]
}
```

## Qanday Ishlatish

### 1. Bot Sozlamalari Sahifasi
- `/admin/bots` sahifasiga o'ting
- Har bir bot uchun checkbox qo'shildi
- "Bulk operatsiyalar" tugmasini bosing
- Kerakli botlarni tanlang va operatsiyani tanlang

### 2. Barcha Foydalanuvchilar
- `/admin/bots/users/all` sahifasiga o'ting
- Filtrlardan foydalanib foydalanuvchilarni qidiring
- Tanlangan foydalanuvchilarga xabar yuboring

### 3. Statistikalar
- Sahifa yuklanganda avtomatik yuklanadi
- Jami botlar, faol botlar, foydalanuvchilar va xabarlar soni

## Texnik Tafsilotlar

### Database Strukturasi
```sql
-- Restoranlar jadvali
restaurants:
- id
- name
- bot_token
- bot_username
- owner_user_id

-- Telegram foydalanuvchilar
telegram_users:
- id
- restaurant_id (har bir foydalanuvchi aniq restoranga tegishli)
- telegram_id
- username
- first_name
- last_name
- is_active
- last_activity

-- Telegram xabarlar
telegram_messages:
- id
- telegram_user_id
- message_text
- is_from_bot
- created_at
```

### Webhook Tizimi
Har bir bot o'ziga tegishli webhook URL-ga ega:
```
POST /telegram/webhook/{bot_token}
```

### Foydalanuvchi Boshqaruvi
- Har bir foydalanuvchi aniq restoranga tegishli
- Ko'p botlar uchun alohida foydalanuvchi ro'yxati
- Har bir bot o'z foydalanuvchilarini boshqaradi

## Xavfsizlik

### Ruxsatlar
- Super admin: barcha restoranlarni ko'ra oladi
- Oddiy admin: faqat o'z restoranlarini ko'ra oladi
- Har bir operatsiya ruxsatlar tekshiriladi

### CSRF Himoyasi
- Barcha POST so'rovlar CSRF token bilan himoyalangan
- XSS va SQL injection himoyasi

## Monitoring va Logging

### Loglar
- Barcha bot operatsiyalari loglanadi
- Xatoliklar alohida loglanadi
- Foydalanuvchi faolligi kuzatiladi

### Monitoring
- Bot holati real vaqtda kuzatiladi
- Webhook holati tekshiriladi
- Foydalanuvchi statistikasi

## O'rnatish

### 1. Migrationlarni Ishga Tushiring
```bash
php artisan migrate
```

### 2. Yangi Routelarni Qo'shing
```bash
# Routes avtomatik yuklanadi
```

### 3. Bot Tokenlarini O'rnating
- Har bir restoran uchun bot token kiriting
- Webhook URL-larni o'rnating

### 4. Test Qiling
- Botlarni test qiling
- Foydalanuvchilarga xabar yuboring
- Statistikani tekshiring

## Muammolarni Hal Qilish

### Bot Ishlamaydi
1. Bot token to'g'ri ekanligini tekshiring
2. Webhook URL to'g'ri o'rnatilganini tekshiring
3. Bot test qiling

### Foydalanuvchilar Ko'rinmaydi
1. Foydalanuvchilar bot bilan suhbatlashganini tekshiring
2. Database-da foydalanuvchilar mavjudligini tekshiring
3. Filtrlarni tozalang

### Xabar Yuborilmaydi
1. Bot token to'g'ri ekanligini tekshiring
2. Foydalanuvchi botni bloklashini tekshiring
3. Xabar matnini tekshiring

## Yangi Funksiyalar

### 1. Real-time Monitoring
- Bot holati real vaqtda kuzatiladi
- Foydalanuvchi faolligi kuzatiladi
- Xabar yuborish natijalari kuzatiladi

### 2. Avtomatik Backup
- Foydalanuvchi ma'lumotlari avtomatik saqlanadi
- Xabarlar avtomatik arxivlanadi
- Statistikalar avtomatik hisoblanadi

### 3. Ko'p Til Dasturi
- O'zbek tilida to'liq interfeys
- Xabarlar o'zbek tilida
- Tizim o'zbek tilida ishlaydi

## Natija

Endi sistema:
- ✅ Ko'p botlarni to'liq qo'llab-quvvatlaydi
- ✅ Har bir bot o'z foydalanuvchilarini boshqaradi
- ✅ Bulk operatsiyalar mavjud
- ✅ Real-time monitoring
- ✅ Xavfsizlik himoyasi
- ✅ Oson boshqarish interfeysi

Barcha botlar endi to'liq ishlaydi va bir-biriga ta'sir qilmaydi! 