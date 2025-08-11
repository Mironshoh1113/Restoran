<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="bot-token" content="{{ $botToken ?? '' }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 16px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .restaurant-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .restaurant-description {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 16px 120px 16px;
            background: #f9fafb;
            min-height: 100vh;
        }
        
        .categories-nav {
            display: flex;
            gap: 8px;
            padding: 12px 16px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .categories-nav::-webkit-scrollbar {
            display: none;
        }
        
        .category-tab {
            padding: 8px 16px;
            background: #f3f4f6;
            border: none;
            border-radius: 20px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        
        .category-tab.active {
            background: #3b82f6;
            color: white;
        }
        
        .category-tab:hover {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .category-tab.active:hover {
            background: #2563eb;
            color: white;
        }
        
        .category-content {
            display: none;
            padding: 16px 0;
        }
        
        .category-content.active {
            display: block;
        }
        
        /* Smooth scrolling for categories */
        .categories-nav {
            scroll-behavior: smooth;
        }
        
        /* Better touch scrolling */
        .categories-nav {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Hide scrollbar but keep functionality */
        .categories-nav::-webkit-scrollbar {
            display: none;
        }
        
        .categories-nav {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .menu-item {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f3f4f6;
        }
        
        .menu-item:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .item-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .item-image i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .item-info {
            flex: 1;
            min-width: 0;
        }
        
        .item-name {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .item-description {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .item-price {
            font-size: 16px;
            font-weight: 700;
            color: #059669;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .quantity-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #3b82f6;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            -webkit-tap-highlight-color: transparent;
        }
        
        .quantity-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }
        
        .quantity-btn:active {
            transform: scale(0.95);
        }
        
        .quantity {
            min-width: 40px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 16px;
            border-top: 2px solid #e5e7eb;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .cart-total {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            text-align: center;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .checkout-btn:active {
            transform: translateY(0);
        }
        
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .order-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            margin: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .form-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .payment-method {
            flex: 1;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            font-size: 13px;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .payment-method.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        .hidden {
            display: none !important;
        }
        
        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 25px;
            border-radius: 16px;
            margin: 15px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 16px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            -webkit-tap-highlight-color: transparent;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .empty-cart i {
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* Mobile-specific improvements */
        @media (max-width: 480px) {
            .container {
                padding: 10px;
                padding-bottom: 100px;
            }
            
            .header {
                padding: 12px 15px;
            }
            
            .restaurant-name {
                font-size: 20px;
            }
            
            .restaurant-description {
                font-size: 12px;
            }
            
            .item-content {
                padding: 12px;
                gap: 10px;
            }
            
            .item-image {
                width: 50px;
                height: 50px;
                font-size: 18px;
            }
            
            .item-name {
                font-size: 14px;
            }
            
            .item-description {
                font-size: 12px;
            }
            
            .item-price {
                font-size: 14px;
            }
            
            .quantity-btn {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }
            
            .quantity {
                min-width: 25px;
                font-size: 14px;
            }
            
            .cart {
                padding: 12px 15px;
            }
            
            .cart-total {
                font-size: 16px;
            }
            
            .checkout-btn {
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .order-form {
                margin: 10px;
                padding: 15px;
            }
            
            .form-title {
                font-size: 18px;
            }
            
            .form-input {
                padding: 10px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .payment-methods {
                flex-direction: column;
                gap: 8px;
            }
            
            .payment-method {
                padding: 10px;
                font-size: 12px;
            }
        }
        
        /* Prevent text selection on buttons */
        button {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Improve touch targets */
        button, input, textarea {
            min-height: 44px;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        /* --- Modern Mobile Redesign Additions --- */
        body {
            background: #f3f4f6;
        }
        .header {
            background: #fff;
            color: #22223b;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.08);
            padding: 18px 12px 12px 12px;
        }
        .restaurant-logo {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            object-fit: cover;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.12);
        }
        .restaurant-name {
            font-size: 22px;
            font-weight: 800;
            color: #22223b;
            margin-bottom: 2px;
        }
        .restaurant-description {
            font-size: 13px;
            color: #4b5563;
            opacity: 0.85;
        }
        .container {
            max-width: 480px;
            margin: 0 auto;
            padding: 0 0 90px 0;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .categories-nav {
            background: #fff;
            border-radius: 0 0 18px 18px;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.04);
        }
        .category-tab {
            font-size: 15px;
            font-weight: 600;
            border-radius: 18px;
            padding: 10px 18px;
            margin-bottom: 2px;
        }
        .category-tab.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.10);
        }
        .menu-item {
            border-radius: 18px;
            margin-bottom: 18px;
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.08);
            border: none;
        }
        .item-content {
            gap: 14px;
        }
        .item-image {
            width: 64px;
            height: 64px;
            border-radius: 14px;
        }
        .item-info {
            min-width: 0;
        }
        .item-name {
            font-size: 16px;
            font-weight: 700;
            color: #22223b;
        }
        .item-description {
            font-size: 13px;
            color: #6b7280;
        }
        .item-price {
            font-size: 15px;
            color: #059669;
            font-weight: 700;
        }
        .item-controls {
            gap: 10px;
        }
        .quantity-btn {
            width: 36px;
            height: 36px;
            font-size: 20px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4f46e5;
            border: none;
            font-weight: 700;
        }
        .quantity-btn.plus {
            background: #4f46e5;
            color: #fff;
        }
        .quantity-btn:active {
            background: #a5b4fc;
        }
        .cart {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            background: #fff;
            border-radius: 18px 18px 0 0;
            box-shadow: 0 -4px 16px rgba(102, 126, 234, 0.10);
            padding: 12px 18px 18px 18px;
            z-index: 1000;
            max-width: 480px;
            margin: 0 auto;
        }
        .cart-header {
            margin-bottom: 0;
        }
        .cart-total {
            font-size: 18px;
            color: #22223b;
        }
        .checkout-btn {
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.10);
        }
        .checkout-btn:active {
            background: #a5b4fc;
        }
        /* Floating cart button for mobile */
        .floating-cart-btn {
            display: none;
            position: fixed;
            right: 18px;
            bottom: 90px;
            z-index: 1100;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.18);
            font-size: 28px;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 480px) {
            .floating-cart-btn {
                display: flex;
            }
            .container {
                padding-bottom: 120px;
            }
        }
        /* Bottom navigation bar */
        .bottom-nav {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -2px 12px rgba(102, 126, 234, 0.08);
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 60px;
            z-index: 1200;
            max-width: 480px;
            margin: 0 auto;
        }
        .bottom-nav-btn {
            flex: 1;
            text-align: center;
            color: #6b7280;
            font-size: 22px;
            padding: 8px 0 0 0;
            transition: color 0.2s;
        }
        .bottom-nav-btn.active, .bottom-nav-btn:active {
            color: #4f46e5;
        }
        .bottom-nav-label {
            display: block;
            font-size: 11px;
            margin-top: 2px;
        }
        /* Order form and success message improvements */
        .order-form {
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.10);
            background: #fff;
        }
        .form-title {
            color: #4f46e5;
        }
        .success-message {
            border-radius: 18px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            font-size: 18px;
            font-weight: 700;
        }
        .back-btn {
            border-radius: 12px;
            background: #e0e7ff;
            color: #4f46e5;
        }
        .back-btn:active {
            background: #a5b4fc;
        }
    .cart-order-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.25);
        z-index: 2000;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    .cart-order-modal-content {
        background: #fff;
        border-radius: 18px 18px 0 0;
        width: 100%;
        max-width: 480px;
        max-height: 95vh;
        overflow-y: auto;
        box-shadow: 0 -4px 24px rgba(102, 126, 234, 0.18);
        padding: 18px 16px 24px 16px;
        position: relative;
        animation: slideUp 0.25s cubic-bezier(.4,2,.6,1) 1;
    }
    @keyframes slideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .close-modal-btn {
        background: none;
        border: none;
        color: #4f46e5;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .modal-section {
        margin-bottom: 18px;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #22223b;
        margin-bottom: 10px;
    }
    .modal-cart-total {
        font-size: 16px;
        font-weight: 700;
        color: #059669;
        margin-top: 8px;
        text-align: right;
    }
    .modal-cart-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 15px;
    }
    .modal-cart-item:last-child {
        border-bottom: none;
    }
    .modal-cart-item-name {
        flex: 1;
        font-weight: 600;
        color: #22223b;
    }
    .modal-cart-item-qty {
        color: #4f46e5;
        font-weight: 700;
        margin: 0 8px;
    }
            .modal-cart-item-price {
            color: #059669;
            font-weight: 700;
            min-width: 60px;
            text-align: right;
        }
        
        .checkout-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        </style>
</head>
<body>
    <div class="header">
        @if($restaurant->logo)
            <img src="{{ $restaurant->logo }}" alt="Logo" class="restaurant-logo">
        @endif
        <div class="restaurant-name">{{ $restaurant->name }}</div>
        <div class="restaurant-description">{{ $restaurant->description ?? 'Restoran menyusi' }}</div>
        @if(isset($botToken))
            <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">
                Bot Token: {{ substr($botToken, 0, 10) }}...<br>
                <span style="color: #10b981;">✓ Bot token mavjud</span>
            </div>
        @else
            <div style="font-size: 10px; opacity: 0.7; margin-top: 4px; color: #ef4444;">
                ⚠ Bot token topilmadi
            </div>
        @endif
    </div>
    
    <div class="container">
        <!-- Categories Navigation -->
        <div class="categories-nav" id="categories-nav">
            @foreach($categories as $category)
                <button class="category-tab {{ $loop->first ? 'active' : '' }}" 
                        onclick="switchCategory({{ $category->id }})">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
        
        <!-- Menu Items by Category -->
        <div id="menu-container">
            @foreach($categories as $category)
                <div class="category-content {{ $loop->first ? 'active' : '' }}" id="category-{{ $category->id }}">
                    @foreach($category->menuItems as $item)
                        @php
                            $hasImage = $item->hasImage();
                            $imageUrl = $item->image_url;
                            \Log::info('Rendering menu item in web interface', [
                                'item_id' => $item->id,
                                'item_name' => $item->name,
                                'has_image' => $hasImage,
                                'image_path' => $item->image,
                                'image_url' => $imageUrl,
                                'full_server_path' => storage_path('app/public/' . $item->image)
                            ]);
                        @endphp
                        <div class="menu-item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
                            <div class="item-content">
                                <div class="item-image">
                                    @if($hasImage)
                                        <img src="{{ $imageUrl }}" alt="{{ $item->name }}" 
                                             onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                             onload="console.log('Image loaded successfully:', this.src);">
                                        <i class="fas fa-utensils" style="display: none;"></i>
                                    @else
                                        <i class="fas fa-utensils"></i>
                                    @endif
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $item->name }}</div>
                                    <div class="item-description">{{ $item->description }}</div>
                                    <div class="item-price">{{ number_format($item->price) }} so'm</div>
                                </div>
                                <div class="item-controls">
                                    <button class="quantity-btn minus" onclick="updateQuantity({{ $item->id }}, -1)">-</button>
                                    <span class="quantity" id="qty-{{ $item->id }}">0</span>
                                    <button class="quantity-btn plus" onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        
        <!-- Cart & Order Modal -->
        <div id="cart-order-modal" class="cart-order-modal hidden">
            <div class="cart-order-modal-content">
                <button class="close-modal-btn" onclick="closeCartOrderModal()"><i class="fas fa-arrow-left"></i> Orqaga</button>
                <div class="modal-section">
                    <div class="modal-title">Savat</div>
                    <div id="modal-cart-items"></div>
                    <div class="modal-cart-total">Jami: <span id="modal-cart-total">0</span> so'm</div>
                </div>
                <div class="modal-section">
                    <div class="modal-title">Buyurtma ma'lumotlari</div>
                    <form id="modal-checkout-form">
                        <div class="form-group">
                            <label class="form-label">Ismingiz</label>
                            <input type="text" class="form-input" id="modal-customer-name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefon raqam</label>
                            <input type="tel" class="form-input" id="modal-customer-phone" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Yetkazib berish manzili</label>
                            <textarea class="form-input" id="modal-delivery-address" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">To'lov usuli</label>
                            <div class="payment-methods">
                                <div class="payment-method" data-method="cash">
                                    <div><i class="fas fa-money-bill-wave"></i> Naqd pul</div>
                                </div>
                                <div class="payment-method" data-method="card">
                                    <div><i class="fas fa-credit-card"></i> Karta</div>
                                </div>
                            </div>
                            <input type="hidden" id="modal-payment-method" value="cash">
                        </div>
                        <button type="submit" class="checkout-btn" id="modal-submit-btn" style="width: 100%; margin-top: 15px;">
                            <span id="modal-submit-text">
                                <i class="fas fa-shopping-cart"></i> Buyurtma berish
                            </span>
                            <span id="modal-submit-spinner" class="hidden">
                                <i class="fas fa-spinner fa-spin"></i> Yuborilmoqda...
                            </span>
                        </button>
                    </form>
                </div>
                <div id="modal-success-message" class="success-message hidden" style="margin-top: 20px;">
                    <i class="fas fa-check-circle"></i><br>
                    <i class="fas fa-check"></i> Buyurtma qabul qilindi! Tez orada siz bilan bog'lanamiz.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating cart button for mobile -->
    <button class="floating-cart-btn" id="floating-cart-btn" onclick="scrollToCart()">
        <i class="fas fa-shopping-cart"></i>
    </button>
    
    <!-- Bottom navigation bar -->
    <div class="bottom-nav">
        <div class="bottom-nav-btn active" onclick="scrollToMenu()">
            <i class="fas fa-utensils"></i>
            <span class="bottom-nav-label">Menyu</span>
        </div>
        <div class="bottom-nav-btn" onclick="scrollToCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="bottom-nav-label">Savat</span>
        </div>
        <div class="bottom-nav-btn" onclick="scrollToProfile()">
            <i class="fas fa-user"></i>
            <span class="bottom-nav-label">Profil</span>
        </div>
    </div>
    
    <script>
        // Initialize Telegram Web App
        let tg = null;
        let telegramChatId = null;
        
        // Check if we're in Telegram Web App
        if (window.Telegram && window.Telegram.WebApp) {
            tg = window.Telegram.WebApp;
            tg.ready();
            tg.expand();
            
            // Get chat ID from Telegram Web App
            if (tg.initDataUnsafe && tg.initDataUnsafe.user) {
                telegramChatId = tg.initDataUnsafe.user.id;
            }
            
            // Get bot token from meta tag or URL
            const botToken = document.querySelector('meta[name="bot-token"]')?.getAttribute('content') || 
                            new URLSearchParams(window.location.search).get('bot_token');
            
            // Log initialization
            console.log('Telegram Web App initialized:', {
                user: tg.initDataUnsafe?.user,
                chatId: telegramChatId,
                botToken: botToken,
                initData: tg.initData
            });
        } else {
            // We're not in Telegram, create a mock object for testing
            tg = {
                ready: function() {},
                expand: function() {},
                sendData: function(data) {
                    console.log('Mock Telegram sendData:', data);
                },
                close: function() {
                    console.log('Mock Telegram close');
                }
            };
        }
        
        // Add error handling for network issues
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });
        
        // Add unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });
        
        let cart = {};
        let selectedPaymentMethod = 'cash';
        
        // Category switching
        function switchCategory(categoryId) {
            // Hide all category contents
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected category content
            document.getElementById('category-' + categoryId).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Scroll to active tab if needed
            const activeTab = event.target;
            const nav = document.querySelector('.categories-nav');
            const navRect = nav.getBoundingClientRect();
            const tabRect = activeTab.getBoundingClientRect();
            
            if (tabRect.left < navRect.left || tabRect.right > navRect.right) {
                activeTab.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        }
        
        // Touch scrolling for categories
        let isScrolling = false;
        let startX = 0;
        let scrollLeft = 0;
        
        const categoriesNav = document.querySelector('.categories-nav');
        
        categoriesNav.addEventListener('touchstart', (e) => {
            isScrolling = true;
            startX = e.touches[0].pageX - categoriesNav.offsetLeft;
            scrollLeft = categoriesNav.scrollLeft;
        });
        
        categoriesNav.addEventListener('touchmove', (e) => {
            if (!isScrolling) return;
            e.preventDefault();
            const x = e.touches[0].pageX - categoriesNav.offsetLeft;
            const walk = (x - startX) * 2;
            categoriesNav.scrollLeft = scrollLeft - walk;
        });
        
        categoriesNav.addEventListener('touchend', () => {
            isScrolling = false;
        });
        
        function updateQuantity(itemId, change) {
            const currentQty = cart[itemId] || 0;
            const newQty = Math.max(0, currentQty + change);
            if (newQty === 0) {
                delete cart[itemId];
            } else {
                cart[itemId] = newQty;
            }
            document.getElementById(`qty-${itemId}`).textContent = newQty;
            updateCartTotal();
            renderModalCart();
            updateModalCheckoutBtn();
        }
        function updateCartTotal() {
            let total = 0;
            for (let itemId in cart) {
                const price = parseInt(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                total += price * cart[itemId];
            }
            document.getElementById('cart-total').textContent = total.toLocaleString();
            document.getElementById('checkout-btn').disabled = total === 0;
            renderModalCart();
            updateModalCheckoutBtn();
        }
        function updateModalCheckoutBtn() {
            const btn = document.querySelector('#cart-order-modal .checkout-btn');
            let hasItems = false;
            for (let itemId in cart) { hasItems = true; break; }
            btn.disabled = !hasItems;
        }
        
        function showOrderForm() {
            document.getElementById('menu-container').classList.add('hidden');
            document.getElementById('order-form').classList.remove('hidden');
            document.getElementById('cart').classList.add('hidden');
        }
        
        function showMenu() {
            document.getElementById('menu-container').classList.remove('hidden');
            document.getElementById('order-form').classList.add('hidden');
            document.getElementById('cart').classList.remove('hidden');
        }
        
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                selectedPaymentMethod = this.dataset.method;
                document.getElementById('payment-method').value = selectedPaymentMethod;
            });
        });
        
        // Set default payment method
        document.querySelector('[data-method="cash"]').classList.add('selected');
        
        // Handle form submission
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const items = [];
            for (let itemId in cart) {
                items.push({
                    id: parseInt(itemId),
                    quantity: cart[itemId]
                });
            }
            
            const orderData = {
                items: items,
                customer_name: document.getElementById('customer-name').value,
                customer_phone: document.getElementById('customer-phone').value,
                delivery_address: document.getElementById('delivery-address').value,
                payment_method: selectedPaymentMethod,
                telegram_chat_id: telegramChatId
            };
            
            // Get bot token from URL parameters or use the current token
            const urlParams = new URLSearchParams(window.location.search);
            const botToken = urlParams.get('bot_token');
            
            // Get the current URL to determine the endpoint
            const currentUrl = window.location.pathname;
            const token = currentUrl.split('/').pop();
            let endpoint = token && token !== 'web-interface' ? 
                `/web-interface/${token}/order` : 
                '/web-interface/order';
            
            // If we have a bot token, use the no-token endpoint with bot_token parameter
            if (botToken) {
                endpoint = '/web-interface/order';
                orderData.bot_token = botToken;
            }
            
            console.log('Submitting order to endpoint:', endpoint);
                
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const headers = {
                'Content-Type': 'application/json'
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            fetch(endpoint, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('order-form').classList.add('hidden');
                    document.getElementById('success-message').classList.remove('hidden');
                    
                    // Send data back to Telegram
                    tg.sendData(JSON.stringify({
                        action: 'order_placed',
                        order_id: data.order_id
                    }));
                    
                    // Close web app after 3 seconds
                    setTimeout(() => {
                        tg.close();
                    }, 3000);
                } else {
                    alert('Xatolik yuz berdi: ' + (data.error || 'Noma\'lum xatolik'));
                }
            })
            .catch(error => {
                console.error('Order submission error:', error);
                
                // Show more detailed error message
                let errorMessage = 'Xatolik yuz berdi: ';
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    errorMessage += 'Server javob qaytarmayapti. Iltimos, qaytadan urinib ko\'ring.';
                } else if (error.message.includes('404')) {
                    errorMessage += 'Sahifa topilmadi. Iltimos, qaytadan urinib ko\'ring.';
                } else if (error.message.includes('500')) {
                    errorMessage += 'Server xatosi. Iltimos, keyinroq urinib ko\'ring.';
                } else if (error.message.includes('422')) {
                    errorMessage += 'Ma\'lumotlarni to\'g\'ri kiriting. Iltimos, barcha maydonlarni to\'ldiring.';
                } else {
                    errorMessage += error.message;
                }
                
                // Log additional details for debugging
                console.log('Order data that failed:', orderData);
                console.log('Endpoint that failed:', endpoint);
                
                alert(errorMessage);
            });
        });
        
        // Initialize cart total
        updateCartTotal();
        
        // Load saved customer data
        loadCustomerData();
        
        // Debug: Log menu items with images
        console.log('Menu items loaded:', document.querySelectorAll('.menu-item').length);
        document.querySelectorAll('.menu-item').forEach((item, index) => {
            const itemId = item.dataset.itemId;
            const image = item.querySelector('.item-image img');
            const icon = item.querySelector('.item-image i');
            
            console.log(`Item ${index + 1}:`, {
                id: itemId,
                hasImage: !!image,
                hasIcon: !!icon,
                imageSrc: image ? image.src : 'none',
                iconDisplay: icon ? icon.style.display : 'none'
            });
        });
        
        // Save customer data when form is submitted
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            saveCustomerData();
        });
        
        function loadCustomerData() {
            const savedName = localStorage.getItem('customer_name');
            const savedPhone = localStorage.getItem('customer_phone');
            const savedAddress = localStorage.getItem('delivery_address');
            
            if (savedName) {
                document.getElementById('customer-name').value = savedName;
            }
            if (savedPhone) {
                document.getElementById('customer-phone').value = savedPhone;
            }
            if (savedAddress) {
                document.getElementById('delivery-address').value = savedAddress;
            }
        }
        
        function saveCustomerData() {
            const name = document.getElementById('customer-name').value;
            const phone = document.getElementById('customer-phone').value;
            const address = document.getElementById('delivery-address').value;
            
            if (name) localStorage.setItem('customer_name', name);
            if (phone) localStorage.setItem('customer_phone', phone);
            if (address) localStorage.setItem('delivery_address', address);
        }

        // Floating cart button logic
        function scrollToCart() {
            openCartOrderModal();
        }
        function scrollToMenu() {
            document.querySelector('.container').scrollIntoView({ behavior: 'smooth' });
        }
        function scrollToProfile() {
            alert('Profil sahifasi tez orada!');
        }
        // Hide floating cart button if cart is visible
        function handleFloatingCartBtn() {
            const cart = document.getElementById('cart');
            const btn = document.getElementById('floating-cart-btn');
            if (window.scrollY + window.innerHeight < cart.offsetTop + cart.offsetHeight - 60) {
                btn.style.display = 'flex';
            } else {
                btn.style.display = 'none';
            }
        }
        window.addEventListener('scroll', handleFloatingCartBtn);
        document.addEventListener('DOMContentLoaded', handleFloatingCartBtn);

        // Cart & Order Modal Logic
        document.getElementById('checkout-btn').onclick = function() {
            openCartOrderModal();
        };
        function openCartOrderModal() {
            renderModalCart();
            loadModalCustomerData();
            document.getElementById('cart-order-modal').classList.remove('hidden');
        }
        function closeCartOrderModal() {
            document.getElementById('cart-order-modal').classList.add('hidden');
        }
        // Render cart items in modal
        function renderModalCart() {
            const modalCart = document.getElementById('modal-cart-items');
            modalCart.innerHTML = '';
            let total = 0;
            for (let itemId in cart) {
                const qty = cart[itemId];
                const itemEl = document.querySelector(`[data-item-id="${itemId}"]`);
                if (!itemEl) continue;
                const name = itemEl.querySelector('.item-name').textContent;
                const price = parseInt(itemEl.dataset.price);
                total += price * qty;
                modalCart.innerHTML += `<div class='modal-cart-item'><span class='modal-cart-item-name'>${name}</span> <span class='modal-cart-item-qty'>x${qty}</span> <span class='modal-cart-item-price'>${(price*qty).toLocaleString()} so'm</span></div>`;
            }
            if (total === 0) {
                modalCart.innerHTML = `<div class='empty-cart'><i class='fas fa-shopping-cart'></i><br>Savat bo'sh</div>`;
            }
            document.getElementById('modal-cart-total').textContent = total.toLocaleString();
            updateModalCheckoutBtn();
        }
        // Payment method selection in modal
        document.querySelectorAll('#cart-order-modal .payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('#cart-order-modal .payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('modal-payment-method').value = this.dataset.method;
            });
        });
        // Modal checkout form submit
        document.getElementById('modal-checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitModalOrder();
        });
        function submitModalOrder() {
            const items = [];
            for (let itemId in cart) {
                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                const price = itemElement ? parseInt(itemElement.dataset.price) : 0;
                const name = itemElement ? itemElement.querySelector('.item-name').textContent : 'Unknown Item';
                
                items.push({ 
                    id: parseInt(itemId), 
                    quantity: cart[itemId],
                    price: price,
                    name: name
                });
            }
            if (items.length === 0) {
                alert('Savat bo\'sh!');
                return;
            }
            
            // Validate form fields
            const customerName = document.getElementById('modal-customer-name').value.trim();
            const customerPhone = document.getElementById('modal-customer-phone').value.trim();
            const deliveryAddress = document.getElementById('modal-delivery-address').value.trim();
            
            if (!customerName) {
                alert('Iltimos, ismingizni kiriting!');
                document.getElementById('modal-customer-name').focus();
                return;
            }
            
            if (!customerPhone) {
                alert('Iltimos, telefon raqamingizni kiriting!');
                document.getElementById('modal-customer-phone').focus();
                return;
            }
            
            if (!deliveryAddress) {
                alert('Iltimos, yetkazib berish manzilini kiriting!');
                document.getElementById('modal-delivery-address').focus();
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('modal-submit-btn');
            const submitText = document.getElementById('modal-submit-text');
            const submitSpinner = document.getElementById('modal-submit-spinner');
            
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitSpinner.classList.remove('hidden');
            const orderData = {
                items: items,
                customer_name: customerName,
                customer_phone: customerPhone,
                delivery_address: deliveryAddress,
                payment_method: document.getElementById('modal-payment-method').value,
                telegram_chat_id: telegramChatId || null
            };
            
            console.log('Submitting order:', orderData);
            console.log('Cart items:', cart);
            console.log('Form data:', {
                customerName,
                customerPhone,
                deliveryAddress,
                paymentMethod: document.getElementById('modal-payment-method').value
            });
            
            // Get bot token from meta tag or URL parameters
            let botToken = document.querySelector('meta[name="bot-token"]')?.getAttribute('content') || 
                          new URLSearchParams(window.location.search).get('bot_token');
            
            // If no bot token in meta tag, try to get from current URL
            if (!botToken || botToken.trim() === '') {
                const currentUrl = window.location.pathname;
                const pathParts = currentUrl.split('/');
                if (pathParts.length > 2 && pathParts[1] === 'web-interface') {
                    botToken = pathParts[2];
                }
            }
            
            console.log('Bot token found:', botToken);
            console.log('Current URL:', window.location.href);
            
            // Determine endpoint based on available data
            let endpoint = '/web-interface/order';
            if (botToken && botToken.trim() !== '') {
                orderData.bot_token = botToken;
                console.log('Using bot token for order:', botToken);
            } else {
                // Try to get from current URL path
                const currentUrl = window.location.pathname;
                const token = currentUrl.split('/').pop();
                if (token && token !== 'web-interface') {
                    endpoint = `/web-interface/${token}/order`;
                    console.log('Using URL token for order:', token);
                } else {
                    console.error('No bot token or URL token found!');
                    console.log('Meta tag content:', document.querySelector('meta[name="bot-token"]')?.getAttribute('content'));
                    console.log('URL search params:', window.location.search);
                    alert('Xatolik: Bot token topilmadi. Iltimos, qaytadan urinib ko\'ring.');
                    return;
                }
            }
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = { 'Content-Type': 'application/json' };
            if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
            console.log('Sending request to:', endpoint);
            console.log('Request headers:', headers);
            console.log('Request body:', orderData);
            
            fetch(endpoint, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(orderData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                console.log('Order response:', data);
                
                if (data.success) {
                    console.log('Order placed successfully:', data);
                    
                    // Hide form and show success message
                    document.getElementById('modal-checkout-form').classList.add('hidden');
                    document.getElementById('modal-success-message').classList.remove('hidden');
                    
                    // Update success message with order details
                    const successMessage = document.getElementById('modal-success-message');
                    if (data.order_number) {
                        successMessage.innerHTML = `
                            <i class="fas fa-check-circle"></i><br>
                            <strong>Buyurtma qabul qilindi!</strong><br>
                            Buyurtma raqami: #${data.order_number}<br>
                            Tez orada siz bilan bog'lanamiz.
                        `;
                    }
                    
                    // Send data back to Telegram if available
                    if (tg && tg.sendData) {
                        const totalAmount = orderData.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                        tg.sendData(JSON.stringify({ 
                            action: 'order_placed', 
                            order_id: data.order_id,
                            order_number: data.order_number,
                            total_amount: totalAmount,
                            customer_name: orderData.customer_name,
                            delivery_address: orderData.delivery_address
                        }));
                        console.log('Data sent to Telegram:', { order_id: data.order_id, total_amount: totalAmount });
                    }
                    
                    // Close web app after 3 seconds if in Telegram
                    if (tg && tg.close) {
                        setTimeout(() => { 
                            console.log('Closing Telegram Web App');
                            tg.close(); 
                        }, 3000);
                    }
                    
                    // Clear cart
                    cart = {};
                    updateCartTotal();
                    renderModalCart();
                    
                } else {
                    console.error('Order failed:', data);
                    alert('Xatolik yuz berdi: ' + (data.error || 'Noma\'lum xatolik'));
                }
            })
            .catch(error => {
                console.error('Order submission error:', error);
                
                let errorMessage = 'Xatolik yuz berdi: ';
                if (error.name === 'TypeError' && error.message.includes('JSON')) {
                    errorMessage += 'Server javob qaytarmayapti. Iltimos, qaytadan urinib ko\'ring.';
                } else if (error.message.includes('404')) {
                    errorMessage += 'Sahifa topilmadi. Iltimos, qaytadan urinib ko\'ring.';
                } else if (error.message.includes('500')) {
                    errorMessage += 'Server xatosi. Iltimos, keyinroq urinib ko\'ring.';
                } else if (error.message.includes('422')) {
                    errorMessage += 'Ma\'lumotlarni to\'g\'ri kiriting. Iltimos, barcha maydonlarni to\'ldiring.';
                } else if (error.message.includes('Restaurant not found')) {
                    errorMessage += 'Restoran topilmadi. Bot token noto\'g\'ri yoki restoran mavjud emas.';
                    console.error('Bot token issue:', {
                        botToken: botToken,
                        endpoint: endpoint,
                        currentUrl: window.location.href
                    });
                } else if (error.message.includes('NetworkError')) {
                    errorMessage += 'Internet aloqasi muammosi. Iltimos, internet aloqasini tekshiring.';
                } else {
                    errorMessage += error.message;
                }
                
                console.error('Full error details:', {
                    error: error,
                    message: error.message,
                    stack: error.stack,
                    orderData: orderData,
                    endpoint: endpoint,
                    botToken: botToken
                });
                
                alert(errorMessage);
            })
            .finally(() => {
                // Re-enable modal submit button
                const submitBtn = document.getElementById('modal-submit-btn');
                const submitText = document.getElementById('modal-submit-text');
                const submitSpinner = document.getElementById('modal-submit-spinner');
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    submitSpinner.classList.add('hidden');
                }
            });
            
            saveModalCustomerData();
        }
        // Save/load customer data for modal
        function loadModalCustomerData() {
            const savedName = localStorage.getItem('customer_name');
            const savedPhone = localStorage.getItem('customer_phone');
            const savedAddress = localStorage.getItem('delivery_address');
            if (savedName) document.getElementById('modal-customer-name').value = savedName;
            if (savedPhone) document.getElementById('modal-customer-phone').value = savedPhone;
            if (savedAddress) document.getElementById('modal-delivery-address').value = savedAddress;
        }
        function saveModalCustomerData() {
            const name = document.getElementById('modal-customer-name').value;
            const phone = document.getElementById('modal-customer-phone').value;
            const address = document.getElementById('modal-delivery-address').value;
            if (name) localStorage.setItem('customer_name', name);
            if (phone) localStorage.setItem('customer_phone', phone);
            if (address) localStorage.setItem('delivery_address', address);
        }
    </script>
</body>
</html> 