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
    <meta name="bot-token" content="{{ $botToken ?? '' }}">
    <link rel="stylesheet" href="{{ asset('assets/css/web-interface-index.css') }}">
    
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
    </style>
</head>
<body class="telegram-theme" data-first-category-id="{{ $categories->first()->id ?? 1 }}" data-restaurant-id="{{ $restaurant->id }}">
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
                     onclick="switchCategory({{ $category->id }}, this)">
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
                                    <div class="image-container">
                                        @if($item->image && !empty(trim($item->image)))
                                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xMDAgNjBDMTI3LjYxNCA2MCAxNTAgODIuMzg2IDE1MCAxMTBDMTUwIDEzNy42MTQgMTI3LjYxNCAxNjAgMTAwIDE2MEM3Mi4zODYgMTYwIDUwIDEzNy42MTQgNTAgMTEwQzUwIDgyLjM4NiA3Mi4zODYgNjAgMTAwIDYwWiIgZmlsbD0iI0Q5RTJFNyIvPgo8L3N2Zz4K" 
                                                 data-src="{{ asset($item->image) }}"
                                                 alt="{{ $item->name }}" 
                                                 class="menu-item-image"
                                                 loading="lazy"
                                                 onerror="handleImageError(this)"
                                                 onload="handleImageLoad(this)">
                                            <div class="menu-item-image-placeholder" style="display: none;">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                        @else
                                            <div class="menu-item-image-placeholder">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                        @endif
                                        
                                        <!-- Fallback image for better UX -->
                                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xMDAgNjBDMTI3LjYxNCA2MCAxNTAgODIuMzg2IDE1MCAxMTBDMTUwIDEzNy42MTQgMTI3LjYxNCAxNjAgMTAwIDE2MEM3Mi4zODYgMTYwIDUwIDEzNy42MTQgNTAgMTEwQzUwIDgyLjM4NiA3Mi4zODYgNjAgMTAwIDYwWiIgZmlsbD0iI0Q5RTJFNyIvPgo8L3N2Zz4K" 
                                             alt="Fallback" 
                                             class="menu-item-image-fallback" 
                                             style="display: none;">
                                    </div>
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
        <div class="modal-dialog modal-fullscreen">
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
    <script src="{{ asset('assets/js/web-interface-index.js') }}"></script>
</body>
</html> 