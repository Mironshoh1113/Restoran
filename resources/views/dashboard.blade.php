@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Xush kelibsiz, {{ Auth::user()->name }}!</h1>
                <p class="text-orange-100">Bugun {{ now()->format('d.m.Y') }} - {{ now()->format('H:i') }}</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl text-orange-200"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Restoranlar</p>
                    <p class="text-3xl font-bold text-orange-600">{{ Auth::user()->ownedRestaurants()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>12% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Buyurtmalar</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ \App\Models\Order::whereHas('project', function($query) {
                            $query->whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'));
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>8% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kuryerlar</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ \App\Models\Courier::whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'))->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>5% o'tgan oydan</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Taomlar</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ \App\Models\MenuItem::whereHas('category.project', function($query) {
                            $query->whereIn('restaurant_id', Auth::user()->ownedRestaurants()->pluck('id'));
                        })->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                <i class="fas fa-arrow-up mr-1"></i>
                <span>15% o'tgan oydan</span>
            </div>
        </div>
    </div>

    <!-- Charts and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">So'nggi buyurtmalar</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 text-sm font-medium">
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
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-bag text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-gray-200">#{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order->project->restaurant->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($order->total_price) }} so'm</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                    @php
                                        $statusColors = [
                                            'new' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200',
                                            'preparing' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200',
                                            'on_way' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200',
                                            'delivered' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200',
                                            'cancelled' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200',
                                        ];
                                        $statusTexts = [
                                            'new' => 'Yangi',
                                            'preparing' => 'Tayyorlanmoqda',
                                            'on_way' => 'Yolda',
                                            'delivered' => 'Yetkazildi',
                                            'cancelled' => 'Bekor qilindi',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                        {{ $statusTexts[$order->status] ?? 'Nomalum' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Hozirda buyurtmalar mavjud emas</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Tezkor amallar</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <a href="{{ route('admin.restaurants.index') }}" 
                       class="flex items-center p-3 text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-colors">
                            <i class="fas fa-store text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Restoranlarni boshqarish</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Menyu va sozlamalar</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 dark:text-gray-500 group-hover:text-orange-600 dark:group-hover:text-orange-400"></i>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center p-3 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors">
                            <i class="fas fa-shopping-cart text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Buyurtmalarni ko'rish</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Holat va boshqarish</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 dark:text-gray-500 group-hover:text-green-600 dark:group-hover:text-green-400"></i>
                    </a>

                    <a href="#" 
                       class="flex items-center p-3 text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-600 dark:hover:text-yellow-400 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800 transition-colors">
                            <i class="fas fa-users text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Kuryerlarni boshqarish</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tayinlash va nazorat</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 dark:text-gray-500 group-hover:text-yellow-600 dark:group-hover:text-yellow-400"></i>
                    </a>

                    <a href="#" 
                       class="flex items-center p-3 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-colors">
                            <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Statistika</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Hisobotlar va tahlil</p>
                        </div>
                        <i class="fas fa-chevron-right ml-auto text-gray-400 dark:text-gray-500 group-hover:text-purple-600 dark:group-hover:text-purple-400"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Faollik tarixi</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 dark:text-green-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Yangi buyurtma qabul qilindi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">#1234 - Oshxona Restaurant</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">2 daqiqa oldin</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-truck text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Buyurtma kuryerga tayinlandi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">#1230 - Kuryer Ahmad</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">15 daqiqa oldin</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-purple-600 dark:text-purple-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Yangi taom qo'shildi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lag'mon - Oshxona Restaurant</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">1 soat oldin</p>
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
