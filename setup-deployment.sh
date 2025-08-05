#!/bin/bash

# Laravel Auto Deployment Setup Script
# Bu script serverda deployment tizimini o'rnatishga yordam beradi

echo "🚀 Laravel Auto Deployment Setup"
echo "================================"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Bu script root huquqlari bilan ishga tushirilishi kerak"
    echo "sudo bash setup-deployment.sh"
    exit 1
fi

# Set variables
PROJECT_DIR="/var/www/restaurant-order-system"
BACKUP_DIR="/var/www/backups"
LOG_DIR="/var/log"

echo "📁 Loyiha papkasi: $PROJECT_DIR"
echo "💾 Backup papkasi: $BACKUP_DIR"
echo "📝 Log papkasi: $LOG_DIR"

# Create necessary directories
echo "📂 Papkalarni yaratish..."
mkdir -p $BACKUP_DIR
mkdir -p $LOG_DIR

# Create log files
echo "📝 Log fayllarini yaratish..."
touch $LOG_DIR/deploy.log
touch $LOG_DIR/webhook.log

# Set permissions
echo "🔐 Huquqlarni o'rnatish..."
chmod 666 $LOG_DIR/deploy.log
chmod 666 $LOG_DIR/webhook.log
chmod -R 755 $PROJECT_DIR
chown -R www-data:www-data $PROJECT_DIR

# Make deploy script executable
echo "🔧 Deployment scriptini executable qilish..."
chmod +x $PROJECT_DIR/deploy.sh

# Generate webhook secret
echo "🔑 Webhook secret key yaratish..."
SECRET_KEY=$(openssl rand -hex 32)
echo "Generated Secret Key: $SECRET_KEY"
echo "Bu keyni .env faylidagi WEBHOOK_SECRET o'zgaruvchisiga qo'shing"

# Create .env backup
if [ -f "$PROJECT_DIR/.env" ]; then
    echo "💾 .env faylini backup qilish..."
    cp $PROJECT_DIR/.env $PROJECT_DIR/.env.backup
fi

# Add webhook secret to .env
echo "📝 .env fayliga webhook secret qo'shish..."
if [ -f "$PROJECT_DIR/.env" ]; then
    if grep -q "WEBHOOK_SECRET" $PROJECT_DIR/.env; then
        sed -i "s/WEBHOOK_SECRET=.*/WEBHOOK_SECRET=$SECRET_KEY/" $PROJECT_DIR/.env
    else
        echo "WEBHOOK_SECRET=$SECRET_KEY" >> $PROJECT_DIR/.env
    fi
else
    echo "❌ .env fayli topilmadi. Qo'lda yarating va WEBHOOK_SECRET=$SECRET_KEY qo'shing"
fi

# Test deployment script
echo "🧪 Deployment scriptini test qilish..."
if [ -f "$PROJECT_DIR/deploy.sh" ]; then
    echo "✅ deploy.sh fayli mavjud"
else
    echo "❌ deploy.sh fayli topilmadi"
fi

# Check PHP and required extensions
echo "🔍 PHP va kerakli extensionlarni tekshirish..."
if command -v php &> /dev/null; then
    echo "✅ PHP o'rnatilgan: $(php -v | head -n1)"
    
    # Check required extensions
    php -m | grep -q "openssl" && echo "✅ OpenSSL extension" || echo "❌ OpenSSL extension yo'q"
    php -m | grep -q "curl" && echo "✅ cURL extension" || echo "❌ cURL extension yo'q"
else
    echo "❌ PHP o'rnatilmagan"
fi

# Check Composer
echo "🔍 Composer tekshirish..."
if command -v composer &> /dev/null; then
    echo "✅ Composer o'rnatilgan: $(composer --version | head -n1)"
else
    echo "❌ Composer o'rnatilmagan"
fi

# Check Node.js and NPM
echo "🔍 Node.js va NPM tekshirish..."
if command -v node &> /dev/null; then
    echo "✅ Node.js o'rnatilgan: $(node --version)"
else
    echo "❌ Node.js o'rnatilmagan"
fi

if command -v npm &> /dev/null; then
    echo "✅ NPM o'rnatilgan: $(npm --version)"
else
    echo "❌ NPM o'rnatilmagan"
fi

# Check Git
echo "🔍 Git tekshirish..."
if command -v git &> /dev/null; then
    echo "✅ Git o'rnatilgan: $(git --version)"
else
    echo "❌ Git o'rnatilmagan"
fi

# Check web server
echo "🔍 Web server tekshirish..."
if command -v nginx &> /dev/null; then
    echo "✅ Nginx o'rnatilgan: $(nginx -v 2>&1)"
elif command -v apache2 &> /dev/null; then
    echo "✅ Apache2 o'rnatilgan"
else
    echo "❌ Web server topilmadi"
fi

echo ""
echo "🎉 Setup tugallandi!"
echo ""
echo "📋 Keyingi qadamlar:"
echo "1. Git repositoryda webhook sozlang:"
echo "   URL: https://yourdomain.com/webhook"
echo "   Secret: $SECRET_KEY"
echo ""
echo "2. Nginx konfiguratsiyasini yangilang"
echo "3. Webhook test qiling"
echo ""
echo "📚 Qo'shimcha ma'lumot uchun DEPLOYMENT_SETUP.md faylini o'qing" 