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

    <!-- Compact Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-3 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs">Jami</p>
                    <p class="text-lg font-bold">{{ $orders->total() }}</p>
                </div>
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-xs"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow p-3 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-xs">Yangi</p>
                    <p class="text-lg font-bold">{{ $orders->where('status', 'new')->count() }}</p>
                </div>
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded flex items-center justify-center">
                    <i class="fas fa-clock text-xs"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-3 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-xs">Faol</p>
                    <p class="text-lg font-bold">{{ $orders->whereIn('status', ['new', 'preparing', 'on_way'])->count() }}</p>
                </div>
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded flex items-center justify-center">
                    <i class="fas fa-utensils text-xs"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-3 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-xs">Bajarildi</p>
                    <p class="text-lg font-bold">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded flex items-center justify-center">
                    <i class="fas fa-check-circle text-xs"></i>
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
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 p-4">
                    @foreach($orders as $order)
                    @php
                        $cardClasses = [
                            'new' => 'bg-yellow-50 border-yellow-200 border-l-4 border-l-yellow-400',
                            'preparing' => 'bg-orange-50 border-orange-200 border-l-4 border-l-orange-400',
                            'on_way' => 'bg-purple-50 border-purple-200 border-l-4 border-l-purple-400',
                            'delivered' => 'bg-green-50 border-green-200 border-l-4 border-l-green-500',
                            'cancelled' => 'bg-red-50 border-red-200 border-l-4 border-l-red-500',
                        ];
                    @endphp
                    <div class="border rounded-lg p-3 hover:shadow-md transition-all duration-200 order-item {{ $cardClasses[$order->status] ?? 'bg-white border-gray-200' }}" 
                         data-status="{{ $order->status }}" 
                         data-number="{{ $order->order_number ?? $order->id }}">
                        
                        <!-- Compact Order Header -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded flex items-center justify-center shadow">
                                    <i class="fas fa-shopping-bag text-white text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">#{{ $order->order_number ?? $order->id }}</h4>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            @php
                                $statusColors = [
                                    'new' => 'bg-yellow-100 text-yellow-800',
                                    'preparing' => 'bg-orange-100 text-orange-800',
                                    'on_way' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusIcons = [
                                    'new' => '⏳',
                                    'preparing' => '👨‍🍳',
                                    'on_way' => '🚚',
                                    'delivered' => '✅',
                                    'cancelled' => '❌',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusIcons[$order->status] ?? '❓' }}
                            </span>
                        </div>
                        
                        <!-- Compact Customer Info -->
                        <div class="space-y-1 mb-2">
                            <div class="flex items-center space-x-1 text-xs">
                                <i class="fas fa-user text-blue-500 w-3"></i>
                                <span class="font-medium text-gray-700 truncate">{{ $order->customer_name ?? 'Nomalum' }}</span>
                            </div>
                            <div class="flex items-center space-x-1 text-xs">
                                <i class="fas fa-phone text-green-500 w-3"></i>
                                <span class="font-medium text-gray-700">{{ $order->customer_phone ?? 'Nomalum' }}</span>
                            </div>
                            @if($order->delivery_address)
                            <div class="flex items-center space-x-1 text-xs">
                                <i class="fas fa-map-marker-alt text-purple-500 w-3"></i>
                                <span class="text-gray-600 truncate">{{ Str::limit($order->delivery_address, 30) }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Inline Items Preview -->
                        @php
                            $lines = [];
                            if ($order->relationLoaded('orderItems') && $order->orderItems && $order->orderItems->count() > 0) {
                                foreach ($order->orderItems as $oi) {
                                    $lines[] = ($oi->menuItem->name ?? 'Taom') . ' x ' . (int)($oi->quantity ?? 1);
                                }
                            } elseif (is_array($order->items)) {
                                foreach ($order->items as $it) {
                                    $lines[] = ($it['name'] ?? 'Taom') . ' x ' . (int)($it['quantity'] ?? 1);
                                }
                            }
                            $preview = implode(', ', array_slice($lines, 0, 3));
                        @endphp
                        @if(!empty($preview))
                        <div class="text-xs text-gray-600 mb-2">
                            🧾 {{ $preview }}@if(count($lines) > 3), ...@endif
                        </div>
                        @endif

                        <!-- Compact Price and Actions -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($order->total_amount ?? $order->total_price ?? 0) }} so'm
                                </div>
                                @if($order->courier)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-truck mr-1"></i>{{ $order->courier->name }}
                                </div>
                                @endif
                                <div class="text-xs mt-1">
                                    <span class="px-2 py-0.5 rounded {{ $order->is_paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $order->is_paid ? 'To\'langan' : 'To\'lanmagan' }}
                                    </span>
                                    @if($order->payment_method)
                                        <span class="ml-1 text-gray-500">({{ strtoupper($order->payment_method) }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('admin.orders.show', ['order' => $order->id, 'page' => request('page')]) }}" 
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-all duration-200 block" title="Batafsil">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <!-- Quick status actions -->
                                <div class="flex items-center space-x-1">
                                    @if($order->status !== 'preparing')
                                        <button class="p-1.5 text-orange-600 hover:bg-orange-50 rounded text-xs" title="Tayyorlanmoqda"
                                                onclick="quickUpdateStatus({{ $order->id }}, 'preparing', this)"><i class="fas fa-utensils"></i></button>
                                    @endif
                                    @if($order->status !== 'on_way')
                                        <button class="p-1.5 text-purple-600 hover:bg-purple-50 rounded text-xs" title="Yolda"
                                                onclick="quickUpdateStatus({{ $order->id }}, 'on_way', this)"><i class="fas fa-truck"></i></button>
                                    @endif
                                    @if($order->status !== 'delivered')
                                        <button class="p-1.5 text-green-600 hover:bg-green-50 rounded text-xs" title="Yetkazildi"
                                                onclick="quickUpdateStatus({{ $order->id }}, 'delivered', this)"><i class="fas fa-check"></i></button>
                                    @endif
                                    @if($order->status !== 'cancelled')
                                        <button class="p-1.5 text-red-600 hover:bg-red-50 rounded text-xs" title="Bekor qilish"
                                                onclick="quickUpdateStatus({{ $order->id }}, 'cancelled', this)"><i class="fas fa-times"></i></button>
                                    @endif
                                    @if(!$order->is_paid)
                                        <button class="p-1.5 text-teal-700 hover:bg-teal-50 rounded text-xs" title="To'langan deb belgilash"
                                                onclick="markAsPaid({{ $order->id }}, '{{ $order->payment_method ?? 'cash' }}', this)"><i class="fas fa-money-bill-wave"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
 
                        <!-- Collapsible details -->
                        <div class="mt-2">
                            <button class="text-xs text-gray-600 hover:text-gray-800" onclick="toggleDetails(this)">
                                <i class="fas fa-chevron-down mr-1"></i> Buyurtma batafsil
                            </button>
                            <div class="hidden text-xs text-gray-700 mt-2 space-y-1">
                                @if(!empty($lines))
                                    <div>
                                        <span class="font-semibold">Taomlar:</span>
                                        <ul class="list-disc ml-4">
                                            @if($order->relationLoaded('orderItems') && $order->orderItems && $order->orderItems->count() > 0)
                                                @foreach($order->orderItems as $oi)
                                                    <li>{{ $oi->menuItem->name ?? 'Taom' }} x {{ (int)($oi->quantity ?? 1) }} — {{ number_format((float)($oi->subtotal ?? ($oi->price * (int)($oi->quantity ?? 1))), 0, ',', ' ') }} so'm</li>
                                                @endforeach
                                            @else
                                                @foreach($lines as $l)
                                                    <li>{{ $l }}</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                                @if($order->delivery_address)
                                    <div><span class="font-semibold">Manzil:</span> {{ $order->delivery_address }}</div>
                                @endif
                                @if($order->notes)
                                    <div><span class="font-semibold">Izoh:</span> {{ $order->notes }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-4 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $orders->appends(request()->query())->links() }}
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
        .then(r => r.json())
        .then(data => { if (data.success) { location.reload(); } else { alert('Xatolik yuz berdi'); } })
        .catch(error => {
            console.error('Error:', error);
            alert('Xatolik yuz berdi');
        });
    }
}

