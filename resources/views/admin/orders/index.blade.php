@extends('admin.layouts.app')

@section('title', 'Buyurtmalar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buyurtmalar</h1>
            <p class="text-gray-600 mt-1">Barcha buyurtmalarni boshqarish va kuzatish</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Buyurtma raqami yoki mijoz nomi..." 
                       class="pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-80">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Barcha holatlar</option>
                <option value="pending">Yangi</option>
                <option value="preparing">Tayyorlanmoqda</option>
                <option value="on_way">Yolda</option>
                <option value="delivered">Yetkazildi</option>
                <option value="cancelled">Bekor qilindi</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Jami buyurtmalar</p>
                    <p class="text-3xl font-bold">{{ $orders->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Yangi</p>
                    <p class="text-3xl font-bold">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Tayyorlanmoqda</p>
                    <p class="text-3xl font-bold">{{ $orders->where('status', 'preparing')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-utensils text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Yolda</p>
                    <p class="text-3xl font-bold">{{ $orders->where('status', 'on_way')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-truck text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Yetkazildi</p>
                    <p class="text-3xl font-bold">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800">Buyurtmalar ro'yxati</h3>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full">{{ $orders->total() }} ta buyurtma</span>
                    <button onclick="location.reload()" class="p-2 text-gray-600 hover:bg-white rounded-lg transition-colors">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            @if($orders->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($orders as $order)
                    <div class="p-6 hover:bg-gray-50 transition-all duration-200 order-item" 
                         data-status="{{ $order->status }}" 
                         data-number="{{ $order->order_number ?? $order->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-shopping-bag text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="text-xl font-bold text-gray-900">#{{ $order->order_number ?? $order->id }}</h4>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'preparing' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                'on_way' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $statusTexts = [
                                                'pending' => 'Yangi',
                                                'preparing' => 'Tayyorlanmoqda',
                                                'on_way' => 'Yolda',
                                                'delivered' => 'Yetkazildi',
                                                'cancelled' => 'Bekor qilindi',
                                            ];
                                        @endphp
                                        <span class="px-4 py-2 text-sm font-semibold rounded-full border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                            {{ $statusTexts[$order->status] ?? 'Nomalum' }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <i class="fas fa-user text-blue-500"></i>
                                            <span class="font-medium">{{ $order->customer_name ?? 'Nomalum' }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <i class="fas fa-phone text-green-500"></i>
                                            <span class="font-medium">{{ $order->customer_phone ?? 'Nomalum' }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-gray-600">
                                            <i class="fas fa-store text-purple-500"></i>
                                            <span class="font-medium">
                                                @if($order->restaurant)
                                                    {{ $order->restaurant->name }}
                                                @elseif($order->project && $order->project->restaurant)
                                                    {{ $order->project->restaurant->name }}
                                                @else
                                                    Nomalum
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                        <span class="flex items-center space-x-1">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                        </span>
                                        @if($order->courier)
                                            <span class="flex items-center space-x-1">
                                                <i class="fas fa-truck"></i>
                                                <span>{{ $order->courier->name }}</span>
                                            </span>
                                        @endif
                                        @if($order->delivery_address)
                                            <span class="flex items-center space-x-1">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span class="truncate max-w-xs">{{ $order->delivery_address }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-3xl font-bold text-gray-900 mb-2">
                                    {{ number_format($order->total_amount ?? $order->total_price ?? 0) }} so'm
                                </div>
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="viewOrder({{ $order->id }})" 
                                            class="p-3 text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-105">
                                        <i class="fas fa-eye text-lg"></i>
                                    </button>
                                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" 
                                                    class="p-3 text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200 hover:scale-105">
                                                <i class="fas fa-edit text-lg"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                                <div class="py-2">
                                                    <button onclick="updateStatus({{ $order->id }}, 'preparing')" 
                                                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-utensils mr-3"></i>Tayyorlanmoqda
                                                    </button>
                                                    <button onclick="updateStatus({{ $order->id }}, 'on_way')" 
                                                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-truck mr-3"></i>Yolda
                                                    </button>
                                                    <button onclick="updateStatus({{ $order->id }}, 'delivered')" 
                                                            class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-check mr-3"></i>Yetkazildi
                                                    </button>
                                                    <hr class="my-1">
                                                    <button onclick="updateStatus({{ $order->id }}, 'cancelled')" 
                                                            class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                        <i class="fas fa-times mr-3"></i>Bekor qilish
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($order->items || $order->orderItems)
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-gray-700">Buyurtma tarkibi:</span>
                                    @php
                                        $itemCount = 0;
                                        if ($order->items) {
                                            $items = json_decode($order->items, true);
                                            $itemCount = count($items);
                                        } elseif ($order->orderItems) {
                                            $itemCount = $order->orderItems->count();
                                        }
                                    @endphp
                                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $itemCount }} ta taom</span>
                                </div>
                                
                                @if($order->items)
                                    @php $items = json_decode($order->items, true); @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach(array_slice($items, 0, 6) as $item)
                                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                                <span class="text-sm font-medium text-gray-700">{{ $item['name'] ?? 'Nomalum' }}</span>
                                                <span class="text-sm font-bold text-gray-800 bg-white px-2 py-1 rounded">{{ $item['quantity'] ?? 1 }}x</span>
                                            </div>
                                        @endforeach
                                        @if(count($items) > 6)
                                            <div class="flex items-center justify-center p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                                                <span class="text-sm text-blue-600 font-medium">+{{ count($items) - 6 }} ta boshqa</span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($order->orderItems)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($order->orderItems->take(6) as $item)
                                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                                <span class="text-sm font-medium text-gray-700">{{ $item->menuItem->name }}</span>
                                                <span class="text-sm font-bold text-gray-800 bg-white px-2 py-1 rounded">{{ $item->quantity }}x</span>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 6)
                                            <div class="flex items-center justify-center p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                                                <span class="text-sm text-blue-600 font-medium">+{{ $order->orderItems->count() - 6 }} ta boshqa</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-6 border-t border-gray-200 bg-gray-50">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Buyurtmalar mavjud emas</h3>
                    <p class="text-gray-500 text-lg">Hozirda hech qanday buyurtma yo'q</p>
                    <div class="mt-6">
                        <button onclick="location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-medium transition-colors">
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