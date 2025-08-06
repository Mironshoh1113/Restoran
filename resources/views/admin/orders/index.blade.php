@extends('admin.layouts.app')

@section('title', 'Buyurtmalar')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Buyurtmalar</h1>
            <p class="text-gray-600 mt-1 text-sm">Barcha buyurtmalarni boshqarish</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Qidirish..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Barcha holatlar</option>
                <option value="new">🆕 Yangi</option>
                <option value="preparing">👨‍🍳 Tayyorlanmoqda</option>
                <option value="on_way">🚚 Yolda</option>
                <option value="delivered">✅ Yetkazildi</option>
                <option value="cancelled">❌ Bekor qilindi</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs font-medium">Jami</p>
                    <p class="text-xl font-bold">{{ $orders->total() }}</p>
                </div>
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-xs font-medium">Yangi</p>
                    <p class="text-xl font-bold">{{ $orders->where('status', 'new')->count() }}</p>
                </div>
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-xs font-medium">Tayyorlanmoqda</p>
                    <p class="text-xl font-bold">{{ $orders->where('status', 'preparing')->count() }}</p>
                </div>
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-xs font-medium">Yolda</p>
                    <p class="text-xl font-bold">{{ $orders->where('status', 'on_way')->count() }}</p>
                </div>
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow p-4 text-white col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-xs font-medium">Yetkazildi</p>
                    <p class="text-xl font-bold">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Buyurtmalar ro'yxati</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">{{ $orders->total() }} ta</span>
                    <button onclick="location.reload()" class="p-1.5 text-gray-600 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            @if($orders->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4">
                    @foreach($orders as $order)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-200 order-item" 
                         data-status="{{ $order->status }}" 
                         data-number="{{ $order->order_number ?? $order->id }}">
                        
                        <!-- Order Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow">
                                    <i class="fas fa-shopping-bag text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">#{{ $order->order_number ?? $order->id }}</h4>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'preparing' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'on_way' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $statusTexts = [
                                    'new' => '🆕 Yangi',
                                    'preparing' => '👨‍🍳 Tayyorlanmoqda',
                                    'on_way' => '🚚 Yolda',
                                    'delivered' => '✅ Yetkazildi',
                                    'cancelled' => '❌ Bekor qilindi',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                {{ $statusTexts[$order->status] ?? 'Nomalum' }}
                            </span>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="space-y-2 mb-3">
                            <div class="flex items-center space-x-2 text-sm">
                                <i class="fas fa-user text-blue-500 w-4"></i>
                                <span class="font-medium text-gray-700">{{ $order->customer_name ?? 'Nomalum' }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm">
                                <i class="fas fa-phone text-green-500 w-4"></i>
                                <span class="font-medium text-gray-700">{{ $order->customer_phone ?? 'Nomalum' }}</span>
                            </div>
                            @if($order->delivery_address)
                            <div class="flex items-center space-x-2 text-sm">
                                <i class="fas fa-map-marker-alt text-purple-500 w-4"></i>
                                <span class="text-gray-600 truncate">{{ $order->delivery_address }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Restaurant Info -->
                        <div class="flex items-center space-x-2 text-sm mb-3">
                            <i class="fas fa-store text-purple-500 w-4"></i>
                            <span class="text-gray-600">
                                @if($order->restaurant)
                                    {{ $order->restaurant->name }}
                                @elseif($order->project && $order->project->restaurant)
                                    {{ $order->project->restaurant->name }}
                                @else
                                    Nomalum
                                @endif
                            </span>
                        </div>
                        
                        <!-- Order Items Preview -->
                        @if($order->items || $order->orderItems)
                            <div class="mb-3">
                                @php
                                    $itemCount = 0;
                                    if ($order->items) {
                                        $items = json_decode($order->items, true);
                                        $itemCount = count($items);
                                    } elseif ($order->orderItems) {
                                        $itemCount = $order->orderItems->count();
                                    }
                                @endphp
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-gray-700">Buyurtma tarkibi:</span>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $itemCount }} ta taom</span>
                                </div>
                                
                                @if($order->items)
                                    @php $items = json_decode($order->items, true); @endphp
                                    <div class="space-y-1">
                                        @foreach(array_slice($items, 0, 3) as $item)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                                <span class="text-xs font-medium text-gray-700">{{ $item['name'] ?? 'Nomalum' }}</span>
                                                <span class="text-xs font-bold text-gray-800 bg-white px-2 py-0.5 rounded">{{ $item['quantity'] ?? 1 }}x</span>
                                            </div>
                                        @endforeach
                                        @if(count($items) > 3)
                                            <div class="text-center p-2 bg-blue-50 rounded-lg">
                                                <span class="text-xs text-blue-600 font-medium">+{{ count($items) - 3 }} ta boshqa</span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($order->orderItems)
                                    <div class="space-y-1">
                                        @foreach($order->orderItems->take(3) as $item)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                                <span class="text-xs font-medium text-gray-700">{{ $item->menuItem->name }}</span>
                                                <span class="text-xs font-bold text-gray-800 bg-white px-2 py-0.5 rounded">{{ $item->quantity }}x</span>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 3)
                                            <div class="text-center p-2 bg-blue-50 rounded-lg">
                                                <span class="text-xs text-blue-600 font-medium">+{{ $order->orderItems->count() - 3 }} ta boshqa</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Price and Actions -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">
                                    {{ number_format($order->total_amount ?? $order->total_price ?? 0) }} so'm
                                </div>
                                @if($order->courier)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-truck mr-1"></i>{{ $order->courier->name }}
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1">
                                <button onclick="viewOrder({{ $order->id }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" 
                                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" 
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                            <div class="py-1">
                                                <button onclick="updateStatus({{ $order->id }}, 'preparing')" 
                                                        class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                                                    <i class="fas fa-utensils mr-2"></i>Tayyorlanmoqda
                                                </button>
                                                <button onclick="updateStatus({{ $order->id }}, 'on_way')" 
                                                        class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                                                    <i class="fas fa-truck mr-2"></i>Yolda
                                                </button>
                                                <button onclick="updateStatus({{ $order->id }}, 'delivered')" 
                                                        class="block w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                                                    <i class="fas fa-check mr-2"></i>Yetkazildi
                                                </button>
                                                <hr class="my-1">
                                                <button onclick="updateStatus({{ $order->id }}, 'cancelled')" 
                                                        class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-times mr-2"></i>Bekor qilish
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-4 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Buyurtmalar mavjud emas</h3>
                    <p class="text-gray-500 text-sm">Hozirda hech qanday buyurtma yo'q</p>
                    <div class="mt-4">
                        <button onclick="location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                            <i class="fas fa-sync-alt mr-2"></i>Yangilash
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const orderItems = document.querySelectorAll('.order-item');
    
    orderItems.forEach(item => {
        const orderNumber = item.getAttribute('data-number').toLowerCase();
        const customerName = item.querySelector('.text-gray-900').textContent.toLowerCase();
        
        if (orderNumber.includes(searchTerm) || customerName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function() {
    const selectedStatus = this.value;
    const orderItems = document.querySelectorAll('.order-item');
    
    orderItems.forEach(item => {
        const status = item.getAttribute('data-status');
        
        if (!selectedStatus || status === selectedStatus) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

function viewOrder(id) {
    window.location.href = `/admin/orders/${id}`;
}

function updateStatus(orderId, status) {
    if (confirm('Buyurtma holatini yangilamoqchimisiz?')) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Xatolik yuz berdi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xatolik yuz berdi');
        });
    }
}

// Auto-refresh orders every 30 seconds
setInterval(function() {
    console.log('Refreshing orders...');
}, 30000);

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    const orderItems = document.querySelectorAll('.order-item');
    orderItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection 