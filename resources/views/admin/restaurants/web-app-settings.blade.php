@extends('admin.layouts.app')

@section('title', 'Telegram Web App sozlamalari')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Telegram Web App sozlamalari</h1>
            <p class="text-gray-600">{{ $restaurant->name }} uchun Web App dizayni</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-edit"></i>
                <span>Umumiy sozlamalar</span>
            </a>
            <a href="{{ route('admin.restaurants.show', $restaurant) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-eye"></i>
                <span>Ko'rish</span>
            </a>
        </div>
    </div>

    <!-- Web App Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-mobile-alt text-blue-600 mr-2"></i>
                Web App ko'rinishi
            </h3>
            <p class="text-sm text-gray-600 mt-1">Telegram da qanday ko'rinishini ko'ring</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Mobile Preview -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Mobil ko'rinish</h4>
                    <div class="bg-gray-900 p-4 rounded-3xl w-80 mx-auto">
                        <!-- Phone Frame -->
                        <div class="bg-white rounded-2xl overflow-hidden">
                            <!-- Status Bar -->
                            <div class="bg-gray-100 px-4 py-2 flex items-center justify-between text-xs">
                                <span>9:41</span>
                                <div class="flex items-center space-x-1">
                                    <div class="w-4 h-2 bg-gray-400 rounded"></div>
                                    <div class="w-4 h-2 bg-gray-400 rounded"></div>
                                    <div class="w-4 h-2 bg-gray-400 rounded"></div>
                                </div>
                            </div>
                            
                            <!-- Web App Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4" id="mobile-preview-header">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        @if($restaurant->logo)
                                            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <i class="fas fa-utensils text-white text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-lg">{{ $restaurant->web_app_title ?? $restaurant->name . ' - Menyu' }}</div>
                                        <div class="text-sm opacity-90">{{ $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Web App Content -->
                            <div class="p-4 space-y-3">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-800">Kategoriyalar</div>
                                    <div class="text-xs text-gray-600 mt-1">Taomlar kategoriyalari</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-800">Menyu</div>
                                    <div class="text-xs text-gray-600 mt-1">Barcha taomlar</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-800">Buyurtma</div>
                                    <div class="text-xs text-gray-600 mt-1">Savatcha va to'lov</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Desktop Preview -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Desktop ko'rinish</h4>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="bg-white rounded-lg overflow-hidden shadow-lg">
                            <!-- Browser Header -->
                            <div class="bg-gray-200 px-4 py-2 flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                <div class="ml-4 text-xs text-gray-600">Telegram Web App</div>
                            </div>
                            
                            <!-- Web App Content -->
                            <div class="p-6">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-lg mb-6" id="desktop-preview-header">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                            @if($restaurant->logo)
                                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-utensils text-white text-2xl"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-2xl font-bold">{{ $restaurant->web_app_title ?? $restaurant->name . ' - Menyu' }}</div>
                                            <div class="text-lg opacity-90">{{ $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-lg font-medium text-gray-800">Kategoriyalar</div>
                                        <div class="text-sm text-gray-600 mt-2">Taomlar kategoriyalari va filtrlari</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-lg font-medium text-gray-800">Menyu</div>
                                        <div class="text-sm text-gray-600 mt-2">Barcha taomlar va ularning narxlari</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-cog text-green-600 mr-2"></i>
                Web App sozlamalari
            </h3>
            <p class="text-sm text-gray-600 mt-1">Web App ning ko'rinishi va xatti-harakatini sozlang</p>
        </div>
        
        <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Web App Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 mb-4">Asosiy sozlamalar</h4>
                    
                    <div>
                        <label for="web_app_title" class="block text-sm font-medium text-gray-700 mb-2">Web App sarlavhasi</label>
                        <input type="text" id="web_app_title" name="web_app_title" 
                               value="{{ old('web_app_title', $restaurant->web_app_title ?? $restaurant->name . ' - Menyu') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Restoran nomi - Menyu">
                        <p class="mt-1 text-xs text-gray-500">Telegram Web App da ko'rinadigan asosiy sarlavha</p>
                    </div>
                    
                    <div>
                        <label for="web_app_description" class="block text-sm font-medium text-gray-700 mb-2">Web App tavsifi</label>
                        <textarea id="web_app_description" name="web_app_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Restoran menyusi va buyurtmalar uchun Web App">{{ old('web_app_description', $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Web App haqida qisqacha ma'lumot va tavsif</p>
                    </div>
                    
                    <div>
                        <label for="web_app_button_text" class="block text-sm font-medium text-gray-700 mb-2">Web App tugma matni</label>
                        <input type="text" id="web_app_button_text" name="web_app_button_text" 
                               value="{{ old('web_app_button_text', $restaurant->web_app_button_text ?? 'Menyuni ko\'rish') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Menyuni ko'rish">
                        <p class="mt-1 text-xs text-gray-500">Telegram da ko'rinadigan tugma matni</p>
                    </div>
                </div>
                
                <!-- Visual Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 mb-4">Visual sozlamalar</h4>
                    
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Restoran logosi</label>
                        @if($restaurant->logo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Current logo" class="w-16 h-16 object-fit-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Tavsiya etilgan o'lcham: 200x200px, PNG yoki JPG</p>
                    </div>
                    
                    <div>
                        <label for="bot_image" class="block text-sm font-medium text-gray-700 mb-2">Bot rasm</label>
                        @if($restaurant->bot_image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $restaurant->bot_image) }}" alt="Current bot image" class="w-16 h-16 object-fit-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" id="bot_image" name="bot_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Tavsiya etilgan o'lcham: 512x512px, PNG yoki JPG</p>
                    </div>
                </div>
            </div>
            
            <!-- Color Customization -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-palette text-purple-600 mr-2"></i>
                    Rang sozlamalari
                </h4>
                <p class="text-sm text-gray-600 mb-6">Web App da ko'rinadigan ranglarni tanlang</p>
                
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
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.restaurants.edit', $restaurant) }}" 
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
    
    <!-- Web App URL Info -->
    @if($restaurant->bot_token)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-link text-orange-600 mr-2"></i>
                    Web App URL va sozlamalar
                </h3>
                <p class="text-sm text-gray-600 mt-1">BotFather da qo'shish uchun kerakli ma'lumotlar</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Web App URL (Asosiy)</label>
                        <div class="flex space-x-2">
                            <input type="text" value="{{ url('/web-interface?bot_token=' . $restaurant->bot_token) }}" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                            <button onclick="copyToClipboard(this.previousElementSibling.value)" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">BotFather da ishlatish uchun</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enhanced Web App URL (Yaxshilangan)</label>
                        <div class="flex space-x-2">
                            <input type="text" value="{{ url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token) }}" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm" readonly>
                            <button onclick="copyToClipboard(this.previousElementSibling.value)" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Yangi dizayn va funksiyalar bilan</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bot nomi</label>
                        <input type="text" value="{{ $restaurant->bot_name ?? $restaurant->name . ' Bot' }}" 
                               readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bot username</label>
                        <input type="text" value="@{{ $restaurant->bot_username }}" 
                               readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded text-sm">
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">BotFather da sozlash tartibi:</h4>
                    <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>@BotFather ga xabar yuboring</li>
                        <li>/mybots buyrug'ini tanlang</li>
                        <li>O'z botingizni tanlang</li>
                        <li>"Bot Settings" ni tanlang</li>
                        <li>"Menu Button" ni tanlang</li>
                        <li>Yuqoridagi Web App URL ni joylashtiring</li>
                        <li>Menu button text ni kiriting: <strong>{{ $restaurant->web_app_button_text ?? 'Menyuni ko\'rish' }}</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Color picker synchronization
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.nextElementSibling;
    
    colorInput.addEventListener('input', function() {
        textInput.value = this.value;
        updatePreviews();
    });
    
    textInput.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            colorInput.value = this.value;
            updatePreviews();
        }
    });
});

// Live preview update
function updatePreviews() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    
    // Update mobile preview header
    const mobileHeader = document.getElementById('mobile-preview-header');
    if (mobileHeader) {
        mobileHeader.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
    }
    
    // Update desktop preview header
    const desktopHeader = document.getElementById('desktop-preview-header');
    if (desktopHeader) {
        desktopHeader.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
    }
}

// Function to copy text to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = document.createElement('button');
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Nusxalandi!';
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-green-600');
        
        // Find the closest button and insert it
        const closestButton = document.querySelector('.space-x-2 button');
        if (closestButton) {
            closestButton.parentNode.insertBefore(button, closestButton);
            button.addEventListener('transitionend', () => {
                button.remove();
            });
        }
    }, function(err) {
        console.error('Nusxalashda xatolik: ', err);
        alert('URL nusxalashda xatolik yuz berdi.');
    });
}

// Initialize previews
document.addEventListener('DOMContentLoaded', function() {
    updatePreviews();
});
</script>
@endsection 