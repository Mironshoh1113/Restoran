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
        
        <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Tavsif</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $restaurant->description) }}</textarea>
                        @error('description')
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
                        <label for="working_hours" class="block text-sm font-medium text-gray-700 mb-2">Ish vaqti</label>
                        <input type="text" id="working_hours" name="working_hours" value="{{ old('working_hours', $restaurant->working_hours) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="9:00 - 23:00">
                        @error('working_hours')
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

                    <div>
                        <label for="bot_name" class="block text-sm font-medium text-gray-700 mb-2">Bot nomi</label>
                        <input type="text" id="bot_name" name="bot_name" value="{{ old('bot_name', $restaurant->bot_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Restoran bot">
                        @error('bot_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bot_description" class="block text-sm font-medium text-gray-700 mb-2">Bot tavsifi</label>
                        <textarea id="bot_description" name="bot_description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Restoran menyusi va buyurtmalar uchun bot">{{ old('bot_description', $restaurant->bot_description) }}</textarea>
                        @error('bot_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
            
            <!-- Logo and Bot Image Upload -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-800 mb-4">Rasm va logo sozlamalari</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Restoran logosi</label>
                        @if($restaurant->logo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Current logo" class="w-20 h-20 object-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Tavsiya etilgan o'lcham: 200x200px, PNG yoki JPG</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bot_image" class="block text-sm font-medium text-gray-700 mb-2">Bot rasm</label>
                        @if($restaurant->bot_image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $restaurant->bot_image) }}" alt="Current bot image" class="w-20 h-20 object-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" id="bot_image" name="bot_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Tavsiya etilgan o'lcham: 512x512px, PNG yoki JPG</p>
                        @error('bot_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Color Customization -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-800 mb-4">Rang va dizayn sozlamalari</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">Asosiy rang</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="primary_color" name="primary_color" 
                                   value="{{ old('primary_color', $restaurant->primary_color ?? '#667eea') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('primary_color', $restaurant->primary_color ?? '#667eea') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('primary_color').value = this.value">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Header va asosiy elementlar uchun</p>
                    </div>
                    
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">Ikkilamchi rang</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="secondary_color" name="secondary_color" 
                                   value="{{ old('secondary_color', $restaurant->secondary_color ?? '#764ba2') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('secondary_color', $restaurant->secondary_color ?? '#764ba2') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Gradient va ikkilamchi elementlar uchun</p>
                    </div>
                    
                    <div>
                        <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-2">Aktsent rang</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="accent_color" name="accent_color" 
                                   value="{{ old('accent_color', $restaurant->accent_color ?? '#ff6b35') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('accent_color', $restaurant->accent_color ?? '#ff6b35') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('accent_color').value = this.value">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Narxlar va tugmalar uchun</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="text_color" class="block text-sm font-medium text-gray-700 mb-2">Matn rangi</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="text_color" name="text_color" 
                                   value="{{ old('text_color', $restaurant->text_color ?? '#2c3e50') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('text_color', $restaurant->text_color ?? '#2c3e50') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('text_color').value = this.value">
                        </div>
                    </div>
                    
                    <div>
                        <label for="bg_color" class="block text-sm font-medium text-gray-700 mb-2">Fon rangi</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="bg_color" name="bg_color" 
                                   value="{{ old('bg_color', $restaurant->bg_color ?? '#f8f9fa') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('bg_color', $restaurant->bg_color ?? '#f8f9fa') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('bg_color').value = this.value">
                        </div>
                    </div>
                    
                    <div>
                        <label for="card_bg" class="block text-sm font-medium text-gray-700 mb-2">Karta fon rangi</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="card_bg" name="card_bg" 
                                   value="{{ old('card_bg', $restaurant->card_bg ?? '#ffffff') }}"
                                   class="w-12 h-10 border border-gray-300 rounded-lg">
                            <input type="text" value="{{ old('card_bg', $restaurant->card_bg ?? '#ffffff') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="document.getElementById('card_bg').value = this.value">
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="border_radius" class="block text-sm font-medium text-gray-700 mb-2">Burchak radiusi</label>
                        <select id="border_radius" name="border_radius" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="8px" {{ (old('border_radius', $restaurant->border_radius ?? '16px') == '8px') ? 'selected' : '' }}>8px - Minimal</option>
                            <option value="12px" {{ (old('border_radius', $restaurant->border_radius ?? '16px') == '12px') ? 'selected' : '' }}>12px - O'rta</option>
                            <option value="16px" {{ (old('border_radius', $restaurant->border_radius ?? '16px') == '16px') ? 'selected' : '' }}>16px - Standart</option>
                            <option value="20px" {{ (old('border_radius', $restaurant->border_radius ?? '16px') == '20px') ? 'selected' : '' }}>20px - Katta</option>
                            <option value="24px" {{ (old('border_radius', $restaurant->border_radius ?? '16px') == '24px') ? 'selected' : '' }}>24px - Juda katta</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="shadow" class="block text-sm font-medium text-gray-700 mb-2">Soya effekti</label>
                        <select id="shadow" name="shadow" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0 2px 8px rgba(0,0,0,0.1)" {{ (old('shadow', $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)') == '0 2px 8px rgba(0,0,0,0.1)') ? 'selected' : '' }}>Minimal</option>
                            <option value="0 4px 16px rgba(0,0,0,0.1)" {{ (old('shadow', $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)') == '0 4px 16px rgba(0,0,0,0.1)') ? 'selected' : '' }}>O'rta</option>
                            <option value="0 8px 32px rgba(0,0,0,0.1)" {{ (old('shadow', $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)') == '0 8px 32px rgba(0,0,0,0.1)') ? 'selected' : '' }}>Standart</option>
                            <option value="0 12px 48px rgba(0,0,0,0.15)" {{ (old('shadow', $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)') == '0 12px 48px rgba(0,0,0,0.15)') ? 'selected' : '' }}>Katta</option>
                            <option value="0 16px 64px rgba(0,0,0,0.2)" {{ (old('shadow', $restaurant->shadow ?? '0 8px 32px rgba(0,0,0,0.1)') == '0 16px 64px rgba(0,0,0,0.2)') ? 'selected' : '' }}>Juda katta</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Business Settings -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-800 mb-4">Biznes sozlamalari</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">Yetkazib berish to'lovi (so'm)</label>
                        <input type="number" id="delivery_fee" name="delivery_fee" 
                               value="{{ old('delivery_fee', $restaurant->delivery_fee ?? 0) }}" min="0" step="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimal buyurtma miqdori (so'm)</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" 
                               value="{{ old('min_order_amount', $restaurant->min_order_amount ?? 0) }}" min="0" step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">To'lov usullari</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="payment_methods[]" value="cash" 
                                   {{ in_array('cash', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Naqd pul</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="payment_methods[]" value="card" 
                                   {{ in_array('card', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Karta</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="payment_methods[]" value="click" 
                                   {{ in_array('click', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Click</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="payment_methods[]" value="payme" 
                                   {{ in_array('payme', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Payme</span>
                        </label>
                    </div>
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

<script>
// Color picker synchronization
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.nextElementSibling;
    
    colorInput.addEventListener('input', function() {
        textInput.value = this.value;
    });
    
    textInput.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            colorInput.value = this.value;
        }
    });
});
</script>
@endsection 