@extends('admin.layouts.app')

@section('title', 'Buyurtma ma\'lumotlari')

@section('content')
<div class="space-y-4">
    <!-- Compact Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Buyurtma #{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-600">{{ $order->project->name ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Compact Order Status -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Buyurtma holati</h3>
                <p class="text-xs text-gray-600">Joriy holatni o'zgartiring</p>
            </div>
            <span class="px-2 py-1 text-xs font-semibold rounded 
                @if($order->status === 'new') bg-blue-100 text-blue-800
                @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                @elseif($order->status === 'on_way') bg-purple-100 text-purple-800
                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ $order->status_with_emoji }}
            </span>
        </div>
        
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mb-3">
            @csrf
            @method('PATCH')
            <div class="flex items-center space-x-2">
                <select name="status" class="px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="new" {{ $order->status === 'new' ? 'selected' : '' }}>🆕 Yangi</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>👨‍🍳 Tayyorlanmoqda</option>
                    <option value="on_way" {{ $order->status === 'on_way' ? 'selected' : '' }}>🚚 Yolda</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Yetkazildi</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Bekor qilindi</option>
                </select>
                <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors text-sm">
                    Yangilash
                </button>
            </div>
        </form>
        
        <form action="{{ route('admin.orders.update-payment', $order) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">To'lov usuli</label>
                    <select name="payment_method" class="px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @php
                            $allowed = $order->restaurant->payment_methods ?? ['cash','card'];
                            if (empty($allowed)) { $allowed = ['cash','card','click','payme']; }
                        @endphp
                        @if(in_array('cash', $allowed))
                            <option value="cash" {{ ($order->payment_method ?? $order->payment_type) === 'cash' ? 'selected' : '' }}>Naqd</option>
                        @endif
                        @if(in_array('card', $allowed))
                            <option value="card" {{ ($order->payment_method ?? $order->payment_type) === 'card' ? 'selected' : '' }}>Karta</option>
                        @endif
                        @if(in_array('click', $allowed))
                            <option value="click" {{ ($order->payment_method ?? $order->payment_type) === 'click' ? 'selected' : '' }}>Click</option>
                        @endif
                        @if(in_array('payme', $allowed))
                            <option value="payme" {{ ($order->payment_method ?? $order->payment_type) === 'payme' ? 'selected' : '' }}>Payme</option>
                        @endif
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">To'langanmi?</label>
                    <select name="is_paid" class="px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="0" {{ !$order->is_paid ? 'selected' : '' }}>Yo'q</option>
                        <option value="1" {{ $order->is_paid ? 'selected' : '' }}>Ha</option>
                    </select>
                </div>
                <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors text-sm">Saqlash</button>
            </div>
        </form>
    </div>

    <!-- Compact Order Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-800 mb-3">Mijoz ma'lumotlari</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Ism:</span>
                    <span class="font-medium">{{ $order->customer_name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Telefon:</span>
                    <span class="font-medium">{{ $order->customer_phone ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Manzil:</span>
                    <span class="font-medium truncate">{{ Str::limit($order->delivery_address ?? 'N/A', 25) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">To'lov:</span>
                    <span class="font-medium">{{ ucfirst($order->payment_method ?? $order->payment_type ?? 'N/A') }}</span>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-800 mb-3">Buyurtma xulosasi</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Jami:</span>
                    <span class="text-lg font-bold text-blue-600">{{ number_format($order->total_amount ?? $order->total_price ?? ($order->orderItems->sum(function($i){ return $i->quantity * $i->price; }) + ($order->delivery_fee ?? 0)), 0, ',', ' ') }} so'm</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Yetkazish:</span>
                    <span class="font-medium">{{ number_format($order->delivery_fee ?? 0, 0, ',', ' ') }} so'm</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">To'lov:</span>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ ($order->is_paid ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ($order->is_paid ?? false) ? 'To\'langan' : 'To\'lanmagan' }}
                    </span>
                </div>
                @if($order->courier)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Kuryer:</span>
                        <span class="font-medium">{{ $order->courier->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-800 mb-3">Vaqt ma'lumotlari</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Buyurtma:</span>
                    <span class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                @if($order->delivered_at)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Yetkazildi:</span>
                        <span class="font-medium">{{ $order->delivered_at->format('d.m.Y H:i') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Izoh:</span>
                    <span class="font-medium truncate">{{ Str::limit($order->notes ?? 'Izoh yo\'q', 20) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Order Items -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Buyurtma tarkibi</h3>
        </div>
        
        <div class="p-4">
            @if($order->orderItems->count() > 0)
                <!-- Traditional order items -->
                <div class="space-y-2">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</h4>
                                    <p class="text-xs text-gray-500">{{ $item->menuItem->category->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 text-sm">
                                <span class="text-gray-600">{{ $item->quantity }}x</span>
                                <span class="font-medium">{{ number_format($item->price, 0, ',', ' ') }} so'm</span>
                                <span class="font-bold text-blue-600">{{ number_format($item->quantity * $item->price, 0, ',', ' ') }} so'm</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(isset($order->decoded_items) && count($order->decoded_items) > 0)
                <!-- Web interface order items -->
                <div class="space-y-2">
                    @foreach($order->decoded_items as $item)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-800">{{ $item['name'] ?? 'N/A' }}</h4>
                                    <p class="text-xs text-gray-500">{{ $item['category'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 text-sm">
                                <span class="text-gray-600">{{ $item['quantity'] ?? 1 }}x</span>
                                <span class="font-medium">{{ number_format($item['price'] ?? 0, 0, ',', ' ') }} so'm</span>
                                <span class="font-bold text-blue-600">{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', ' ') }} so'm</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-shopping-cart text-gray-400 text-2xl mb-2"></i>
                    <p class="text-gray-500 text-sm">Buyurtma elementlari topilmadi</p>
                    @if($order->items)
                        <p class="text-xs text-gray-400 mt-1">Items JSON: {{ $order->items }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Compact Courier Assignment -->
    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-base font-semibold text-gray-800 mb-3">Kuryer tayinlash</h3>
            
            @php
                $availableCouriers = \App\Models\Courier::where('restaurant_id', $order->project->restaurant_id ?? null)->get();
            @endphp
            
            @if($availableCouriers->count() > 0)
                <form action="{{ route('admin.orders.assign-courier', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center space-x-2">
                        <select name="courier_id" class="px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Kuryer tanlang</option>
                            @foreach($availableCouriers as $courier)
                                <option value="{{ $courier->id }}" {{ $order->courier_id === $courier->id ? 'selected' : '' }}>
                                    {{ $courier->name }} ({{ $courier->phone }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors text-sm">
                            Tayinlash
                        </button>
                    </div>
                </form>
            @else
                <div class="p-2 bg-yellow-50 border border-yellow-200 rounded">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xs"></i>
                        <span class="text-xs text-yellow-800">Bu restoran uchun kuryerlar mavjud emas</span>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection 