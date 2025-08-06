<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #333;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .restaurant-description {
            font-size: 14px;
            color: #666;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 15px;
            padding-bottom: 120px; /* Space for cart */
        }
        
        .categories-nav {
            display: flex;
            overflow-x: auto;
            gap: 8px;
            padding: 10px 0;
            margin-bottom: 15px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
        }
        
        .categories-nav::-webkit-scrollbar {
            display: none;
        }
        
        .category-tab {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 10px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            color: #666;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        
        .category-tab.active {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }
        
        .category-content {
            display: none;
        }
        
        .category-content.active {
            display: block;
        }
        
        .menu-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        }
        
        .item-content {
            display: flex;
            padding: 15px;
            align-items: center;
            gap: 12px;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .item-image i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
        
        .item-info {
            flex: 1;
            min-width: 0; /* Allow text to shrink */
        }
        
        .item-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
            color: #333;
            line-height: 1.3;
        }
        
        .item-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .item-price {
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            -webkit-tap-highlight-color: transparent;
        }
        
        .quantity-btn:hover {
            transform: scale(1.1);
        }
        
        .quantity-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .quantity {
            font-weight: bold;
            min-width: 30px;
            text-align: center;
            font-size: 16px;
            color: #333;
        }
        
        .cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255,255,255,0.2);
            padding: 15px 20px;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .cart-total {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            -webkit-tap-highlight-color: transparent;
        }
        
        .checkout-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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
    </style>
</head>
<body>
    <div class="header">
        <div class="restaurant-name">{{ $restaurant->name }}</div>
        <div class="restaurant-description">{{ $restaurant->description ?? 'Menyu va buyurtma berish' }}</div>
    </div>
    
    <div class="container">
        <!-- Categories Navigation -->
        <div class="categories-nav" id="categories-nav">
            @foreach($categories as $category)
                <button class="category-tab {{ $loop->first ? 'active' : '' }}" 
                        onclick="showCategory({{ $category->id }})">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
        
        <!-- Menu Items by Category -->
        <div id="menu-container">
            @foreach($categories as $category)
                <div class="category-content {{ $loop->first ? 'active' : '' }}" id="category-{{ $category->id }}">
                    @foreach($category->menuItems as $item)
                        <div class="menu-item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
                            <div class="item-content">
                                <div class="item-image">
                                    @if($item->hasImage())
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
        
        <div id="order-form" class="order-form hidden">
            <button class="back-btn" onclick="showMenu()">
                <i class="fas fa-arrow-left"></i> Orqaga
            </button>
            <div class="form-title">Buyurtma ma'lumotlari</div>
            <form id="checkout-form">
                <div class="form-group">
                    <label class="form-label">Ismingiz</label>
                    <input type="text" class="form-input" id="customer-name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telefon raqam</label>
                    <input type="tel" class="form-input" id="customer-phone" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Yetkazib berish manzili</label>
                    <textarea class="form-input" id="delivery-address" rows="3" required></textarea>
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
                    <input type="hidden" id="payment-method" value="cash">
                </div>
                
                <button type="submit" class="checkout-btn" style="width: 100%; margin-top: 15px;">
                    <i class="fas fa-shopping-cart"></i> Buyurtma berish
                </button>
            </form>
        </div>
        
        <div id="success-message" class="success-message hidden">
            <i class="fas fa-check-circle"></i><br>
            <i class="fas fa-check"></i> Buyurtma qabul qilindi! Tez orada siz bilan bog'lanamiz.
        </div>
    </div>
    
    <div class="cart" id="cart">
        <div class="cart-header">
            <div class="cart-total">
                <i class="fas fa-shopping-cart"></i> Jami: <span id="cart-total">0</span> so'm
            </div>
            <button class="checkout-btn" id="checkout-btn" onclick="showOrderForm()" disabled>
                <i class="fas fa-credit-card"></i> Buyurtma berish
            </button>
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
        
        function showCategory(categoryId) {
            // Hide all category contents
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected category content
            document.getElementById(`category-${categoryId}`).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
        
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
        }
        
        function updateCartTotal() {
            let total = 0;
            for (let itemId in cart) {
                const price = parseInt(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
                total += price * cart[itemId];
            }
            
            document.getElementById('cart-total').textContent = total.toLocaleString();
            document.getElementById('checkout-btn').disabled = total === 0;
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
            
            // Get the current URL to determine the endpoint
            const currentUrl = window.location.pathname;
            const token = currentUrl.split('/').pop();
            const endpoint = token && token !== 'web-interface' ? 
                `/web-interface/${token}/order` : 
                '/web-interface/order';
            
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
    </script>
</body>
</html> 