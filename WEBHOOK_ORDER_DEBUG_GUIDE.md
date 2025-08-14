# Webhook va Buyurtma Debug Qo'llanmasi

## 🎯 **Muammolar va yechimlar:**

### ❌ **1. Buyurtma berish ishlamayapti**
### ❌ **2. Telegram webhook javob bermayapti**

## 🔧 **Qo'shilgan debug vositalari:**

### **1. Buyurtma debug:**
- ✅ Debug API route: `/api/debug-orders`
- ✅ Debug tugma Web App da
- ✅ Console logging
- ✅ Order model ga `bot_token` field qo'shildi

### **2. Webhook debug:**
- ✅ Debug webhook route: `/api/debug-webhook/{token}`
- ✅ Webhook setup script: `setup_webhook_debug.php`
- ✅ Test webhook setup: `/api/test-webhook-setup`

## 🧪 **Test qilish bosqichlari:**

### **1. Buyurtma debug testi:**

1. **Web App ni oching:**
   ```
   http://your-domain.com/test-enhanced-web-interface
   ```

2. **Taomlarni tanlang** (savat to'lsin)

3. **"Debug Test" tugmasini bosing** (savat ostida paydo bo'ladi)

4. **Console ni oching** (F12) va natijalarni ko'ring:
   ```javascript
   🐛 DEBUG: Testing order data...
   🐛 DEBUG: Response status: 200
   🐛 DEBUG: Response data: {...}
   ```

5. **Kutilayotgan natijalar:**
   - ✅ `Debug test passed! Order data is valid.`
   - ❌ `Debug test failed: [error message]`

### **2. Webhook debug testi:**

1. **Webhook URLs ni olish:**
   ```
   http://your-domain.com/api/test-webhook-setup
   ```

2. **Webhook setup script ishlatish:**
   ```bash
   php setup_webhook_debug.php YOUR_BOT_TOKEN
   ```

3. **Manual webhook test:**
   ```bash
   curl -X POST http://your-domain.com/api/debug-webhook/YOUR_BOT_TOKEN \
   -H "Content-Type: application/json" \
   -d '{"test": "data"}'
   ```

## 🔍 **Xatoliklarni aniqlash:**

### **Buyurtma xatoliklari:**

#### **❌ "Restaurant not found"**
- **Sabab**: Restaurant ID noto'g'ri yoki restaurant mavjud emas
- **Yechim**: Database da restaurant mavjudligini tekshiring

#### **❌ "Restaurant is not active"**
- **Sabab**: Restaurant `is_active = false`
- **Yechim**: Admin panelda restaurant ni active qiling

#### **❌ "Bot token mismatch"**
- **Sabab**: Web App da bot token noto'g'ri
- **Yechim**: Restaurant sozlamalarida bot token ni tekshiring

#### **❌ "Some menu items not found"**
- **Sabab**: Menu item ID lar noto'g'ri
- **Yechim**: Database da menu items mavjudligini tekshiring

### **Webhook xatoliklari:**

#### **❌ "Webhook endpoint is not reachable"**
- **Sabab**: Server yoki SSL muammosi
- **Yechim**: 
  1. Domain HTTPS bilan ishlayotganini tekshiring
  2. SSL certificate yaroqli ekanini tekshiring
  3. Server ishlab turganini tekshiring

#### **❌ "Restaurant not found for token"**
- **Sabab**: Bot token database da mavjud emas
- **Yechim**: Admin panelda bot token ni to'g'ri kiriting

## 📱 **Test URL lari:**

### **Web App:**
```
http://your-domain.com/test-enhanced-web-interface
http://your-domain.com/enhanced-web-interface?bot_token=YOUR_BOT_TOKEN
```

### **API Debug:**
```
POST /api/debug-orders
POST /api/debug-webhook/{token}
GET /api/test-webhook-setup
```

### **Admin Panel:**
```
http://your-domain.com/admin/restaurants
http://your-domain.com/admin/orders
```

## 🐛 **Debug buyruqlari:**

### **1. Buyurtma debug:**
```javascript
// Browser Console da
fetch('/api/debug-orders', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    restaurant_id: 1,
    items: [{menu_item_id: 1, quantity: 1, price: 10000}],
    total_amount: 10000,
    bot_token: 'YOUR_BOT_TOKEN'
  })
}).then(r => r.json()).then(console.log)
```

### **2. Webhook debug:**
```bash
# Webhook ma'lumotini olish
curl https://api.telegram.org/botYOUR_BOT_TOKEN/getWebhookInfo

# Webhook o'rnatish
curl -X POST https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook \
-d "url=https://your-domain.com/api/telegram-webhook/YOUR_BOT_TOKEN"

# Webhook test
curl -X POST https://your-domain.com/api/debug-webhook/YOUR_BOT_TOKEN \
-H "Content-Type: application/json" \
-d '{"message": {"text": "/start"}}'
```

### **3. Server logs:**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Apache/Nginx logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

## 📊 **Kutilayotgan natijalar:**

### **✅ Buyurtma debug muvaffaqiyatli:**
```json
{
  "success": true,
  "message": "All checks passed - ready to create order",
  "restaurant": {"id": 1, "name": "Test Restaurant", "is_active": true},
  "menu_items": {"1": "Pizza", "2": "Burger"},
  "total_items": 2,
  "total_amount": 25000
}
```

### **✅ Webhook debug muvaffaqiyatli:**
```json
{
  "success": true,
  "message": "Webhook debug successful",
  "restaurant": {"id": 1, "name": "Test Restaurant", "is_active": true},
  "token_match": true,
  "received_data": {...}
}
```

## 🚀 **Qadamlar:**

### **1. Buyurtma muammosini hal qilish:**
1. Web App da "Debug Test" tugmasini bosing
2. Console da xatolikni ko'ring
3. Xatolikni yuqoridagi ro'yxatdan toping
4. Yechimni qo'llang
5. Qaytadan test qiling

### **2. Webhook muammosini hal qilish:**
1. `php setup_webhook_debug.php YOUR_BOT_TOKEN` ishlatng
2. Webhook URL ni tekshiring
3. SSL certificate ni tekshiring
4. Server logs ni ko'ring
5. Bot ga /start yuboring

---

**🎉 Debug vositalari tayyor!**

**Endi muammolarni aniq aniqlash va hal qilish mumkin!**

**Test qilib, natijalarni xabar bering!** 🚀 