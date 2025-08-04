@extends('admin.layouts.app')

@section('title', 'Buyurtma ma\'lumotlari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buyurtma #{{ $order->order_number }}</h1>
            <p class="text-gray-600">{{ $order->project->name ?? 'N/A' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Order Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Buyurtma holati</h3>
                <p class="text-sm text-gray-600">Joriy holatni o'zgartiring</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                @if($order->status === 'new') bg-blue-100 text-blue-800
                @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                @elseif($order->status === 'on_way') bg-purple-100 text-purple-800
                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mt-4">
            @csrf
            @method('PATCH')
            <div class="flex items-center space-x-3">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="new" {{ $order->status === 'new' ? 'selected' : '' }}>Yangi</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                    <option value="on_way" {{ $order->status === 'on_way' ? 'selected' : '' }}>Yolda</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Yetkazildi</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Bekor qilindi</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Yangilash
                </button>
            </div>
        </form>
    </div>

    <!-- Order Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Mijoz ma'lumotlari</h3>
            <div class="space-y-3">
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
                    <span class="font-medium">{{ $order->delivery_address ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">To'lov turi:</span>
                    <span class="font-medium">{{ ucfirst($order->payment_type ?? 'N/A') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Buyurtma vaqti:</span>
                    <span class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                @if($order->delivered_at)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Yetkazilgan vaqt:</span>
                        <span class="font-medium">{{ $order->delivered_at->format('d.m.Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Buyurtma xulosasi</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Jami summa:</span>
                    <span class="text-xl font-bold text-blue-600">{{ number_format($order->total_amount, 0, ',', ' ') }} so'm</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Yetkazib berish:</span>
                    <span class="font-medium">{{ number_format($order->delivery_fee ?? 0, 0, ',', ' ') }} so'm</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">To'lov holati:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $order->is_paid ? 'To\'langan' : 'To\'lanmagan' }}
                    </span>
                </div>
                @if($order->courier)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Kuryer:</span>
                        <span class="font-medium">{{ $order->courier->name }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Izoh:</span>
                    <span class="font-medium">{{ $order->notes ?? 'Izoh yo\'q' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Buyurtma tarkibi</h3>
        </div>
        
        <div class="p-6">
            @if($order->orderItems->count() > 0)
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</h4>
                                    <p class="text-sm text-gray-500">{{ $item->menuItem->category->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-600">{{ $item->quantity }}x</span>
                                <span class="font-medium">{{ number_format($item->price, 0, ',', ' ') }} so'm</span>
                                <span class="font-bold text-blue-600">{{ number_format($item->quantity * $item->price, 0, ',', ' ') }} so'm</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Buyurtma elementlari topilmadi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Courier Assignment -->
    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kuryer tayinlash</h3>
            
            @php
                $availableCouriers = \App\Models\Courier::where('restaurant_id', $order->project->restaurant_id ?? null)->get();
            @endphp
            
            @if($availableCouriers->count() > 0)
                <form action="{{ route('admin.orders.assign-courier', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center space-x-3">
                        <select name="courier_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Kuryer tanlang</option>
                            @foreach($availableCouriers as $courier)
                                <option value="{{ $courier->id }}" {{ $order->courier_id === $courier->id ? 'selected' : '' }}>
                                    {{ $courier->name }} ({{ $courier->phone }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            Tayinlash
                        </button>
                    </div>
                </form>
            @else
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        <span class="text-sm text-yellow-800">Bu restoran uchun kuryerlar mavjud emas</span>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection 