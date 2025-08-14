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
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.restaurants.show', $restaurant) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-eye"></i>
                <span>Ko'rish</span>
            </a>
            <a href="{{ route('admin.restaurants.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data" id="restaurantForm">
        @csrf
        @method('PUT')
        
        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button type="button" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600" data-tab="basic-info">
                        <i class="fas fa-info-circle mr-2"></i>Asosiy ma'lumotlar
                    </button>
                    <button type="button" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="telegram-bot">
                        <i class="fab fa-telegram-plane mr-2"></i>Telegram Bot
                    </button>
                    <button type="button" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="web-app">
                        <i class="fas fa-mobile-alt mr-2"></i>Web App
                    </button>
                    <button type="button" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="design">
                        <i class="fas fa-palette mr-2"></i>Dizayn
                    </button>
                    <button type="button" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="business">
                        <i class="fas fa-business-time mr-2"></i>Biznes
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Basic Information Tab -->
                <div id="basic-info-tab" class="tab-content">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Asosiy ma'lumotlar</h3>
                            <p class="text-sm text-gray-600 mb-6">Restoran haqidagi asosiy ma'lumotlarni kiriting</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restoran nomi *</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Restoran nomi kiriting">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefon raqam *</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="+998901234567">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Tavsif</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Restoran haqida qisqacha ma'lumot">{{ old('description', $restaurant->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Manzil *</label>
                            <textarea id="address" name="address" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Restoran manzili">{{ old('address', $restaurant->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="working_hours" class="block text-sm font-medium text-gray-700 mb-2">Ish vaqti</label>
                                <input type="text" id="working_hours" name="working_hours" value="{{ old('working_hours', $restaurant->working_hours) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="9:00 - 23:00">
                                @error('working_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex items-center justify-center">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $restaurant->is_active) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Restoran faol</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Telegram Bot Tab -->
                <div id="telegram-bot-tab" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Telegram Bot sozlamalari</h3>
                            <p class="text-sm text-gray-600 mb-6">BotFather dan olingan ma'lumotlarni kiriting</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <label for="bot_name" class="block text-sm font-medium text-gray-700 mb-2">Bot nomi</label>
                                <input type="text" id="bot_name" name="bot_name" value="{{ old('bot_name', $restaurant->bot_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Restoran Bot">
                                @error('bot_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                            <label for="bot_description" class="block text-sm font-medium text-gray-700 mb-2">Bot tavsifi</label>
                            <textarea id="bot_description" name="bot_description" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Restoran menyusi va buyurtmalar uchun bot">{{ old('bot_description', $restaurant->bot_description) }}</textarea>
                            @error('bot_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bot Status -->
                        @if($restaurant->bot_token && $restaurant->bot_username)
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-sm text-green-800 font-medium">Bot to'liq sozlangan</span>
                                </div>
                                <p class="text-xs text-green-600 mt-1">Barcha kerakli ma'lumotlar to'ldirilgan</p>
                            </div>
                        @else
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    <span class="text-sm text-yellow-800 font-medium">Bot to'liq sozlanmagan</span>
                                </div>
                                <p class="text-xs text-yellow-600 mt-1">Bot token va username ni to'ldiring</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Web App Tab -->
                <div id="web-app-tab" class="tab-content hidden">
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Web App sozlamalari</h3>
                            <p class="text-sm text-gray-600 mb-6">Telegram Web App ning ko'rinishi va mazmunini sozlang</p>
                        </div>

                        <!-- Web App Preview -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                            <h4 class="font-medium text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-eye text-blue-600 mr-2"></i>
                                Telegram Web App ko'rinishi
                            </h4>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Mobile Preview -->
                                <div class="space-y-4">
                                    <h5 class="text-sm font-medium text-gray-700">Mobil ko'rinish</h5>
                                    <div class="bg-gray-900 p-4 rounded-3xl max-w-sm mx-auto">
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
                                            <div class="text-white p-4" id="mobile-preview-header" style="background: linear-gradient(to right, #667eea, #764ba2);">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                        @if($restaurant->logo)
                                                            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-10 h-10 rounded-full object-cover" id="mobile-logo-preview">
                                                        @else
                                                            <i class="fas fa-utensils text-white text-xl"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-lg" id="mobile-title-preview">{{ $restaurant->web_app_title ?? $restaurant->name . ' - Menyu' }}</div>
                                                        <div class="text-sm opacity-90" id="mobile-desc-preview">{{ $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar' }}</div>
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
                                    <h5 class="text-sm font-medium text-gray-700">Desktop ko'rinish</h5>
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
                                                <div class="text-white p-6 rounded-lg mb-6" id="desktop-preview-header" style="background: linear-gradient(to right, #667eea, #764ba2);">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                            @if($restaurant->logo)
                                                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Logo" class="w-12 h-12 rounded-full object-cover" id="desktop-logo-preview">
                                                            @else
                                                                <i class="fas fa-utensils text-white text-2xl"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="text-2xl font-bold" id="desktop-title-preview">{{ $restaurant->web_app_title ?? $restaurant->name . ' - Menyu' }}</div>
                                                            <div class="text-lg opacity-90" id="desktop-desc-preview">{{ $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar' }}</div>
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

                        <!-- Web App Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Settings -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800">Asosiy sozlamalar</h4>
                                
                                <div>
                                    <label for="web_app_title" class="block text-sm font-medium text-gray-700 mb-2">Web App sarlavhasi</label>
                                    <input type="text" id="web_app_title" name="web_app_title" 
                                           value="{{ old('web_app_title', $restaurant->web_app_title ?? $restaurant->name . ' - Menyu') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Restoran nomi - Menyu"
                                           onchange="updatePreviews()">
                                    <p class="mt-1 text-xs text-gray-500">Telegram Web App da ko'rinadigan sarlavha</p>
                                </div>
                                
                                <div>
                                    <label for="web_app_description" class="block text-sm font-medium text-gray-700 mb-2">Web App tavsifi</label>
                                    <textarea id="web_app_description" name="web_app_description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Restoran menyusi va buyurtmalar"
                                              onchange="updatePreviews()">{{ old('web_app_description', $restaurant->web_app_description ?? 'Restoran menyusi va buyurtmalar') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Web App haqida qisqacha ma'lumot</p>
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
                            
                            <!-- Image Settings -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-800">Rasm sozlamalari</h4>
                                
                                <div>
                                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Restoran logosi</label>
                                    @if($restaurant->logo)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Current logo" class="w-16 h-16 object-cover rounded-lg border">
                                        </div>
                                    @endif
                                    <input type="file" id="logo" name="logo" accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           onchange="previewLogo(this)">
                                    <p class="mt-1 text-xs text-gray-500">Tavsiya etilgan o'lcham: 200x200px, PNG yoki JPG</p>
                                    @error('logo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="bot_image" class="block text-sm font-medium text-gray-700 mb-2">Bot rasm</label>
                                    @if($restaurant->bot_image)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $restaurant->bot_image) }}" alt="Current bot image" class="w-16 h-16 object-cover rounded-lg border">
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

                        <!-- Web App URLs -->
                        @if($restaurant->bot_token)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                <h4 class="font-medium text-blue-800 mb-4 flex items-center">
                                    <i class="fas fa-link mr-2"></i>
                                    Web App URL va BotFather sozlamalari
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-2">Asosiy Web Interface URL</label>
                                        <div class="flex space-x-2">
                                            <input type="text" value="{{ url('/web-interface?bot_token=' . $restaurant->bot_token) }}" 
                                                   class="flex-1 px-3 py-2 bg-white border border-blue-300 rounded text-sm" readonly>
                                            <button type="button" onclick="copyToClipboard(this.previousElementSibling.value)" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-green-700 mb-2">Enhanced Web Interface URL (Yaxshilangan)</label>
                                        <div class="flex space-x-2">
                                            <input type="text" value="{{ url('/enhanced-web-interface?bot_token=' . $restaurant->bot_token) }}" 
                                                   class="flex-1 px-3 py-2 bg-white border border-green-300 rounded text-sm" readonly>
                                            <button type="button" onclick="copyToClipboard(this.previousElementSibling.value)" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 p-4 bg-white rounded-lg">
                                    <h5 class="font-medium text-gray-800 mb-3">BotFather da sozlash tartibi:</h5>
                                    <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                        <li>@BotFather ga xabar yuboring</li>
                                        <li>/mybots buyrug'ini tanlang</li>
                                        <li>O'z botingizni tanlang: <strong>@{{ $restaurant->bot_username }}</strong></li>
                                        <li>"Bot Settings" ni tanlang</li>
                                        <li>"Menu Button" ni tanlang</li>
                                        <li>Yuqoridagi URL lardan birini joylashtiring</li>
                                        <li>Menu button text: <strong>{{ $restaurant->web_app_button_text ?? 'Menyuni ko\'rish' }}</strong></li>
                                    </ol>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Design Tab -->
                <div id="design-tab" class="tab-content hidden">
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Dizayn va rang sozlamalari</h3>
                            <p class="text-sm text-gray-600 mb-6">Web App ning ko'rinishini va ranglarini sozlang</p>
                        </div>

                        <!-- Color Settings -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-palette text-purple-600 mr-2"></i>
                                Rang sozlamalari
                            </h4>
                            
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
                        </div>

                        <!-- Style Settings -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-paint-brush text-indigo-600 mr-2"></i>
                                Stil sozlamalari
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </div>
                </div>

                <!-- Business Tab -->
                <div id="business-tab" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Biznes sozlamalari</h3>
                            <p class="text-sm text-gray-600 mb-6">Yetkazib berish va to'lov sozlamalarini belgilang</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">Yetkazib berish to'lovi (so'm)</label>
                                <input type="number" id="delivery_fee" name="delivery_fee" 
                                       value="{{ old('delivery_fee', $restaurant->delivery_fee ?? 0) }}" min="0" step="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">0 qiymati bepul yetkazib berishni bildiradi</p>
                            </div>
                            
                            <div>
                                <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimal buyurtma miqdori (so'm)</label>
                                <input type="number" id="min_order_amount" name="min_order_amount" 
                                       value="{{ old('min_order_amount', $restaurant->min_order_amount ?? 0) }}" min="0" step="1000"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">Bu miqdordan kam buyurtmalar qabul qilinmaydi</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">To'lov usullari</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="payment_methods[]" value="cash" 
                                           {{ in_array('cash', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                                        Naqd pul
                                    </span>
                                </label>
                                
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="payment_methods[]" value="card" 
                                           {{ in_array('card', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                                        <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                        Karta
                                    </span>
                                </label>
                                
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="payment_methods[]" value="click" 
                                           {{ in_array('click', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                                        <i class="fas fa-mobile-alt text-orange-500 mr-2"></i>
                                        Click
                                    </span>
                                </label>
                                
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="payment_methods[]" value="payme" 
                                           {{ in_array('payme', old('payment_methods', $restaurant->payment_methods ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-3 text-sm text-gray-700 flex items-center">
                                        <i class="fas fa-wallet text-purple-500 mr-2"></i>
                                        Payme
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    Barcha o'zgarishlar avtomatik saqlanadi
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.restaurants.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                        Bekor qilish
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Saqlash</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-red-800 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Xavfli zona
            </h3>
            <p class="text-sm text-gray-600 mt-1">Ehtiyotkorlik bilan harakat qiling</p>
        </div>
        
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-medium text-gray-800">Restoranni o'chirish</h4>
                    <p class="text-sm text-gray-600">Bu amalni qaytarib bo'lmaydi. Barcha ma'lumotlar va bog'liq resurslar o'chiriladi.</p>
                </div>
                <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST" 
                      onsubmit="return confirm('DIQQAT!\n\nBu restoranni o\'chirish:\n- Barcha menyu va kategoriyalarni o\'chiradi\n- Buyurtmalar tarixini o\'chiradi\n- Bot sozlamalarini o\'chiradi\n- Barcha fayl va rasmlarni o\'chiradi\n\nHaqiqatan ham davom etmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                        <i class="fas fa-trash-alt"></i>
                        <span>O'chirish</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            tabContents.forEach(content => content.classList.add('hidden'));

            // Add active class to clicked button
            button.classList.add('active', 'border-blue-500', 'text-blue-600');
            button.classList.remove('border-transparent', 'text-gray-500');

            // Show corresponding content
            const tabId = button.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.remove('hidden');
        });
    });

    // Initialize color picker synchronization
    initializeColorPickers();
    
    // Initialize live preview updates
    updatePreviews();
});

// Color picker synchronization
function initializeColorPickers() {
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
}

// Live preview updates
function updatePreviews() {
    const primaryColor = document.getElementById('primary_color')?.value || '#667eea';
    const secondaryColor = document.getElementById('secondary_color')?.value || '#764ba2';
    const title = document.getElementById('web_app_title')?.value || 'Restoran - Menyu';
    const description = document.getElementById('web_app_description')?.value || 'Restoran menyusi va buyurtmalar';
    
    // Update gradient backgrounds
    const mobileHeader = document.getElementById('mobile-preview-header');
    const desktopHeader = document.getElementById('desktop-preview-header');
    
    if (mobileHeader) {
        mobileHeader.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
    }
    if (desktopHeader) {
        desktopHeader.style.background = `linear-gradient(to right, ${primaryColor}, ${secondaryColor})`;
    }
    
    // Update text content
    const mobileTitlePreview = document.getElementById('mobile-title-preview');
    const mobileDescPreview = document.getElementById('mobile-desc-preview');
    const desktopTitlePreview = document.getElementById('desktop-title-preview');
    const desktopDescPreview = document.getElementById('desktop-desc-preview');
    
    if (mobileTitlePreview) mobileTitlePreview.textContent = title;
    if (mobileDescPreview) mobileDescPreview.textContent = description;
    if (desktopTitlePreview) desktopTitlePreview.textContent = title;
    if (desktopDescPreview) desktopDescPreview.textContent = description;
}

// Logo preview functionality
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const mobileLogoPreview = document.getElementById('mobile-logo-preview');
            const desktopLogoPreview = document.getElementById('desktop-logo-preview');
            
            if (mobileLogoPreview) {
                mobileLogoPreview.src = e.target.result;
            }
            if (desktopLogoPreview) {
                desktopLogoPreview.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Copy to clipboard functionality
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create temporary success message
        const successMsg = document.createElement('div');
        successMsg.innerHTML = '<i class="fas fa-check mr-2"></i>URL nusxalandi!';
        successMsg.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        document.body.appendChild(successMsg);
        
        setTimeout(() => {
            document.body.removeChild(successMsg);
        }, 3000);
    }).catch(function(err) {
        console.error('Nusxalashda xatolik: ', err);
        
        // Create temporary error message
        const errorMsg = document.createElement('div');
        errorMsg.innerHTML = '<i class="fas fa-times mr-2"></i>Nusxalashda xatolik!';
        errorMsg.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        document.body.appendChild(errorMsg);
        
        setTimeout(() => {
            document.body.removeChild(errorMsg);
        }, 3000);
    });
}

// Form validation
document.getElementById('restaurantForm').addEventListener('submit', function(e) {
    const requiredFields = ['name', 'phone', 'address'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && !field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500');
            
            // Remove red border after user starts typing
            field.addEventListener('input', function() {
                this.classList.remove('border-red-500');
            }, { once: true });
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        // Show error message
        const errorMsg = document.createElement('div');
        errorMsg.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Iltimos, barcha majburiy maydonlarni to\'ldiring!';
        errorMsg.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        document.body.appendChild(errorMsg);
        
        setTimeout(() => {
            document.body.removeChild(errorMsg);
        }, 5000);
    }
});
</script>
@endsection 