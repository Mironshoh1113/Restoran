@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Xush kelibsiz, {{ Auth::user()->name }}!</h1>
                <p class="text-blue-100">Bugun {{ now()->format('d.m.Y') }} - {{ now()->format('H:i') }}</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Restoranlar</p>
                    <p class="text-3xl font-bold text-blue-600">{{ Auth::user()->ownedRestaurants()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>12% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Buyurtmalar</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ \App\Models\Order::whereHas('project', function($query) {
                            $query->whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'));
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>8% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kuryerlar</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        {{ \App\Models\Courier::whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'))->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>5% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Taomlar</p>
                    <p class="text-3xl font-bold text-purple-600">
                        {{ \App\Models\MenuItem::whereHas('category.project', function($query) {
                            $query->whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'));
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>15% o'tgan oydan</span>
            </div>
        </div>
    </div>

    <!-- Charts and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">So'nggi buyurtmalar</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Barchasini ko'rish
                    </a>
                </div>
            </div>
            <div class="p-6">
                @php
                    $recentOrders = \App\Models\Order::whereHas('project', function($query) {
                        $query->whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'));
                    })->with(['project.restaurant'])->latest()->limit(5)->get();
                @endphp
                
                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-bag text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">#{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->customer_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->project->restaurant->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">{{ number_format($order->total_price) }} so'm</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
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
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusTexts[$order->status] ?? 'Nomalum' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Hozirda buyurtmalar mavjud emas</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Tezkor amallar</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <a href="{{ route('admin.restaurants.index') }}" 
                       class="flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-store text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Restoranlarni boshqarish</p>
                            <p class="text-sm text-gray-500">Menyu va sozlamalar</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-blue-600"></i>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center p-3 text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-shopping-cart text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Buyurtmalarni ko'rish</p>
                            <p class="text-sm text-gray-500">Holat va boshqarish</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-green-600"></i>
                    </a>

                    <a href="#" 
                       class="flex items-center p-3 text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                            <i class="fas fa-users text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Kuryerlarni boshqarish</p>
                            <p class="text-sm text-gray-500">Tayinlash va nazorat</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-yellow-600"></i>
                    </a>

                    <a href="#" 
                       class="flex items-center p-3 text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Statistika</p>
                            <p class="text-sm text-gray-500">Hisobotlar va tahlil</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 group-hover:text-purple-600"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Faollik tarixi</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Yangi buyurtma qabul qilindi</p>
                        <p class="text-xs text-gray-500">#1234 - Oshxona Restaurant</p>
                        <p class="text-xs text-gray-400">2 daqiqa oldin</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-truck text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Buyurtma kuryerga tayinlandi</p>
                        <p class="text-xs text-gray-500">#1230 - Kuryer Ahmad</p>
                        <p class="text-xs text-gray-400">15 daqiqa oldin</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Yangi taom qo'shildi</p>
                        <p class="text-xs text-gray-500">Lag'mon - Oshxona Restaurant</p>
                        <p class="text-xs text-gray-400">1 soat oldin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover\\:shadow-md');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Auto-refresh dashboard data every 30 seconds
    setInterval(function() {
        // You can add AJAX call here to refresh data
        console.log('Refreshing dashboard data...');
    }, 30000);
});
</script>
@endsection
