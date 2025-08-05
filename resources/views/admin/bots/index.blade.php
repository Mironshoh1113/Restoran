@extends('admin.layouts.app')

@section('title', 'Bot sozlamalari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bot sozlamalari</h1>
            <p class="text-gray-600">Telegram botlarni boshqarish</p>
        </div>
    </div>

    <!-- Bot Settings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($restaurants as $restaurant)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $restaurant->name }}</h3>
                        @if($restaurant->owner)
                            <p class="text-sm text-gray-500">Egasi: {{ $restaurant->owner->name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($restaurant->bot_token && $restaurant->bot_username)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Faol
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times mr-1"></i>Faol emas
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fab fa-telegram w-4 mr-2"></i>
                        <span>
                            @if($restaurant->bot_username)
                                @{{ $restaurant->bot_username }}
                            @else
                                <span class="text-red-500">O'rnatilmagan</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-key w-4 mr-2"></i>
                        <span>
                            @if($restaurant->bot_token)
                                <span class="text-green-500">O'rnatilgan</span>
                            @else
                                <span class="text-red-500">O'rnatilmagan</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.bots.users', $restaurant) }}" 
                           class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" 
                           title="Foydalanuvchilar">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="{{ route('admin.bots.show', $restaurant) }}" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                           title="Sozlamalar">
                            <i class="fas fa-cog"></i>
                        </a>
                        <button onclick="testBot({{ $restaurant->id }})" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                title="Test qilish">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                    <span class="text-xs text-gray-500">{{ $restaurant->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($restaurants->count() === 0)
        <div class="text-center py-12">
            <i class="fab fa-telegram text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Hali botlar yo'q</h3>
            <p class="text-gray-500 mb-6">Bot sozlamalarini ko'rish uchun avval restoran yarating</p>
            <a href="{{ route('admin.restaurants.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 mx-auto">
                <i class="fas fa-store"></i>
                <span>Restoranlar</span>
            </a>
        </div>
    @endif
</div>

<script>
function testBot(restaurantId) {
    fetch(`/admin/bots/${restaurantId}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}
</script>
@endsection 