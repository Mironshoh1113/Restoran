# Telegram Bot Sozlash Qo'llanmasi

## Muammo
3 ta restoran bor, lekin faqat 1 tasida bot ishlayapti. Qolgan 2 tasida botlar ishlamayapti.

## Yechim

### 1. Telegram Bot Yaratish

Har bir restoran uchun alohida bot yarating:

#### Restoran 1: "Test Restoran"
1. Telegram'da @BotFather ga boring
2. `/newbot` buyrug'ini yuboring
3. Bot nomini kiriting: "Test Restoran Bot"
4. Bot username ni kiriting: "test_restoran_bot" (yoki boshqa)
5. Bot tokenini saqlang

#### Restoran 2: "dsgdsfsg"
1. Telegram'da @BotFather ga boring
2. `/newbot` buyrug'ini yuboring
3. Bot nomini kiriting: "dsgdsfsg Bot"
4. Bot username ni kiriting: "dsgdsfsg_bot" (yoki boshqa)
5. Bot tokenini saqlang

### 2. Admin Panelda Bot Sozlash

Har bir restoran uchun:

1. Admin panelga kiring
2. "Bots" bo'limiga boring
3. Har bir restoran uchun:
   - Bot tokenini kiriting
   - Webhook URL ni o'rnating
   - Bot nomini va tavsifini to'ldiring

### 3. Webhook URL lari

Har bir bot uchun webhook URL:
```
http://your-domain.com/telegram/webhook/BOT_TOKEN
```

### 4. Test Qilish

Har bir bot uchun:
1. Botga `/start` xabarini yuboring
2. Bot javob berishini tekshiring
3. Menyu tugmalari ishlashini tekshiring

## Avtomatik Sozlash

Quyidagi script orqali barcha botlarni avtomatik sozlash mumkin:

```bash
php setup_all_bots.php
```

## Xatoliklar

Agar bot ishlamasa:
1. Bot tokenini tekshiring
2. Webhook URL ni tekshiring
3. Server loglarini tekshiring
4. Bot tokenini @BotFather da tekshiring

## Qo'shimcha Restoranlar

Yangi restoran qo'shganingizda:
1. Yangi bot yarating
2. Bot tokenini admin panelda sozlang
3. Webhook ni o'rnating
4. Test qiling 