@extends('admin.layouts.app')

@section('title', 'Restoran ma\'lumotlari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }}</h1>
            <p class="text-gray-600">Restoran ma'lumotlari</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-edit"></i>
                <span>Tahrirlash</span>
            </a>
            <a href="{{ route('admin.restaurants.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Restaurant Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Asosiy ma'lumotlar</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Nomi:</span>
                    <span class="font-medium">{{ $restaurant->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Egasi:</span>
                    <span class="font-medium">{{ $restaurant->owner->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Telefon:</span>
                    <span class="font-medium">{{ $restaurant->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Manzil:</span>
                    <span class="font-medium">{{ $restaurant->address ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Holat:</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $restaurant->is_active ? 'Faol' : 'Faol emas' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Yaratilgan:</span>
                    <span class="font-medium">{{ $restaurant->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Telegram Bot Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Telegram Bot</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Bot username:</span>
                    <span class="font-medium">
                        @if($restaurant->bot_username)
                            @{{ $restaurant->bot_username }}
                        @else
                            <span class="text-red-500">O'rnatilmagan</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Bot token:</span>
                    <span class="font-medium">
                        @if($restaurant->bot_token)
                            <span class="text-green-500">O'rnatilgan</span>
                        @else
                            <span class="text-red-500">O'rnatilmagan</span>
                        @endif
                    </span>
                </div>
                @if($restaurant->bot_token && $restaurant->bot_username)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <span class="text-sm text-green-800">Bot to'liq sozlangan</span>
                        </div>
                    </div>
                @else
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            <span class="text-sm text-yellow-800">Bot to'liq sozlanmagan</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Proyektlar</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $restaurant->projects->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kategoriyalar</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ \App\Models\Category::whereHas('project', function($query) use ($restaurant) {
                            $query->where('restaurant_id', $restaurant->id);
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Taomlar</p>
                    <p class="text-3xl font-bold text-purple-600">
                        {{ \App\Models\MenuItem::whereHas('category.project', function($query) use ($restaurant) {
                            $query->where('restaurant_id', $restaurant->id);
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Buyurtmalar</p>
                    <p class="text-3xl font-bold text-orange-600">
                        {{ \App\Models\Order::whereHas('project', function($query) use ($restaurant) {
                            $query->where('restaurant_id', $restaurant->id);
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Proyektlar</h3>
                <a href="{{ route('admin.projects.index', $restaurant) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Barchasini ko'rish
                </a>
            </div>
        </div>
        
        <div class="p-6">
            @if($restaurant->projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($restaurant->projects as $project)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-800">{{ $project->name }}</h4>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $project->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $project->is_active ? 'Faol' : 'Faol emas' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($project->description, 100) }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                <span>{{ $project->categories->count() }} kategoriya</span>
                                <span>{{ $project->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.categories.index', [$restaurant, $project]) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-xs">
                                    Kategoriyalar
                                </a>
                                <a href="{{ route('admin.projects.edit', [$restaurant, $project]) }}" 
                                   class="text-yellow-600 hover:text-yellow-700 text-xs">
                                    Tahrirlash
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 mb-4">Hali proyektlar qo'shilmagan</p>
                    <a href="{{ route('admin.projects.create', $restaurant) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-2"></i>Loyiha qo'shish
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">So'nggi buyurtmalar</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Barchasini ko'rish
                </a>
            </div>
        </div>
        
        <div class="p-6">
            @php
                $recentOrders = \App\Models\Order::whereHas('project', function($query) use ($restaurant) {
                    $query->where('restaurant_id', $restaurant->id);
                })->with(['project', 'courier'])->latest()->take(5)->get();
            @endphp
            
            @if($recentOrders->count() > 0)
                <div class="space-y-3">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">#{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->project->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status === 'new') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'on_way') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $order->status_with_emoji }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Hali buyurtmalar yo'q</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 