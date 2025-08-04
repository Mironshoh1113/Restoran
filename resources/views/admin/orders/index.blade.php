@extends('admin.layouts.app')

@section('title', 'Buyurtmalar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buyurtmalar</h1>
            <p class="text-gray-600">Barcha buyurtmalarni boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Buyurtma qidirish..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Barcha holatlar</option>
                <option value="new">Yangi</option>
                <option value="preparing">Tayyorlanmoqda</option>
                <option value="on_way">Yolda</option>
                <option value="delivered">Yetkazildi</option>
                <option value="cancelled">Bekor qilindi</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jami buyurtmalar</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $orders->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Yangi</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $orders->where('status', 'new')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tayyorlanmoqda</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $orders->where('status', 'preparing')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Yetkazildi</p>
                    <p class="text-3xl font-bold text-green-600">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Buyurtmalar ro'yxati</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $orders->total() }} ta buyurtma</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            @if($orders->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <div class="p-6 hover:bg-gray-50 transition-colors order-item" 
                         data-status="{{ $order->status }}" 
                         data-number="{{ $order->order_number }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-lg font-semibold text-gray-800">#{{ $order->order_number }}</h4>
                                        @php
                                            $statusColors = [
                                                'new' => 'bg-blue-100 text-blue-800',
                                                'preparing' => 'bg-yellow-100 text-yellow-800',
                                                'on_way' => 'bg-purple-100 text-purple-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusTexts = [
                                                'new' => 'Yangi',
                                                'preparing' => 'Tayyorlanmoqda',
                                                'on_way' => 'Yolda',
                                                'delivered' => 'Yetkazildi',
                                                'cancelled' => 'Bekor qilindi',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusTexts[$order->status] ?? 'Nomalum' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                        <span><i class="fas fa-user mr-1"></i>{{ $order->customer_name }}</span>
                                        <span><i class="fas fa-phone mr-1"></i>{{ $order->customer_phone }}</span>
                                        @if($order->project)
                                            <span><i class="fas fa-store mr-1"></i>{{ $order->project->name }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                        <span><i class="fas fa-clock mr-1"></i>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                        @if($order->courier)
                                            <span><i class="fas fa-truck mr-1"></i>{{ $order->courier->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-800 mb-2">
                                    {{ number_format($order->total_price) }} so'm
                                </div>
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="viewOrder({{ $order->id }})" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" 
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                                <div class="py-2">
                                                    <button onclick="updateStatus({{ $order->id }}, 'preparing')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-utensils mr-2"></i>Tayyorlanmoqda
                                                    </button>
                                                    <button onclick="updateStatus({{ $order->id }}, 'on_way')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-truck mr-2"></i>Yolda
                                                    </button>
                                                    <button onclick="updateStatus({{ $order->id }}, 'delivered')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-check mr-2"></i>Yetkazildi
                                                    </button>
                                                    <button onclick="updateStatus({{ $order->id }}, 'cancelled')" 
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <i class="fas fa-times mr-2"></i>Bekor qilish
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($order->orderItems->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Buyurtma tarkibi:</span>
                                    <span class="text-sm text-gray-500">{{ $order->orderItems->count() }} ta taom</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($order->orderItems->take(3) as $item)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <span class="text-sm text-gray-700">{{ $item->menuItem->name }}</span>
                                            <span class="text-sm font-medium text-gray-800">{{ $item->quantity }}x</span>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 3)
                                        <div class="flex items-center justify-center p-2 bg-gray-50 rounded">
                                            <span class="text-sm text-gray-500">+{{ $order->orderItems->count() - 3 }} ta boshqa</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Buyurtmalar mavjud emas</h3>
                    <p class="text-gray-500">Hozirda hech qanday buyurtma yo'q</p>
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
        const customerName = item.querySelector('.text-gray-800').textContent.toLowerCase();
        
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
        });
    }
}

// Auto-refresh orders every 30 seconds
setInterval(function() {
    // You can add AJAX call here to refresh orders
    console.log('Refreshing orders...');
}, 30000);
</script>
@endsection 