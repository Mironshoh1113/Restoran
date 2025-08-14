<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $restaurant->name ?? 'Restaurant' }} - Simple Interface</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>{{ $restaurant->name ?? 'Restaurant' }}</h1>
        <p>Simple Web Interface Test</p>
        
        <div class="alert alert-success">
            ✅ Web interface is working!
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Restaurant Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $restaurant->name ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $restaurant->phone ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $restaurant->address ?? 'N/A' }}</p>
                <p><strong>Bot Token:</strong> {{ substr($botToken ?? '', 0, 10) }}...</p>
            </div>
        </div>
        
        @if(isset($categories) && $categories->count() > 0)
        <div class="mt-4">
            <h3>Categories ({{ $categories->count() }})</h3>
            @foreach($categories as $category)
            <div class="card mt-2">
                <div class="card-body">
                    <h5>{{ $category->name }}</h5>
                    @if($category->menuItems && $category->menuItems->count() > 0)
                        <p>Menu items: {{ $category->menuItems->count() }}</p>
                        @foreach($category->menuItems as $item)
                        <div class="border p-2 mt-1">
                            <strong>{{ $item->name }}</strong> - {{ number_format($item->price) }} so'm
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No menu items</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-warning mt-4">
            No categories found
        </div>
        @endif
        
        <div class="mt-4">
            <button class="btn btn-primary" onclick="testTelegram()">Test Telegram Integration</button>
        </div>
    </div>

    <script>
        // Initialize Telegram Web App
        const tg = window.Telegram?.WebApp;
        
        if (tg) {
            tg.ready();
            tg.expand();
            console.log('Telegram Web App initialized');
        } else {
            console.log('Not running in Telegram');
        }
        
        function testTelegram() {
            if (tg) {
                tg.showAlert('Telegram integration is working!');
            } else {
                alert('Not running in Telegram Web App');
            }
        }
        
        console.log('Simple web interface loaded');
        console.log('Restaurant:', @json($restaurant ?? null));
        console.log('Categories:', @json($categories ?? null));
        console.log('Bot Token:', '{{ substr($botToken ?? '', 0, 10) }}...');
    </script>
</body>
</html> 