// Quick inline update without reload
function quickUpdateStatus(orderId, status, btn) {
    btn.disabled = true;
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status })
    }).then(r => r.json()).then(data => {
        if (!data.success) throw new Error('update_failed');
        // Update badge color/icon by reloading minimal UI (fallback: refresh)
        location.reload();
    }).catch(() => { alert('Xatolik yuz berdi'); }).finally(() => { btn.disabled = false; });
}

function toggleDetails(trigger) {
    const wrap = trigger.nextElementSibling;
    if (!wrap) return;
    const icon = trigger.querySelector('i');
    if (wrap.classList.contains('hidden')) {
        wrap.classList.remove('hidden');
        if (icon) icon.classList.replace('fa-chevron-down','fa-chevron-up');
    } else {
        wrap.classList.add('hidden');
        if (icon) icon.classList.replace('fa-chevron-up','fa-chevron-down');
    }
}

function markAsPaid(orderId, method, btn) {
    btn.disabled = true;
    fetch(`/admin/orders/${orderId}/payment`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ payment_method: method || 'cash', is_paid: true })
    }).then(r => r.json()).then(data => {
        if (!data.success) throw new Error('update_failed');
        location.reload();
    }).catch(() => { alert('To\'lovni belgilashda xatolik'); }).finally(() => { btn.disabled = false; });
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