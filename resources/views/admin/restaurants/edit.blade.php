@extends('admin.layouts.app')

@section('title', 'Restoran tahrirlash')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Restoran tahrirlash</h1>
            <p class="text-gray-600">{{ $restaurant->name }}</p>
        </div>
        <a href="{{ route('admin.restaurants.show', $restaurant) }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Orqaga</span>
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Restoran ma'lumotlari</h3>
        </div>
        
        <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 mb-4">Asosiy ma'lumotlar</h4>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restoran nomi</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon raqam</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Manzil</label>
                        <textarea id="address" name="address" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $restaurant->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $restaurant->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Faol</span>
                        </label>
                    </div>
                </div>
                
                <!-- Telegram Bot Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 mb-4">Telegram Bot sozlamalari</h4>
                    
                    <div>
                        <label for="bot_username" class="block text-sm font-medium text-gray-700 mb-2">Bot username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">@</span>
                            <input type="text" id="bot_username" name="bot_username" value="{{ old('bot_username', $restaurant->bot_username) }}"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="bot_username">
                        </div>
                        @error('bot_username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bot_token" class="block text-sm font-medium text-gray-700 mb-2">Bot token</label>
                        <input type="text" id="bot_token" name="bot_token" value="{{ old('bot_token', $restaurant->bot_token) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                        @error('bot_token')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">BotFather dan olingan token</p>
                    </div>
                    
                    @if($restaurant->bot_token && $restaurant->bot_username)
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span class="text-sm text-green-800">Bot to'liq sozlangan</span>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                <span class="text-sm text-yellow-800">Bot to'liq sozlanmagan</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.restaurants.show', $restaurant) }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Bekor qilish
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Saqlash
                </button>
            </div>
        </form>
    </div>
    
    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-red-800">Xavfli zona</h3>
        </div>
        
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-medium text-gray-800">Restoranni o'chirish</h4>
                    <p class="text-sm text-gray-600">Bu amalni qaytarib bo'lmaydi. Barcha ma'lumotlar o'chiriladi.</p>
                </div>
                <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST" 
                      onsubmit="return confirm('Haqiqatan ham bu restoranni o\'chirmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        O'chirish
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 