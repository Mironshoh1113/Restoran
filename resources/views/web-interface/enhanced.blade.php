<!DOCTYPE html>
<html lang="uz" class="shrink-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="bot-token" content="{{ $botToken ?? $restaurant->bot_token ?? '' }}">
    @if(!config('app.debug'))
    <script>
        // Disable console logs in production
        console.log = function(){};
        console.debug = function(){};
        console.warn = function(){};
    </script>
    @endif
    
    <style>
        :root {
            --primary-color: {{ $restaurant->primary_color ?? '#667eea' }};
            --secondary-color: {{ $restaurant->secondary_color ?? '#764ba2' }};
            --accent-color: {{ $restaurant->accent_color ?? '#ff6b35' }};
            --text-color: {{ $restaurant->text_color ?? '#2c3e50' }};
            --bg-color: {{ $restaurant->bg_color ?? '#f8f9fa' }};
            --card-bg: {{ $restaurant->card_bg ?? '#ffffff' }};
            --border-radius: {{ $restaurant->border_radius ?? '16px' }};
            --shadow: {{ $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)' }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-color, #f8f9fa) !important;
            color: var(--text-color, #2c3e50) !important;
            line-height: 1.6;
            overflow-x: hidden;
            padding-bottom: 160px; /* prevent cart overlay from covering controls */
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2)) !important;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow, 0 8px 32px rgba(0,0,0,0.1)) !important;
        }

        .restaurant-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .restaurant-info h1 {
            color: white !important;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .restaurant-info p {
            color: rgba(255,255,255,0.9) !important;
            margin: 0;
            font-size: 0.9rem;
        }

        /* Categories */
        .categories-container {
            padding: 1rem 0;
            background: var(--card-bg, #ffffff) !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            position: sticky;
            top: 50px;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .category-tabs {
            display: flex;
            overflow-x: auto;
            gap: 0.5rem;
            padding: 0 1rem;
            scrollbar-width: none;
        }

        .category-tabs::-webkit-scrollbar {
            display: none;
        }

        .category-tab {
            background: var(--card-bg, #ffffff) !important;
            border: 2px solid var(--primary-color, #667eea) !important;
            color: var(--primary-color, #667eea) !important;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
            transition: all 0.3s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        .category-tab.active {
            background: var(--primary-color, #667eea) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        }

        .category-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        /* Menu Items */
        .menu-container { padding: 0.75rem; }
        .category-content { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
        
        .menu-item {
            background: var(--card-bg, #ffffff) !important;
            border-radius: var(--border-radius, 12px) !important;
            box-shadow: var(--shadow, 0 8px 32px rgba(0,0,0,0.1)) !important;
            margin: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            display: block;
        }

        /* Override bootstrap grid inside card to stack vertically */
        .menu-item .row { display: block; margin: 0; }
        .menu-item .col-md-4, .menu-item .col-md-8 { width: 100%; padding: 0; }
        
        .menu-item-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f1f5f9;
            transition: transform 0.3s ease;
            border-radius: 0; /* rectangular */
        }
        
        .menu-item-image-placeholder {
            width: 100%;
            height: 120px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 2rem;
            border-radius: 0; /* rectangular */
            border: 1px dashed #e2e8f0;
        }
        
        .menu-item-content { padding: 0.75rem; }
        .menu-item-title { font-size: 1rem; margin-bottom: 0.25rem; }
        .menu-item-description { font-size: 0.8rem; margin-bottom: 0.5rem; }
        .menu-item-price { font-size: 1.1rem; margin-bottom: 0.5rem; }
        .quantity-controls { gap: 0.5rem; }
        
        /* Responsive tweaks */
        @media (min-width: 768px) {
            .category-content { grid-template-columns: repeat(3, 1fr); gap: 1rem; }
            .menu-item-image, .menu-item-image-placeholder { height: 140px; }
        }
        @media (min-width: 1024px) {
            .category-content { grid-template-columns: repeat(4, 1fr); }
            .menu-item-title { font-size: 1.05rem; }
        }

        .menu-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .menu-item-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transition: transform 0.3s ease;
            cursor: zoom-in;
        }
        
        .menu-item-image:hover {
            transform: scale(1.05);
        }
        
        /* Image preview rotation support */
        #imagePreviewImage {
            transition: transform 0.25s ease;
            will-change: transform;
            transform-origin: center center;
            transform: rotate(calc(var(--auto-rotate, 0deg) + var(--manual-rotate, 0deg)));
        }
        .image-preview-rotated {
            --auto-rotate: 90deg;
            max-width: 90vh;
            max-height: 90vw;
        }
        
        .menu-item-image-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 3rem;
            border-radius: var(--border-radius, 16px);
            border: 2px dashed #dee2e6;
        }

        .menu-item-content {
            padding: 1.5rem;
        }

        .menu-item-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color, #2c3e50) !important;
            margin-bottom: 0.5rem;
        }

        .menu-item-description {
            color: #6c757d;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .menu-item-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-color, #ff6b35) !important;
            margin-bottom: 1rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: space-between;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn.minus {
            background: #e74c3c;
            color: white;
        }

        .quantity-btn.plus {
            background: var(--accent-color, #ff6b35) !important;
            color: white;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
        }

        .quantity-display {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color, #2c3e50) !important;
            min-width: 40px;
            text-align: center;
        }

        /* Cart */
        .cart-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg, #ffffff) !important;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            z-index: 1001;
            backdrop-filter: blur(10px);
        }

        .cart-content {
            padding: 1.5rem;
        }

        .cart-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .cart-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color, #2c3e50) !important;
        }

        .cart-count {
            background: var(--accent-color, #ff6b35) !important;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2)) !important;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius, 16px) !important;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .checkout-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Search and Filters */
        .search-container {
            padding: 1rem;
            background: var(--card-bg, #ffffff) !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--primary-color, #667eea) !important;
            border-radius: 25px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            background: var(--card-bg, #ffffff) !important;
            color: var(--text-color, #2c3e50) !important;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filters-container {
            padding: 1rem;
            background: var(--card-bg, #ffffff) !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .filter-btn {
            background: var(--card-bg, #ffffff) !important;
            border: 1px solid var(--primary-color, #667eea) !important;
            color: var(--primary-color, #667eea) !important;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: var(--primary-color, #667eea) !important;
            color: white !important;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
        }

        /* Checkout Modal */
        		.checkout-modal {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,0.5);
			z-index: 10001;
			backdrop-filter: blur(5px);
		}
        
        .checkout-modal.show { display: flex; }
        
        .checkout-modal-content {
            background: var(--card-bg, #ffffff);
            border-radius: 0; /* fullscreen */
            padding: 1rem 1rem 6rem; /* leave room for footer buttons */
            max-width: none;
            width: 100%;
            height: 100vh;
            max-height: none;
            overflow-y: auto;
            box-shadow: none;
        }

        .checkout-modal-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkout-modal-header h3 {
            color: var(--text-color, #2c3e50);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color, #2c3e50);
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--card-bg, #ffffff);
            color: var(--text-color, #2c3e50);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color, #667eea);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkout-actions {
            display: flex;
            gap: 0.75rem;
            position: fixed;
            left: 0; right: 0; bottom: 0;
            padding: 0.75rem 1rem;
            background: var(--card-bg, #ffffff);
            box-shadow: 0 -8px 24px rgba(0,0,0,0.08);
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-primary {
            background: var(--primary-color, #667eea);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }

        /* Loading */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .restaurant-info h1 {
                font-size: 1.25rem;
            }
            
            .menu-item-content {
                padding: 1rem;
            }
            
            .menu-item-title {
                font-size: 1.1rem;
            }
            
            .cart-content {
                padding: 1rem;
            }
            
            .menu-item-image,
            .menu-item-image-placeholder {
                height: 250px;
            }
            
            .checkout-modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }
        }

        /* Telegram Theme Integration - DISABLED to use custom colors */
        /*
        .telegram-theme {
            --bg-color: var(--tg-theme-bg-color, var(--bg-color));
            --text-color: var(--tg-theme-text-color, var(--text-color));
            --card-bg: var(--tg-theme-secondary-bg-color, var(--card-bg));
            --primary-color: var(--tg-theme-button-color, var(--primary-color));
        }
        */

        /* Force apply custom colors */
        body {
            background: var(--bg-color, #f8f9fa) !important;
            color: var(--text-color, #2c3e50) !important;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2)) !important;
        }

        .category-tab {
            border-color: var(--primary-color, #667eea) !important;
            color: var(--primary-color, #667eea) !important;
            background: var(--card-bg, #ffffff) !important;
        }

        .category-tab.active {
            background: var(--primary-color, #667eea) !important;
            color: white !important;
        }

        .menu-item {
            background: var(--card-bg, #ffffff) !important;
            border-radius: var(--border-radius, 16px) !important;
            box-shadow: var(--shadow, 0 8px 32px rgba(0,0,0,0.1)) !important;
        }

        .menu-item-title {
            color: var(--text-color, #2c3e50) !important;
        }

        .menu-item-price {
            color: var(--accent-color, #ff6b35) !important;
        }

        .quantity-btn.plus {
            background: var(--accent-color, #ff6b35) !important;
        }

        .cart-fixed {
            background: var(--card-bg, #ffffff) !important;
        }

        .cart-total {
            color: var(--text-color, #2c3e50) !important;
        }

        .cart-count {
            background: var(--accent-color, #ff6b35) !important;
        }

        .checkout-btn {
            background: linear-gradient(135deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2)) !important;
            border-radius: var(--border-radius, 16px) !important;
        }

        .search-container,
        .filters-container {
            background: var(--card-bg, #ffffff) !important;
        }

        .search-input {
            border-color: var(--primary-color, #667eea) !important;
            background: var(--card-bg, #ffffff) !important;
            color: var(--text-color, #2c3e50) !important;
        }

        .filter-btn {
            background: var(--card-bg, #ffffff) !important;
            border-color: var(--primary-color, #667eea) !important;
            color: var(--primary-color, #667eea) !important;
        }

        .filter-btn.active {
            background: var(--primary-color, #667eea) !important;
            color: white !important;
        }

        .checkout-modal-content {
            background: var(--card-bg, #ffffff) !important;
            border-radius: var(--border-radius, 16px) !important;
            box-shadow: var(--shadow, 0 8px 32px rgba(0,0,0,0.1)) !important;
        }

        .checkout-modal-header h3 {
            color: var(--text-color, #2c3e50) !important;
        }

        .form-label {
            color: var(--text-color, #2c3e50) !important;
        }

        .form-input {
            background: var(--card-bg, #ffffff) !important;
            color: var(--text-color, #2c3e50) !important;
            border-color: #e2e8f0;
        }

        .form-input:focus {
            border-color: var(--primary-color, #667eea) !important;
        }

        .btn-primary {
            background: var(--primary-color, #667eea) !important;
        }
		.cart-fixed { z-index: 9999; padding: 0.75rem 1rem; }
		.cart-content { padding-bottom: 0; }
		.checkout-btn { position: relative; }
		.menu-item { margin-bottom: 0; }
		.category-content { margin-bottom: 1rem; }
		.checkout-modal-content { position: relative; padding-bottom: 6.5rem; }
		.checkout-actions {
			display: flex;
			gap: 0.75rem;
			position: fixed;
			left: 0; right: 0; bottom: 0;
			background: var(--card-bg, #ffffff);
			padding: 0.5rem 0.75rem;
			box-shadow: 0 -8px 24px rgba(0,0,0,0.08);
			z-index: 10002;
		}

        /* Ultra-small phones */
        @media (max-width: 420px) {
            body { padding-bottom: 120px; }
            .header { padding: 0.5rem 0; }
            .restaurant-logo { width: 44px; height: 44px; border: 2px solid rgba(255,255,255,0.3); }
            .restaurant-info h1 { font-size: 1rem; }
            .restaurant-info p { font-size: 0.8rem; }

            .search-container { padding: 0.75rem; }
            .search-input { padding: 0.6rem 0.9rem; font-size: 0.9rem; border-radius: 18px; }

            .categories-container { padding: 0.5rem 0; top: 80px; }
            .category-tabs { gap: 0.4rem; padding: 0 0.75rem; }
            .category-tab { padding: 0.45rem 0.9rem; font-size: 0.8rem; border-radius: 18px; }

            .menu-container { padding: 0.5rem; }
            .category-content { gap: 0.5rem; }
            .menu-item-image,
            .menu-item-image-placeholder { height: 110px; }
            .menu-item-content { padding: 0.6rem; }
            .menu-item-title { font-size: 0.95rem; }
            .menu-item-description { font-size: 0.75rem; }
            .menu-item-price { font-size: 1rem; }

            .quantity-controls { gap: 0.4rem; }
            .quantity-btn { width: 32px; height: 32px; font-size: 0.95rem; }
            .quantity-display { font-size: 0.95rem; min-width: 30px; }

            .cart-content { padding: 0.75rem; }
            .cart-total { font-size: 1.05rem; }
            .cart-count { padding: 0.15rem 0.5rem; font-size: 0.8rem; }
            .checkout-btn { padding: 0.75rem; font-size: 1rem; }

            .checkout-modal-content { padding: 0.75rem 0.75rem 4.5rem; }
            .checkout-actions { gap: 0.5rem; padding: 0.5rem 0.75rem; }
            .btn { padding: 0.6rem 1rem; font-size: 0.95rem; }
        }
		/* Compact 50% scale for enhanced view */
		.shrink-50 body { font-size: 0.75rem; }
		.shrink-50 .header { padding: 0.5rem 0; }
		.shrink-50 .restaurant-logo { width: 36px; height: 36px; }
		.shrink-50 .restaurant-info h1 { font-size: 1rem; }
		.shrink-50 .restaurant-info p { font-size: 0.8rem; }
		.shrink-50 .category-tab { padding: 0.4rem 0.8rem; font-size: 0.75rem; border-radius: 16px; }
		.shrink-50 .category-content { grid-template-columns: repeat(2, 1fr); gap: 0.45rem; }
		.shrink-50 .menu-item { margin-bottom: 0.45rem; }
		.shrink-50 .menu-item-image, .shrink-50 .menu-item-image-placeholder { height: 90px; }
		.shrink-50 .menu-item-content { padding: 0.5rem; }
		.shrink-50 .menu-item-title { font-size: 0.85rem; }
		.shrink-50 .menu-item-description { font-size: 0.7rem; }
		.shrink-50 .menu-item-price { font-size: 0.9rem; }
		.shrink-50 .quantity-btn { width: 26px; height: 26px; font-size: 0.85rem; }
		.shrink-50 .quantity-display { font-size: 0.85rem; min-width: 26px; }
		.shrink-50 .cart-content { padding: 0.7rem; }
		.shrink-50 .cart-total { font-size: 0.95rem; }
		.shrink-50 .checkout-btn { padding: 0.65rem; font-size: 0.9rem; }
		.shrink-50 .checkout-modal-content { padding: 0.6rem 0.6rem 3.5rem; }
		.shrink-50 .checkout-modal-header h3 { font-size: 1rem; }
		.shrink-50 .form-label { font-size: 0.8rem; }
		.shrink-50 .form-input { padding: 0.45rem 0.7rem; font-size: 0.85rem; }
		.shrink-50 .checkout-actions { gap: 0.45rem; padding: 0.45rem 0.6rem; }
		.shrink-50 .btn { padding: 0.45rem 0.85rem; font-size: 0.85rem; }
		/* Fullscreen-feel checkout modal */
		.checkout-modal.show { align-items: stretch; }
		.checkout-modal-content { height: 100vh; max-height: 100vh; border-radius: 0; }
	</style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($restaurant->logo)
                        <img src="{{ asset('storage/' . $restaurant->logo) }}" 
                             alt="{{ $restaurant->name }}" 
                             class="restaurant-logo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="restaurant-logo" style="display: none; background: rgba(255,255,255,0.2);">
                            <i class="fas fa-utensils text-white" style="font-size: 1.5rem; line-height: 60px; text-align: center; width: 100%;"></i>
                        </div>
                    @else
                        <div class="restaurant-logo" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-utensils text-white" style="font-size: 1.5rem; line-height: 60px; text-align: center; width: 100%;"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <div class="restaurant-info">
                        <h1>{{ $restaurant->web_app_title ?? $restaurant->name . ' ' }}</h1>
                        <p>{{ $restaurant->web_app_description ?? 'maxsulotlar menyusi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Search -->
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Maxsulotni qidiring..." id="searchInput">
    </div>

    <!-- Filters removed per request -->

    <!-- Categories -->
    <div class="categories-container">
        <div class="category-tabs">
            @foreach($categories as $category)
                <div class="category-tab {{ $loop->first ? 'active' : '' }}" 
                     data-category-id="{{ $category->id }}" 
                     onclick="switchCategory({{ $category->id }})">
                    {{ $category->name }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Menu Container -->
    <div class="menu-container">
        @foreach($categories as $category)
            <div class="category-content {{ $loop->first ? '' : 'd-none' }}" id="cat-content-{{ $category->id }}">
                @if($category->menuItems->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-utensils"></i>
                        <h5>Bu kategoriyada hali taomlar yo'q</h5>
                        <p>Tez orada yangi taomlar qo'shiladi</p>
                    </div>
                @else
                    @foreach($category->menuItems as $item)
                        <div class="menu-item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}" data-description="{{ strtolower($item->description ?? '') }}">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    @if($item->image && !empty(trim($item->image)))
                                        @php($imgPath = \Illuminate\Support\Facades\Storage::url($item->image))
                                        <img src="{{ $imgPath }}"
                                             alt="{{ $item->name }}" 
                                             class="menu-item-image"
                                             loading="lazy"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="menu-item-image-placeholder" style="display: none;">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                    @else
                                        <div class="menu-item-image-placeholder">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <div class="menu-item-content">
                                        <h3 class="menu-item-title">{{ $item->name }}</h3>
                                        @if($item->description)
                                            <p class="menu-item-description">{{ $item->description }}</p>
                                        @endif
                                        <div class="menu-item-price">{{ number_format($item->price) }} so'm</div>
                                        <div class="quantity-controls">
                                            <button class="quantity-btn minus" onclick="changeQuantity({{ $item->id }}, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="quantity-display" id="qty-{{ $item->id }}">0</span>
                                            <button class="quantity-btn plus" onclick="changeQuantity({{ $item->id }}, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>

    <!-- Checkout Modal -->
    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-modal-content">
            <div class="checkout-modal-header">
                <h3>Buyurtma ma'lumotlari</h3>
                <p class="text-muted">Iltimos, ma'lumotlaringizni kiriting</p>
            </div>
            
            <form id="checkoutForm">
                <div class="form-group">
                    <label class="form-label">Ismingiz</label>
                    <input type="text" class="form-input" id="customerName" name="customer_name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telefon raqam</label>
                    <input type="tel" class="form-input" id="customerPhone" name="customer_phone" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Manzil</label>
                    <textarea class="form-input" id="customerAddress" name="customer_address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">To'lov usuli</label>
                    <select class="form-input" id="paymentMethod" name="payment_method" required>
                        @php($allowed = $restaurant->payment_methods ?? ['cash','card'])
                        @if(in_array('cash', $allowed))<option value="cash">Naqd</option>@endif
                        @if(in_array('card', $allowed))<option value="card">Karta</option>@endif
                        @if(in_array('click', $allowed))<option value="click">Click</option>@endif
                        @if(in_array('payme', $allowed))<option value="payme">Payme</option>@endif
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Izoh (ixtiyoriy)</label>
                    <textarea class="form-input" id="customerNotes" name="customer_notes" rows="2"></textarea>
                </div>
                
                <div class="form-group" style="background:#f8f9fa;border-radius:12px;padding:12px;">
					<div style="font-weight:600;margin-bottom:6px;">Tanlangan taomlar</div>
					<div id="checkoutItems" style="font-size:0.9rem;color:#374151;margin-bottom:8px;"></div>
					<div style="display:flex;justify-content:space-between;margin-bottom:6px;">
						<span>Oraliq summa</span>
						<span id="checkoutSubtotal">0 so'm</span>
					</div>
					<div style="display:flex;justify-content:space-between;margin-bottom:6px;">
						<span>Yetkazib berish</span>
						<span id="checkoutDeliveryFee">{{ number_format((float)($restaurant->delivery_fee ?? 0), 0, ',', ' ') }} so'm</span>
					</div>
					<hr style="margin:8px 0;" />
					<div style="display:flex;justify-content:space-between;font-weight:600;">
						<span>Jami</span>
						<span id="checkoutTotal">0 so'm</span>
					</div>
				</div>
                
                <div class="checkout-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeCheckoutModal()">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Buyurtmani tasdiqlash</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content bg-transparent border-0 position-relative">
                <button id="imageRotateBtn" type="button" class="btn btn-light btn-sm rounded-circle shadow position-absolute" style="top:8px; right:8px; z-index: 1056;" title="Aylantirish">
                    <i class="fas fa-rotate-right"></i>
                </button>
                <img id="imagePreviewImage" src="" alt="Preview" class="img-fluid d-block mx-auto rounded shadow" style="max-height: 90vh; cursor: zoom-out;" data-bs-dismiss="modal">
            </div>
        </div>
    </div>

    <!-- Fixed Cart -->
    <div class="cart-fixed">
        <div class="cart-content">
            <div class="cart-summary">
                <div class="cart-total">
                    Jami: <span id="cart-total-price">0</span> so'm
                </div>
                <div class="cart-count" id="cart-total-items">0</div>
            </div>
            <button class="checkout-btn" id="checkout-btn" type="button" onclick="handleCheckoutClick()">
                <i class="fas fa-shopping-cart me-2"></i>Buyurtma berish
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let cart = {};
        let currentCategory = {{ $categories->first()->id ?? 1 }};
        // Prefill customer data
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const saved = JSON.parse(localStorage.getItem('customerData') || '{}');
                if (saved.name) document.getElementById('customerName').value = saved.name;
                if (saved.phone) document.getElementById('customerPhone').value = saved.phone;
                if (saved.address) document.getElementById('customerAddress').value = saved.address;
                if (saved.notes) document.getElementById('customerNotes').value = saved.notes;
            } catch(e) {}
        });
        
        // Restaurant customization settings
        const restaurantSettings = {
            primaryColor: '{{ $restaurant->primary_color ?? "#667eea" }}',
            secondaryColor: '{{ $restaurant->secondary_color ?? "#764ba2" }}',
            accentColor: '{{ $restaurant->accent_color ?? "#ff6b35" }}',
            textColor: '{{ $restaurant->text_color ?? "#2c3e50" }}',
            bgColor: '{{ $restaurant->bg_color ?? "#f8f9fa" }}',
            cardBg: '{{ $restaurant->card_bg ?? "#ffffff" }}',
            borderRadius: '{{ $restaurant->border_radius ?? "16px" }}',
            shadow: '{{ $restaurant->shadow ?? "0 8px 32px rgba(0,0,0,0.1)" }}'
        };
        
        // Initialize Telegram Web App (safe fallback outside Telegram)
        let tg = {
            ready: function(){},
            expand: function(){},
            showAlert: function(msg){ try { alert(msg); } catch(_) {} },
            sendData: function(_){},
            initDataUnsafe: {}
        };
        try {
            if (window.Telegram && window.Telegram.WebApp) {
                tg = window.Telegram.WebApp;
                if (typeof tg.ready === 'function') tg.ready();
                if (typeof tg.expand === 'function') tg.expand();
            }
        } catch(_) {}

        // Persist Telegram user id for fallback outside WebApp
        try {
            const cid = tg?.initDataUnsafe?.user?.id || window.Telegram?.WebApp?.initDataUnsafe?.user?.id;
            if (cid) {
                localStorage.setItem('tg_chat_id', String(cid));
            }
        } catch(_) {}
        
        // Apply Telegram theme
// document.body.classList.add('telegram-theme'); // DISABLED

// Open compact checkout popup
function openCheckoutPopup(){
	try {
		const items = Object.keys(cart||{}).map(id=>({
			menu_item_id: parseInt(id),
			name: document.querySelector(`[data-item-id="${id}"] .menu-item-title`)?.textContent||'',
			price: parseFloat(document.querySelector(`[data-item-id="${id}"]`)?.dataset.price||'0'),
			quantity: cart[id]
		}));
		const total = Object.keys(cart||{}).reduce((t,id)=>t + (cart[id]*(parseFloat(document.querySelector(`[data-item-id="${id}"]`)?.dataset.price||'0'))),0);
		const preview = {
			restaurant_id: {{ $restaurant->id }},
			items,
			total_amount: total,
			telegram_chat_id: (window.Telegram?.WebApp?.initDataUnsafe?.user?.id)||localStorage.getItem('tg_chat_id')||null,
			bot_token: '{{ $botToken ?? $restaurant->bot_token ?? "" }}'
		};
		localStorage.setItem('checkout_preview', JSON.stringify(preview));
		const url = `/web-interface/checkout?restaurant_id={{ $restaurant->id }}&bot_token={{ urlencode($botToken ?? $restaurant->bot_token ?? "") }}`;
		const w = window.open(url, 'checkout', 'width=420,height=700,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no');
		if(!w){ openCheckoutModal(); }
	} catch(e) {
		openCheckoutModal();
	}
}

// Centralized checkout click handler
function handleCheckoutClick(){
	try {
		const totalItems = Object.values(cart||{}).reduce((a,b)=>a + (b||0), 0);
		if (!totalItems) {
			try { tg.showAlert("Savatcha bo'sh. Avval taom tanlang."); } catch(_) { alert("Savatcha bo'sh. Avval taom tanlang."); }
			return;
		}
		// In Telegram WebApp: always use in-page modal for best UX
		if (window.Telegram && window.Telegram.WebApp) {
			openCheckoutModal();
			return;
		}
		// Outside Telegram: try popup, fallback to modal
		openCheckoutPopup();
	} catch(_){
		openCheckoutModal();
	}
}

// Apply custom restaurant settings
function applyCustomSettings() {
            console.log('Applying restaurant settings:', restaurantSettings);
            
            // Update CSS variables on document root
            document.documentElement.style.setProperty('--primary-color', restaurantSettings.primaryColor);
            document.documentElement.style.setProperty('--secondary-color', restaurantSettings.secondaryColor);
            document.documentElement.style.setProperty('--accent-color', restaurantSettings.accentColor);
            document.documentElement.style.setProperty('--text-color', restaurantSettings.textColor);
            document.documentElement.style.setProperty('--bg-color', restaurantSettings.bgColor);
            document.documentElement.style.setProperty('--card-bg', restaurantSettings.cardBg);
            document.documentElement.style.setProperty('--border-radius', restaurantSettings.borderRadius);
            document.documentElement.style.setProperty('--shadow', restaurantSettings.shadow);
            
            // Force apply to body
            document.body.style.background = restaurantSettings.bgColor;
            document.body.style.color = restaurantSettings.textColor;
            
            // Update specific elements that might not inherit CSS variables
            updateElementStyles();
            
            console.log('CSS variables updated:', {
                '--primary-color': getComputedStyle(document.documentElement).getPropertyValue('--primary-color'),
                '--secondary-color': getComputedStyle(document.documentElement).getPropertyValue('--secondary-color'),
                '--accent-color': getComputedStyle(document.documentElement).getPropertyValue('--accent-color'),
                '--text-color': getComputedStyle(document.documentElement).getPropertyValue('--text-color'),
                '--bg-color': getComputedStyle(document.documentElement).getPropertyValue('--bg-color'),
                '--card-bg': getComputedStyle(document.documentElement).getPropertyValue('--card-bg'),
                '--border-radius': getComputedStyle(document.documentElement).getPropertyValue('--border-radius'),
                '--shadow': getComputedStyle(document.documentElement).getPropertyValue('--shadow')
            });
        }
        
        // Update specific element styles
        function updateElementStyles() {
            console.log('Updating element styles...');
            
            // Update header gradient
            const header = document.querySelector('.header');
            if (header) {
                header.style.background = `linear-gradient(135deg, ${restaurantSettings.primaryColor}, ${restaurantSettings.secondaryColor})`;
                console.log('Header background updated');
            }
            
            // Update category tabs
            const categoryTabs = document.querySelectorAll('.category-tab');
            categoryTabs.forEach(tab => {
                tab.style.borderColor = restaurantSettings.primaryColor;
                tab.style.color = restaurantSettings.primaryColor;
            });
            console.log('Category tabs updated:', categoryTabs.length);
            
            // Update active category tab
            const activeTab = document.querySelector('.category-tab.active');
            if (activeTab) {
                activeTab.style.background = restaurantSettings.primaryColor;
                activeTab.style.color = 'white';
                console.log('Active tab updated');
            }
            
            // Update menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.style.background = restaurantSettings.cardBg;
                item.style.borderRadius = restaurantSettings.borderRadius;
                item.style.boxShadow = restaurantSettings.shadow;
            });
            console.log('Menu items updated:', menuItems.length);
            
            // Update menu item titles
            const menuTitles = document.querySelectorAll('.menu-item-title');
            menuTitles.forEach(title => {
                title.style.color = restaurantSettings.textColor;
            });
            
            // Update menu item prices
            const menuPrices = document.querySelectorAll('.menu-item-price');
            menuPrices.forEach(price => {
                price.style.color = restaurantSettings.accentColor;
            });
            
            // Update quantity buttons
            const plusButtons = document.querySelectorAll('.quantity-btn.plus');
            plusButtons.forEach(btn => {
                btn.style.background = restaurantSettings.accentColor;
            });
            
            // Update cart
            const cartFixed = document.querySelector('.cart-fixed');
            if (cartFixed) {
                cartFixed.style.background = restaurantSettings.cardBg;
                cartFixed.style.padding = '0.75rem 1rem'; // Apply padding here
            }
            
            // Update cart content padding
            const cartContent = document.querySelector('.cart-content');
            if (cartContent) {
                cartContent.style.paddingBottom = '0'; // Apply padding here
            }
            
            // Update cart total
            const cartTotal = document.querySelector('.cart-total');
            if (cartTotal) {
                cartTotal.style.color = restaurantSettings.textColor;
            }
            
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.style.background = restaurantSettings.accentColor;
            }
            
            // Update checkout button
            const checkoutBtn = document.querySelector('.checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.style.background = `linear-gradient(135deg, ${restaurantSettings.primaryColor}, ${restaurantSettings.secondaryColor})`;
                checkoutBtn.style.borderRadius = restaurantSettings.borderRadius;
                checkoutBtn.style.position = 'relative'; // Apply position here
            }
            
            // Update search and filters
            const searchContainer = document.querySelector('.search-container');
            if (searchContainer) {
                searchContainer.style.background = restaurantSettings.cardBg;
            }
            
            const filtersContainer = document.querySelector('.filters-container');
            if (filtersContainer) {
                filtersContainer.style.background = restaurantSettings.cardBg;
            }
            
            // Update search input
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.style.borderColor = restaurantSettings.primaryColor;
                searchInput.style.background = restaurantSettings.cardBg;
                searchInput.style.color = restaurantSettings.textColor;
            }
            
            // Update filter buttons
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.style.background = restaurantSettings.cardBg;
                btn.style.borderColor = restaurantSettings.primaryColor;
                btn.style.color = restaurantSettings.primaryColor;
            });
            
            // Update checkout modal
            const checkoutModal = document.querySelector('.checkout-modal-content');
            if (checkoutModal) {
                checkoutModal.style.background = restaurantSettings.cardBg;
                checkoutModal.style.borderRadius = restaurantSettings.borderRadius;
                checkoutModal.style.boxShadow = restaurantSettings.shadow;
            }
            
            // Update form elements
            const formLabels = document.querySelectorAll('.form-label');
            formLabels.forEach(label => {
                label.style.color = restaurantSettings.textColor;
            });
            
            const formInputs = document.querySelectorAll('.form-input');
            formInputs.forEach(input => {
                input.style.background = restaurantSettings.cardBg;
                input.style.color = restaurantSettings.textColor;
            });
            
            const primaryBtn = document.querySelector('.btn-primary');
            if (primaryBtn) {
                primaryBtn.style.background = restaurantSettings.primaryColor;
            }
            
            console.log('All element styles updated successfully');
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const name = item.dataset.name;
                const description = item.dataset.description;
                const matches = name.includes(searchTerm) || description.includes(searchTerm);
                
                if (matches) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                // Implement filter logic here
                console.log('Filter:', filter);
                
                // Update active button styles
                updateElementStyles();
            });
        });
        
        // Category switching
        function switchCategory(categoryId) {
            // Hide all category contents
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.add('d-none');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected category content
            document.getElementById(`cat-content-${categoryId}`).classList.remove('d-none');
            
            // Add active class to selected tab
            const clicked = Array.from(document.querySelectorAll('.category-tab')).find(t => t.textContent.trim() === (document.querySelector(`#cat-content-${categoryId}`)?.previousCategoryName || t.textContent));
            const tabEl = document.querySelector(`.category-tab[onclick=\"switchCategory(${categoryId})\"]`) || clicked;
            if (tabEl) { tabEl.classList.add('active'); }
            
            currentCategory = categoryId;
            
            // Update styles after category change
            updateElementStyles();
        }
        
        // Add click handler for checkout button (open popup or modal)
        (function(){
            const btn = document.getElementById('checkout-btn');
            if (btn) {
                btn.addEventListener('click', function(){
                    handleCheckoutClick();
                });
            }
        })();
        
        // Quantity management
        function changeQuantity(itemId, change) {
            const currentQty = cart[itemId] || 0;
            const newQty = Math.max(0, currentQty + change);
            
            if (newQty === 0) {
                delete cart[itemId];
            } else {
                cart[itemId] = newQty;
            }
            
            // Update display
            document.getElementById(`qty-${itemId}`).textContent = newQty;
            updateCart();
        }
        
        // Update cart display
        function updateCart() {
            let totalPrice = 0;
            let totalItems = 0;
            
            Object.keys(cart).forEach(itemId => {
                const qty = cart[itemId];
                const price = parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                totalPrice += qty * price;
                totalItems += qty;
            });
            
            document.getElementById('cart-total-price').textContent = totalPrice.toLocaleString();
            document.getElementById('cart-total-items').textContent = totalItems;
            
            // Enable/disable checkout button
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.disabled = totalItems === 0;
            }
        }
        
        // Checkout modal functions
        function openCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            const btn = document.getElementById('checkout-btn');
            if (btn) btn.style.display = 'none';
            const cartBar = document.querySelector('.cart-fixed');
            if (cartBar) cartBar.style.display = 'none';
            // Update totals in modal
            let subtotal = 0;
            let itemsList = '';
            const formatPrice = (num) => num.toLocaleString();
            Object.keys(cart).forEach(itemId => {
                const qty = cart[itemId];
                const price = parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                subtotal += qty * price;
                const name = document.querySelector(`[data-item-id="${itemId}"]`).dataset.name;
                itemsList += `- ${qty} x ${name}: ${formatPrice(qty * price)} so'm\n`;
            });
            document.getElementById('checkoutSubtotal').textContent = subtotal.toLocaleString() + " so'm";
            document.getElementById('checkoutItems').textContent = itemsList;
            const deliveryFee = {{ (float)($restaurant->delivery_fee ?? 0) }};
            document.getElementById('checkoutDeliveryFee').textContent = deliveryFee.toLocaleString() + " so'm";
            document.getElementById('checkoutTotal').textContent = (subtotal + deliveryFee).toLocaleString() + " so'm";
        }
        
        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            const btn = document.getElementById('checkout-btn');
            if (btn) btn.style.display = '';
            const cartBar = document.querySelector('.cart-fixed');
            if (cartBar) cartBar.style.display = '';
        }
        
        // Handle checkout form submission
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const customerData = {
                name: document.getElementById('customerName').value,
                phone: document.getElementById('customerPhone').value,
                address: document.getElementById('customerAddress').value,
                notes: document.getElementById('customerNotes').value
            };
            
            // Save customer data to localStorage
            localStorage.setItem('customerData', JSON.stringify(customerData));

            // Proceed with order
            proceedToCheckout(customerData);
        });
        
        // Proceed to checkout
        function proceedToCheckout(customerData = null) {
            if (Object.keys(cart).length === 0) return;
            
            const botToken = '{{ $botToken ?? $restaurant->bot_token ?? "" }}';
            console.log('Bot token:', botToken);
            
            // Prepare order data
            const orderData = {
                restaurant_id: {{ $restaurant->id }},
                items: Object.keys(cart).map(itemId => ({
                    menu_item_id: parseInt(itemId),
                    quantity: cart[itemId],
                    price: parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price)
                })),
                total_amount: Object.keys(cart).reduce((total, itemId) => {
                    const qty = cart[itemId];
                    const price = parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                    return total + (qty * price);
                }, 0),
                payment_method: document.getElementById('paymentMethod').value,
                telegram_chat_id: tg.initDataUnsafe?.user?.id || localStorage.getItem('tg_chat_id') || null,
                bot_token: botToken,
                customer_name: customerData?.name || 'Anonim',
                customer_phone: customerData?.phone || 'Kiritilmagan',
                customer_address: customerData?.address || 'Kiritilmagan',
                customer_notes: customerData?.notes || '',
                subtotal: Object.keys(cart).reduce((total, itemId) => {
                    const qty = cart[itemId];
                    const price = parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                    return total + (qty * price);
                }, 0),
                delivery_fee: {{ (float)($restaurant->delivery_fee ?? 0) }}
            };
            
            console.log('Order data:', orderData);
            
            // Send order to server
            fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Clear cart
                    cart = {};
                    updateCart();
                    
                    // Update all quantity displays
                    document.querySelectorAll('.quantity-display').forEach(display => {
                        display.textContent = '0';
                    });
                    
                    // Close modal
                    closeCheckoutModal();
                    
                    // Show success message
                    tg.showAlert('Buyurtmangiz muvaffaqiyatli qabul qilindi! 🎉');
                    
                    // Send order to Telegram bot
                    tg.sendData(JSON.stringify({
                        action: 'order_placed',
                        order_id: data.order_id
                    }));
                } else {
                    console.error('Order failed:', data);
                    tg.showAlert('Xatolik yuz berdi: ' + (data.error || 'Noma\'lum xatolik'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tg.showAlert('Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
            });
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            
            // Apply custom settings first
            applyCustomSettings();
            
            // Wire checkout button
            try {
                const btn = document.getElementById('checkout-btn');
                if (btn) {
                    btn.addEventListener('click', function(){
                        handleCheckoutClick();
                    });
                }
            } catch(_) {}
            
            // Update cart
            updateCart();
            
            // Log settings for debugging
            console.log('Restaurant settings applied:', restaurantSettings);
            
            // Apply settings again after a short delay to ensure they take effect
            setTimeout(() => {
                console.log('Applying settings again after delay...');
                applyCustomSettings();
            }, 100);
            
            // Apply settings again after images load
            window.addEventListener('load', function() {
                console.log('Window loaded, applying settings again...');
                applyCustomSettings();
            });

            // Attach image preview modal handlers
            attachImagePreview();
        });
        
        // Apply settings when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('Page became visible, applying settings...');
                applyCustomSettings();
            }
        });
        
        // Apply settings on window focus
        window.addEventListener('focus', function() {
            console.log('Window focused, applying settings...');
            applyCustomSettings();
        });
        
        // Image preview modal initializer
        function attachImagePreview() {
            try {
                const modalEl = document.getElementById('imagePreviewModal');
                if (!modalEl) return;
                const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
                const imgEl = document.getElementById('imagePreviewImage');
                const rotateBtn = document.getElementById('imageRotateBtn');
                let manualRotate = 0;
                let rotationIntervalId = null;

                const computeIsLandscape = () => {
                    try {
                        if (window.screen && window.screen.orientation && typeof window.screen.orientation.angle === 'number') {
                            return Math.abs(window.screen.orientation.angle % 180) === 90;
                        }
                    } catch(_) {}
                    try {
                        if (typeof window.orientation === 'number') {
                            return Math.abs(window.orientation) === 90;
                        }
                    } catch(_) {}
                    try {
                        if (window.matchMedia && window.matchMedia('(orientation: landscape)').matches) return true;
                    } catch(_) {}
                    return (window.innerWidth || 0) > (window.innerHeight || 0);
                };

                const applyManualRotate = () => {
                    imgEl.style.setProperty('--manual-rotate', `${manualRotate}deg`);
                };

                const updateRotation = () => {
                    const isLandscape = computeIsLandscape();
                    if (isLandscape) {
                        imgEl.classList.add('image-preview-rotated');
                    } else {
                        imgEl.classList.remove('image-preview-rotated');
                    }
                };

                const onDeviceOrientation = () => updateRotation();
                const onResize = () => updateRotation();

                function startWatchingRotation() {
                    updateRotation();
                    if (rotationIntervalId) clearInterval(rotationIntervalId);
                    window.addEventListener('orientationchange', onResize);
                    window.addEventListener('resize', onResize);
                    if (window.visualViewport) window.visualViewport.addEventListener('resize', onResize);
                    window.addEventListener('deviceorientation', onDeviceOrientation, true);
                    rotationIntervalId = setInterval(updateRotation, 500);
                }

                function stopWatchingRotation() {
                    window.removeEventListener('orientationchange', onResize);
                    window.removeEventListener('resize', onResize);
                    if (window.visualViewport) window.visualViewport.removeEventListener('resize', onResize);
                    window.removeEventListener('deviceorientation', onDeviceOrientation, true);
                    if (rotationIntervalId) { clearInterval(rotationIntervalId); rotationIntervalId = null; }
                }
                
                // Recompute on modal show/hide + on orientation/resize
                modalEl.addEventListener('shown.bs.modal', () => { manualRotate = 0; applyManualRotate(); startWatchingRotation(); });
                modalEl.addEventListener('hidden.bs.modal', () => { stopWatchingRotation(); });
                
                if (rotateBtn) {
                    rotateBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        manualRotate = (manualRotate + 90) % 360;
                        applyManualRotate();
                    });
                }
                
                document.querySelectorAll('.menu-item-image').forEach(img => {
                    img.style.cursor = 'zoom-in';
                    img.addEventListener('click', function() {
                        const src = this.getAttribute('src') || '';
                        imgEl.setAttribute('src', src);
                        manualRotate = 0; applyManualRotate();
                        modal.show();
                        updateRotation();
                    });
                });
                // Close when clicking on backdrop
                modalEl.addEventListener('click', (e) => { if (e.target === modalEl) { modal.hide(); } });
            } catch(_) {}
        }
		
		window.addEventListener('message', function(event){
			if (event.origin !== window.location.origin) return;
			if (event.data && event.data.type === 'order_placed') {
				cart = {}; updateCart();
				document.querySelectorAll('.quantity-display').forEach(el=>el.textContent='0');
				try { tg.showAlert('Buyurtmangiz muvaffaqiyatli qabul qilindi! 🎉'); } catch(_) {}
			}
		});
    </script>
</body>
</html> 