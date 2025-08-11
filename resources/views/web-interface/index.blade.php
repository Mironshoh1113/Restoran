<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name }} - Menyu</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="bot-token" content="{{ $botToken ?? '' }}">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 15px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; border: none; }
        .quantity-btn { width: 35px; height: 35px; border-radius: 50%; }
        .cart-fixed { position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <span class="navbar-brand fw-bold">{{ $restaurant->name }}</span>
            <small class="text-white-50">{{ $restaurant->description ?? 'Restoran menyusi' }}</small>
        </div>
    </nav>

    <div class="container mt-3 mb-5">
        <!-- Categories -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group w-100" role="group">
                    @foreach($categories as $category)
                        <input type="radio" class="btn-check" name="category" id="cat-{{ $category->id }}" 
                               {{ $loop->first ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="cat-{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        @foreach($categories as $category)
            <div class="category-content {{ $loop->first ? '' : 'd-none' }}" id="cat-content-{{ $category->id }}">
                @foreach($category->menuItems as $item)
                    <div class="card mb-3" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h6 class="card-title mb-1">{{ $item->name }}</h6>
                                    <p class="card-text text-muted small mb-2">{{ $item->description }}</p>
                                    <strong class="text-success">{{ number_format($item->price) }} so'm</strong>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-secondary quantity-btn" 
                                                onclick="changeQuantity({{ $item->id }}, -1)">-</button>
                                        <span class="btn btn-sm btn-outline-secondary" id="qty-{{ $item->id }}">0</span>
                                        <button class="btn btn-sm btn-outline-primary quantity-btn" 
                                                onclick="changeQuantity({{ $item->id }}, 1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Fixed Cart -->
    <div class="cart-fixed p-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6">
                    <strong>Jami: <span id="cart-total">0</span> so'm</strong>
                </div>
                <div class="col-6 text-end">
                    <button class="btn btn-primary" id="order-btn" onclick="showOrderModal()" disabled>
                        Buyurtma berish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buyurtma ma'lumotlari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Cart Items -->
                    <div class="mb-3">
                        <h6>Savat</h6>
                        <div id="modal-cart-items"></div>
                        <hr>
                        <div class="text-end">
                            <strong>Jami: <span id="modal-total">0</span> so'm</strong>
                        </div>
                    </div>

                    <!-- Order Form -->
                    <form id="orderForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ismingiz</label>
                                <input type="text" class="form-control" id="customerName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="tel" class="form-control" id="customerPhone" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Manzil</label>
                            <textarea class="form-control" id="deliveryAddress" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">To'lov usuli</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cash" value="cash" checked>
                                <label class="form-check-label" for="cash">Naqd pul</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="card" value="card">
                                <label class="form-check-label" for="card">Karta</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                    <button type="button" class="btn btn-primary" onclick="submitOrder()">
                        <span id="submit-text">Buyurtma berish</span>
                        <span id="submit-spinner" class="d-none">
                            <i class="fas fa-spinner fa-spin"></i> Yuborilmoqda...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Buyurtma qabul qilindi!</h5>
                    <p class="text-muted">Tez orada siz bilan bog'lanamiz</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Telegram Web App
        let tg = null;
        let telegramChatId = null;
        let cart = {};
        let orderModal, successModal;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            successModal = new bootstrap.Modal(document.getElementById('successModal'));

            // Initialize Telegram Web App
            if (window.Telegram && window.Telegram.WebApp) {
                tg = window.Telegram.WebApp;
                tg.ready();
                tg.expand();
                
                if (tg.initDataUnsafe && tg.initDataUnsafe.user) {
                    telegramChatId = tg.initDataUnsafe.user.id;
                }
                console.log('Telegram Web App initialized');
            }

            // Category switching
            document.querySelectorAll('input[name="category"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.category-content').forEach(content => {
                        content.classList.add('d-none');
                    });
                    document.getElementById('cat-content-' + this.id.replace('cat-', '')).classList.remove('d-none');
                });
            });
        });

        // Cart functions
        function changeQuantity(itemId, change) {
            const currentQty = cart[itemId] || 0;
            const newQty = Math.max(0, currentQty + change);
            
            if (newQty === 0) {
                delete cart[itemId];
            } else {
                cart[itemId] = newQty;
            }
            
            document.getElementById(`qty-${itemId}`).textContent = newQty;
            updateCart();
        }

        function updateCart() {
            let total = 0;
            let hasItems = false;
            
            for (let itemId in cart) {
                hasItems = true;
                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                if (itemElement) {
                    const price = parseInt(itemElement.dataset.price);
                    total += price * cart[itemId];
                }
            }
            
            document.getElementById('cart-total').textContent = total.toLocaleString();
            document.getElementById('order-btn').disabled = !hasItems;
        }

        // Order functions
        function showOrderModal() {
            renderModalCart();
            orderModal.show();
        }

        function renderModalCart() {
            const container = document.getElementById('modal-cart-items');
            let total = 0;
            let html = '';
            
            for (let itemId in cart) {
                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                if (itemElement) {
                    const name = itemElement.querySelector('.card-title').textContent;
                    const price = parseInt(itemElement.dataset.price);
                    const qty = cart[itemId];
                    const subtotal = price * qty;
                    total += subtotal;
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>${name} x${qty}</span>
                            <span>${subtotal.toLocaleString()} so'm</span>
                        </div>
                    `;
                }
            }
            
            container.innerHTML = html;
            document.getElementById('modal-total').textContent = total.toLocaleString();
        }

        function submitOrder() {
            const form = document.getElementById('orderForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get form data
            const orderData = {
                items: Object.keys(cart).map(id => ({
                    id: parseInt(id),
                    quantity: cart[id]
                })),
                customer_name: document.getElementById('customerName').value,
                customer_phone: document.getElementById('customerPhone').value,
                delivery_address: document.getElementById('deliveryAddress').value,
                payment_method: document.querySelector('input[name="payment"]:checked').value,
                telegram_chat_id: telegramChatId
            };

            // Get bot token
            const botToken = document.querySelector('meta[name="bot-token"]')?.getAttribute('content');
            if (botToken) {
                orderData.bot_token = botToken;
            }

            // Show loading
            document.getElementById('submit-text').classList.add('d-none');
            document.getElementById('submit-spinner').classList.remove('d-none');

            // Submit order
            fetch('/web-interface/order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    orderModal.hide();
                    successModal.show();
                    
                    // Send to Telegram
                    if (tg && tg.sendData) {
                        tg.sendData(JSON.stringify({
                            action: 'order_placed',
                            order_id: data.order_id
                        }));
                    }
                    
                    // Close after 3 seconds
                    setTimeout(() => {
                        if (tg && tg.close) {
                            tg.close();
                        } else {
                            successModal.hide();
                            location.reload();
                        }
                    }, 3000);
                    
                    // Clear cart
                    cart = {};
                    updateCart();
                } else {
                    alert('Xatolik: ' + (data.error || 'Noma\'lum xatolik'));
                }
            })
            .catch(error => {
                console.error('Order error:', error);
                alert('Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
            })
            .finally(() => {
                document.getElementById('submit-text').classList.remove('d-none');
                document.getElementById('submit-spinner').classList.add('d-none');
            });
        }
    </script>
</body>
</html> 