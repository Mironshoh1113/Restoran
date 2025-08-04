<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Telegram bot settings.
    |
    */

    'default_bot' => env('TELEGRAM_BOT_TOKEN', null),
    
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL', null),
    
    'api_url' => 'https://api.telegram.org/bot',
    
    'timeout' => 30,
    
    'debug' => env('TELEGRAM_DEBUG', false),
    
    /*
    |--------------------------------------------------------------------------
    | Bot Commands
    |--------------------------------------------------------------------------
    |
    | Default bot commands that will be set when bot starts.
    |
    */
    
    'commands' => [
        'start' => 'Botni ishga tushirish',
        'menu' => 'Menyu ko\'rish',
        'cart' => 'Savatni ko\'rish',
        'order' => 'Buyurtma qilish',
        'orders' => 'Buyurtmalarim',
        'help' => 'Yordam',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Message Templates
    |--------------------------------------------------------------------------
    |
    | Default message templates for bot responses.
    |
    */
    
    'messages' => [
        'welcome' => 'Assalomu alaykum! 🍽️

Restoran buyurtma botiga xush kelibsiz!

📋 Menyu ko\'rish uchun "Menyu" tugmasini bosing
🛒 Savatni ko\'rish uchun "Savat" tugmasini bosing
📞 Buyurtma qilish uchun "Buyurtma qilish" tugmasini bosing
📊 Buyurtmalaringizni ko\'rish uchun "Buyurtmalarim" tugmasini bosing',
        
        'menu_not_found' => 'Kechirasiz, hozircha menyu mavjud emas.',
        'cart_empty' => 'Savat bo\'sh. Menyudan taom tanlang.',
        'order_success' => 'Buyurtmangiz muvaffaqiyatli qabul qilindi! 🎉

Buyurtma raqami: #{order_number}
Jami summa: {total_amount} so\'m

Buyurtmangiz tayyorlanmoqda. Tez orada siz bilan bog\'lanamiz.',
        
        'order_not_found' => 'Buyurtma topilmadi.',
        'help' => 'Yordam kerakmi? 

📞 Qo\'ng\'iroq: {phone}
📍 Manzil: {address}

Yoki restoran bilan to\'g\'ridan-to\'g\'ri bog\'laning.',
    ],
]; 