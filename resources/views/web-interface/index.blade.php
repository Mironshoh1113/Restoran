<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .restaurant-description {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .category {
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-header {
            background: #f8f9fa;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        
        .menu-item {
            display: flex;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .item-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
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
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: #007bff;
            color: white;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .quantity {
            font-weight: bold;
            min-width: 30px;
            text-align: center;
        }
        
        .cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #eee;
            padding: 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .cart-total {
            font-weight: bold;
            font-size: 18px;
        }
        
        .checkout-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }
        
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .order-form {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .payment-method {
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
        }
        
        .payment-method.selected {
            border-color: #007bff;
            background: #f8f9ff;
        }
        
        .hidden {
            display: none;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="restaurant-name">{{ $restaurant->name }}</div>
        <div class="restaurant-description">{{ $restaurant->description ?? 'Menyu va buyurtma berish' }}</div>
    </div>
    
    <div class="container">
        <div id="menu-container">
            @foreach($categories as $category)
                <div class="category">
                    <div class="category-header">{{ $category->name }}</div>
                    @foreach($category->menuItems as $item)
                        <div class="menu-item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
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
                    @endforeach
                </div>
            @endforeach
        </div>
        
        <div id="order-form" class="order-form hidden">
            <h3>Buyurtma ma'lumotlari</h3>
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
                            <div>💵 Naqd pul</div>
                        </div>
                        <div class="payment-method" data-method="card">
                            <div>💳 Karta</div>
                        </div>
                    </div>
                    <input type="hidden" id="payment-method" value="cash">
                </div>
                
                <button type="submit" class="checkout-btn" style="width: 100%; margin-top: 20px;">
                    Buyurtma berish
                </button>
            </form>
        </div>
        
        <div id="success-message" class="success-message hidden">
            ✅ Buyurtma qabul qilindi! Tez orada siz bilan bog'lanamiz.
        </div>
    </div>
    
    <div class="cart" id="cart">
        <div class="cart-header">
            <div class="cart-total">
                Jami: <span id="cart-total">0</span> so'm
            </div>
            <button class="checkout-btn" id="checkout-btn" onclick="showOrderForm()" disabled>
                Buyurtma berish
            </button>
        </div>
    </div>
    
    <script>
        // Initialize Telegram Web App
        let tg = null;
        
        // Check if we're in Telegram Web App
        if (window.Telegram && window.Telegram.WebApp) {
            tg = window.Telegram.WebApp;
            tg.ready();
            tg.expand();
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
        
        let cart = {};
        let selectedPaymentMethod = 'cash';
        
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
                payment_method: selectedPaymentMethod
            };
            
            // Get the current URL to determine the endpoint
            const currentUrl = window.location.pathname;
            const token = currentUrl.split('/').pop();
            const endpoint = token && token !== 'web-interface' ? 
                `/web-interface/${token}/order` : 
                '/web-interface/order';
            
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
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
                    alert('Xatolik yuz berdi: ' + data.error);
                }
            })
            .catch(error => {
                alert('Xatolik yuz berdi: ' + error.message);
            });
        });
        
        // Initialize cart total
        updateCartTotal();
    </script>
</body>
</html> 