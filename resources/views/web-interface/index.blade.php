<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="bot-token" content="{{ $botToken ?? '' }}">
    
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
            background: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
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
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
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
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .restaurant-info p {
            color: rgba(255,255,255,0.9);
            margin: 0;
            font-size: 0.9rem;
        }

        /* Categories */
        .categories-container {
            padding: 1rem 0;
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
            z-index: 999;
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
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
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
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        }

        .category-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        /* Menu Items */
        .menu-container {
            padding: 1rem;
        }

        .menu-item {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .menu-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .menu-item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
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
        }

        .menu-item-content {
            padding: 1.5rem;
        }

        .menu-item-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
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
            color: var(--accent-color);
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
            background: var(--accent-color);
            color: white;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
        }

        .quantity-display {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
            min-width: 40px;
            text-align: center;
        }

        /* Cart */
        .cart-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
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
            color: var(--text-color);
        }

        .cart-count {
            background: var(--accent-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius);
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
        }

        /* Dark Theme Support */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #1a1a1a;
                --card-bg: #2a2a2a;
                --text-color: #ffffff;
            }
        }

        /* Telegram Theme Integration */
        .telegram-theme {
            --bg-color: var(--tg-theme-bg-color, #ffffff);
            --text-color: var(--tg-theme-text-color, #2c3e50);
            --card-bg: var(--tg-theme-secondary-bg-color, #ffffff);
            --primary-color: var(--tg-theme-button-color, #667eea);
        }
    </style>
</head>
<body class="telegram-theme">
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
                        <h1>{{ $restaurant->name }}</h1>
                        <p>{{ $restaurant->description ?? 'Zamonaviy restoran menyusi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories -->
    <div class="categories-container">
        <div class="category-tabs">
            @foreach($categories as $category)
                <div class="category-tab {{ $loop->first ? 'active' : '' }}" 
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
                        <div class="menu-item" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    @if($item->image && !empty(trim($item->image)))
                                        <img src="{{ asset('storage/' . $item->image) }}" 
                                             alt="{{ $item->name }}" 
                                             class="menu-item-image"
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

    <!-- Fixed Cart -->
    <div class="cart-fixed">
        <div class="cart-content">
            <div class="cart-summary">
                <div class="cart-total">
                    Jami: <span id="cart-total-price">0</span> so'm
                </div>
                <div class="cart-count" id="cart-total-items">0</div>
            </div>
            <button class="checkout-btn" id="checkout-btn" onclick="proceedToCheckout()" disabled>
                <i class="fas fa-shopping-cart me-2"></i>Buyurtma berish
            </button>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buyurtma ma'lumotlari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="checkout-items"></div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Jami:</h6>
                        <h5 id="checkout-total">0 so'm</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                    <button type="button" class="btn btn-primary" onclick="confirmOrder()">Tasdiqlash</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let cart = {};
        let currentCategory = {{ $categories->first()->id ?? 1 }};
        
        // Initialize Telegram Web App
        let tg = window.Telegram.WebApp;
        tg.ready();
        tg.expand();
        
        // Apply Telegram theme
        document.body.classList.add('telegram-theme');
        
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
            event.target.classList.add('active');
            
            currentCategory = categoryId;
        }
        
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
            if (totalItems > 0) {
                checkoutBtn.disabled = false;
            } else {
                checkoutBtn.disabled = true;
            }
        }
        
        // Proceed to checkout
        function proceedToCheckout() {
            if (Object.keys(cart).length === 0) return;
            
            let checkoutHtml = '';
            let totalPrice = 0;
            
            Object.keys(cart).forEach(itemId => {
                const qty = cart[itemId];
                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                const name = itemElement.querySelector('.menu-item-title').textContent;
                const price = parseFloat(itemElement.dataset.price);
                const itemTotal = qty * price;
                totalPrice += itemTotal;
                
                checkoutHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>${name}</strong>
                            <br><small class="text-muted">${qty} x ${price.toLocaleString()} so'm</small>
                        </div>
                        <strong>${itemTotal.toLocaleString()} so'm</strong>
                    </div>
                `;
            });
            
            document.getElementById('checkout-items').innerHTML = checkoutHtml;
            document.getElementById('checkout-total').textContent = totalPrice.toLocaleString() + ' so\'m';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
            modal.show();
        }
        
        // Confirm order
        function confirmOrder() {
            if (Object.keys(cart).length === 0) return;
            
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
                telegram_chat_id: tg.initDataUnsafe?.user?.id || null,
                bot_token: '{{ $botToken }}'
            };
            
            // Send order to server
            fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear cart
                    cart = {};
                    updateCart();
                    
                    // Update all quantity displays
                    Object.keys(cart).forEach(itemId => {
                        document.getElementById(`qty-${itemId}`).textContent = '0';
                    });
                    
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
                    modal.hide();
                    
                    // Show success message
                    tg.showAlert('Buyurtmangiz muvaffaqiyatli qabul qilindi! 🎉');
                    
                    // Send order to Telegram bot
                    tg.sendData(JSON.stringify({
                        action: 'order_placed',
                        order_id: data.order_id
                    }));
                } else {
                    tg.showAlert('Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tg.showAlert('Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
            });
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCart();
        });
    </script>
</body>
</html> 