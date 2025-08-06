@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

REM Telegram Webhook Setup Script for Windows
REM Bu script Telegram webhook ni o'rnatish va sozlash uchun ishlatiladi

echo 🚀 Telegram Webhook Setup Script
echo ================================
echo.

REM Sozlamalarni o'qish
set /p BOT_TOKEN="Bot token ni kiriting: "
set /p DOMAIN="Domain manzilini kiriting (masalan: yourdomain.com): "
set /p CHAT_ID="Chat ID ni kiriting (test uchun): "

REM Sozlamalarni tekshirish
if "%BOT_TOKEN%"=="" (
    echo ❌ Xatolik: Bot token kiritilmagan
    pause
    exit /b 1
)

if "%DOMAIN%"=="" (
    echo ❌ Xatolik: Domain kiritilmagan
    pause
    exit /b 1
)

if "%CHAT_ID%"=="" (
    echo ❌ Xatolik: Chat ID kiritilmagan
    pause
    exit /b 1
)

REM Webhook URL yaratish
set WEBHOOK_URL=https://%DOMAIN%/telegram-webhook/%BOT_TOKEN%

echo ℹ️  Webhook URL: %WEBHOOK_URL%

REM 1. Bot token ni tekshirish
echo.
echo ℹ️  1. Bot token ni tekshirish...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'https://api.telegram.org/bot%BOT_TOKEN%/getMe' -Method Get; if ($response.ok) { Write-Host '✅ Bot topildi:' $response.result.first_name } else { Write-Host '❌ Bot token noto''g''ri' } } catch { Write-Host '❌ Xatolik:' $_.Exception.Message }"

REM 2. Webhook URL ni tekshirish
echo.
echo ℹ️  2. Webhook URL ni tekshirish...
powershell -Command "try { $response = Invoke-WebRequest -Uri '%WEBHOOK_URL%' -Method Head -UseBasicParsing; Write-Host '✅ Webhook URL mavjud (HTTP:' $response.StatusCode ')' } catch { Write-Host '⚠️  Webhook URL ga ulanishda muammo' }"

REM 3. Webhook o'rnatish
echo.
echo ℹ️  3. Webhook o'rnatish...
powershell -Command "try { $body = @{ url = '%WEBHOOK_URL%' } | ConvertTo-Json; $response = Invoke-RestMethod -Uri 'https://api.telegram.org/bot%BOT_TOKEN%/setWebhook' -Method Post -Body $body -ContentType 'application/json'; if ($response.ok) { Write-Host '✅ Webhook muvaffaqiyatli o''rnatildi' } else { Write-Host '❌ Webhook o''rnatishda xatolik:' $response.description } } catch { Write-Host '❌ Xatolik:' $_.Exception.Message }"

REM 4. Webhook holatini tekshirish
echo.
echo ℹ️  4. Webhook holatini tekshirish...
powershell -Command "try { $response = Invoke-RestMethod -Uri 'https://api.telegram.org/bot%BOT_TOKEN%/getWebhookInfo' -Method Get; if ($response.ok) { Write-Host '✅ Webhook holati to''g''ri'; Write-Host 'Webhook ma''lumotlari:'; $response | ConvertTo-Json -Depth 3 } else { Write-Host '⚠️  Webhook holatini tekshirishda muammo' } } catch { Write-Host '❌ Xatolik:' $_.Exception.Message }"

REM 5. Test xabar yuborish
echo.
echo ℹ️  5. Test xabar yuborish...
for /f "tokens=1-6 delims=:., " %%a in ("%date% %time%") do set DATETIME=%%a-%%b-%%c %%d:%%e:%%f
set TEST_MESSAGE=🧪 Test xabar - %DATETIME%

powershell -Command "try { $body = @{ chat_id = '%CHAT_ID%'; text = '%TEST_MESSAGE%'; parse_mode = 'HTML' } | ConvertTo-Json; $response = Invoke-RestMethod -Uri 'https://api.telegram.org/bot%BOT_TOKEN%/sendMessage' -Method Post -Body $body -ContentType 'application/json'; if ($response.ok) { Write-Host '✅ Test xabar muvaffaqiyatli yuborildi' } else { Write-Host '⚠️  Test xabar yuborishda muammo:' $response.description } } catch { Write-Host '❌ Xatolik:' $_.Exception.Message }"

REM 6. Test fayllarini yangilash
echo.
echo ℹ️  6. Test fayllarini yangilash...

if exist "test-telegram-webhook.php" (
    powershell -Command "(Get-Content 'test-telegram-webhook.php') -replace 'YOUR_BOT_TOKEN', '%BOT_TOKEN%' -replace 'YOUR_CHAT_ID', '%CHAT_ID%' -replace 'yourdomain.com', '%DOMAIN%' | Set-Content 'test-telegram-webhook.php'"
    echo ✅ test-telegram-webhook.php fayli yangilandi
)

REM 7. Environment faylini yangilash
echo.
echo ℹ️  7. Environment faylini yangilash...
if exist ".env" (
    powershell -Command "if (Select-String -Path '.env' -Pattern 'TELEGRAM_BOT_TOKEN') { (Get-Content '.env') -replace 'TELEGRAM_BOT_TOKEN=.*', 'TELEGRAM_BOT_TOKEN=%BOT_TOKEN%' | Set-Content '.env' } else { Add-Content '.env' 'TELEGRAM_BOT_TOKEN=%BOT_TOKEN%' }"
    powershell -Command "if (Select-String -Path '.env' -Pattern 'TELEGRAM_WEBHOOK_URL') { (Get-Content '.env') -replace 'TELEGRAM_WEBHOOK_URL=.*', 'TELEGRAM_WEBHOOK_URL=%WEBHOOK_URL%' | Set-Content '.env' } else { Add-Content '.env' 'TELEGRAM_WEBHOOK_URL=%WEBHOOK_URL%' }"
    echo ✅ .env fayli yangilandi
) else (
    echo ⚠️  .env fayli topilmadi
)

REM 8. Natijalar
echo.
echo 🎉 Telegram Webhook Setup Yakunlandi!
echo =====================================
echo.
echo 📋 Sozlamalar:
echo    Bot Token: %BOT_TOKEN%
echo    Domain: %DOMAIN%
echo    Webhook URL: %WEBHOOK_URL%
echo    Chat ID: %CHAT_ID%
echo.
echo 🧪 Test qilish uchun:
echo    php test-telegram-webhook.php
echo.
echo 📖 Qo'shimcha ma'lumot uchun:
echo    TELEGRAM_WEBHOOK_SETUP.md faylini o'qing
echo.
echo 🔧 Muammolar bo'lsa:
echo    - Log fayllarini tekshiring: tail -f storage/logs/laravel.log
echo    - Webhook holatini tekshiring: curl https://api.telegram.org/bot%BOT_TOKEN%/getWebhookInfo
echo.

REM Test scriptini ishga tushirish so'ralishi
set /p RUN_TEST="Test scriptini ishga tushirishni xohlaysizmi? (y/n): "

if /i "%RUN_TEST%"=="y" (
    echo.
    echo ℹ️  Test scriptini ishga tushirish...
    php test-telegram-webhook.php
)

echo.
echo ✅ Setup yakunlandi! 🎉
pause 