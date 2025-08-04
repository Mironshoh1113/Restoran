@extends('admin.layouts.app')

@section('title', 'Taomlar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }} - {{ $project->name }}</h1>
            <p class="text-gray-600">{{ $category->name }} - Taomlarni boshqarish</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.categories.index', [$restaurant, $project]) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
            <a href="{{ route('admin.menu-items.create', [$restaurant, $project, $category]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Yangi taom</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jami taomlar</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $menuItems->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Faol taomlar</p>
                    <p class="text-3xl font-bold text-green-600">{{ $menuItems->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">O'rtacha narx</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($menuItems->avg('price'), 0) }} so'm</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($menuItems as $menuItem)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
            <div class="p-6">
                @if($menuItem->image)
                <div class="mb-4">
                    <img src="{{ Storage::url($menuItem->image) }}" 
                         alt="{{ $menuItem->name }}"
                         class="w-full h-48 object-cover rounded-lg">
                </div>
                @endif
                
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $menuItem->name }}</h3>
                        <p class="text-sm text-gray-500">{{ Str::limit($menuItem->description, 100) }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($menuItem->is_active)
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
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Narx:</span>
                        <span class="font-semibold text-lg text-green-600">{{ number_format($menuItem->price) }} so'm</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Tartib:</span>
                        <span>{{ $menuItem->sort_order }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Yaratilgan:</span>
                        <span>{{ $menuItem->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.menu-items.edit', [$restaurant, $project, $category, $menuItem]) }}" 
                       class="text-yellow-600 hover:text-yellow-700 text-sm font-medium flex items-center">
                        <i class="fas fa-edit mr-1"></i>
                        Tahrirlash
                    </a>
                    <button onclick="deleteMenuItem({{ $menuItem->id }})" 
                            class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <i class="fas fa-utensils text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Taomlar mavjud emas</h3>
                <p class="text-gray-500 mb-6">Birinchi taomni yarating va narxini belgilang</p>
                <a href="{{ route('admin.menu-items.create', [$restaurant, $project, $category]) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Taom yaratish</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<script>
function deleteMenuItem(id) {
    if (confirm('Haqiqatan ham bu taomni o\'chirmoqchimisiz? Bu amalni qaytarib bo\'lmaydi.')) {
        fetch(`/admin/restaurants/{{ $restaurant->id }}/projects/{{ $project->id }}/categories/{{ $category->id }}/menu-items/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                location.reload();
            } else if (data && data.message) {
                alert(data.message);
            } else {
                alert('Xatolik yuz berdi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Taom o\'chirishda xatolik yuz berdi');
        });
    }
}
</script>
@endsection 