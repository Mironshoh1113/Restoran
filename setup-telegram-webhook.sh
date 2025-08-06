#!/bin/bash

# Telegram Webhook Setup Script
# Bu script Telegram webhook ni o'rnatish va sozlash uchun ishlatiladi

echo "🚀 Telegram Webhook Setup Script"
echo "================================"
echo ""

# Ranglar
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Xatolik funksiyasi
error() {
    echo -e "${RED}❌ Xatolik: $1${NC}"
    exit 1
}

# Muvaffaqiyat funksiyasi
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Ogohlantirish funksiyasi
warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Ma'lumot funksiyasi
info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Sozlamalarni o'qish
read -p "Bot token ni kiriting: " BOT_TOKEN
read -p "Domain manzilini kiriting (masalan: yourdomain.com): " DOMAIN
read -p "Chat ID ni kiriting (test uchun): " CHAT_ID

# Sozlamalarni tekshirish
if [ -z "$BOT_TOKEN" ]; then
    error "Bot token kiritilmagan"
fi

if [ -z "$DOMAIN" ]; then
    error "Domain kiritilmagan"
fi

if [ -z "$CHAT_ID" ]; then
    error "Chat ID kiritilmagan"
fi

# Webhook URL yaratish
WEBHOOK_URL="https://$DOMAIN/telegram-webhook/$BOT_TOKEN"

info "Webhook URL: $WEBHOOK_URL"

# 1. Bot token ni tekshirish
echo ""
info "1. Bot token ni tekshirish..."
BOT_INFO=$(curl -s "https://api.telegram.org/bot$BOT_TOKEN/getMe")

if echo "$BOT_INFO" | grep -q '"ok":true'; then
    BOT_NAME=$(echo "$BOT_INFO" | grep -o '"first_name":"[^"]*"' | cut -d'"' -f4)
    success "Bot topildi: $BOT_NAME"
else
    error "Bot token noto'g'ri yoki bot mavjud emas"
fi

# 2. Webhook URL ni tekshirish
echo ""
info "2. Webhook URL ni tekshirish..."
HTTP_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$WEBHOOK_URL")

if [ "$HTTP_RESPONSE" = "200" ] || [ "$HTTP_RESPONSE" = "404" ]; then
    success "Webhook URL mavjud (HTTP: $HTTP_RESPONSE)"
else
    warning "Webhook URL ga ulanishda muammo (HTTP: $HTTP_RESPONSE)"
fi

# 3. Webhook o'rnatish
echo ""
info "3. Webhook o'rnatish..."
WEBHOOK_RESPONSE=$(curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/setWebhook" \
  -H "Content-Type: application/json" \
  -d "{\"url\": \"$WEBHOOK_URL\"}")

if echo "$WEBHOOK_RESPONSE" | grep -q '"ok":true'; then
    success "Webhook muvaffaqiyatli o'rnatildi"
else
    error "Webhook o'rnatishda xatolik: $WEBHOOK_RESPONSE"
fi

# 4. Webhook holatini tekshirish
echo ""
info "4. Webhook holatini tekshirish..."
WEBHOOK_INFO=$(curl -s "https://api.telegram.org/bot$BOT_TOKEN/getWebhookInfo")

if echo "$WEBHOOK_INFO" | grep -q '"ok":true'; then
    success "Webhook holati to'g'ri"
    echo "Webhook ma'lumotlari:"
    echo "$WEBHOOK_INFO" | python3 -m json.tool 2>/dev/null || echo "$WEBHOOK_INFO"
else
    warning "Webhook holatini tekshirishda muammo"
fi

# 5. Test xabar yuborish
echo ""
info "5. Test xabar yuborish..."
TEST_MESSAGE="🧪 Test xabar - $(date '+%Y-%m-%d %H:%M:%S')"
SEND_RESPONSE=$(curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
  -H "Content-Type: application/json" \
  -d "{
    \"chat_id\": \"$CHAT_ID\",
    \"text\": \"$TEST_MESSAGE\",
    \"parse_mode\": \"HTML\"
  }")

if echo "$SEND_RESPONSE" | grep -q '"ok":true'; then
    success "Test xabar muvaffaqiyatli yuborildi"
else
    warning "Test xabar yuborishda muammo: $SEND_RESPONSE"
fi

# 6. Test fayllarini yangilash
echo ""
info "6. Test fayllarini yangilash..."

# test-telegram-webhook.php faylini yangilash
if [ -f "test-telegram-webhook.php" ]; then
    sed -i "s/YOUR_BOT_TOKEN/$BOT_TOKEN/g" test-telegram-webhook.php
    sed -i "s/YOUR_CHAT_ID/$CHAT_ID/g" test-telegram-webhook.php
    sed -i "s|yourdomain.com|$DOMAIN|g" test-telegram-webhook.php
    success "test-telegram-webhook.php fayli yangilandi"
fi

# 7. Environment faylini yangilash
echo ""
info "7. Environment faylini yangilash..."
if [ -f ".env" ]; then
    # .env faylida TELEGRAM_BOT_TOKEN ni yangilash
    if grep -q "TELEGRAM_BOT_TOKEN" .env; then
        sed -i "s/TELEGRAM_BOT_TOKEN=.*/TELEGRAM_BOT_TOKEN=$BOT_TOKEN/" .env
    else
        echo "TELEGRAM_BOT_TOKEN=$BOT_TOKEN" >> .env
    fi
    
    # .env faylida TELEGRAM_WEBHOOK_URL ni yangilash
    if grep -q "TELEGRAM_WEBHOOK_URL" .env; then
        sed -i "s|TELEGRAM_WEBHOOK_URL=.*|TELEGRAM_WEBHOOK_URL=$WEBHOOK_URL|" .env
    else
        echo "TELEGRAM_WEBHOOK_URL=$WEBHOOK_URL" >> .env
    fi
    
    success ".env fayli yangilandi"
else
    warning ".env fayli topilmadi"
fi

# 8. Natijalar
echo ""
echo "🎉 Telegram Webhook Setup Yakunlandi!"
echo "====================================="
echo ""
echo "📋 Sozlamalar:"
echo "   Bot Token: $BOT_TOKEN"
echo "   Domain: $DOMAIN"
echo "   Webhook URL: $WEBHOOK_URL"
echo "   Chat ID: $CHAT_ID"
echo ""
echo "🧪 Test qilish uchun:"
echo "   php test-telegram-webhook.php"
echo ""
echo "📖 Qo'shimcha ma'lumot uchun:"
echo "   TELEGRAM_WEBHOOK_SETUP.md faylini o'qing"
echo ""
echo "🔧 Muammolar bo'lsa:"
echo "   - Log fayllarini tekshiring: tail -f storage/logs/laravel.log"
echo "   - Webhook holatini tekshiring: curl https://api.telegram.org/bot$BOT_TOKEN/getWebhookInfo"
echo ""

# Test scriptini ishga tushirish so'ralishi
read -p "Test scriptini ishga tushirishni xohlaysizmi? (y/n): " RUN_TEST

if [ "$RUN_TEST" = "y" ] || [ "$RUN_TEST" = "Y" ]; then
    echo ""
    info "Test scriptini ishga tushirish..."
    php test-telegram-webhook.php
fi

echo ""
success "Setup yakunlandi! 🎉" 